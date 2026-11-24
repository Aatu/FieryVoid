<?php
/**
 * CPU opponent - leaving the battle through a jump point (TACTICAL_AI_PLAN.md section 10.8, stage T6c).
 *
 * The user, 2026-11-21: "Ships should jump to hyperspace when they can no longer contribute to the battle effectively (e.g. lost all
 * weapons or are so damaged they can easily be destroyed) to preserve their remaining points value, this is however quite rare and
 * most ships fight until they are destroyed or the game is won. A damaged Jump Engine trying to open a jump point can also destroy
 * the ship, so if more than 50% damage is taken the Jump should not be attempted". Ruled the same day - a ship leaves when:
 *   - it has NO USABLE WEAPON (Posture::usableGuns), or
 *   - it CANNOT GET BACK INTO THE FIGHT: nothing of it can come within reach of any enemy within AIConfig::JUMP_REACH_TURNS turns
 *     (Posture::canFight - far off the map, unable to brake or turn; this turn alone until stage T9's M2, which sent slow carriers home
 *     on turn 1 - the user's ruling of 2026-11-24), or
 *   - it is EXPECTED TO BE DESTROYED: the loaded enemy guns that can reach it - each enemy from the facing that brings most of them
 *     to bear (it will turn to it), a balanced EW split both ways - are expected to deal more than what is left of it (its primary
 *     structure and the section facing the worst of them, AIView::healthLeft), it is already "so damaged" - its primary structure
 *     down to AIConfig::JUMP_DAMAGED_SHARE (the self-play batches had sound ships leave on turn 3: a whole enemy fleet's fire, all
 *     at one ship, outweighs most ships' primary and one section; then ships whose primary was whole but which had lost the
 *     section facing the enemy) - and its own fire this turn would remove less than it is worth (ShipDecider::removed): trading
 *     itself would not pay.
 * Only through a jump point of its OWN, opened now: a vortex drive (not a legacy one - an Ancient's, a Shadow's - nor a gate), not
 * destroyed, switched on, charged, holding no jump point, and no more than half its boxes lost (the failure chance IS the share lost -
 * JumpEngine::getJumpFailureChance).
 *
 * HOW - the existing jump-out flow (JUMP_POINTS_PLAN.md), as a player does it:
 *   Initial Orders (plan()): the jump point is DECLARED where a legal plan of THIS turn ends - within the drive's range of the ship,
 *     in its line of sight (the page's test), on a hex the server's own rule allows (Firing::getVortexDeclarationBlock: no terrain,
 *     no Enormous unit, the drive charged...) - its mouth facing the way that plan's last step comes in. The plan is the one
 *     ShipDecider scores best among those (ShipDecider::$planFilter). It forms this turn and opens next turn.
 *   Movement, this turn (approachRows): the ship flies a plan that ends in the jump point's hex coming in through the mouth - one
 *     always exists, the declaration was made from one. Fire as usual.
 *   Movement, next turn (jumpOutRows): the jump point is open and the ship stands in it, entered through the mouth (a unit already in
 *     the hex is judged on the step that put it there), so its first order is the JUMP-OUT (ShipMover::JUMP_OUT, the page's Jump Out
 *     button). The server takes it off the board at the end of that Movement phase (Movement::resolveJumpOuts).
 */
class JumpDecider
{
    //Lines about the last plan() / Movement decisions, for tac_ai_log.
    public static $last = array();

    public static function reset()
    {
        self::$last = array();
    }

    /* Initial Orders: the jump points to declare this turn - ship id => array('engine' => JumpEngine, 'hex' => OffsetCoordinate,
       'facing' => int (the mouth), 'reason'). $view is the bot's masked view; $power PowerDecider::plan()'s rows (by reference): a
       ship that leaves keeps its jump engine on, which a power deficit switches off first (PowerDecider::keepOn). */
    public static function plan(AIView $view, Hazards $hazards, array &$power = array())
    {
        self::$last = array();
        $plan = array();
        $enemies = self::enemies($view);
        if (count($enemies) == 0) return $plan;

        foreach ($view->myUnits() as $ship) {
            if ($ship instanceof FighterFlight || !empty($ship->mine) || $ship->isTerrain()) continue;
            if ($view->unitBlock($ship) !== null) continue;
            $engine = self::engine($ship, $view->turn, true);
            if ($engine === null) continue;
            $reason = self::reason($view, $ship, $enemies);
            if ($reason === null) continue;

            $undo = null;
            if ($engine->isOfflineOnTurn($view->turn)) {
                $undo = PowerDecider::keepOn($ship, $engine, $power, $view->turn);
                if ($undo === null) {
                    self::$last[] = $ship->name . ': would jump out (' . $reason . '), but cannot spare the power for its jump engine';
                    continue;
                }
            }
            $site = self::site($view, $ship, $engine, $hazards);
            if ($site === null) {
                if ($undo !== null) $undo();
                self::$last[] = $ship->name . ': would jump out (' . $reason . '), but no jump point can be reached this turn';
                continue;
            }
            $plan[(int)$ship->id] = array('engine' => $engine, 'hex' => $site['hex'], 'facing' => $site['facing'], 'reason' => $reason);
            self::$last[] = sprintf('%s: JUMPING OUT (%s) - jump point at %d,%d, mouth facing %d', $ship->name, $reason, $site['hex']->q, $site['hex']->r, $site['facing']);
        }
        return $plan;
    }

    //The declaration of one plan() entry, as weaponManager.createJumpPointOrder builds it (mode = facing + 1, the hex in x / y).
    public static function declaration($ship, array $jump, $turn)
    {
        $engine = $jump['engine'];
        return AIOrderBuilder::jumpPoint($ship->id, $engine->id, $turn, $jump['facing'], $jump['hex'], (int)$engine->defaultShots, self::damageClass($engine));
    }

    /* The ship's jump engine that can open a jump point now, or null - see the header. A flight's engines are one level down and a
       flight never jumps here. $ownRowsAside: one switched off this turn only by the bot's own power rows still counts (a deficit sheds
       the jump engine first - plan() keeps it on with PowerDecider::keepOn); forced off by a critical, never. */
    public static function engine($ship, $turn, $ownRowsAside = false)
    {
        foreach (JumpEngine::getUnitJumpEngines($ship) as $engine) {
            if ($engine->isLegacyJump() || $engine->isGateJump()) continue;
            if ($engine->isDestroyed() || PowerDecider::forcedOff($engine, $turn)) continue;
            if (!$ownRowsAside && $engine->isOfflineOnTurn($turn)) continue;
            if ($engine->hasOpenVortex($turn)) continue;
            if ($engine->getVortexRechargeLoad($turn) < $engine->getLoadingTime()) continue;
            if (self::driveDamage($engine) > AIConfig::JUMP_MAX_DRIVE_DAMAGE) continue;
            return $engine;
        }
        return null;
    }

    //The share of a drive's boxes lost - which is also the chance a jump point opened with it destroys the ship.
    public static function driveDamage(JumpEngine $engine)
    {
        return ($engine->maxhealth - $engine->getRemainingHealth()) / max(1, (int)$engine->maxhealth);
    }

    /* Why this ship should leave, or null when it fights on - the user's three cases (see the header). */
    public static function reason(AIView $view, $ship, array $enemies)
    {
        if (count(Posture::usableGuns($view, $ship)) == 0) return 'no usable weapon';
        //(stage T9, M2 - the user, 2026-11-24: over AIConfig::JUMP_REACH_TURNS, not this turn alone - on turn 1 of a 42x30 game slow
        //short-ranged carriers 34 hexes off "could not get back" and jumped out, their HK control nodes with them)
        if (!Posture::canFight($view, $ship, $enemies, AIConfig::JUMP_REACH_TURNS)) return 'cannot get back into the fight';

        /* Only a ship already "so damaged" (AIConfig::JUMP_DAMAGED_SHARE): its PRIMARY structure - the boxes whose loss destroys it -
           down to that share. A sound one fights on, whatever could be thrown at it; so does one that has lost the section facing
           the enemy but not its primary - it can turn a fresh side. */
        $primary = $ship->getStructureSystem(0);
        if ($primary && $primary->getRemainingHealth() > AIConfig::JUMP_DAMAGED_SHARE * (int)$primary->maxhealth) return null;

        $incoming = 0;
        $left = null;
        foreach ($enemies as $enemy) {
            $fire = self::fireAt($view, $enemy, $ship);
            if ($fire <= 0) continue;
            $incoming += $fire;
            $health = $view->healthLeft($ship, $enemy);
            $left = ($left === null) ? $health : min($left, $health);
        }
        if ($left === null || $incoming < $left) return null;
        if (self::offence($view, $ship, $enemies) >= $view->value($ship)) return null; //it takes as much with it as it is worth: it fights
        return 'expected to be destroyed';
    }

    /* What $enemy's loaded guns are expected to do to $ship this turn, from the facing that brings the most of them to bear (it will
       turn to the crippled ship - but not bring both broadsides at once), with a BALANCED split of EW both ways: half the enemy's points
       locked on it, half the ship's own as DEW (neither side's EW for the turn is known yet; a full lock and no DEW called a fresh
       Omega facing one other "doomed"). */
    private static function fireAt(AIView $view, $enemy, $ship)
    {
        $points = ($enemy instanceof FighterFlight) ? 0 : (int)ceil(EWDecider::points($enemy, $view->turn) / 2);
        $dew = ($ship instanceof FighterFlight) ? 0 : (int)floor(EWDecider::points($ship, $view->turn) / 2);
        $kept = $enemy->EW;
        $keptOwn = $ship->EW;
        if ($points > 0) $enemy->EW[] = new EWentry(-1, $enemy->id, $view->turn, 'OEW', $points, $ship->id);
        if ($dew > 0 && !self::hasDew($ship, $view->turn)) $ship->EW[] = new EWentry(-1, $ship->id, $view->turn, 'DEW', $dew, -1);
        try {
            return self::bestFacing($view, $enemy, function () use ($view, $enemy, $ship) {
                $total = 0;
                foreach ($view->mounts($enemy) as $mount) {
                    $gun = $mount['weapon'];
                    if (!$view->plainGun($gun, $mount['fighter']) || !$view->loaded($gun)) continue;
                    $total += $view->estimate($enemy, $gun, $ship, true);
                }
                return $total;
            });
        } finally {
            $enemy->EW = $kept;
            $ship->EW = $keptOwn;
        }
    }

    //Has the ship a DEW row of this turn already (an EW decision made before this one)?
    private static function hasDew($ship, $turn)
    {
        foreach ($ship->EW as $entry) {
            if ((int)$entry->turn === (int)$turn && $entry->type === 'DEW') return true;
        }
        return false;
    }

    /* What $ship's own loaded guns could remove this turn - value, from its best facing (it may turn), each target no further than
       the damage that finishes it (ShipDecider::removed). */
    private static function offence(AIView $view, $ship, array $enemies)
    {
        $spec = array();
        foreach ($enemies as $e => $enemy) {
            $health = $view->healthLeft($enemy, $ship);
            $spec[$e] = array('health' => $health, 'worth' => $view->value($enemy) / max(1, $health));
        }
        return self::bestFacing($view, $ship, function () use ($view, $ship, $enemies, $spec) {
            $damage = array();
            foreach ($view->mounts($ship) as $g => $mount) {
                $gun = $mount['weapon'];
                if (!$view->plainGun($gun, $mount['fighter']) || !$view->loaded($gun)) continue;
                foreach ($enemies as $e => $enemy) {
                    $d = $view->estimate($ship, $gun, $enemy, true);
                    if ($d > 0) $damage[$g][$e] = $d;
                }
            }
            return ShipDecider::removed($damage, $spec);
        });
    }

    /* The most $measure gives with $unit turned to each of the six facings where it stands (a movement row on top of its list,
       taken off again) - "what could it bring to bear once it has turned". A flight's guns face its heading too. */
    private static function bestFacing(AIView $view, $unit, callable $measure)
    {
        $last = $unit->getLastMovement();
        if (!$last || !$last->position) return $measure();
        $best = 0;
        for ($facing = 0; $facing < 6; $facing++) {
            $unit->movement[] = new MovementOrder(-1, 'move', new OffsetCoordinate($last->position), 0, 0, (int)$last->speed, $facing, $facing, false, $view->turn, 0, 0);
            try {
                $best = max($best, $measure());
            } finally {
                array_pop($unit->movement);
            }
        }
        return $best;
    }

    /* Where to declare: the plan of this turn ShipDecider scores best among those ending where a jump point may be declared from
       here (legalSite), entered through the hex side its mouth will face. array('hex', 'facing', 'rows'), or null when none can. */
    private static function site(AIView $view, $ship, JumpEngine $engine, Hazards $hazards)
    {
        $mover = ShipMover::forShip($ship, $view->gamedata);
        if ($mover === null) return null;
        $start = $ship->getHexPos();
        $history = self::positions($ship->movement);
        $legal = array();
        ShipDecider::$planFilter = function (array $plan) use ($view, $ship, $engine, $start, $history, &$legal) {
            $end = $plan['position'];
            if ($start->distanceTo($end) > (int)$engine->range) return false;
            $entry = ShipMover::entryDirectionOf(array_merge($history, self::rowPositions($plan['rows'])));
            if ($entry === null) return false;
            $key = $end->q . ',' . $end->r . ',' . $entry;
            if (!isset($legal[$key])) $legal[$key] = self::legalSite($view, $ship, $engine, $end, ($entry + 3) % 6);
            return $legal[$key];
        };
        //an Initial Orders question asked of the Movement decider: its log line and timing are not a Movement decision
        $last = ShipDecider::$last;
        $timings = ShipDecider::$timings;
        try {
            $rows = ShipDecider::rows($view, $mover, $ship, $hazards);
        } finally {
            ShipDecider::$planFilter = null;
            ShipDecider::$last = $last;
            ShipDecider::$timings = $timings;
        }
        if ($rows === null || count($rows) == 0) return null;

        $end = $rows[count($rows) - 1]['position'];
        $entry = ShipMover::entryDirectionOf(array_merge($history, self::rowPositions($rows)));
        if ($entry === null) return null;
        return array('hex' => new OffsetCoordinate((int)$end['q'], (int)$end['r']), 'facing' => ($entry + 3) % 6, 'rows' => $rows);
    }

    /* May $ship declare a jump point on $hex with its mouth facing $facing now? The page's tests (weaponManager.targetHex: the drive's
       range, line of sight; getVortexHexBlock) and the server's own (Firing::getVortexDeclarationBlock - the authority: no terrain, no
       Enormous unit, the drive charged, holding nothing, one declaration a ship), asked on a throwaway order. */
    public static function legalSite(AIView $view, $ship, JumpEngine $engine, OffsetCoordinate $hex, $facing)
    {
        $from = $ship->getHexPos();
        if ($from->distanceTo($hex) > (int)$engine->range) return false;
        $blocked = is_array($view->gamedata->blockedHexes) ? $view->gamedata->blockedHexes : array();
        if (!$engine->ignoresLoS && count($blocked) > 0 && Mathlib::isLoSBlocked($from, $hex, $blocked)) return false;

        $order = new FireOrder(-1, 'ballistic', $ship->id, -1, $engine->id, -1, $view->turn, (int)$facing + 1, 0, 0, (int)$engine->defaultShots, 0, 0,
            (int)$hex->q, (int)$hex->r, self::damageClass($engine));
        $block = Closure::bind(function ($fire, $weapon, $shooter, $gamedata, $orders) {
            return self::getVortexDeclarationBlock($fire, $weapon, $shooter, $gamedata, $orders);
        }, null, Firing::class);
        return $block($order, $engine, $ship, $view->gamedata, array($order)) === null;
    }

    //The damage class the page stamps on the declaration: the weapon's type, lower case ('jumppoint').
    private static function damageClass(JumpEngine $engine)
    {
        return strtolower((string)$engine->weaponClass);
    }

    // ------------------------------------------------------------------ Movement

    /* This ship's own jump point and its state, or null: array('vortex' => the SpawnJumpPoint, 'hex', 'entry' - the direction a unit
       must be moving in to enter it, 'open' - false on the turn it was declared, while it forms). */
    public static function pending(AIView $view, $ship)
    {
        if ($ship instanceof FighterFlight) return null;
        foreach (JumpEngine::getUnitJumpEngines($ship) as $engine) {
            if ($engine->activeVortexId === null) continue;
            $vortex = $view->gamedata->getShipById((int)$engine->activeVortexId);
            if (!($vortex instanceof SpawnJumpPoint) || $vortex instanceof SpawnJumpPointExit) continue;
            if (!empty($vortex->removed) && $vortex->removedTurn !== null && $view->turn >= (int)$vortex->removedTurn) continue; //closed
            $move = $vortex->getLastMovement();
            if (!$move || !$move->position) continue;
            $open = ($vortex->spawned === null || (int)$vortex->spawned === -1 || (int)$vortex->spawned <= $view->turn);
            return array('vortex' => $vortex, 'hex' => Movement::toOffset($move->position), 'entry' => ((int)$move->facing + 3) % 6, 'open' => $open);
        }
        return null;
    }

    /* The turn the jump point forms: the best plan that ends in its hex through its mouth (ShipDecider, filtered). Null when none does
       any more - the jump is off and the ship decides as usual. */
    public static function approachRows(AIView $view, ShipMover $mover, $ship, Hazards $hazards, array $jump)
    {
        $history = self::positions($ship->movement);
        ShipDecider::$planFilter = function (array $plan) use ($jump, $history) {
            if (!$plan['position']->equals($jump['hex'])) return false;
            return ShipMover::entryDirectionOf(array_merge($history, self::rowPositions($plan['rows']))) === $jump['entry'];
        };
        try {
            $rows = ShipDecider::rows($view, $mover, $ship, $hazards);
        } finally {
            ShipDecider::$planFilter = null;
        }
        if ($rows === null) self::$last[] = $ship->name . ': cannot reach its jump point through the mouth any more - the jump is off';
        return $rows;
    }

    /* A ship that should leave but holds no jump point - too fast to end within the drive's range this turn (Initial Orders found no
       site): its slowest plans, so that next turn it can. Null when it should not leave, cannot open a jump point at all, or cannot
       slow down (a ship whose retro thrusters are gone flies on - the case of self-play game 5749). */
    public static function brakeRows(AIView $view, ShipMover $mover, $ship, Hazards $hazards)
    {
        if ($ship instanceof FighterFlight || self::engine($ship, $view->turn, true) === null) return null;
        $enemies = self::enemies($view);
        if (count($enemies) == 0) return null;
        $reason = self::reason($view, $ship, $enemies);
        if ($reason === null) return null;

        $now = $mover->state();
        $slowest = null;
        $search = clone $mover;
        foreach ($search->enumerate(AIConfig::SHIP_PLANS) as $plan) $slowest = ($slowest === null) ? $plan['speed'] : min($slowest, $plan['speed']);
        if ($slowest === null || $slowest >= (int)$now['speed']) return null;

        ShipDecider::$planFilter = function (array $plan) use ($slowest) {
            return (int)$plan['speed'] <= $slowest;
        };
        try {
            $rows = ShipDecider::rows($view, $mover, $ship, $hazards);
        } finally {
            ShipDecider::$planFilter = null;
        }
        if ($rows !== null) self::$last[] = $ship->name . ': braking to speed ' . $slowest . ' to leave next turn (' . $reason . ')';
        return $rows;
    }

    /* The turn after: the jump-out, first thing (an extended turn owed from last turn completed before it, as the page insists). Null
       when the page would not offer it - the ship is not standing in the jump point, or did not come in through the mouth. */
    public static function jumpOutRows(ShipMover $mover, $ship, array $jump)
    {
        $plot = clone $mover;
        $plot->vortexForJumpOut($jump['vortex']);
        if ($plot->can(ShipMover::COMPLETE_EXTENDED)) $plot->apply(ShipMover::COMPLETE_EXTENDED);
        if (!$plot->apply(ShipMover::JUMP_OUT)) return null;
        self::$last[] = $ship->name . ': jumps out';
        return $plot->rows();
    }

    // ------------------------------------------------------------------ helpers

    //Every visible enemy that is not a mine.
    private static function enemies(AIView $view)
    {
        $list = array();
        foreach ($view->enemies() as $enemy) {
            if (empty($enemy->mine)) $list[] = $enemy;
        }
        return $list;
    }

    //A unit's loaded movement as array('q', 'r'), oldest first.
    private static function positions(array $movement)
    {
        $out = array();
        foreach ($movement as $move) {
            $at = Movement::toOffset($move->position);
            if ($at) $out[] = array('q' => (int)$at->q, 'r' => (int)$at->r);
        }
        return $out;
    }

    //Rows as AIOrderBuilder::move writes them, as array('q', 'r').
    private static function rowPositions(array $rows)
    {
        $out = array();
        foreach ($rows as $row) $out[] = array('q' => (int)$row['position']['q'], 'r' => (int)$row['position']['r']);
        return $out;
    }
}
