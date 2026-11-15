<?php
/**
 * CPU opponent - power, in Initial Orders (TACTICAL_AI_PLAN.md section 5.3, stage T3).
 *
 * ⚠️ THE POWER BALANCE IS THE CLIENT'S. The server has no twin of shipManager.power.getReactorPower:
 * it stores whatever power rows it is sent, and "you need to turn off systems before you can commit
 * the turn" is said by the page alone (gamedata.js, shipManager.power.getShipsNegativePower). A bot
 * that commits a ship in deficit is therefore a bot CHEATING, and nothing downstream would stop it.
 * balance() is a PORT of getReactorPower and tests/ai/ordersOracle.js replays every decision
 * through the real power.js. Change the balance in the client and it must be ported here.
 *
 * What this does, and all it does:
 *   - a system the game has forced off (a ForcedOffline critical) gets the "off" row the page
 *     writes for it at the start of the turn (power.js applyForcedOfflineEntry). The server reads
 *     the critical by itself (ShipSystem::isOfflineOnTurn), so this is for agreement, not for rule;
 *   - a ship in deficit switches systems off, least useful first, until it is not:
 *       the jump engine (the bot does not jump before stage T6), weapons still loading, other
 *       systems, loaded weapons, and only then the engine and the scanner.
 * It never boosts and never overloads. A bot posts only this turn's rows, and carries nothing over:
 * the same deficit switches the same systems off again next turn, and a ship that has its power
 * back gets everything back.
 *
 * LEFT ALONE (principle P4) - no rows at all, as before this stage: bases (a reactor per section),
 * Mag-Grav reactors (every system is a draw), ships with a Power Capacitor or a Plasma Battery,
 * and a ship that somehow carries a boost or an overload this turn.
 */
class PowerDecider
{
    //What the last plan did, for tac_ai_log: array('off' => systems switched off for power, 'short' => ships still in deficit).
    public static $last = array('off' => 0, 'short' => 0);

    //Systems the bot never switches off: the page ties other rules to them.
    const KEEP_ON = array('reactor', 'cnC', 'shieldGenerator');

    /* The bot's power rows for this turn: ship id => list of AIOrderBuilder::power rows. Every row
       is also written onto the loaded system, so that what is decided next (the scanner's output,
       which guns count) sees the ship as the server will. */
    public static function plan(TacGamedata $gamedata, $botId)
    {
        $turn = (int)$gamedata->turn;
        $plan = array();
        self::$last = array('off' => 0, 'short' => 0);

        foreach (AIFallback::ownUnits($gamedata, $botId) as $ship) {
            if ($ship instanceof FighterFlight || $ship->isTerrain() || $ship->isDestroyed()) continue;
            if ($ship->getTurnDeployed($gamedata) > $turn) continue;

            $rows = array();
            foreach ($ship->systems as $system) {
                if ($system->isDestroyed() || !self::forcedOff($system, $turn) || self::hasRow($system, $turn, 1)) continue;
                $rows[] = self::switchOff($ship, $system, $turn);
            }

            //(a mine is asked for no balance by the page - only for the rows above)
            if (empty($ship->mine) && self::understands($ship, $turn)) {
                $balance = self::balance($ship, $turn);
                if ($balance < 0) {
                    foreach (self::sheddable($ship, $turn) as $system) {
                        if ($balance >= 0) break;
                        $rows[] = self::switchOff($ship, $system, $turn);
                        $balance += $system->powerReq;
                        self::$last['off']++;
                    }
                    if ($balance < 0) self::$last['short']++; //nothing left to switch off: the page lets that through too
                }
            }

            if (count($rows) > 0) $plan[(int)$ship->id] = $rows;
        }

        return $plan;
    }

    //Is this a ship whose balance the port below answers for?
    public static function understands($ship, $turn)
    {
        if (!empty($ship->base) || !empty($ship->smallBase)) return false;

        $reactor = self::reactor($ship);
        if (!$reactor || $reactor->isDestroyed() || $reactor->fixedPower) return false;
        if (self::crits($reactor, 'ForcedOfflineOneTurn', $turn) > 0) return false; //powerless: the page asks nothing of it

        foreach ($ship->systems as $system) {
            if ($system->name === 'powerCapacitor' || $system->name === 'PlasmaBattery') return false;
            if (self::hasRow($system, $turn, 2) || self::hasRow($system, $turn, 3)) return false;
        }
        return true;
    }

    /* shipManager.power.getReactorPower for a ship with one ordinary reactor and nothing boosted:
       what the reactor gives beyond the ship's needs, plus the need of every system that is off.
       Negative = the deficit the page will not let a player commit. */
    public static function balance($ship, $turn)
    {
        $reactor = self::reactor($ship);
        $sent = $reactor->stripForJson(); //the one-turn drains (Energy Draining Field, EM storm) reach the page as edfDrain
        $output = $reactor->output + $reactor->outputMod - (isset($sent->edfDrain) ? (int)$sent->edfDrain : 0);

        foreach ($ship->systems as $system) {
            if ($system->displayName === 'C&C') $output -= self::crits($system, 'tmppowerdown', $turn);
            if ($system->isDestroyed()) continue;
            if (self::isOff($system, $turn)) $output += $system->powerReq;
        }

        return $output;
    }

    /* The systems a deficit may switch off, in the order they go. What the page's Off button
       allows (SystemInfoButtons canOffline): a system that draws power, is on, and is not locked on. */
    public static function sheddable($ship, $turn)
    {
        $list = array();
        foreach ($ship->systems as $system) {
            if ($system->powerReq <= 0 || in_array($system->name, self::KEEP_ON, true)) continue;
            if ($system->isDestroyed() || self::isOff($system, $turn)) continue;
            $sent = $system->stripForJson();
            if (!empty($sent->powerLocked)) continue;

            if ($system instanceof JumpEngine) $rank = 0;
            else if ($system instanceof Weapon) $rank = ($system->loadingtime > $system->turnsloaded && $system->loadingtime > $system->overloadturns) ? 1 : 3;
            else if ($system instanceof Engine) $rank = 4;
            else if ($system instanceof Scanner) $rank = 5;
            else $rank = 2;

            //within a rank: the gun that gives least fire for its power first; anything else in its own order
            $worth = 0;
            if ($system instanceof Weapon && !($system instanceof JumpEngine)) {
                $worth = (($system->minDamage + $system->maxDamage) / 2) * max(1, (int)$system->defaultShots) * max(1, (int)$system->guns)
                    / max(1, (int)$system->loadingtime) / $system->powerReq;
            }
            $list[] = array($rank, $worth, (int)$system->id, $system);
        }

        usort($list, function ($a, $b) {
            if ($a[0] !== $b[0]) return $a[0] - $b[0];
            if ($a[1] != $b[1]) return ($a[1] < $b[1]) ? -1 : 1;
            return $a[2] - $b[2];
        });

        return array_map(function ($row) { return $row[3]; }, $list);
    }

    //shipManager.power.isOfflineOnTurn: forced off by a critical, or carrying an "off" row this turn.
    public static function isOff($system, $turn)
    {
        return self::forcedOff($system, $turn) || self::hasRow($system, $turn, 1);
    }

    private static function forcedOff($system, $turn)
    {
        return self::crits($system, 'ForcedOfflineOneTurn', $turn) > 0 || self::crits($system, 'ForcedOfflineForTurns', $turn) > 0;
    }

    //shipManager.criticals.countCriticalOnTurn, on the criticals the page is sent.
    public static function crits($system, $name, $turn)
    {
        $count = 0;
        foreach ($system->criticals as $critical) {
            if ($critical->phpclass === $name && $critical->turn <= $turn && ($critical->turnend == 0 || $critical->turnend >= $turn)) $count++;
        }
        return $count;
    }

    private static function hasRow($system, $turn, $type)
    {
        foreach ($system->power as $power) {
            if ((int)$power->turn === (int)$turn && (int)$power->type === $type) return true;
        }
        return false;
    }

    private static function switchOff($ship, $system, $turn)
    {
        $system->power[] = new PowerManagementEntry(-1, $ship->id, $system->id, 1, $turn, 0);
        return AIOrderBuilder::power($ship->id, $system->id, $turn, 1);
    }

    //shipManager.systems.getSystemByName(ship, "reactor"): the first one.
    private static function reactor($ship)
    {
        foreach ($ship->systems as $system) {
            if ($system->name === 'reactor') return $system;
        }
        return null;
    }
}
