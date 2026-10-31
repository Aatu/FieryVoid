<?php
/**
 * MoveLock - THE MOVEMENT LOCK (OFFICERS_PLAN.md Stage 31, §9.38): a unit's movement this turn up to an order it can no
 * longer take back.
 *
 * Some Movement-phase orders are decided by the SERVER the moment they are given, and must not be undone. The first is an
 * Expert Graviton Controller's rotation, whose d6 the server rolls (Officers::rotateGravitic): undoing it would let the
 * player roll again, and replotting the moves before it after seeing the die would let him choose where he was when it
 * happened. So the order and every row of the turn before it are LOCKED, Minesweeping Mode's way (user ruling 2026-10-25):
 *   - the client's Cancel cannot take them back (client/moveLock.js, through movement.js isLastMoveFinal);
 *   - a reload puts them back (game.php's window.fvMoveLocks -> moveLock.onStrategyActivated);
 *   - the commit must begin with them (keepsLock, in MovementGamePhase::process), and a lock-type row anywhere else is
 *     dropped (dropUnlocked) - only the server writes one.
 * Generic on purpose (the user asked for storage "with wider applications than just this rare event"): another order of this
 * kind adds its row type to LOCK_TYPES, at both ends, and saves its lock here - the restore, the Cancel rule and the commit
 * check come with it. Minesweeping Mode keeps its own table (tac_minesweep), whose lock is a count of swept hexes with rules
 * of its own.
 *
 * STORAGE - the existing tac_systemdata, no new table: one row per game, turn and unit, system id SYSTEM_ID (-1 is no system:
 * DBManager::getSystemDataForShips hands a row only to a system it finds, so every gamedata load skips these) and key KEY in
 * `subsystem`, holding the unit's rows of the turn up to and including the order, as the client wrote them (JSON text,
 * decoded as OBJECTS so an empty {} stays one when it is written back). deleteGames and leaveSlot already clear
 * tac_systemdata; Movement's end clears the locks (forgetGame) - a lock binds only its own turn's commit.
 */
class MoveLock
{
    const KEY = 'moveLock';
    const SYSTEM_ID = -1;

    /* The row types only a lock may hold: today Movement::GRAV_ROTATE. ⚠️ MIRROR PAIR with moveLock.LOCK_TYPES (client/moveLock.js). */
    const LOCK_TYPES = array('gravRotate');

    /** The largest plotted movement accepted, in bytes of JSON text and in rows (Manager::sweepMines' limits). */
    const MAX_BYTES = 262144;
    const MAX_ROWS = 400;

    public static function save(DBManager $dbManager, $gameid, $turn, $shipid, array $rows){
        //Default flags (no JSON_NUMERIC_CHECK): the text is stored and given back as is.
        $dbManager->saveMoveLock((int)$gameid, (int)$turn, (int)$shipid, json_encode(array_values($rows)));
    }

    /* A unit's lock this turn - its rows as objects - or null. */
    public static function get(DBManager $dbManager, $gameid, $turn, $shipid){
        return self::decode($dbManager->getMoveLock((int)$gameid, (int)$turn, (int)$shipid));
    }

    /* Every unit's lock this turn, by unit id - $playerid: only that player's units. */
    public static function forTurn(DBManager $dbManager, $gameid, $turn, $playerid = null){
        $out = array();
        foreach ($dbManager->getMoveLocks((int)$gameid, (int)$turn, $playerid === null ? null : (int)$playerid) as $shipid => $json){
            $rows = self::decode($json);
            if ($rows !== null) $out[(int)$shipid] = $rows;
        }
        return $out;
    }

    private static function decode($json){
        if (!is_string($json) || $json === '') return null;
        $rows = json_decode($json);
        return (is_array($rows) && !empty($rows)) ? $rows : null;
    }

    /* The unit's own orders among $rows: this turn's rows that are neither the server's (preturn, or forced - an Augmenter's
       free jink is re-added at every load) - in order. Rows of any shape (a MovementOrder, or a row decoded from JSON). */
    private static function orders($rows, $turn){
        $out = array();
        foreach ((array)$rows as $row){
            if (Movement::rowField($row, 'turn') != $turn) continue;
            if (!empty(Movement::rowField($row, 'preturn')) || !empty(Movement::rowField($row, 'forced'))) continue;
            $out[] = $row;
        }
        return $out;
    }

    public static function isLockType($type){
        return in_array((string)$type, self::LOCK_TYPES, true);
    }

    /* ⭐ Does the commit begin with the lock? Each locked order must come back in its place with the same type, hex, facing,
       heading and speed - and a lock-type row with its value too (the rotation's die). A commit that does not is refused
       outright (MovementGamePhase::process): the client never lets the rows be taken back, so it is a second tab out of step,
       or a tampered client. ⚠️ MIRROR PAIR with moveLock.beginsWith (JS), which decides whether a reload keeps a saved draft. */
    public static function keepsLock(array $lock, $rows, $turn){
        $locked = self::orders($lock, $turn);
        $sent = self::orders($rows, $turn);
        if (count($sent) < count($locked)) return false;
        foreach ($locked as $i => $row){
            if (!self::sameOrder($row, $sent[$i])) return false;
        }
        return true;
    }

    private static function sameOrder($a, $b){
        if ((string)Movement::rowField($a, 'type') !== (string)Movement::rowField($b, 'type')) return false;
        $p = Movement::toOffset(Movement::rowField($a, 'position'));
        $q = Movement::toOffset(Movement::rowField($b, 'position'));
        if ($p === null || $q === null || !$p->equals($q)) return false;
        foreach (array('facing', 'heading', 'speed') as $field){
            if ((int)Movement::rowField($a, $field) !== (int)Movement::rowField($b, $field)) return false;
        }
        if (self::isLockType(Movement::rowField($a, 'type')) && (string)Movement::rowField($a, 'value') !== (string)Movement::rowField($b, 'value')) return false;
        return true;
    }

    /* $rows without any lock-type row the server did not write: one is kept only among the first orders, as many as the lock
       holds ($lock null: none) - keepsLock has already checked those are the lock's own. */
    public static function dropUnlocked($rows, $lock, $turn){
        $keep = ($lock === null) ? 0 : count(self::orders($lock, $turn));
        $out = array();
        $n = 0;
        foreach ((array)$rows as $row){
            $isOrder = Movement::rowField($row, 'turn') == $turn && empty(Movement::rowField($row, 'preturn')) && empty(Movement::rowField($row, 'forced'));
            if ($isOrder){
                $n++;
                if ($n > $keep && self::isLockType(Movement::rowField($row, 'type'))) continue;
            }
            $out[] = $row;
        }
        return $out;
    }

    /* Movement is over: no lock means anything now. Never throws: it runs inside the advance. */
    public static function forgetGame(DBManager $dbManager, $gameid){
        try {
            $dbManager->deleteMoveLocks((int)$gameid);
        } catch (Throwable $e) {
            Debug::error($e);
        }
    }
}
