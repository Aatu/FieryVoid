<?php
/**
 * FieryVoid replay-based regression harness.
 *
 * Re-runs the deterministic parts of the game engine against real recorded
 * games in the local Docker database and diffs the results against a recorded
 * baseline. Run "record" on known-good code, then "check" before every deploy:
 * any difference means a code change altered engine behaviour on real play.
 *
 * Entirely READ-ONLY against the database (SELECT-only load paths).
 *
 * Checks per game:
 *   snapshot  - full client gamedata JSON (stripForJson) at the game's current
 *               state, once per player perspective. Catches load/serialization
 *               regressions: notes handling, shared-reference mutations,
 *               visibility masking, autoload/constructor breakage.
 *   movement  - Movement::validateThrustPayment replayed over every ship-turn
 *               (enforcement forced ON in-memory only). Catches regressions in
 *               thrust/maneuver math: every recorded legal move must stay legal.
 *   tohit     - Weapon::calculateHitBase recomputed for every recorded direct
 *               fire order, per turn, against as-of-turn state. Catches
 *               regressions in hit-chance math (arcs, range, EW, jink, modes).
 *   damage    - damage AND critical resolution replayed per turn. The turn's own
 *               damage entries and criticals are first REWOUND off the loaded
 *               state (an as-of-turn-T load already contains them), which
 *               reconstructs what the engine saw when it began resolving turn T.
 *               Every recorded fire order that scored hits is then re-resolved in
 *               its recorded resolutionOrder through the real allocation path
 *               (beforeDamage -> getFinalDamage -> damage -> doDamage ->
 *               assignDamageReturnOverkill), applying the recorded number of
 *               hits; finally every system the replay damaged rolls its critical
 *               (ShipSystem::testCritical). Catches regressions in damage
 *               modifiers, armour (advanced/adaptive/pierced), hit charts,
 *               overkill routing, protective systems and the critical tables.
 *   masking   - per-viewer information hiding: TacGamedata::prepareForPlayer(),
 *               which is what deleteHiddenData() hangs off and which the snapshot
 *               check never calls. Swept across every live phase (-1,1,2,5,3) per
 *               player, because almost every masking rule is phase-conditional.
 *               Records a compact fingerprint of exactly the fields masking can
 *               touch. Catches regressions in hidden fire orders, hidden EW/power,
 *               hideActiveShipMovement, combat pivots, stealth movement, the
 *               deployment-dock hide and the Chameleon disguise passes.
 *
 * Determinism: hit-chance calculation is genuinely random in the live engine -
 * Weapon::calculateHitBase rolls the hit LOCATION (BaseShip::getHitSection,
 * Dice::d over the profile) and the chosen section feeds the final chance. So
 * this harness pre-empts the autoloader with a SEEDED Dice class (mt_rand) and
 * re-seeds at fixed points (per game / per turn / per fire order), making every
 * recomputation reproducible run-to-run. This affects only this process; the
 * game code itself is untouched.
 *
 * Usage (inside the php container, repo mounted at /usr/src/current):
 *   php tests/replay/replayHarness.php list
 *   php tests/replay/replayHarness.php record [--games=1,2,3] [--checks=...]
 *   php tests/replay/replayHarness.php check  [--games=1,2,3] [--diff-limit=N]
 *
 * Exit codes: 0 = pass/success, 1 = differences or errors found, 2 = bad usage.
 */

error_reporting(E_ERROR | E_PARSE);
ini_set('display_errors', '1');
set_time_limit(0);

$FV_ROOT = dirname(__DIR__, 2);
require_once $FV_ROOT . '/source/autoload.php';

// Deterministic replacement for the game's Dice class (lib/dice.php uses
// random_int, which cannot be seeded). Declared BEFORE any game class loads,
// so the autoloader never pulls in the real one. Same API; seedable.
class Dice {
    public static function d($max, $times = 1) {
        $total = 0;
        for ($i = 0; $i < $times; $i++) {
            $total += mt_rand(1, (int)$max);
        }
        return $total;
    }
    public static function seed($seed) {
        mt_srand((int)$seed);
    }
}

class ReplayHarness {

    const CHECKS = array('snapshot', 'movement', 'tohit', 'damage', 'masking');

    /* Live phases the masking check sweeps, in PhaseFactory's own order: -1 Deployment,
       1 Initial Orders, 2 Movement, 5 Pre-Firing, 3 Firing. Nearly every rule in
       deleteHiddenData() is phase-conditional (deploy rows at -1, EW/power at 1,
       hideActiveShipMovement at 2, combat pivots at 3, pre-firing orders at 5,
       hidetarget at < 6), so a check that only used each game's CURRENT phase would
       exercise whichever one or two branches the corpus happened to be sitting in.
       Sweeping instead makes coverage independent of what state the local games are in. */
    const MASKING_PHASES = array(-1, 1, 2, 5, 3);

    // Games excluded from the corpus entirely (record/check/list). Use for games
    // that are broken for legacy reasons the harness can't and shouldn't model -
    // e.g. corrupt/old data that no longer loads. Keyed by game id => why.
    const EXCLUDED_GAMES = array(
        4173 => 'legacy corrupt data - ship fails to load (getSystemById() on null)',
    );

    private $root;
    private $baselineDir;
    private $checks;
    private $diffLimit;
    /** @var DBManager */
    private $db;
    /** @var mysqli raw connection for harness-side discovery queries */
    private $raw;
    /** @var ReflectionMethod[] cached accessors for Weapon::beforeDamage, keyed by weapon class */
    private $beforeDamageRef = array();
    /** @var (ReflectionProperty|false)[] cached $gamedata property, keyed by weapon class */
    private $gamedataPropRef = array();

    public function __construct($root, $baselineDir, $checks, $diffLimit) {
        $this->root = $root;
        $this->baselineDir = $baselineDir;
        $this->checks = $checks;
        $this->diffLimit = $diffLimit;

        $host = getenv('FV_DB_HOST') ?: 'mariadb';
        $port = (int)(getenv('FV_DB_PORT') ?: 3306);
        $name = getenv('FV_DB_NAME') ?: 'B5CGM';
        $user = getenv('FV_DB_USER') ?: 'root';
        $pass = getenv('FV_DB_PASS') ?: 'fieryvoid';

        // testMode: endTransaction(false) becomes a no-op, so nothing this process
        // opens can be committed by accident. The damage check adds an explicit
        // rollback on top - see replayDamage().
        $this->db = new DBManager($host, $port, $name, $user, $pass, true);
        $this->raw = mysqli_connect($host, $user, $pass, $name, $port);
        if (!$this->raw) {
            throw new Exception('Harness discovery connection failed: ' . mysqli_connect_error());
        }

        // The damage check runs real weapon code, and a handful of weapons write to
        // the DB from inside the damage path (e.g. the Gravitic Tracting Rod's
        // beforeDamage -> Manager::insertSingleMovement). Manager lazily builds its
        // OWN DBManager on a separate connection, which no transaction of ours could
        // ever roll back, so hand it ours BEFORE any game class can trigger that.
        // With this plus the rollback in replayDamage(), the harness stays read-only
        // even when engine code tries to write.
        if (class_exists('Manager') && method_exists('Manager', 'setDBManager')) {
            Manager::setDBManager($this->db);
        }
    }

    // ------------------------------------------------------------- discovery

    /**
     * All games with recorded play: turn >= 1, out of the lobby, and not on the
     * EXCLUDED_GAMES list (games broken for legacy reasons the harness can't model).
     */
    public function discoverGames() {
        $sql = "SELECT g.id, g.turn, g.status, g.name,
                       (SELECT COUNT(*) FROM tac_ship s WHERE s.tacgameid = g.id) AS ships
                FROM tac_game g
                WHERE g.turn >= 1 AND g.status <> 'LOBBY'
                ORDER BY g.id";
        $games = array();
        $res = mysqli_query($this->raw, $sql);
        while ($row = mysqli_fetch_object($res)) {
            $id = (int)$row->id;
            if (isset(self::EXCLUDED_GAMES[$id])) continue;
            $games[$id] = $row;
        }
        return $games;
    }

    private function gamePlayers($gameid) {
        $players = array();
        $stmt = mysqli_prepare($this->raw,
            'SELECT DISTINCT playerid FROM tac_playeringame WHERE gameid = ? AND playerid > 0 ORDER BY playerid');
        mysqli_stmt_bind_param($stmt, 'i', $gameid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $pid);
        while (mysqli_stmt_fetch($stmt)) {
            $players[] = (int)$pid;
        }
        mysqli_stmt_close($stmt);
        return $players;
    }

    // ----------------------------------------------------------- game loading

    /**
     * Load gamedata exactly like production (DBManager::getTacGamedata).
     * $turn = null loads the game's current state; otherwise state as-of $turn,
     * with the turn/phase statics aligned the way the proven replay method does.
     * Output-buffers the load to keep stray prints in model code off the console.
     */
    private function loadGame($playerid, $gameid, $turn = null, $phase = null) {
        ob_start();
        try {
            $gd = $this->db->getTacGamedata($playerid, $gameid, $turn);
            if ($gd !== null && $turn !== null) {
                $gd->setTurn($turn);           // aligns TacGamedata::$currentTurn (isDestroyed(T-1) etc.)
                if ($phase !== null) {
                    $gd->setPhase($phase);
                }
            }
            return $gd;
        } finally {
            ob_end_clean();
        }
    }

    // -------------------------------------------------------------- reports

    /** Build every requested report for one game: array of filename => content. */
    public function buildReports($gameRow) {
        $gameid = (int)$gameRow->id;
        $finalTurn = (int)$gameRow->turn;
        $players = $this->gamePlayers($gameid);
        $reports = array();

        if (in_array('snapshot', $this->checks)) {
            foreach ($players as $pid) {
                Dice::seed($gameid * 10007 + $pid);
                $reports["snapshot_p$pid.json"] = $this->guard(function () use ($gameid, $pid) {
                    return $this->buildSnapshot($gameid, $pid);
                });
            }
        }

        $wantMovement = in_array('movement', $this->checks);
        $wantTohit = in_array('tohit', $this->checks);
        if ($wantMovement || $wantTohit) {
            $pid = isset($players[0]) ? $players[0] : -1;
            $movementLines = array();
            $tohitLines = array();
            for ($t = 1; $t <= $finalTurn; $t++) {
                $err = null;
                $gd = null;
                Dice::seed($gameid * 1009 + $t);
                try {
                    $gd = $this->loadGame($pid, $gameid, $t, 2);
                } catch (Throwable $e) {
                    $err = 'LOAD-ERROR turn ' . $t . ': ' . $this->describeThrowable($e);
                }
                if ($gd === null && $err === null) {
                    $err = 'LOAD-ERROR turn ' . $t . ': gamedata is null';
                }
                if ($err !== null) {
                    if ($wantMovement) $movementLines[] = $err;
                    if ($wantTohit) $tohitLines[] = $err;
                    continue;
                }
                if ($wantMovement) {
                    $movementLines = array_merge($movementLines, $this->buildMovementLines($gd, $t));
                }
                if ($wantTohit) {
                    $gd->setPhase(3); // firing resolves in phase 3
                    $tohitLines = array_merge($tohitLines, $this->buildTohitLines($gd, $t));
                }
                unset($gd);
            }
            if ($wantMovement) $reports['movement.txt'] = implode("\n", $movementLines) . "\n";
            if ($wantTohit) $reports['tohit.txt'] = implode("\n", $tohitLines) . "\n";
        }

        /* The damage check deliberately gets its OWN per-turn load rather than sharing
           the one above. It rewinds the turn's damage off the loaded state and then
           mutates that state heavily, and the movement/tohit checks mutate it a little
           too (changeFiringMode, setExpectedDamage, validateThrustPayment). Sharing
           would make damage.txt depend on WHICH other checks ran, so
           `check --checks=damage` would diff against a baseline recorded by a full run
           and fail for no reason. One extra load per turn buys exact independence. */
        if (in_array('damage', $this->checks)) {
            $pid = isset($players[0]) ? $players[0] : -1;
            $damageLines = array();
            for ($t = 1; $t <= $finalTurn; $t++) {
                $gd = null;
                Dice::seed($gameid * 2027 + $t);
                try {
                    $gd = $this->loadGame($pid, $gameid, $t, 3); // firing resolves in phase 3
                } catch (Throwable $e) {
                    $damageLines[] = 'LOAD-ERROR turn ' . $t . ': ' . $this->describeThrowable($e);
                    continue;
                }
                if ($gd === null) {
                    $damageLines[] = 'LOAD-ERROR turn ' . $t . ': gamedata is null';
                    continue;
                }
                $damageLines = array_merge($damageLines, $this->buildDamageLines($gd, $t));
                unset($gd);
            }
            $reports['damage.txt'] = implode("\n", $damageLines) . "\n";
        }

        if (in_array('masking', $this->checks)) {
            $reports['masking.txt'] = implode("\n", $this->buildMaskingLines($gameid, $players)) . "\n";
        }

        gc_collect_cycles();
        return $reports;
    }

    /** Run a report builder, converting any throwable into deterministic report text. */
    private function guard($fn) {
        try {
            return $fn();
        } catch (Throwable $e) {
            return 'HARNESS-ERROR: ' . $this->describeThrowable($e) . "\n";
        }
    }

    private function describeThrowable(Throwable $e) {
        // Strip absolute paths so messages stay stable across environments.
        $msg = str_replace($this->root, '', $e->getMessage());
        return get_class($e) . ': ' . $msg;
    }

    // ---- snapshot check

    private function buildSnapshot($gameid, $playerid) {
        $gd = $this->loadGame($playerid, $gameid); // production-shaped load, current turn
        if ($gd === null) {
            return "HARNESS-ERROR: gamedata is null\n";
        }
        ob_start();
        try {
            $stripped = $gd->stripForJson();
        } finally {
            ob_end_clean();
        }
        $json = json_encode($stripped, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            return 'HARNESS-ERROR: json_encode failed: ' . json_last_error_msg() . "\n";
        }
        return $json . "\n";
    }

    // ---- movement check

    private function buildMovementLines($gd, $turn) {
        $lines = array();
        if (!class_exists('Movement') || !method_exists('Movement', 'validateThrustPayment')) {
            $lines[] = "t$turn movement-check SKIPPED (Movement::validateThrustPayment not present)";
            return $lines;
        }
        $hadFlag = property_exists('Movement', 'enforceThrustValidation');
        $saved = $hadFlag ? Movement::$enforceThrustValidation : null;
        if ($hadFlag) {
            Movement::$enforceThrustValidation = true; // in-memory only: makes rejects visible in the returned array
        }
        try {
            foreach ($gd->ships as $ship) {
                if (!is_array($ship->movement)) continue;
                $turnMoves = 0;
                foreach ($ship->movement as $move) {
                    if ($move->turn == $turn) $turnMoves++;
                }
                if ($turnMoves === 0) continue;

                $before = $this->serializeMovement($ship->movement);
                $verdict = '';
                Dice::seed($ship->id * 101 + $turn);
                ob_start();
                try {
                    $after = Movement::validateThrustPayment($ship, $turn);
                    $afterSer = $this->serializeMovement($after);
                    $verdict = ($afterSer === $before)
                        ? 'LEGAL'
                        : "REBUILT >> " . $afterSer;
                } catch (Throwable $e) {
                    $verdict = 'ERROR(' . $this->describeThrowable($e) . ')';
                } finally {
                    ob_end_clean();
                }
                $lines[] = sprintf('t%d ship%d moves=%d %s | %s',
                    $turn, $ship->id, $turnMoves, $verdict, $before);
            }
        } finally {
            if ($hadFlag) {
                Movement::$enforceThrustValidation = $saved;
            }
        }
        return $lines;
    }

    private function serializeMovement($movement) {
        if (!is_array($movement)) return '(none)';
        $parts = array();
        foreach ($movement as $move) {
            $q = 'x';
            $r = 'x';
            if (isset($move->position)) {
                $pos = $move->position;
                if (is_object($pos)) {
                    $q = isset($pos->q) ? $pos->q : 'x';
                    $r = isset($pos->r) ? $pos->r : 'x';
                } elseif (is_array($pos)) {
                    $q = isset($pos['q']) ? $pos['q'] : 'x';
                    $r = isset($pos['r']) ? $pos['r'] : 'x';
                }
            }
            $parts[] = sprintf('%s@%s,%s:h%s:f%s:s%s:t%s',
                $move->type, $q, $r,
                isset($move->heading) ? $move->heading : 'x',
                isset($move->facing) ? $move->facing : 'x',
                isset($move->speed) ? $move->speed : 'x',
                $move->turn);
        }
        return implode(' ', $parts);
    }

    // ---- tohit check

    private function buildTohitLines($gd, $turn) {
        $lines = array();
        $collected = array();

        foreach ($gd->ships as $ship) {
            $orders = $ship->getAllFireOrders($turn);
            if (!is_array($orders)) continue;
            foreach ($orders as $fire) {
                if ($fire->turn != $turn) continue;
                // mirror Firing::preparePreFiring's skip rules
                if ($fire->type === 'intercept' || $fire->type === 'selfIntercept' || $fire->type === 'ballistic') continue;
                if ($fire->targetid === null || $fire->targetid <= 0) continue; // hex-targeted / no target
                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)) continue;
                $collected[] = array($ship, $weapon, $fire);
            }
        }

        usort($collected, function ($a, $b) {
            return $a[2]->id <=> $b[2]->id;
        });

        $matches = 0;
        foreach ($collected as $entry) {
            list($ship, $weapon, $fire) = $entry;
            // fresh order so the recomputation cannot contaminate the recorded one
            $clone = new FireOrder(
                $fire->id, $fire->type, $fire->shooterid, $fire->targetid,
                $fire->weaponid, $fire->calledid, $fire->turn, $fire->firingMode,
                0, 0, $fire->shots, 0, 0, $fire->x, $fire->y, $fire->damageclass
            );
            $recomputed = null;
            Dice::seed($fire->id); // hit-location roll inside calculateHitBase must reproduce
            ob_start();
            try {
                $weapon->changeFiringMode($fire->firingMode); // same call pattern as Firing::preparePreFiring
                $weapon->calculateHitBase($gd, $clone);
                $recomputed = (string)$clone->needed;
            } catch (Throwable $e) {
                $recomputed = 'ERR(' . get_class($e) . ')';
            } finally {
                ob_end_clean();
            }
            if ($recomputed === (string)$fire->needed) $matches++;
            $lines[] = sprintf('t%d fo%d %s w%d m%s %d->%d recomputed=%s recorded=%s',
                $turn, $fire->id, get_class($weapon), $fire->weaponid, $fire->firingMode,
                $fire->shooterid, $fire->targetid, $recomputed, $fire->needed);
        }

        if (count($collected) > 0) {
            $lines[] = sprintf('t%d summary orders=%d recomputed==recorded=%d',
                $turn, count($collected), $matches);
        }
        return $lines;
    }

    // ---- damage check

    /**
     * Replay one turn's damage and critical resolution.
     *
     * $gd is an as-of-turn-$turn load, which ALREADY contains that turn's damage and
     * criticals, so step one is to rewind them off: what is left is the state the
     * engine saw when it started resolving turn $turn's fire. Every recorded fire
     * order that scored hits is then re-resolved in recorded resolutionOrder, and
     * finally every system the replay damaged rolls its critical.
     *
     * Like the tohit check, the baseline stores RECOMPUTED values, not the recorded
     * ones: the engine rolls fresh dice for damage amount, hit location and criticals,
     * and those per-roll results were never stored. Pass/fail is therefore
     * recomputed(now) vs recomputed(baseline) under the harness's seeded Dice.
     */
    private function buildDamageLines($gd, $turn) {
        $lines = array();

        /* Write safety. Almost all of the damage path is pure in-memory arithmetic, but a
           few weapons persist from inside it (the Gravitic Tracting Rod's beforeDamage
           inserts a movement row). The constructor already pointed Manager at our
           DBManager; this wraps the replay so anything that does slip through is rolled
           back. The harness must stay READ-ONLY against the corpus it replays - a stray
           write would silently corrupt the very games the baseline was recorded from. */
        $inTransaction = false;
        try {
            $this->db->startTransaction();
            $inTransaction = true;
        } catch (Throwable $e) {
            $lines[] = sprintf('t%d HARNESS-ERROR: could not open write guard: %s',
                $turn, $this->describeThrowable($e));
        }

        try {
            $index = $this->rewindTurn($gd, $turn);
            $collected = $this->collectDamagingOrders($gd, $turn);

            $errors = array();
            $locations = array();
            foreach ($collected as $entry) {
                list($shooter, $weapon, $target, $fire) = $entry;
                $outcome = $this->replayFireOrderDamage($gd, $shooter, $weapon, $target, $fire, $turn);
                if ($outcome['err'] !== null) $errors[(int)$fire->id] = $outcome['err'];
                $locations[(int)$fire->id] = $outcome['loc'];
            }

            $crits = $this->rollDamageCriticals($gd, $turn);
            $newDamage = $this->collectNewDamage($index);

            $totalEntries = 0;
            $totalEffective = 0;
            $totalDestroyed = 0;
            $totalHits = 0;

            foreach ($collected as $entry) {
                list($shooter, $weapon, $target, $fire) = $entry;
                $foid = (int)$fire->id;
                $hits = (int)$fire->shotshit;
                $totalHits += $hits;
                $allocs = isset($newDamage[$foid]) ? $newDamage[$foid] : array();
                $effective = 0;
                $destroyed = 0;
                $parts = array();
                foreach ($allocs as $a) {
                    $effective += max(0, (int)$a['damage'] - (int)$a['armour']);
                    if ($a['destroyed']) $destroyed++;
                    $parts[] = sprintf('sh%ds%d:%d/%d%s',
                        $a['shipid'], $a['systemid'], $a['damage'], $a['armour'],
                        $a['destroyed'] ? 'D' : '');
                }
                $totalEntries += count($allocs);
                $totalEffective += $effective;
                $totalDestroyed += $destroyed;

                $lines[] = sprintf('t%d fo%d %s w%d m%s %d->%d hits=%d loc=%s eff=%d ent=%d dest=%d%s | %s',
                    $turn, $foid, get_class($weapon), $fire->weaponid, $fire->firingMode,
                    $fire->shooterid, $fire->targetid, $hits,
                    (!isset($locations[$foid]) || $locations[$foid] === null) ? 'x' : $locations[$foid],
                    $effective, count($allocs), $destroyed,
                    isset($errors[$foid]) ? ' ' . $errors[$foid] : '',
                    (count($parts) > 0) ? implode(', ', $parts) : '(none)');
            }

            /* Damage entries whose fireorderid matches no order we replayed - collateral
               chains and sub-orders create their own FireOrders with id -1, and a weapon
               that spawns a child order gives it that child's id. Reported once, grouped,
               so they are still covered without pretending to belong to a replayed line. */
            $orphanIds = array_diff(array_keys($newDamage), array_map(function ($e) {
                return (int)$e[3]->id;
            }, $collected));
            sort($orphanIds);
            foreach ($orphanIds as $foid) {
                $parts = array();
                foreach ($newDamage[$foid] as $a) {
                    $totalEntries++;
                    $totalEffective += max(0, (int)$a['damage'] - (int)$a['armour']);
                    if ($a['destroyed']) $totalDestroyed++;
                    $parts[] = sprintf('sh%ds%d:%d/%d%s',
                        $a['shipid'], $a['systemid'], $a['damage'], $a['armour'],
                        $a['destroyed'] ? 'D' : '');
                }
                $lines[] = sprintf('t%d indirect fo%d ent=%d | %s',
                    $turn, $foid, count($newDamage[$foid]), implode(', ', $parts));
            }

            foreach ($crits as $crit) {
                $lines[] = sprintf('t%d crit sh%d s%d %s%s',
                    $turn, $crit['shipid'], $crit['systemid'], $crit['phpclass'],
                    ($crit['turnend'] != 0) ? ' turnend=' . $crit['turnend'] : '');
            }

            if (count($collected) > 0 || $totalEntries > 0 || count($crits) > 0) {
                $lines[] = sprintf('t%d summary orders=%d hits=%d entries=%d effective=%d destroyed=%d crits=%d',
                    $turn, count($collected), $totalHits, $totalEntries,
                    $totalEffective, $totalDestroyed, count($crits));
            }
        } catch (Throwable $e) {
            $lines[] = sprintf('t%d HARNESS-ERROR: %s', $turn, $this->describeThrowable($e));
        } finally {
            if ($inTransaction) {
                try {
                    $this->db->endTransaction(true); // ALWAYS roll back - never commit engine writes
                } catch (Throwable $e) {
                    $lines[] = sprintf('t%d HARNESS-ERROR: write guard rollback failed: %s',
                        $turn, $this->describeThrowable($e));
                }
            }
        }

        return $lines;
    }

    /**
     * Strip $turn's own damage entries and criticals off every system, returning a flat
     * index of every system with the damage/critical counts that survived. The index is
     * what collectNewDamage/rollDamageCriticals use to tell replay-created records from
     * loaded ones, so it must be built here, after the rewind and before any replay.
     *
     * Nothing here is memoized in the engine - ShipSystem::isDestroyed,
     * getRemainingHealth, isDamagedOnTurn and BaseShip::isDestroyed all recompute from
     * $system->damage on every call - so removing the entries genuinely rolls the state
     * back to the start of the turn.
     */
    private function rewindTurn($gd, $turn) {
        $index = array();
        foreach ($gd->ships as $ship) {
            foreach ($this->shipSystems($ship) as $system) {
                $kept = array();
                foreach ($system->damage as $d) {
                    if ((int)$d->turn === (int)$turn) continue;
                    $kept[] = $d;
                }
                $system->damage = $kept;

                $keptCrits = array();
                foreach ($system->criticals as $c) {
                    if ((int)$c->turn === (int)$turn) continue;
                    $keptCrits[] = $c;
                }
                $system->criticals = $keptCrits;

                $index[] = array(
                    'ship' => $ship,
                    'system' => $system,
                    'damageCount' => count($kept),
                    'critCount' => count($keptCrits),
                );
            }
        }
        return $index;
    }

    /**
     * Every system on a unit, both levels: a FighterFlight's $systems are Fighters and
     * the weapons hang off those (FighterFlight::getSystemById walks the same two
     * levels). Damage entries only ever land on systems reachable this way.
     */
    private function shipSystems($ship) {
        $out = array();
        foreach ($ship->systems as $system) {
            $out[] = $system;
            if (isset($system->systems) && is_array($system->systems)) {
                foreach ($system->systems as $sub) {
                    $out[] = $sub;
                }
            }
        }
        return $out;
    }

    /**
     * Recorded fire orders that actually landed something this turn, in the order the
     * live engine resolved them.
     *
     * shotshit is the ground truth for how many hits to apply: re-rolling to-hit here
     * would put this check at the mercy of the hit-chance maths the tohit check already
     * covers, and would drift the number of hits (and so all the accumulated state) on
     * any unrelated EW change. resolutionorder is persisted on tac_fireorder, so damage
     * accumulates against the same targets in the same sequence it originally did;
     * orders that never resolved (-1) sort last, by id.
     */
    private function collectDamagingOrders($gd, $turn) {
        $collected = array();
        foreach ($gd->ships as $ship) {
            $orders = $ship->getAllFireOrders($turn);
            if (!is_array($orders)) continue;
            foreach ($orders as $fire) {
                if ($fire->turn != $turn) continue;
                // intercepts never allocate damage of their own; mirrors Firing::fireWeapons
                if ($fire->type === 'intercept' || $fire->type === 'selfIntercept') continue;
                if ((int)$fire->shotshit <= 0) continue;             // nothing landed
                if ($fire->targetid === null || $fire->targetid <= 0) continue; // hex-targeted
                $weapon = $ship->getSystemById($fire->weaponid);
                if (!($weapon instanceof Weapon)) continue;
                $target = $gd->getShipById($fire->targetid);
                if ($target === null) continue;
                $collected[] = array($ship, $weapon, $target, $fire);
            }
        }

        usort($collected, function ($a, $b) {
            $ra = ((int)$a[3]->resolutionOrder < 0) ? PHP_INT_MAX : (int)$a[3]->resolutionOrder;
            $rb = ((int)$b[3]->resolutionOrder < 0) ? PHP_INT_MAX : (int)$b[3]->resolutionOrder;
            if ($ra !== $rb) return $ra <=> $rb;
            return (int)$a[3]->id <=> (int)$b[3]->id;
        });

        return $collected;
    }

    /**
     * Re-resolve one recorded fire order's damage. Returns array('err' => null or a
     * deterministic ERR(...) tag for that order's report line, 'loc' => the section the
     * shot resolved on). Nothing is written back onto the recorded order: the replay
     * works entirely on a clone, so a later order can never read state this one left.
     *
     * Mirrors the per-hit block of Weapon::fire() exactly - minus the to-hit roll, which
     * shotshit stands in for, and minus fire()'s own bookkeeping (ammo decrement and its
     * DB write, resolutionOrder assignment, the CHAM: notes tag). chosenLocation is
     * derived once per order with the same two-branch rule calculateHitBase uses, because
     * live resolution fixes the section for the whole volley: leaving it null would let
     * damageOneSheet re-roll the section per hit and spread a raking shot across sections
     * it never touched.
     */
    private function replayFireOrderDamage($gd, $shooter, $weapon, $target, $fire, $turn) {
        /* Captured at derivation time, NOT read back off the clone afterwards: resolution
           REASSIGNS chosenLocation (a Piercing split or an overkill chain leaves it on
           PRIMARY, 0), so reading it after the hits would report where the damage ended
           up rather than the section the shot actually resolved against. */
        $loc = null;
        $clone = new FireOrder(
            $fire->id, $fire->type, $fire->shooterid, $fire->targetid,
            $fire->weaponid, $fire->calledid, $fire->turn, $fire->firingMode,
            $fire->needed, $fire->rolled, $fire->shots, 0, $fire->intercepted,
            $fire->x, $fire->y, $fire->damageclass
        );

        Dice::seed((int)$fire->id * 3 + 1); // distinct from the tohit check's per-order seed
        ob_start();
        try {
            $weapon->changeFiringMode($fire->firingMode);
            /* The one piece of fire()-level state the damage path genuinely needs.
               A few weapons stash the gamedata on themselves at the top of their fire()
               override and read it back from getDamage() - RammingAttack does, to look
               its own target up for the collision maths - so skipping fire() leaves it
               null and every ram fatals. Other fire()-level tweaks (per-mode raking
               size, Pulse/Standard switches) are NOT replayed: they belong to the
               to-hit half of resolution, which this check deliberately does not re-run. */
            $this->seedWeaponGamedata($weapon, $gd);

            $pos = null;
            if ($weapon->ballistic) {
                $pos = $weapon->getFiringHex($gd, $clone);
                $clone->chosenLocation = $target->getHitSectionPos(mathlib::hexCoToPixel($pos), $turn);
            } else {
                $clone->chosenLocation = $target->getHitSection($shooter, $turn);
            }
            $loc = $clone->chosenLocation;

            for ($i = 0; $i < (int)$fire->shotshit; $i++) {
                $clone->shotshit++;
                $target->clearVreeHitSectionChoice($shooter->id, $clone);
                $this->invokeBeforeDamage($weapon, $target, $shooter, $clone, $pos, $gd);
            }
            return array('err' => null, 'loc' => $loc);
        } catch (Throwable $e) {
            return array(
                'err' => 'ERR(' . $this->describeThrowable($e) . ')',
                'loc' => $loc,
            );
        } finally {
            ob_end_clean();
        }
    }

    /**
     * Give $weapon the gamedata its fire() override would have stashed on it.
     *
     * RammingAttack declares $gamedata PRIVATE, so a plain assignment from out here
     * creates a second, dynamic property and the private one stays null - the ram then
     * fatals in getDamage(). Walk up to the class that actually declares it and write
     * that one.
     *
     * Weapons with NO declared $gamedata are left alone rather than given a dynamic
     * one: on PHP 8.2 that is a deprecation, and Manager.php's global error handler
     * turns every deprecation into a thrown ErrorException - so writing one here would
     * put an ERR(...) on the report line of every weapon in the game. A weapon that
     * really does depend on a dynamic $gamedata hits exactly the same wall inside its
     * own fire(), so letting it error is the honest signal. Cached per weapon class.
     */
    private function seedWeaponGamedata($weapon, $gd) {
        $cls = get_class($weapon);
        if (!array_key_exists($cls, $this->gamedataPropRef)) {
            $prop = false;
            for ($c = $cls; $c !== false; $c = get_parent_class($c)) {
                if (!property_exists($c, 'gamedata')) continue;
                try {
                    $candidate = new ReflectionProperty($c, 'gamedata');
                } catch (Throwable $e) {
                    continue; // declared further up; keep walking
                }
                $candidate->setAccessible(true);
                $prop = $candidate;
                break;
            }
            $this->gamedataPropRef[$cls] = $prop;
        }

        $prop = $this->gamedataPropRef[$cls];
        if ($prop !== false) {
            $prop->setValue($weapon, $gd);
        }
    }

    /**
     * Weapon::beforeDamage is protected and is overridden by dozens of weapons (EM
     * shields, dropout crits, tractor moves, damage-doubling modes), so it - not the
     * public damage() - is the entry point that exercises the real allocation. Reflection
     * is the only way in from outside the class; the accessor is cached per weapon class.
     */
    private function invokeBeforeDamage($weapon, $target, $shooter, $fireOrder, $pos, $gd) {
        $cls = get_class($weapon);
        if (!isset($this->beforeDamageRef[$cls])) {
            $method = new ReflectionMethod($cls, 'beforeDamage');
            $method->setAccessible(true);
            $this->beforeDamageRef[$cls] = $method;
        }
        $this->beforeDamageRef[$cls]->invoke($weapon, $target, $shooter, $fireOrder, $pos, $gd);
    }

    /**
     * Roll criticals for every system the replay damaged, mirroring the damage-driven
     * branch of Criticals::setCriticals pass 1 (same iteration shape, same skip rules).
     *
     * Deliberately NOT the whole of setCriticals: the thruster-overthrust and
     * weapon-force-shutdown branches are driven by movement and power rather than by
     * damage, and criticalPhaseEffects (pass 2) runs cross-ship machinery - Hangar Ops
     * dock orders, jump sequencing - that has nothing to do with damage resolution and
     * would drag half the turn-advance into this check.
     *
     * Re-seeded per system so an extra roll inside one system's critical table cannot
     * shift every later system's roll.
     */
    private function rollDamageCriticals($gd, $turn) {
        $found = array();
        foreach ($gd->ships as $ship) {
            if ($ship->isDestroyed()) continue;
            foreach ($ship->systems as $system) {
                if ($system->isDestroyed() && !($system instanceof MissileLauncher)) continue;
                if (!$system->isDamagedOnTurn($turn)) continue;

                Dice::seed((int)$ship->id * 7919 + (int)$system->id * 31 + (int)$turn);
                $before = count($system->criticals);
                ob_start();
                try {
                    $system->testCritical($ship, $gd, array());
                } catch (Throwable $e) {
                    $found[] = array(
                        'shipid' => (int)$ship->id, 'systemid' => (int)$system->id,
                        'phpclass' => 'ERR(' . $this->describeThrowable($e) . ')', 'turnend' => 0,
                    );
                } finally {
                    ob_end_clean();
                }
                // Read the SYSTEM rather than testCritical's return value. addCritical
                // does both - appends to $system->criticals and returns the crit - but
                // overrides are free to add one without putting it in the accumulator
                // they hand back, and several do. The system's own list is the record
                // the rest of the engine goes on, so it is the honest thing to report.
                for ($i = $before; $i < count($system->criticals); $i++) {
                    $crit = $system->criticals[$i];
                    $found[] = array(
                        'shipid' => (int)$crit->shipid,
                        'systemid' => (int)$crit->systemid,
                        'phpclass' => $crit->phpclass,
                        'turnend' => (int)$crit->turnend,
                    );
                }
            }
        }

        usort($found, function ($a, $b) {
            if ($a['shipid'] !== $b['shipid']) return $a['shipid'] <=> $b['shipid'];
            if ($a['systemid'] !== $b['systemid']) return $a['systemid'] <=> $b['systemid'];
            return strcmp($a['phpclass'], $b['phpclass']);
        });
        return $found;
    }

    /**
     * Damage entries the replay appended, grouped by the fire order they belong to.
     * Entries are APPENDED to $system->damage, so anything past the count captured by
     * rewindTurn() is ours - which is exact, and does not assume replay-created entries
     * are the only ones carrying id -1.
     */
    private function collectNewDamage($index) {
        $byOrder = array();
        foreach ($index as $row) {
            $system = $row['system'];
            $count = count($system->damage);
            for ($i = $row['damageCount']; $i < $count; $i++) {
                $d = $system->damage[$i];
                $foid = (int)$d->fireorderid;
                if (!isset($byOrder[$foid])) $byOrder[$foid] = array();
                $byOrder[$foid][] = array(
                    'shipid' => (int)$d->shipid,
                    'systemid' => (int)$d->systemid,
                    'damage' => (int)$d->damage,
                    'armour' => (int)$d->armour,
                    'destroyed' => (bool)$d->destroyed,
                );
            }
        }
        return $byOrder;
    }

    // ---- masking check

    /**
     * Replay per-viewer information hiding for every player, across every live phase.
     *
     * The snapshot check loads gamedata and calls stripForJson() directly, so it never
     * touches prepareForPlayer() - which is where deleteHiddenData() and the three
     * Chameleon passes live. That left every "can the enemy see X before I commit?" rule
     * untested: a masking regression could pass the whole harness while leaking freely.
     *
     * Design notes, each of which cost something to get right:
     *
     *  - ONE FRESH LOAD PER (player, phase). prepareForPlayer() is destructive - it
     *    deletes fire orders, empties EW and power arrays and rewrites targets in place -
     *    so the object cannot be reused for a second viewer or a second phase. There is no
     *    cheaper option: the mutation IS the thing under test.
     *
     *  - PHASE IS FORCED AFTER THE LOAD, never before. TacGamedata's constructor calls
     *    setPhase() with the game's real phase, which re-seeds the static
     *    TacGamedata::$currentPhase on every load - so the synthetic phase set here cannot
     *    survive into the next iteration's load. (Getting this backwards would make the
     *    report order-dependent, the per-load-static trap that has bitten this codebase
     *    before.) A phase the game is not actually in yields a synthetic but perfectly
     *    deterministic payload, which is all a recomputed-vs-recomputed baseline needs -
     *    the same licence the tohit and damage checks already take.
     *
     *  - FINGERPRINT, NOT FULL JSON. Dumping five phases x N players of stripForJson()
     *    would add hundreds of MB to the baseline and would mostly re-test what the
     *    snapshot check already covers. Instead each line records only the fields masking
     *    is able to touch, so a diff points straight at the rule that changed rather than
     *    at a wall of unrelated payload.
     *
     *  - WRAPPED IN A ROLLBACK. The paths involved read as pure in-memory work
     *    (setWaiting, calculateTurndelays, beforeTurn -> setSystemDataWindow), but the
     *    damage check already proved that assumption wrong once elsewhere in the engine,
     *    so this follows the same belt-and-braces pattern rather than trusting a reading.
     *
     *  - IT DOES NOT USE THE GAME'S CURRENT TURN. Every fire-order mask is gated on
     *    `$fire->turn == $this->turn`, and a recorded game almost always sits on a fresh
     *    turn nobody has declared on yet: across the corpus 13 games in 15 have ZERO fire
     *    orders at their current turn. Loading there would have exercised
     *    hideSystemFireOrders - the largest rule in deleteHiddenData - against an empty
     *    list, which is how this check could have looked healthy while testing nothing.
     *    maskingTurn() picks the turn with the most recorded orders instead.
     */
    private function buildMaskingLines($gameid, $players) {
        $turn = $this->maskingTurn($gameid);
        $lines = array('turn=' . ($turn === null ? 'current' : $turn));
        foreach ($players as $pid) {
            foreach (self::MASKING_PHASES as $phase) {
                $prefix = 'p' . $pid . ' ph' . $phase;
                $gd = null;
                $err = null;
                Dice::seed($gameid * 7919 + $pid * 31 + ($phase + 2));
                $this->db->startTransaction();
                try {
                    // Phase is applied by loadGame AFTER setTurn, which is the order the
                    // masks need: they compare a fire order's turn against $gd->turn.
                    $gd = $this->loadGame($pid, $gameid, $turn, $phase);
                    if ($gd === null) {
                        $err = 'gamedata is null';
                    } else {
                        if ($turn === null) $gd->setPhase($phase); // loadGame only sets it alongside a turn
                        ob_start();
                        try {
                            $gd->prepareForPlayer();
                        } finally {
                            ob_end_clean();
                        }
                    }
                } catch (Throwable $e) {
                    $err = $this->describeThrowable($e);
                } finally {
                    $this->db->endTransaction(true);
                }

                if ($err !== null) {
                    $lines[] = $prefix . ' MASK-ERROR: ' . $err;
                    continue;
                }
                $lines = array_merge($lines, $this->maskingFingerprint($gd, $prefix));
                unset($gd);
            }
        }
        return $lines;
    }

    /**
     * The turn this game's masking is replayed at: whichever turn carries the most
     * recorded fire orders, so the fire-order masks have the largest possible surface to
     * act on. Ties break to the LATER turn, so the choice is stable run-to-run.
     *
     * Returns null when the game has no fire orders at all (deployment-only corpus
     * entries), which loads the current state - still worth doing for the movement, EW
     * and deployment-dock masks, which do not need orders to exist.
     *
     * The chosen turn is written into the report, so if the corpus gains orders on a
     * later turn the baseline diff says so outright instead of silently shifting.
     */
    private function maskingTurn($gameid) {
        $stmt = mysqli_prepare($this->raw,
            'SELECT turn FROM tac_fireorder WHERE gameid = ? GROUP BY turn ORDER BY COUNT(*) DESC, turn DESC LIMIT 1');
        if (!$stmt) return null;
        mysqli_stmt_bind_param($stmt, 'i', $gameid);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $turn);
        $found = mysqli_stmt_fetch($stmt) ? (int)$turn : null;
        mysqli_stmt_close($stmt);
        return $found;
    }

    /**
     * Compact record of every field deleteHiddenData() and the Chameleon passes can alter,
     * for one already-prepared gamedata. Deterministically ordered throughout (ships by id,
     * systems by id, fire orders by id) because the load path makes no ordering promise.
     *
     * Reads the masked state directly rather than diffing against an unmasked load: a
     * post-state fingerprint detects under-masking (an id that should have gone is still
     * there, a count that should be 0 is not) and over-masking (the viewer's own data went
     * missing) equally well, at half the loads.
     */
    private function maskingFingerprint($gd, $prefix) {
        $lines = array();
        $lines[] = $prefix . ' waiting=' . ($gd->waiting ? 1 : 0)
            . ' stealthPresent=' . ($gd->isStealthPresent ? 1 : 0)
            . ' minesPresent=' . ($gd->areMinesPresent ? 1 : 0);

        // Slot fleet-value adjustment - the Chameleon points-cap mask.
        $slots = is_array($gd->slots) ? $gd->slots : array();
        $slotIds = array_keys($slots);
        sort($slotIds);
        foreach ($slotIds as $sid) {
            $slot = $slots[$sid];
            if (empty($slot->fleetValueAdjust)) continue; // 0 is the overwhelming default; keep lines meaningful
            $lines[] = $prefix . ' slot' . $sid . ' fleetValueAdjust=' . (int)$slot->fleetValueAdjust;
        }

        $ships = is_array($gd->ships) ? $gd->ships : array();
        usort($ships, function ($a, $b) { return (int)$a->id <=> (int)$b->id; });

        foreach ($ships as $ship) {
            $mv = is_array($ship->movement) ? $ship->movement : array();
            // The off-map sentinel hideStealthShipMovement() substitutes for a real track.
            $hidden = 0;
            foreach ($mv as $m) {
                $qr = $this->positionQR(isset($m->position) ? $m->position : null);
                if ($qr !== null && $qr[0] === -10000 && $qr[1] === -10000) {
                    $hidden = 1;
                    break;
                }
            }
            $lines[] = $prefix . ' ship' . (int)$ship->id
                . ' mv=' . count($mv)
                . ' stealthHidden=' . $hidden
                . ' ew=' . (is_array($ship->EW) ? count($ship->EW) : 0)
                . ' cham=' . (!empty($ship->chameleonDisguisedForViewer) ? 1 : 0)
                . ' removed=' . (!empty($ship->removed) ? 1 : 0);

            foreach ($this->maskedSystems($ship) as $system) {
                $parts = array();
                $pw = is_array($system->power) ? count($system->power) : 0;
                if ($pw > 0) $parts[] = 'pw=' . $pw;

                $orders = is_array($system->fireOrders) ? $system->fireOrders : array();
                if (count($orders) > 0) {
                    usort($orders, function ($a, $b) { return (int)$a->id <=> (int)$b->id; });
                    $fo = array();
                    foreach ($orders as $f) {
                        // targetid/x/y are what the hidetarget mask rewrites; needed/shotshit are
                        // what the Chameleon fire-order mask rewrites.
                        $fo[] = (int)$f->id . ':' . $f->targetid . ':' . $f->x . ':' . $f->y
                            . ':' . (int)$f->needed . ':' . (int)$f->shotshit;
                    }
                    $parts[] = 'fo=' . implode(',', $fo);
                }

                // Hangar state hidden by hideDeploymentDocks().
                if (isset($system->hangarUsage) && is_array($system->hangarUsage) && count($system->hangarUsage) > 0) {
                    $parts[] = 'hangar=' . count($system->hangarUsage);
                }
                if (!empty($system->isLCVRail)) {
                    $parts[] = 'lcv=' . (empty($system->lcvDocked) ? 0 : 1);
                }

                if (count($parts) === 0) continue; // nothing masking could have touched
                $lines[] = $prefix . ' ship' . (int)$ship->id . ' sys' . (int)$system->id
                    . ' ' . implode(' ', $parts);
            }
        }

        // Ballistics carry their own copy of the target, nulled alongside the fire order.
        $balls = is_array($gd->ballistics) ? $gd->ballistics : array();
        usort($balls, function ($a, $b) { return (int)$a->id <=> (int)$b->id; });
        foreach ($balls as $b) {
            $qr = $this->positionQR($b->targetposition);
            $pos = ($qr === null) ? 'null' : ($qr[0] . '/' . $qr[1]);
            $lines[] = $prefix . ' ball' . (int)$b->id
                . ' fo=' . (int)$b->fireOrderId . ' target=' . $b->targetid . ' pos=' . $pos;
        }

        return $lines;
    }

    /**
     * A hex position as array(q, r), or null if there isn't one.
     *
     * Positions are NOT reliably OffsetCoordinate objects: rows that came back through a
     * POST round-trip arrive as a plain array or stdClass instead (the same trap
     * Movement::validateThrustPayment guards with `new OffsetCoordinate($anchor->position)`).
     * Reading ->q blind fataled the record on real corpus data.
     */
    private function positionQR($pos) {
        if ($pos === null) return null;
        if (is_object($pos)) {
            if (!isset($pos->q) || !isset($pos->r)) return null;
            return array((int)$pos->q, (int)$pos->r);
        }
        if (is_array($pos)) {
            if (!isset($pos['q']) || !isset($pos['r'])) return null;
            return array((int)$pos['q'], (int)$pos['r']);
        }
        return null;
    }

    /**
     * Every system a masking rule can reach, both levels. hideSystemFireOrders() descends
     * into a FighterFlight's Fighters explicitly, so the fingerprint must too or a flight's
     * weapons would be invisible to this check - which is exactly where hidden fire orders
     * would go unnoticed.
     */
    private function maskedSystems($ship) {
        $out = array();
        $systems = is_array($ship->systems) ? $ship->systems : array();
        usort($systems, function ($a, $b) { return (int)$a->id <=> (int)$b->id; });
        foreach ($systems as $system) {
            $out[] = $system;
            if (isset($system->systems) && is_array($system->systems)) {
                $subs = $system->systems;
                usort($subs, function ($a, $b) { return (int)$a->id <=> (int)$b->id; });
                foreach ($subs as $sub) {
                    $out[] = $sub;
                }
            }
        }
        return $out;
    }

    // ------------------------------------------------------------- commands

    public function cmdList() {
        $games = $this->discoverGames();
        $manifest = $this->readManifest();
        $baselined = ($manifest !== null) ? $manifest['games'] : array();
        printf("%-7s %-5s %-12s %-6s %-9s %s\n", 'game', 'turn', 'status', 'ships', 'baseline', 'name');
        foreach ($games as $id => $row) {
            printf("%-7d %-5d %-12s %-6d %-9s %s\n",
                $id, $row->turn, $row->status, $row->ships,
                isset($baselined[$id]) ? 'yes' : '-', $row->name);
        }
        printf("\n%d games with recorded play. Baseline: %s\n",
            count($games),
            ($manifest !== null) ? count($baselined) . ' games recorded' : 'none recorded yet');
        return 0;
    }

    public function cmdRecord($gameFilter) {
        $games = $this->selectGames($gameFilter);
        if (count($games) === 0) {
            fwrite(STDERR, "No matching games with recorded play found.\n");
            return 1;
        }
        if (!is_dir($this->baselineDir)) {
            mkdir($this->baselineDir, 0777, true);
        }

        $manifest = array(
            'formatVersion' => 1,
            'recorded' => date('c'),
            'checks' => $this->checks,
            'games' => array(),
        );
        $errors = 0;
        $start = microtime(true);
        foreach ($games as $id => $row) {
            $t0 = microtime(true);
            $reports = $this->buildReports($row);
            $dir = $this->gameDir($id);
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
            }
            $errNames = array();
            foreach ($reports as $name => $content) {
                file_put_contents($dir . '/' . $name, $content);
                if (strpos($content, 'HARNESS-ERROR') !== false || strpos($content, 'LOAD-ERROR') !== false) {
                    $errNames[] = $name;
                }
            }
            $manifest['games'][$id] = array(
                'turn' => (int)$row->turn,
                'status' => $row->status,
                'reports' => array_keys($reports),
            );
            $note = '';
            if (count($errNames) > 0) {
                $errors++;
                $note = '  [contains load errors: ' . implode(', ', $errNames) . ']';
            }
            printf("recorded game %-6d turn %-3d %d report(s) %.1fs%s\n",
                $id, $row->turn, count($reports), microtime(true) - $t0, $note);
        }
        file_put_contents($this->baselineDir . '/manifest.json',
            json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . "\n");
        printf("\nBaseline recorded: %d games in %.1fs -> %s\n",
            count($games), microtime(true) - $start, $this->baselineDir);
        if ($errors > 0) {
            printf("NOTE: %d game(s) recorded WITH load errors (see above) - their error text is\n", $errors);
            printf("part of the baseline; if a later check shows them loading cleanly, that's a fix.\n");
        }
        return 0;
    }

    public function cmdCheck($gameFilter) {
        $manifest = $this->readManifest();
        if ($manifest === null) {
            fwrite(STDERR, "No baseline found at {$this->baselineDir}. Run 'record' first.\n");
            return 2;
        }
        $ids = array_keys($manifest['games']);
        if ($gameFilter !== null) {
            $ids = array_values(array_intersect($ids, $gameFilter));
        }
        if (count($ids) === 0) {
            fwrite(STDERR, "No baselined games match the filter.\n");
            return 2;
        }

        $corpus = $this->discoverGames();
        $failed = 0;
        $passed = 0;
        $start = microtime(true);

        foreach ($ids as $id) {
            $meta = $manifest['games'][$id];
            if (!isset($corpus[$id])) {
                printf("game %-6d SKIP (no longer in database)\n", $id);
                continue;
            }
            $row = $corpus[$id];
            if ((int)$row->turn !== (int)$meta['turn'] || $row->status !== $meta['status']) {
                printf("game %-6d SKIP (game advanced since record: turn %d->%d %s->%s; re-record it)\n",
                    $id, $meta['turn'], $row->turn, $meta['status'], $row->status);
                continue;
            }

            $t0 = microtime(true);
            $reports = $this->buildReports($row);
            $diffs = array();
            $compared = 0;
            foreach ($meta['reports'] as $name) {
                if (!in_array($this->checkNameForReport($name), $this->checks)) {
                    continue; // baseline has it, but this run was limited via --checks
                }
                $compared++;
                $baselineFile = $this->gameDir($id) . '/' . $name;
                if (!file_exists($baselineFile)) {
                    $diffs[$name] = array('baseline file missing: ' . $name);
                    continue;
                }
                $baseline = file_get_contents($baselineFile);
                $current = isset($reports[$name]) ? $reports[$name] : null;
                if ($current === null) {
                    $diffs[$name] = array('report not produced by this run');
                    continue;
                }
                if ($current !== $baseline) {
                    $diffs[$name] = $this->diffReport($name, $baseline, $current);
                }
            }

            if (count($diffs) === 0) {
                $passed++;
                printf("game %-6d PASS  (%d report(s), %.1fs)\n", $id, $compared, microtime(true) - $t0);
            } else {
                $failed++;
                printf("game %-6d FAIL  (%.1fs)\n", $id, microtime(true) - $t0);
                foreach ($diffs as $name => $detail) {
                    printf("  %s:\n", $name);
                    foreach ($detail as $line) {
                        printf("    %s\n", $line);
                    }
                }
            }
        }

        printf("\n%d passed, %d failed (%.1fs total)\n", $passed, $failed, microtime(true) - $start);
        if ($failed > 0) {
            printf("A FAIL means current code produces different results than the recorded baseline\n");
            printf("for real recorded play. If the change is INTENTIONAL, re-run 'record' to accept it.\n");
        }
        return ($failed > 0) ? 1 : 0;
    }

    // ------------------------------------------------------------- diffing

    /** Human-readable difference summary between baseline and current report. */
    private function diffReport($name, $baseline, $current) {
        if (substr($name, -5) === '.json') {
            $b = json_decode($baseline, true);
            $c = json_decode($current, true);
            if ($b !== null && $c !== null) {
                $paths = array();
                $this->jsonDiff($b, $c, '', $paths);
                if (count($paths) === 0) {
                    return array('(byte-level difference only, e.g. key order/float formatting)');
                }
                $out = array_slice($paths, 0, $this->diffLimit);
                if (count($paths) > $this->diffLimit) {
                    $out[] = '... and ' . (count($paths) - $this->diffLimit) . ' more differing path(s)';
                }
                return $out;
            }
        }
        // line diff for text reports (and unparseable JSON)
        $bLines = explode("\n", $baseline);
        $cLines = explode("\n", $current);
        $out = array();
        $max = max(count($bLines), count($cLines));
        for ($i = 0; $i < $max && count($out) < $this->diffLimit + 1; $i++) {
            $bl = isset($bLines[$i]) ? $bLines[$i] : '<absent>';
            $cl = isset($cLines[$i]) ? $cLines[$i] : '<absent>';
            if ($bl !== $cl) {
                if (count($out) === $this->diffLimit) {
                    $out[] = '... more lines differ';
                    break;
                }
                $out[] = 'line ' . ($i + 1) . ':';
                $out[] = '  baseline: ' . $this->truncate($bl);
                $out[] = '  current:  ' . $this->truncate($cl);
            }
        }
        return $out;
    }

    private function jsonDiff($a, $b, $path, &$paths) {
        if (count($paths) > $this->diffLimit * 4) return; // enough detail collected
        if (is_array($a) && is_array($b)) {
            foreach ($a as $k => $v) {
                $p = $path . '/' . $k;
                if (!array_key_exists($k, $b)) {
                    $paths[] = "$p: removed (was " . $this->scalarRepr($v) . ')';
                } else {
                    $this->jsonDiff($v, $b[$k], $p, $paths);
                }
            }
            foreach ($b as $k => $v) {
                if (!array_key_exists($k, $a)) {
                    $paths[] = "$path/$k: added (" . $this->scalarRepr($v) . ')';
                }
            }
            return;
        }
        if ($a !== $b) {
            $paths[] = "$path: " . $this->scalarRepr($a) . ' -> ' . $this->scalarRepr($b);
        }
    }

    private function scalarRepr($v) {
        if (is_array($v)) return '<' . count($v) . ' item array>';
        if ($v === null) return 'null';
        if (is_bool($v)) return $v ? 'true' : 'false';
        return $this->truncate((string)$v, 80);
    }

    private function truncate($s, $len = 160) {
        return (strlen($s) > $len) ? substr($s, 0, $len) . '...' : $s;
    }

    // ------------------------------------------------------------- plumbing

    private function gameDir($gameid) {
        return $this->baselineDir . '/game_' . $gameid;
    }

    /** Which check a baseline report filename belongs to. */
    private function checkNameForReport($name) {
        if (strpos($name, 'snapshot_') === 0) return 'snapshot';
        if ($name === 'movement.txt') return 'movement';
        if ($name === 'tohit.txt') return 'tohit';
        if ($name === 'damage.txt') return 'damage';
        if ($name === 'masking.txt') return 'masking';
        return $name;
    }

    private function readManifest() {
        $file = $this->baselineDir . '/manifest.json';
        if (!file_exists($file)) return null;
        $manifest = json_decode(file_get_contents($file), true);
        return is_array($manifest) ? $manifest : null;
    }

    private function selectGames($gameFilter) {
        $games = $this->discoverGames();
        if ($gameFilter === null) return $games;
        $selected = array();
        foreach ($gameFilter as $id) {
            if (isset($games[$id])) {
                $selected[$id] = $games[$id];
            } else {
                fwrite(STDERR, "WARNING: game $id not found in corpus (turn>=1, not LOBBY) - skipped.\n");
            }
        }
        return $selected;
    }
}

// ------------------------------------------------------------------- main

function usage() {
    fwrite(STDERR, "Usage: php replayHarness.php <list|record|check> [options]\n");
    fwrite(STDERR, "  --games=1,2,3      only these game ids (default: all recorded games)\n");
    fwrite(STDERR, "  --checks=a,b       subset of: snapshot,movement,tohit,damage,masking (default: all)\n");
    fwrite(STDERR, "  --baseline=path    baseline directory (default: tests/replay/baseline)\n");
    fwrite(STDERR, "  --diff-limit=N     max differences shown per report (default: 15)\n");
    exit(2);
}

$command = null;
$gameFilter = null;
$checks = ReplayHarness::CHECKS;
$baselineDir = $FV_ROOT . '/tests/replay/baseline';
$diffLimit = 15;

for ($i = 1; $i < $argc; $i++) {
    $arg = $argv[$i];
    if ($arg === 'list' || $arg === 'record' || $arg === 'check') {
        $command = $arg;
    } elseif (strpos($arg, '--games=') === 0) {
        $gameFilter = array_map('intval', array_filter(explode(',', substr($arg, 8))));
    } elseif (strpos($arg, '--checks=') === 0) {
        $checks = array_values(array_intersect(explode(',', substr($arg, 9)), ReplayHarness::CHECKS));
        if (count($checks) === 0) usage();
    } elseif (strpos($arg, '--baseline=') === 0) {
        $baselineDir = substr($arg, 11);
    } elseif (strpos($arg, '--diff-limit=') === 0) {
        $diffLimit = max(1, (int)substr($arg, 13));
    } else {
        fwrite(STDERR, "Unknown argument: $arg\n");
        usage();
    }
}

if ($command === null) usage();

try {
    $harness = new ReplayHarness($FV_ROOT, $baselineDir, $checks, $diffLimit);
    switch ($command) {
        case 'list':
            exit($harness->cmdList());
        case 'record':
            exit($harness->cmdRecord($gameFilter));
        case 'check':
            exit($harness->cmdCheck($gameFilter));
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'FATAL: ' . get_class($e) . ': ' . $e->getMessage() . "\n");
    exit(1);
}
