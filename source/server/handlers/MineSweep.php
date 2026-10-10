<?php
/**
 * MineSweep - LIVE SWEEPING (MINE_DETECTION_PLAN.md §1.4, Stage 2).
 *
 * As a unit with a Detect Mines rating enters a hex, its owner's client asks the server about
 * that hex (mineSweep.php -> Manager::sweepMines). The server answers from here, records any
 * find, and the hex becomes FINAL for that unit, found or not: its committed path must pass
 * through it, in the same order (D10, D11, T20). That is what stops a player probing a path and
 * taking it back - the server never answers about a hex the unit can still back out of.
 *
 * ⚠️ THE CLIENT HOLDS NOTHING ABOUT AN UNFOUND MINE (plan §0.3). Any client-side "would this hex
 * find a mine?" is an oracle - run it over every hex in reach and the "yes" hexes form a disc
 * centred on the mine - so the question is asked here, one entered hex at a time.
 *
 * THE TURN CONTEXT. Everything an answer needs is fixed once Initial Orders resolve: each mine's
 * hex and signature, each unit's rating and start hex, the hexes that block line of sight. It is
 * built from ONE gamedata load and kept in APCu per game and turn, so a question costs a few small
 * queries instead of a load. Primed where Movement begins (InitialOrdersGamePhase::advance) and
 * rebuilt on a miss under an apcu_add lock. Without APCu (the CLI, or a host with it off) every
 * request builds its own.
 * ⚠️ T15: the context holds every mine's real hex. Server only - never in a reply, an error
 * message, or anything else a player can read.
 *
 * ⚠️ T19 HOLDS ONLY IN PART. Terrain does not move during Movement, but an Enormous SHIP blocks
 * line of sight too and does (the Explorer, the Kraken...), so the blocked hexes go stale when one
 * commits. Every Movement commit therefore bumps a generation counter (onMovementCommitted, called
 * by Manager::submitTacGamedata AFTER its transaction), and a context built before the bump is
 * rebuilt on its next use. Mines, ratings and start hexes cannot change during the phase. Which
 * mines a team has FOUND can, from several places, so that is never cached: it is read fresh on
 * every request (DBManager::getMinesFoundByTeam).
 */
class MineSweep
{
    /*
     * ⭐ THE SWITCH for live sweeping. Set false to turn it off everywhere at once:
     * - units plot with free undo and speed changes again, exactly as before Stage 2;
     * - what a unit finds shows when it commits. Stage 1 stays on either way - every hex of the
     *   committed path still counts, and Movement still starts with a check of every start hex;
     * - hexes swept before the switch was flipped stop binding (the commit check is skipped).
     * Nothing else has to change, and no client rebuild: game.php hands this to the client
     * (window.fvLiveMineSweeping), and a page opened before the flip turns itself off at its next
     * sweep request, which answers "disabled".
     * Off also removes the purple Minesweeping Mode icon (MINE_DETECTION_PLAN.md Q7) - nobody is
     * offered live sweeping. ⚠️ The FAQ ("Minesweeping Mode") and the starter guide (Movement)
     * describe it - take that text out too while the switch is off.
     */
    public static $liveSweeping = true;

    /** How long a context lives unused, in seconds. A rebuild costs one gamedata load. */
    const CONTEXT_TTL = 7200;

    private static function cacheAvailable(){
        return function_exists('apcu_enabled') && apcu_enabled();
    }

    private static function contextKey($gameid, $turn){
        return Manager::getCachePrefix() . 'minesweep_ctx_' . (int)$gameid . '_' . (int)$turn;
    }

    private static function generationKey($gameid, $turn){
        return Manager::getCachePrefix() . 'minesweep_gen_' . (int)$gameid . '_' . (int)$turn;
    }

    /* The turn context, from a real gamedata load - never from ships rebuilt from a POST, which
       have no earlier movement and no loaded notes. Plain arrays only: it lives in APCu. */
    public static function buildContext(TacGamedata $gamedata){
        $context = array(
            'gameid' => (int)$gamedata->id,
            'turn' => (int)$gamedata->turn,
            'mines' => array(),   //every mine that can be found: id, team, MineStealth id, hex, facing, signature
            'units' => array(),   //by unit id: team, Detect Mines rating, start hex and speed
            'blocked' => array(), //[q, r] of every hex that blocks line of sight
        );

        foreach ($gamedata->ships as $ship){
            if ($ship instanceof Mine){
                /* On the board, alive, and hidden. A mine with no MineStealth (or not trueStealth)
                   is never masked, so the player sees it already - "finding" it would interrupt a
                   move for nothing. */
                if (!$ship->trueStealth || $ship->isDestroyed()) continue;
                if ($ship->getTurnDeployed($gamedata) > $gamedata->turn) continue;

                $stealth = null;
                foreach ($ship->systems as $system){
                    if ($system instanceof MineStealth){
                        $stealth = $system;
                        break;
                    }
                }
                $deploy = MineStealth::getMineDeployMove($ship);
                $pos = $deploy ? Movement::toOffset($deploy->position) : null;
                if (!$stealth || !$pos) continue;

                $context['mines'][] = array(
                    'id' => (int)$ship->id,
                    'team' => (int)$ship->team,
                    'sys' => (int)$stealth->id,
                    'q' => $pos->q,
                    'r' => $pos->r,
                    'f' => (int)$deploy->facing,
                    'sig' => (int)$ship->signature, //an activated DEW mine's is already lowered: its note loaded with the game
                );
                continue;
            }

            if ($ship->isTerrain()) continue;

            $start = $ship->getLastTurnMovement($gamedata->turn);
            $pos = $start ? Movement::toOffset($start->position) : null;
            if (!$pos) continue; //not on the board

            $context['units'][(int)$ship->id] = array(
                'team' => (int)$ship->team,
                'rating' => MineStealth::getDetectionRating($ship, $gamedata),
                'q' => $pos->q,
                'r' => $pos->r,
                'speed' => (int)$start->speed,
            );
        }

        if (is_array($gamedata->blockedHexes)){
            foreach ($gamedata->blockedHexes as $hex){
                $context['blocked'][] = array((int)$hex->q, (int)$hex->r);
            }
        }

        return $context;
    }

    /* Movement has just begun: store the context built from the load the advance already holds,
       so the first question of the phase does not pay for a load. Must never break the advance -
       if it fails, the first request builds the context instead. */
    public static function primeContext(TacGamedata $gamedata){
        if (!self::$liveSweeping || !self::cacheAvailable()) return;

        try {
            $context = self::buildContext($gamedata);
            $context['gen'] = apcu_fetch(self::generationKey($gamedata->id, $gamedata->turn));
            apcu_store(self::contextKey($gamedata->id, $gamedata->turn), $context, self::CONTEXT_TTL);
        } catch (Throwable $e) {
            Debug::error($e);
        }
    }

    /* A Movement commit may have moved an Enormous unit, and with it the hexes that block line of
       sight, so every context built before now is rebuilt on its next use. Called AFTER the commit's
       transaction: a request that read the generation before this ran may have loaded the game
       before the commit was visible, and it stores its context under the generation it read - the
       old one, which no longer matches. */
    public static function onMovementCommitted($gameid, $turn){
        if (!self::cacheAvailable()) return;
        apcu_store(self::generationKey($gameid, $turn), uniqid('', true), self::CONTEXT_TTL);
    }

    /* The context for a sweep request, or null when the game is no longer in this turn's Movement. */
    public static function getContext(DBManager $dbManager, $gameid, $turn, $playerid){
        if (!self::cacheAvailable()) return self::loadContext($dbManager, $gameid, $turn, $playerid);

        $key = self::contextKey($gameid, $turn);
        $genKey = self::generationKey($gameid, $turn);

        $context = apcu_fetch($key);
        if (is_array($context) && $context['gen'] === apcu_fetch($genKey)) return $context;

        $lockKey = $key . '_lock';
        if (apcu_add($lockKey, 1, 30)) {
            try {
                $gen = apcu_fetch($genKey); //before the load - see onMovementCommitted
                $context = self::loadContext($dbManager, $gameid, $turn, $playerid);
                if ($context !== null) {
                    $context['gen'] = $gen;
                    apcu_store($key, $context, self::CONTEXT_TTL);
                }
                return $context;
            } finally {
                apcu_delete($lockKey);
            }
        }

        //Another request is building it: wait for that one rather than load the game a second time.
        for ($i = 0; $i < 30; $i++) {
            usleep(100000);
            $context = apcu_fetch($key);
            if (is_array($context) && $context['gen'] === apcu_fetch($genKey)) return $context;
        }

        return self::loadContext($dbManager, $gameid, $turn, $playerid);
    }

    private static function loadContext(DBManager $dbManager, $gameid, $turn, $playerid){
        $gamedata = $dbManager->getTacGamedata($playerid, $gameid);
        if (!$gamedata || (int)$gamedata->turn !== (int)$turn || (int)$gamedata->phase !== 2) return null;

        return self::buildContext($gamedata);
    }

    /* MINE_DETECTION_PLAN.md §1.4.3 - THE WALK. $hexes are the hexes newly entered, in order, and
       $foundIds the mines the unit's team has found already. Every enemy mine still unfound is
       tested from each hex (MineStealth::canDetectAt - distance first, line of sight only in range),
       and the walk STOPS after the first hex with a find: the client interrupts the move there (D13).
       Returns [index into $hexes of that hex, or null; the mines found from it (context entries)]. */
    public static function walk(array $context, array $unit, array $hexes, array $foundIds){
        $candidates = array();
        foreach ($context['mines'] as $mine){
            if ($mine['team'] === $unit['team']) continue;
            if (in_array($mine['id'], $foundIds, true)) continue;
            $candidates[] = $mine;
        }
        if (empty($candidates)) return array(null, array());

        $blocked = array();
        foreach ($context['blocked'] as $pair) $blocked[] = new OffsetCoordinate($pair[0], $pair[1]);

        foreach ($hexes as $index => $hex){
            $found = array();
            foreach ($candidates as $mine){
                if (MineStealth::canDetectAt(new OffsetCoordinate($mine['q'], $mine['r']), $mine['sig'], $hex, $unit['rating'], $blocked)) {
                    $found[] = $mine;
                }
            }
            if (!empty($found)) return array($index, $found);
        }

        return array(null, array());
    }

    /* What a reply says about each mine just found: where it is, so the client can draw it (§1.4.5).
       Only mines the requesting team has now found - nothing else from the context leaves here. */
    public static function describeFound(array $context, array $mineIds){
        $out = array();
        foreach ($context['mines'] as $mine){
            if (!in_array($mine['id'], $mineIds, true)) continue;
            $out[] = array('id' => $mine['id'], 'q' => $mine['q'], 'r' => $mine['r'], 'f' => $mine['f']);
        }
        return $out;
    }

    /* MINE_DETECTION_PLAN.md §1.4.4 - for the commit: the swept hexes of every unit this turn, by
       unit id. Never throws - a database without the table (db/mineSweep.sql not applied yet) has
       nothing to verify, and must not cost anyone their commit. Nothing at all with the switch off:
       a hex swept before it was flipped binds no one. */
    public static function getFinalHexesForCommit(DBManager $dbManager, $gameid, $turn){
        if (!self::$liveSweeping) return array();

        try {
            return $dbManager->getMineSweepFinalHexes($gameid, $turn);
        } catch (Throwable $e) {
            Debug::error($e);
            return array();
        }
    }

    /* Does the committed path begin with the unit's final hexes, in order? $final is the list the
       sweep stored ([[q, r], ...], start hex excluded), $start the unit's authoritative start hex. */
    public static function pathKeepsFinalHexes($final, OffsetCoordinate $start, $rows, $turn){
        $entered = Movement::getHexesEnteredFrom($start, $rows, $turn);
        array_shift($entered); //the start hex

        foreach ($final as $i => $pair){
            if (!isset($entered[$i])) return false;
            if ($entered[$i]->q !== (int)$pair[0] || $entered[$i]->r !== (int)$pair[1]) return false;
        }

        return true;
    }

    /* Movement is over: nobody's swept hexes mean anything now (T16 - also cleared by
       DBManager::deleteGames and leaveSlot). Never throws: it runs inside the advance. */
    public static function forgetGame(DBManager $dbManager, $gameid){
        try {
            $dbManager->deleteMineSweeps($gameid);
        } catch (Throwable $e) {
            Debug::error($e);
        }
    }
}
