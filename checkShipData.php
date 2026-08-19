<?php
/**
 * Ship-data validator.
 *
 * Instantiates every ship class under source/server/model/ships/ and asserts a
 * set of blueprint invariants that nothing else in the toolchain checks. 40% of
 * bug-fix commits touch that directory and none of that data had any automated
 * validation. The headline failure mode is SILENT:
 *
 *   ShipClasses.php getHitSystemByTable() resolves a hit-chart entry with
 *   getSystemsByNameLoc($name, $location, ...). A name matching no system in
 *   that section returns an empty array and the damage is quietly rerouted to
 *   getStructureSystem($location). No error, no log, wrong game outcome. One
 *   misspelt letter makes a real system unhittable for the life of the ship
 *   and nobody ever finds out.
 *
 * It is a RUNTIME validator on purpose. A static probe over the same data
 * reports hundreds of false hits: systems assigned to a local before
 * addLeftSystem($t1l), displayName overridden after construction, six-sided
 * hulls whose location numbering differs, and the "2:Thruster" location-
 * qualified name syntax all resolve correctly once the ship is built. Only an
 * instantiated ship can answer "is this name reachable from this section".
 *
 * ---------------------------------------------------------------------------
 * CHECKS (all run by default; pick with --checks= / drop with --skip=)
 *
 *   construct  the hull builds at all. ALWAYS runs, whatever the filters say:
 *              nothing else can be inspected on a ship that will not
 *              instantiate, and a hull that throws in its constructor takes
 *              the lobby's whole faction list down with it.
 *   hitchart   every hit-chart entry resolves to a real system in that
 *              section - via displayName, hitChartName, the "LOC:Name"
 *              redirect or the "TAG:tag" selector. Failures are classified
 *              (typo / missing TAG: prefix / wrong section / stray
 *              whitespace / no match) so each one says what to do about it.
 *              Also checks the exact-case 'Primary' keyword - the main
 *              resolution path compares it case-sensitively, so "PRIMARY"
 *              silently becomes a Structure hit - and Primary self-recursion
 *              on location 0.
 *   chart      chart shape: keys are integers 1..20, the chart terminates at
 *              20 (a lower terminator leaves the top rolls falling through to
 *              Structure), every charted location exists on the hull, and
 *              every hull location has a chart.
 *   dupkeys    duplicate roll keys in the SOURCE. PHP silently keeps the last
 *              value for a repeated key, so this is the one check that cannot
 *              be done at runtime - it tokenises the file instead
 *              (token_get_all, not a regex: comments and nested arrays live
 *              inside these literals).
 *   image      ship $imagePath resolves to a real file under source/public/,
 *              CASE-EXACTLY. Windows dev is case-insensitive and live is not,
 *              so a case slip works locally and 404s only in production.
 *   sysimage   the same for per-system icons. A ShipSystem's $iconPath is
 *              always resolved under img/systemicons/ (SystemIcon.js
 *              getBackgroundImage); a Fighter's $iconPath / $imagePath are
 *              web-root relative (FlightIcon.js / FighterIcon.js). Reported
 *              once per distinct asset path, not once per ship.
 *   variantof  $variantOf is empty, a known retirement sentinel, or the
 *              $shipClass of a real ship. Anything else hides the ship from
 *              the lobby completely - it is neither a base design nor
 *              nestable under one (gamelobby.js) - which is how a truncated
 *              sentinel silently retires a refit nobody meant to retire.
 *   hangar     declared $fighters[] fit the ship's hangar boxes. Mirrors
 *              HangarOps::populateInitialHangarUsage's own arithmetic
 *              (capacity in BOXES, catapult/rail/LCV bays partitioned out,
 *              ultralights two to a box). An over-declaration is silently
 *              truncated at game start.
 *   arcs       startArc / endArc are numeric and within 0..360.
 *   ids        system ids are positional and unique - id == array index for
 *              top-level systems, and the 1000+(id*10)+i sub-weapon ids of
 *              duo/dual weapons and missile racks do not collide (they start
 *              colliding once a parent carries more than 10 sub-weapons).
 *              Persisted thrust / fire / power / crit rows are keyed by these
 *              ids, so a collision misroutes real saved data.
 *
 * ---------------------------------------------------------------------------
 * THE BASELINE
 *
 * The tree carries a large amount of pre-existing ship-data debt, so a bare
 * "exit 1 on any violation" gate would be red forever and therefore useless.
 * Instead the accepted state lives in tests/shipdata/baseline.txt and a normal
 * run only fails on findings that are NOT in it. Fix something and it is
 * reported as FIXED (never a failure); introduce something and the run fails
 * naming it.
 *
 * Unlike the replay harness's baseline, this one is derived purely from the
 * repository - no database, no local games - so it is deterministic, portable
 * and COMMITTED. Every dev and every contributor gets the same gate, and the
 * baseline diff itself is reviewable: a PR that adds a line to it is a PR that
 * added a known-broken ship entry.
 *
 * ---------------------------------------------------------------------------
 * USAGE (from the repo root, inside the php container)
 *
 *     docker exec -w /usr/src/current fieryvoid-php-1 php checkShipData.php
 *
 *     --record         rewrite the baseline from the current tree, then exit 0.
 *                      Run it only after reviewing the findings you are
 *                      accepting - it accepts ALL of them.
 *     --no-baseline    ignore the baseline and report every finding (the
 *                      "show me the whole debt" mode).
 *     --checks=a,b     run only these checks
 *     --skip=a,b       run everything except these
 *     --ship=Foo       only ships whose class name contains Foo
 *     --faction=Bar    only ships whose faction contains Bar
 *     --max=N          print at most N findings per check (default 40, 0=all)
 *     --strict         new warnings fail the run too
 *
 * Exits 1 when a new ERROR-severity finding appears (or any new finding under
 * --strict), 0 otherwise - so it can sit in scripts/fvbuild.ps1 -Check beside
 * the replay harness.
 */

ini_set('display_errors', 1);
ini_set('memory_limit', '1G');
error_reporting(E_ALL);

require_once __DIR__ . '/source/autoload.php';

$SHIPS_DIR     = __DIR__ . '/source/server/model/ships/';
$WEB_ROOT      = __DIR__ . '/source/public/';
$BASELINE_FILE = __DIR__ . '/tests/shipdata/baseline.txt';

/* Retirement sentinels: a $variantOf that deliberately matches no hull, which
   hides the ship from the lobby while keeping the class loadable so old games
   containing it still open. All three spellings are in live use. */
const RETIREMENT_SENTINELS = array('NONE', 'OBSOLETE', 'OBSELETE');

/* Severity per check. ERROR fails the build when new; WARN only under --strict. */
$CHECK_SEVERITY = array(
    /* Always active regardless of --checks/--skip: nothing else can be
       inspected on a ship that will not build, and a hull that throws in its
       constructor takes the lobby's faction list down with it. */
    'construct' => 'ERROR',
    'hitchart'  => 'ERROR',
    'chart'     => 'WARN',
    'dupkeys'   => 'ERROR',
    'image'     => 'ERROR',
    'sysimage'  => 'WARN',
    'variantof' => 'WARN',
    'hangar'    => 'WARN',
    'arcs'      => 'ERROR',
    'ids'       => 'ERROR',
);

// ---------------------------------------------------------------- CLI parsing

$opts = array(
    'checks'   => null,
    'skip'     => array(),
    'ship'     => null,
    'faction'  => null,
    'max'      => 40,
    'strict'   => false,
    'record'   => false,
    'baseline' => true,
);

foreach (array_slice($argv, 1) as $arg) {
    if (preg_match('/^--checks=(.*)$/', $arg, $m)) {
        $opts['checks'] = array_values(array_filter(array_map('trim', explode(',', strtolower($m[1])))));
    } elseif (preg_match('/^--skip=(.*)$/', $arg, $m)) {
        $opts['skip'] = array_values(array_filter(array_map('trim', explode(',', strtolower($m[1])))));
    } elseif (preg_match('/^--ship=(.*)$/', $arg, $m)) {
        $opts['ship'] = $m[1];
    } elseif (preg_match('/^--faction=(.*)$/', $arg, $m)) {
        $opts['faction'] = $m[1];
    } elseif (preg_match('/^--max=(\d+)$/', $arg, $m)) {
        $opts['max'] = (int)$m[1];
    } elseif ($arg === '--strict') {
        $opts['strict'] = true;
    } elseif ($arg === '--record') {
        $opts['record'] = true;
    } elseif ($arg === '--no-baseline') {
        $opts['baseline'] = false;
    } else {
        fwrite(STDERR, "unknown option: $arg\n");
        fwrite(STDERR, "checks: " . implode(', ', array_keys($CHECK_SEVERITY)) . "\n");
        exit(2);
    }
}

if ($opts['checks'] !== null) {
    foreach ($opts['checks'] as $c) {
        if (!isset($CHECK_SEVERITY[$c])) {
            fwrite(STDERR, "unknown check: $c (have: " . implode(', ', array_keys($CHECK_SEVERITY)) . ")\n");
            exit(2);
        }
    }
}

$active = array();
foreach (array_keys($CHECK_SEVERITY) as $c) {
    if ($opts['checks'] !== null && !in_array($c, $opts['checks'], true)) continue;
    if (in_array($c, $opts['skip'], true)) continue;
    $active[$c] = true;
}
if (empty($active)) {
    fwrite(STDERR, "no checks selected. Available: " . implode(', ', array_keys($CHECK_SEVERITY)) . "\n");
    exit(2);
}
$active['construct'] = true;   // see the note on CHECK_SEVERITY

/* A filtered --record would write a baseline covering only the ships that
   survived the filter, and the very next full run would then report every
   other known finding as brand new. Same trap the replay harness has with
   `record --games=` - refuse it outright rather than silently shrink the gate. */
if ($opts['record'] && ($opts['ship'] !== null || $opts['faction'] !== null)) {
    fwrite(STDERR, "--record cannot be combined with --ship= / --faction=: it would write a\n");
    fwrite(STDERR, "baseline covering only the filtered ships and silently shrink the gate.\n");
    exit(2);
}

// ------------------------------------------------------------------ findings

/* A finding carries two strings: `key` is the baseline identity (must be
   deterministic across runs on unchanged data) and `detail` is extra context
   printed for a human but deliberately kept OUT of the key, so that e.g. the
   list of ships referencing a missing icon can grow without inventing a new
   "violation". */
$findings = array();
$stats    = array('files' => 0, 'skipped' => 0, 'ships' => 0, 'charts' => 0, 'entries' => 0, 'systems' => 0);

function finding($check, $ship, $where, $message, $detail = '')
{
    global $findings;
    $findings[] = array(
        'check'   => $check,
        'ship'    => $ship,
        'where'   => $where,
        'message' => $message,
        'detail'  => $detail,
        'key'     => $check . "\t" . $ship . "\t" . $where . "\t" . $message,
    );
}

// ------------------------------------------------------- case-exact fs lookup

/* file_exists() is case-INSENSITIVE on the Windows dev box and case-SENSITIVE
   on the live Linux host, so it cannot answer the question that matters. Walk
   the path one segment at a time against real directory listings instead. */
function dirEntries($dir)
{
    static $cache = array();
    if (!array_key_exists($dir, $cache)) {
        $names = @scandir($dir);
        if ($names === false) {
            $cache[$dir] = false;
        } else {
            $map = array();
            foreach ($names as $n) $map[$n] = true;
            $cache[$dir] = $map;
        }
    }
    return $cache[$dir];
}

/* '' when the path resolves case-exactly, otherwise the reason it does not. */
function resolveAssetPath($webRoot, $rel)
{
    $rel   = str_replace('\\', '/', trim($rel));
    $parts = array();
    foreach (explode('/', $rel) as $p) {
        if ($p === '' || $p === '.') continue;
        $parts[] = $p;
    }
    if (empty($parts)) return 'empty path';

    $cur = rtrim($webRoot, '/');
    foreach ($parts as $i => $p) {
        $entries = dirEntries($cur);
        if ($entries === false) return 'no such directory: ' . $cur;
        if (isset($entries[$p])) {
            $cur .= '/' . $p;
            continue;
        }
        // Absent under that exact name - a case slip, or a real miss?
        foreach (array_keys($entries) as $name) {
            if (strcasecmp((string)$name, $p) === 0) {
                $shown = implode('/', array_slice($parts, 0, $i));
                if ($shown !== '') $shown .= '/';
                return 'case mismatch: on disk as "' . $shown . $name . '" (works on Windows, 404s on live)';
            }
        }
        return 'no such file';
    }
    return '';
}

// ---------------------------------------------------- source-level duplicates

/* Duplicate roll keys are invisible at runtime: PHP builds the array literal
   with the LAST value for a repeated key and never says a word, so the earlier
   system just loses its slice of the chart. The only place the mistake still
   exists is the source text. */
function findDuplicateChartKeys($file)
{
    $src = @file_get_contents($file);
    if ($src === false) return array();

    $raw = @token_get_all($src);
    if (!is_array($raw)) return array();

    // Drop whitespace/comments, remembering the line each surviving token is on.
    $toks = array();
    foreach ($raw as $t) {
        if (is_array($t)) {
            if ($t[0] === T_WHITESPACE || $t[0] === T_COMMENT || $t[0] === T_DOC_COMMENT) continue;
            $toks[] = array($t[0], $t[1], $t[2]);
        } else {
            $line = empty($toks) ? 0 : $toks[count($toks) - 1][2];
            $toks[] = array(-1, $t, $line);
        }
    }

    $dups = array();
    $n    = count($toks);

    for ($i = 0; $i < $n - 2; $i++) {
        // Match `$this -> hitChart`
        if ($toks[$i][0] !== T_VARIABLE || $toks[$i][1] !== '$this') continue;
        if ($toks[$i + 1][0] !== T_OBJECT_OPERATOR) continue;
        if ($toks[$i + 2][0] !== T_STRING || $toks[$i + 2][1] !== 'hitChart') continue;

        $j = $i + 3;

        /* Optional [ ... ] subscript: `$this->hitChart[$loc] = array(...)`, the
           shape used by hulls that build their sections in a loop. With a
           subscript the literal IS one location's chart, so its roll keys sit
           one nesting level shallower than in the whole-chart form. */
        $subscripted = false;
        if ($j < $n && $toks[$j][0] === -1 && $toks[$j][1] === '[') {
            $depth = 0;
            while ($j < $n) {
                if ($toks[$j][0] === -1 && $toks[$j][1] === '[') $depth++;
                if ($toks[$j][0] === -1 && $toks[$j][1] === ']') {
                    $depth--;
                    if ($depth === 0) { $j++; break; }
                }
                $j++;
            }
            $subscripted = true;
        }

        if ($j >= $n || $toks[$j][0] !== -1 || $toks[$j][1] !== '=') continue;
        $j++;
        if ($j >= $n) continue;

        /* The literal has to start right here, or there is nothing to walk
           (e.g. `$this->hitChart = $this->hitChart[$location]`). */
        $isArrayOpen = ($toks[$j][0] === T_ARRAY) || ($toks[$j][0] === -1 && $toks[$j][1] === '[');
        if (!$isArrayOpen) continue;

        $rollDepth = $subscripted ? 1 : 2;

        // Walk the literal, keeping one seen-keys set per open array level.
        $seen  = array();   // depth => array(key => line first written)
        $depth = 0;
        for (; $j < $n; $j++) {
            $tok = $toks[$j];

            if ($tok[0] === T_ARRAY) {
                // `array` only opens a literal when followed by `(`
                if (isset($toks[$j + 1]) && $toks[$j + 1][0] === -1 && $toks[$j + 1][1] === '(') {
                    $depth++;
                    $seen[$depth] = array();
                    $j++;
                }
                continue;
            }
            if ($tok[0] === -1 && ($tok[1] === '[' || $tok[1] === '(')) {
                $depth++;
                $seen[$depth] = array();
                continue;
            }
            if ($tok[0] === -1 && ($tok[1] === ']' || $tok[1] === ')')) {
                unset($seen[$depth]);
                $depth--;
                if ($depth <= 0) break;
                continue;
            }

            // An integer key: `N =>`
            if ($tok[0] === T_LNUMBER
                && isset($toks[$j + 1]) && $toks[$j + 1][0] === T_DOUBLE_ARROW
                && $depth === $rollDepth) {
                $key = $tok[1];
                if (isset($seen[$depth][$key])) {
                    $dups[] = array('key' => $key, 'line' => $tok[2], 'first' => $seen[$depth][$key]);
                } else {
                    $seen[$depth][$key] = $tok[2];
                }
            }
        }
        $i = $j;
    }

    return $dups;
}

// --------------------------------------------------------- hit-chart resolver

/* True when $tag is carried by some system on the ship, reachable from at
   least one bearing (getSystemsByTag is arc-filtered; we only care that the
   tag exists at all, not that every direction finds it). */
function tagExists($ship, $tag)
{
    for ($bearing = 0; $bearing < 360; $bearing += 15) {
        if (count($ship->getSystemsByTag($tag, $bearing, true)) > 0) return true;
    }
    return false;
}

/* Mirror of the lookup getHitSystemByTable performs, minus the dice. Returns
   array('' , '') when the entry resolves, else array(message, hint). */
function chartEntryProblem($ship, $nameIndex, $name, $location)
{
    if (!is_string($name) || trim($name) === '') {
        return array('entry is not a non-empty string (' . var_export($name, true) . ')', '');
    }

    /* 'Primary' is a redirect keyword, not a system name, and the main
       resolution path compares it CASE-SENSITIVELY ($name == 'Primary').
       'PRIMARY'/'primary' therefore fall through to the name lookup, match
       nothing, and silently become Structure hits. (The Flash and Piercing
       chart rebuilds uppercase first and so DO match - which is exactly why
       the bug is so hard to spot from play: the same entry behaves
       differently depending on the weapon that fired.) */
    if (strcasecmp($name, 'Primary') === 0) {
        if ($name !== 'Primary') {
            return array('"' . $name . '" must be spelt exactly "Primary" - getHitSystemByTable'
                       . ' compares case-sensitively, so this is looked up as a system name,'
                       . ' matches nothing and reroutes to Structure', 'rename to "Primary"');
        }
        if ((int)$location === 0) {
            return array('"Primary" on the PRIMARY chart redirects location 0 to itself'
                       . ' - infinite recursion', '');
        }
        return array('', '');
    }

    $parts = explode(':', $name);

    if (count($parts) === 2 && $parts[0] === 'TAG') {
        if (tagExists($ship, $parts[1])) return array('', '');
        return array('no system carries tag "' . $parts[1] . '" - every hit here is rerouted to Structure',
                     'dead TAG');
    }

    /* getSystemsByNameLoc handles the "LOC:Name" redirect itself, matches
       displayName OR hitChartName case-insensitively, and special-cases
       'Structure' (which legitimately falls back to the PRIMARY structure on
       hulls whose section has none - MCVs do that by design). */
    if (count($ship->getSystemsByNameLoc($name, $location, 0, true)) > 0) return array('', '');

    // Failed. Work out WHY, so the report says what to do about it.
    $bare = (count($parts) === 2) ? $parts[1] : $name;
    $lc   = strtolower($bare);
    $hint = '';

    if (tagExists($ship, $bare)) {
        $hint = 'a system carries this as a TAG - write it "TAG:' . $bare . '"';
    } elseif (isset($nameIndex[strtolower(trim($bare))]) && trim($bare) !== $bare) {
        $locs = array_keys($nameIndex[strtolower(trim($bare))]);
        $hint = 'stray whitespace - trimmed it matches a system at location ' . implode(',', $locs);
    } elseif (isset($nameIndex[$lc])) {
        $locs = array_keys($nameIndex[$lc]);
        $hint = (count($parts) === 2)
            ? 'that system is at location ' . implode(',', $locs)
              . ', not ' . $parts[0] . ' - the "LOC:Name" prefix points at the wrong section'
            : 'that system exists, but at location ' . implode(',', $locs)
              . ' - use the "LOC:Name" form to reach it';
    } else {
        $best = null; $bestDist = PHP_INT_MAX;
        foreach (array_keys($nameIndex) as $candidate) {
            $d = levenshtein(strtolower(trim($bare)), $candidate);
            if ($d < $bestDist || ($d === $bestDist && $best !== null && strcmp($candidate, $best) < 0)) {
                $bestDist = $d;
                $best     = $candidate;
            }
        }
        if ($best !== null && $bestDist <= 3) {
            $locs = array_keys($nameIndex[$best]);
            $hint = 'probable typo for "' . $best . '" (at location ' . implode(',', $locs) . ')';
        }
    }

    return array('no system named "' . $name . '" on location ' . $location
               . ' - every hit here is silently rerouted to Structure', $hint);
}

// --------------------------------------------------------------- ship helpers

function collectSystemIds($system, &$ids, $path)
{
    if (isset($system->id)) {
        $ids[] = array('id' => $system->id, 'path' => $path);
    }
    /* The two sub-weapon containers that get their own ids: `weapons` on
       duo/dual weapons and `missileArray` on missile racks. Both assign
       1000+($id*10)+$i, which is unique only while a parent holds fewer than
       10 of them - past that it walks straight into the next parent's block.
       Measured 2026-08-18: NO ship blueprint currently reaches this (the one
       DualWeapon subclass is commented out and FighterMissileRack lives
       inside flights), so this arm is a guard against the day one does, not
       live coverage. Do not read a green `ids` check as proof that sub-weapon
       ids were exercised. */
    foreach (array('weapons', 'missileArray') as $bag) {
        if (!isset($system->$bag) || !is_array($system->$bag)) continue;
        foreach ($system->$bag as $i => $sub) {
            if (!is_object($sub)) continue;
            collectSystemIds($sub, $ids, $path . '/' . get_class($sub) . '#' . $i);
        }
    }
}

/* Declared craft in BOXES vs available hangar boxes, arithmetic lifted from
   HangarOps::populateInitialHangarUsage. Catapults, fighter rails and LCV
   rails are partitioned out on both sides (their boxes are structural HP or a
   single dedicated slot, not shuttle-pool capacity), as are the categories
   that ride them. Returns array(declared, capacity), or null when the ship has
   no ordinary hangar at all. */
function hangarBudget($ship)
{
    if (!class_exists('HangarOps')) return null;

    $hangars = HangarOps::collectHangars($ship);
    if (empty($hangars)) return null;

    $hasCatapult = false;
    $pool        = array();
    foreach ($hangars as $h) {
        if (!empty($h->isCatapult))     { $hasCatapult = true; continue; }
        if (!empty($h->isRail))         continue;
        if (!empty($h->isLCVRail))      continue;
        if (!empty($h->isShadowHangar)) continue;
        $pool[] = $h;
    }
    if (empty($pool)) return null;

    $railCategories = HangarOps::railFighterCategories($ship);

    $declared = 0;
    if (isset($ship->fighters) && is_array($ship->fighters)) {
        foreach ($ship->fighters as $category => $count) {
            $key = strtolower(trim((string)$category));
            if ($hasCatapult && $key === 'superheavy') continue;
            if (isset($railCategories[$key]))          continue;
            if ($key === 'lcvs')                       continue;
            $declared += HangarOps::shuttlePoolBoxesFor($category, (int)$count);
        }
    }

    $capacity = 0;
    foreach ($pool as $h) $capacity += (int)$h->maxhealth;

    return array($declared, $capacity);
}

// ------------------------------------------------------------------ main scan

$shipClassNames = array();   // $shipClass => first phpclass declaring it
$variantRefs    = array();   // phpclass => array(variantOf, where)
$assetRefs      = array();   // asset path => array(problem, referencing systems)

$subdirs = scandir($SHIPS_DIR);
sort($subdirs);

foreach ($subdirs as $subdir) {
    if ($subdir === '.' || $subdir === '..') continue;
    $subPath = $SHIPS_DIR . $subdir;
    if (!is_dir($subPath)) continue;

    $files = scandir($subPath);
    sort($files);

    foreach ($files as $file) {
        if (substr($file, -4) !== '.php') continue;

        $className = substr($file, 0, -4);
        $stats['files']++;

        /* Mirrors ShipLoader: a class is a ship only when filename ==
           classname and the autoloader can find it. Everything else in these
           directories is a helper or a multi-class file.
           The output buffer is here because several ship files carry stray
           bytes after their closing `?>`, which the autoloader's include then
           echoes straight into our report. */
        ob_start();
        $isShip = class_exists($className) && is_subclass_of($className, 'BaseShip');
        ob_end_clean();

        if (!$isShip) { $stats['skipped']++; continue; }
        if ($opts['ship'] !== null && stripos($className, $opts['ship']) === false) continue;

        $relFile = 'source/server/model/ships/' . $subdir . '/' . $file;

        ob_start();
        try {
            $ship = new $className(0, 0, '', 0, 0, false, false, array());
        } catch (\Throwable $e) {
            ob_end_clean();
            finding('construct', $className, $relFile,
                'ship construction failed: ' . $e->getMessage());
            continue;
        }
        ob_end_clean();

        if ($opts['faction'] !== null
            && stripos((string)$ship->faction, $opts['faction']) === false) continue;

        $stats['ships']++;
        $label = $className;

        $shipClass = trim((string)$ship->shipClass);
        if ($shipClass !== '' && !isset($shipClassNames[$shipClass])) {
            $shipClassNames[$shipClass] = $className;
        }
        $variantRefs[$className] = array(
            'variantOf' => trim((string)$ship->variantOf),
            'where'     => $relFile,
        );

        $systems = (isset($ship->systems) && is_array($ship->systems)) ? $ship->systems : array();
        $stats['systems'] += count($systems);

        // ----- ids: positional and unique
        if (isset($active['ids'])) {
            $ids = array();
            foreach ($systems as $index => $system) {
                if (!is_object($system)) {
                    finding('ids', $label, $relFile, "systems[$index] is not an object");
                    continue;
                }
                if (isset($system->id) && (int)$system->id !== (int)$index) {
                    finding('ids', $label, get_class($system) . " at systems[$index]",
                        'id ' . var_export($system->id, true) . ' != its array index ' . $index
                        . ' - persisted thrust/fire/power/crit rows are keyed by this id');
                }
                collectSystemIds($system, $ids, get_class($system) . '[' . $index . ']');
            }
            $byId = array();
            foreach ($ids as $rec) $byId[(string)$rec['id']][] = $rec['path'];
            foreach ($byId as $id => $paths) {
                if (count($paths) > 1) {
                    finding('ids', $label, 'id ' . $id,
                        'collides across ' . count($paths) . ' systems'
                        . ' - getSystemById returns whichever comes first',
                        implode(', ', $paths));
                }
            }
        }

        // ----- arcs
        if (isset($active['arcs'])) {
            foreach ($systems as $index => $system) {
                if (!is_object($system)) continue;
                foreach (array('startArc', 'endArc') as $prop) {
                    if (!isset($system->$prop)) continue;   // null = "no arc", legitimate
                    $v = $system->$prop;
                    if (!is_int($v) && !is_float($v) && !(is_string($v) && is_numeric($v))) {
                        finding('arcs', $label, get_class($system) . "[$index]",
                            "$prop is not numeric: " . var_export($v, true));
                        continue;
                    }
                    $v = (float)$v;
                    if ($v < 0 || $v > 360) {
                        finding('arcs', $label, get_class($system) . "[$index]",
                            "$prop = $v is outside 0..360");
                    }
                }
            }
        }

        // ----- system icons
        if (isset($active['sysimage'])) {
            foreach ($systems as $index => $system) {
                if (!is_object($system)) continue;

                /* Two different conventions, both live:
                   - a ShipSystem's $iconPath is ALWAYS resolved under
                     img/systemicons/ (SystemIcon.js getBackgroundImage,
                     FiringModeSelector.js, SelfRepair.getCurrSystemIcon) even
                     when it contains directory separators;
                   - a Fighter's $iconPath / $imagePath are full web-root paths
                     (FlightIcon.js, FighterIcon.js).
                   A non-Fighter system's $imagePath has no client consumer, so
                   it is deliberately not checked. */
                $isFighter = ($system instanceof Fighter);
                $props     = $isFighter ? array('imagePath', 'iconPath') : array('iconPath');

                foreach ($props as $prop) {
                    if (!isset($system->$prop)) continue;
                    $val = trim((string)$system->$prop);
                    if ($val === '') continue;

                    $rel = $isFighter ? $val : 'img/systemicons/' . $val;
                    if (!isset($assetRefs[$rel])) {
                        $assetRefs[$rel] = array(
                            'problem' => resolveAssetPath($WEB_ROOT, $rel),
                            'refs'    => array(),
                        );
                    }
                    if ($assetRefs[$rel]['problem'] !== '' && count($assetRefs[$rel]['refs']) < 4) {
                        $assetRefs[$rel]['refs'][$label . ' ' . get_class($system)] = true;
                    }
                }
            }
        }

        // ----- ship image
        if (isset($active['image'])) {
            $img = trim((string)$ship->imagePath);
            if ($img === '') {
                finding('image', $label, $relFile, '$imagePath is empty');
            } else {
                $problem = resolveAssetPath($WEB_ROOT, $img);
                if ($problem !== '') {
                    finding('image', $label, $relFile, '"' . $img . '" - ' . $problem);
                }
            }
        }

        // ----- hangar budget
        if (isset($active['hangar'])) {
            try {
                $budget = hangarBudget($ship);
            } catch (\Throwable $e) {
                $budget = null;
            }
            if ($budget !== null) {
                list($declared, $capacity) = $budget;
                if ($declared > $capacity) {
                    finding('hangar', $label, $relFile,
                        "\$fighters declares $declared boxes but the hangar bays hold $capacity"
                        . ' - populateInitialHangarUsage silently drops the excess');
                }
            }
        }

        // ----- hit chart
        $hitChart = (isset($ship->hitChart) && is_array($ship->hitChart)) ? $ship->hitChart : array();

        if (!empty($hitChart)) {
            // displayName / hitChartName -> the locations that carry it
            $nameIndex = array();
            foreach ($systems as $system) {
                if (!is_object($system)) continue;
                foreach (array('displayName', 'hitChartName') as $prop) {
                    if (!isset($system->$prop)) continue;
                    $n = (string)$system->$prop;
                    if (trim($n) === '') continue;
                    $nameIndex[strtolower($n)][(int)$system->location] = true;
                }
            }

            $hullLocations = array(0 => true);
            try {
                foreach ($ship->getLocations() as $line) {
                    if (isset($line['loc'])) $hullLocations[(int)$line['loc']] = true;
                }
            } catch (\Throwable $e) {
                // hull with an unusual getLocations - fall back to "PRIMARY only"
            }

            foreach ($hitChart as $location => $chart) {
                $stats['charts']++;

                if (!is_array($chart)) {
                    finding('chart', $label, "location $location",
                        'chart is not an array: ' . var_export($chart, true));
                    continue;
                }

                if (isset($active['chart'])) {
                    if (!isset($hullLocations[(int)$location])) {
                        finding('chart', $label, "location $location",
                            'no such section on this hull - the chart is dead data',
                            'getLocations gives ' . implode(',', array_keys($hullLocations)));
                    }

                    $maxKey = null;
                    foreach ($chart as $roll => $name) {
                        if (!is_int($roll) && !(is_string($roll) && ctype_digit($roll))) {
                            finding('chart', $label, "location $location",
                                'roll key is not an integer: ' . var_export($roll, true));
                            continue;
                        }
                        $roll = (int)$roll;
                        if ($roll < 1 || $roll > 20) {
                            finding('chart', $label, "location $location",
                                "roll key $roll is outside 1..20");
                        }
                        if ($maxKey === null || $roll > $maxKey) $maxKey = $roll;
                    }
                    if ($maxKey !== null && $maxKey !== 20) {
                        finding('chart', $label, "location $location",
                            "chart terminates at $maxKey, not 20 - rolls " . ($maxKey + 1)
                            . "..20 fall through to Structure");
                    }
                }

                if (isset($active['hitchart'])) {
                    foreach ($chart as $roll => $name) {
                        $stats['entries']++;
                        try {
                            list($problem, $hint) = chartEntryProblem($ship, $nameIndex, $name, $location);
                        } catch (\Throwable $e) {
                            $problem = 'lookup threw: ' . $e->getMessage();
                            $hint    = '';
                        }
                        if ($problem !== '') {
                            finding('hitchart', $label, "location $location, roll $roll", $problem, $hint);
                        }
                    }
                }
            }

            if (isset($active['chart'])) {
                foreach (array_keys($hullLocations) as $loc) {
                    if (!isset($hitChart[$loc])) {
                        finding('chart', $label, "location $loc",
                            'hull section has NO hit chart - every hit there becomes a Structure hit');
                    }
                }
            }
        }

        // ----- duplicate roll keys (source-level)
        if (isset($active['dupkeys'])) {
            foreach (findDuplicateChartKeys($subPath . '/' . $file) as $dup) {
                finding('dupkeys', $label, $relFile . ':' . $dup['line'],
                    'roll key ' . $dup['key'] . ' is repeated (first written on line '
                    . $dup['first'] . ') - PHP keeps only the last, so the earlier entry is lost');
            }
        }

        unset($ship);
    }
}

// ----- system icons, once per distinct asset path
if (isset($active['sysimage'])) {
    ksort($assetRefs);
    foreach ($assetRefs as $path => $rec) {
        if ($rec['problem'] === '') continue;
        $refs = array_keys($rec['refs']);
        sort($refs);
        finding('sysimage', '(assets)', $path, $rec['problem'],
            'referenced by ' . implode(', ', $refs) . (count($refs) >= 4 ? ', ...' : ''));
    }
}

// ----- variantOf resolution (needs the full shipClass index, so it runs last)
if (isset($active['variantof'])) {
    ksort($variantRefs);
    foreach ($variantRefs as $phpclass => $rec) {
        $v = $rec['variantOf'];
        if ($v === '') continue;                                              // base design
        if (in_array(strtoupper($v), RETIREMENT_SENTINELS, true)) continue;   // deliberately retired
        if (isset($shipClassNames[$v])) continue;                             // nests under a real hull
        finding('variantof', $phpclass, $rec['where'],
            '$variantOf = "' . $v . '" matches no ship\'s $shipClass - the lobby shows it neither as'
            . ' a base design nor as a variant, so players never see it');
    }
}

// ------------------------------------------------------------------ baseline

$nl = PHP_SAPI === 'cli' ? "\n" : "<br>\n";

// Stable order for both the report and the baseline file.
usort($findings, function ($a, $b) { return strcmp($a['key'], $b['key']); });

if ($opts['record']) {
    $dir = dirname($BASELINE_FILE);
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) {
        fwrite(STDERR, "cannot create $dir\n");
        exit(2);
    }
    $lines = array(
        '# Ship-data validator baseline - the ACCEPTED findings for this tree.',
        '# Regenerate with:  php checkShipData.php --record',
        '# A normal run fails only on findings that are NOT listed here, so this file',
        '# is the record of known ship-data debt. Adding a line to it in a PR means',
        '# that PR added a known-broken ship entry - review it, do not rubber-stamp it.',
        '# Format:  check<TAB>ship<TAB>where<TAB>message',
    );
    foreach ($findings as $f) $lines[] = $f['key'];
    file_put_contents($BASELINE_FILE, implode("\n", $lines) . "\n");

    echo $nl . 'Recorded ' . count($findings) . ' finding(s) to '
       . str_replace(__DIR__ . DIRECTORY_SEPARATOR, '', $BASELINE_FILE) . $nl;
    exit(0);
}

$baseline = array();
$haveBaselineFile = false;
if ($opts['baseline'] && is_readable($BASELINE_FILE)) {
    $haveBaselineFile = true;
    foreach (file($BASELINE_FILE, FILE_IGNORE_NEW_LINES) as $line) {
        if ($line === '' || $line[0] === '#') continue;
        $baseline[$line] = true;
    }
}

$isNew = array();
foreach ($findings as $f) {
    if ($opts['baseline'] && isset($baseline[$f['key']])) continue;
    $isNew[] = $f;
}

/* Baseline entries no longer produced. Only meaningful when the run covered
   the whole tree - a --ship / --faction filter makes everything else look
   "fixed", and a --checks filter does the same for the checks it dropped. */
$fixed = array();
if ($opts['baseline'] && $haveBaselineFile && $opts['ship'] === null && $opts['faction'] === null) {
    $seen = array();
    foreach ($findings as $f) $seen[$f['key']] = true;
    foreach (array_keys($baseline) as $key) {
        $check = substr($key, 0, strpos($key, "\t"));
        if (!isset($active[$check])) continue;      // that check did not run
        if (isset($seen[$key])) continue;
        $fixed[] = $key;
    }
    sort($fixed);
}

// ---------------------------------------------------------------- reporting

$newErrors = 0;
$newWarns  = 0;
foreach ($isNew as $f) {
    if ($CHECK_SEVERITY[$f['check']] === 'ERROR') $newErrors++;
    else $newWarns++;
}

echo $nl . 'Ship-data validator' . $nl;
echo str_repeat('=', 72) . $nl;
echo 'Checks run:            ' . implode(', ', array_keys($active)) . $nl;
echo "Ship files seen:       {$stats['files']}" . $nl;
echo "Skipped (not a ship):  {$stats['skipped']}" . $nl;
echo "Ships instantiated:    {$stats['ships']}" . $nl;
echo "Systems inspected:     {$stats['systems']}" . $nl;
echo "Hit charts:            {$stats['charts']} ({$stats['entries']} entries)" . $nl;
echo 'Findings total:        ' . count($findings) . $nl;
if ($opts['baseline']) {
    echo 'In baseline (accepted):' . (count($findings) - count($isNew)) . $nl;
    if (!$haveBaselineFile) {
        echo 'NOTE: no baseline file yet - every finding below counts as new.' . $nl;
        echo '      Review them, then run with --record to accept the current state.' . $nl;
    }
}
echo 'New errors:            ' . $newErrors . $nl;
echo 'New warnings:          ' . $newWarns . $nl;
if (!empty($fixed)) echo 'Fixed since baseline:  ' . count($fixed) . $nl;
echo str_repeat('=', 72) . $nl;

$byCheck = array();
foreach ($isNew as $f) $byCheck[$f['check']][] = $f;

foreach (array_keys($CHECK_SEVERITY) as $check) {
    if (empty($byCheck[$check])) continue;
    $sev  = $CHECK_SEVERITY[$check];
    $list = $byCheck[$check];
    echo $nl . "[$sev] $check - " . count($list) . ' finding(s)' . $nl;
    echo str_repeat('-', 72) . $nl;
    $shown = 0;
    foreach ($list as $f) {
        if ($opts['max'] > 0 && $shown >= $opts['max']) {
            echo '  ... and ' . (count($list) - $shown) . ' more (raise with --max=0)' . $nl;
            break;
        }
        echo "  {$f['ship']} :: {$f['where']}" . $nl;
        echo "      {$f['message']}" . $nl;
        if ($f['detail'] !== '') echo "      -> {$f['detail']}" . $nl;
        $shown++;
    }
}

if (!empty($fixed)) {
    echo $nl . 'FIXED since the baseline was recorded - ' . count($fixed) . ' finding(s)' . $nl;
    echo str_repeat('-', 72) . $nl;
    $shown = 0;
    foreach ($fixed as $key) {
        if ($opts['max'] > 0 && $shown >= $opts['max']) {
            echo '  ... and ' . (count($fixed) - $shown) . ' more' . $nl;
            break;
        }
        $cols = explode("\t", $key);
        echo '  [' . $cols[0] . '] ' . $cols[1] . ' :: ' . (isset($cols[2]) ? $cols[2] : '') . $nl;
        $shown++;
    }
    echo $nl . 'Run with --record to fold these into the baseline.' . $nl;
}

echo $nl;
if ($newErrors > 0) {
    echo "FAIL - $newErrors new error(s) not in the baseline." . $nl;
    exit(1);
}
if ($opts['strict'] && $newWarns > 0) {
    echo "FAIL (--strict) - $newWarns new warning(s)." . $nl;
    exit(1);
}
echo 'PASS' . ($newWarns > 0 ? " - $newWarns new warning(s), no new errors." : ' - no new findings.') . $nl;
exit(0);
