<?php
/**
 * Hangar spawnable-class sanity check.
 *
 * Walks every ship under source/server/model/ships/, instantiates each one,
 * and inspects every system's $spawnableClasses array. For each listed
 * phpclass it verifies:
 *
 *   1. The class is autoloadable (i.e. listed in source/autoload.php) — the
 *      live `HangarOps::performLaunch` path calls `new $phpclass(...)` and
 *      a missing autoload entry produces a runtime fatal at the carrier's
 *      first launch.
 *   2. A static blueprint can be produced via `ShipLoader::getShipsByClass`
 *      — this is the same call `game.php` uses at page load to preload
 *      `window.staticShips`, so a class that can't be built here would land
 *      a launched flight on a client with no blueprint to render it from.
 *   3. The 4-arg runtime launch constructor `new $cls($id, $userid, $name, $slot)`
 *      succeeds — mirrors the exact call inside `HangarOps::performLaunch`.
 *
 * Usage (from repo root, inside the php container):
 *
 *     docker-compose exec php php /usr/src/current/checkHangarSpawnables.php
 *
 * Exits 0 on success, 1 if any violations are reported. Designed to be
 * wired into CI later (or run by hand after touching a hangar / FighterFlight).
 */

ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/source/autoload.php';

$shipsDir = __DIR__ . '/source/server/model/ships/';

$shipsScanned       = 0;
$shipsSkipped       = 0;
$systemsWithSpawn   = 0;
$spawnablesChecked  = 0;
$violations         = [];

function recordViolation(array &$violations, $ship, $system, $class, $reason)
{
    $violations[] = [
        'ship'   => $ship,
        'system' => $system,
        'class'  => $class,
        'reason' => $reason,
    ];
}

foreach (scandir($shipsDir) as $subdir) {
    if ($subdir === '.' || $subdir === '..') continue;
    $subPath = $shipsDir . $subdir;
    if (!is_dir($subPath)) continue;

    foreach (scandir($subPath) as $file) {
        if (substr($file, -4) !== '.php') continue;

        $className = substr($file, 0, -4);
        if (!class_exists($className)) {
            $shipsSkipped++;
            continue;
        }

        try {
            $ship = new $className(0, 0, '', 0, 0, false, false, []);
        } catch (\Throwable $e) {
            recordViolation(
                $violations,
                $className,
                '-',
                '-',
                'ship construction failed: ' . $e->getMessage()
            );
            continue;
        }

        if (!isset($ship->systems) || !is_array($ship->systems)) {
            continue;
        }

        $shipsScanned++;

        foreach ($ship->systems as $system) {
            $spawnables = isset($system->spawnableClasses) ? $system->spawnableClasses : null;
            if (!is_array($spawnables) || empty($spawnables)) continue;

            $systemsWithSpawn++;
            $sysLabel = !empty($system->displayName)
                ? $system->displayName
                : get_class($system);

            foreach ($spawnables as $spawnClass) {
                $spawnablesChecked++;

                if (!is_string($spawnClass) || $spawnClass === '') {
                    recordViolation(
                        $violations,
                        $className,
                        $sysLabel,
                        var_export($spawnClass, true),
                        'spawnableClasses entry is not a non-empty string'
                    );
                    continue;
                }

                if (!class_exists($spawnClass)) {
                    recordViolation(
                        $violations,
                        $className,
                        $sysLabel,
                        $spawnClass,
                        'class not autoloadable — add an entry to source/autoload.php'
                    );
                    continue;
                }

                // Mirror the runtime launch constructor used by
                // HangarOps::performLaunch.
                try {
                    new $spawnClass(0, 0, '', 0);
                } catch (\Throwable $e) {
                    recordViolation(
                        $violations,
                        $className,
                        $sysLabel,
                        $spawnClass,
                        'launch-time construction (id,userid,name,slot) failed: ' . $e->getMessage()
                    );
                    continue;
                }

                // Mirror the static-blueprint preload used by game.php at
                // page load — game.php aggregates spawnableClasses and
                // calls ShipLoader::getShipsByClass on them.
                try {
                    $blueprintMap = ShipLoader::getShipsByClass([$spawnClass]);
                } catch (\Throwable $e) {
                    recordViolation(
                        $violations,
                        $className,
                        $sysLabel,
                        $spawnClass,
                        'static blueprint generation failed: ' . $e->getMessage()
                    );
                    continue;
                }

                $found = false;
                foreach ($blueprintMap as $factionShips) {
                    if (isset($factionShips[$spawnClass])) {
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    recordViolation(
                        $violations,
                        $className,
                        $sysLabel,
                        $spawnClass,
                        'ShipLoader::getShipsByClass produced no blueprint — game.php cannot preload it'
                    );
                }
            }
        }
    }
}

$nl = PHP_SAPI === 'cli' ? "\n" : "<br>\n";
echo $nl . "Hangar spawnable-class sanity check" . $nl;
echo str_repeat('=', 50) . $nl;
echo "Ships scanned:             $shipsScanned" . $nl;
echo "Ships skipped (not autoloaded): $shipsSkipped" . $nl;
echo "Systems with spawnables:   $systemsWithSpawn" . $nl;
echo "Spawnable entries checked: $spawnablesChecked" . $nl;
echo "Violations:                " . count($violations) . $nl;
echo str_repeat('=', 50) . $nl;

if (empty($violations)) {
    echo "OK — every spawnableClasses entry is autoloadable and constructable." . $nl;
    exit(0);
}

echo $nl . "Violations:" . $nl;
foreach ($violations as $v) {
    echo "  - [{$v['ship']} :: {$v['system']}] {$v['class']}" . $nl;
    echo "      {$v['reason']}" . $nl;
}
exit(1);
