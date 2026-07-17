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

// autoload.php does not include CustomException; it only surfaces on a DB
// connect failure, masking the real error. Stub it (same as scratch scripts).
if (!class_exists('CustomException')) {
    class CustomException extends Exception {
        public function __construct($code, $msg, $s = 0, $e = null) {
            parent::__construct($msg, (int)$code);
        }
    }
}

class ReplayHarness {

    const CHECKS = array('snapshot', 'movement', 'tohit');

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

        $this->db = new DBManager($host, $port, $name, $user, $pass);
        $this->raw = mysqli_connect($host, $user, $pass, $name, $port);
        if (!$this->raw) {
            throw new Exception('Harness discovery connection failed: ' . mysqli_connect_error());
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
    fwrite(STDERR, "  --checks=a,b       subset of: snapshot,movement,tohit (default: all)\n");
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
