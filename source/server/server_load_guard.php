<?php
/**
 * APCu Load Guard (Robust & Quiet Edition)
 */

// Two separate reasons to sit this one out, and both matter:
//
//  - APCu absent OR present-but-disabled. function_exists() alone is not enough. Once
//    the extension is installed, every apcu_* call still EXISTS while apc.enabled (or,
//    under CLI, apc.enable_cli) is off — it just returns false. The global limiter below
//    reads that false as "could not acquire a slot", spins its full second, and 503s.
//    A disabled cache must mean "no limiting", never "limit everything".
//  - CLI. This is a concurrency limiter for web requests; a console script is not one.
//    Checked independently of APCu so that flipping apc.enable_cli=1 to poke at the cache
//    cannot start rate-limiting the generators and one-off scripts.
//
// Both were live between 2026-08-16 and 2026-08-17: installing APCu in the container
// silently turned `php generateStaticShipFile.php` into a 1-second no-op that printed
// nothing and exited 0, so fvbuild.ps1 reported success while regenerating no statics.
if (PHP_SAPI === 'cli' || !function_exists('apcu_enabled') || !apcu_enabled()) {
    return;
}

// ----------------------
// 1. Path-Based Isolation
// ----------------------
$_slg_base = dirname(__DIR__, 2); 
$_slg_prefix = 'fv_' . substr(md5($_slg_base), 0, 8) . '_';

// ----------------------
// 2. Immediate Bypass (Assets)
// ----------------------
if (isset($_SERVER['REQUEST_URI']) && preg_match('/\.(webp|png|jpg|jpeg|gif|css|js|ico|auto|svg|woff2|woff|ttf)(\?.*)?$/i', $_SERVER['REQUEST_URI'])) {
    return;
}

// ----------------------
// 3. Configuration
// ----------------------
$maxGlobal = 23;      
$maxIP = 20;            
$ttlGlobal = 30;
$keyGlobal = $_slg_prefix . 'server_active_requests';
$ipHash = md5($_SERVER['REMOTE_ADDR'] ?? 'local');
$keyIP = $_slg_prefix . 'server_ip_' . $ipHash;
$keySpy = $_slg_prefix . 'server_spy_' . $ipHash; 

// ----------------------
// 4. Poll Detection
// ----------------------
$isKnownPoll = false;
$script = $_SERVER['PHP_SELF'] ?? '';

$knownScripts = ['chatdata.php', 'gamedata.php', 'gamelobbyloader.php', 'allgames.php', 'games.php', 'guard_debug.php'];
foreach ($knownScripts as $ks) {
    if (strpos($script, $ks) !== false) {
        $isKnownPoll = true;
        break;
    }
}

// Special case for Lobby 
if (!$isKnownPoll && strpos($script, 'gamelobby.php') !== false) {
    $isKnownPoll = true;
}

// ----------------------
// 4b. Poll Instrumentation (DIAGNOSTIC — measures only, limits nothing)
// ----------------------
// Groundwork for CHAT_DB_RESILIENCE_PLAN item 6, whose own warning is that the cap must
// come from a measurement rather than a guess. Placed HERE, above the limiter, so the
// duration it records covers the whole request; placed inside this file because this is the
// earliest code that runs on every web request and already knows the script name.
//
// Costs nothing on requests it does not care about — begin() returns immediately unless the
// script is gamedata.php or gamelobbyloader.php.
//
// KILL SWITCH: create the file source/logs/pollstats.off and instrumentation stops dead.
// A marker file rather than a varconfig flag on purpose — global.php requires THIS file at
// line 36 and varconfig.php at line 37, so no varconfig setting exists yet when this runs.
// It is also the better switch for a live shared host: stopping a week-long diagnostic
// becomes an FTP upload rather than a code edit and a deploy.
$_pi_dir = dirname(__DIR__) . '/logs';
if (!is_file($_pi_dir . '/pollstats.off')) {
    $_pi_file = __DIR__ . '/lib/PollInstrument.php';
    if (is_file($_pi_file)) {
        require_once $_pi_file;
        PollInstrument::begin($script);
    }
    unset($_pi_file);
}
unset($_pi_dir);

// ----------------------
// 5. Limit Enforcement
// ----------------------
$ipAcquired = false;
$globalAcquired = false;
$start = microtime(true);

register_shutdown_function(function() use (&$globalAcquired, $keyGlobal, &$ipAcquired, $keyIP) {
    if ($globalAcquired) {
        $val = apcu_fetch($keyGlobal);
        if ($val !== false && $val > 0) apcu_dec($keyGlobal);
    }
    if ($ipAcquired) {
        $i = apcu_fetch($keyIP);
        if ($i !== false && $i > 0) {
            $new = apcu_dec($keyIP);
            if ($new <= 0) apcu_delete($keyIP);
        }
    }
});

// Increment IP counter for non-exempt scripts
if (!$isKnownPoll) {
    $ipCount = apcu_inc($keyIP, 1, $exists);
    $ipAcquired = true;
    apcu_store($keyIP, $ipCount, 20); 
    apcu_store($keySpy, $script . ' (at ' . date('H:i:s') . ')', 60);

    if ($ipCount > $maxIP) {
        header("HTTP/1.1 503 Service Unavailable");
        exit;
    }
}

// Global limiter (Non-Fast-Polls)
if (!$isKnownPoll) {
    do {
        // ⚠️ The apcu_add MUST be inside the loop. It used to sit above it, and that was a
        // bug that could only ever fire while the limiter was actually engaged:
        //
        //   - apcu_cas() FAILS on a key that does not exist (verified against real APCu,
        //     not read from the docs — it returns false and does not create the key).
        //   - $keyGlobal carries a $ttlGlobal-second TTL, and neither apcu_add on an
        //     existing key nor apcu_cas refreshes it, so the key expires $ttlGlobal
        //     seconds after CREATION regardless of traffic (also verified).
        //
        // With the add above the loop, nothing inside it could recreate a key that expired
        // mid-spin. Every subsequent apcu_fetch returned false, every apcu_cas failed, and
        // the request burned its full 1-second budget before being handed a 503 — while
        // the counter it was queueing for was, in truth, empty.
        //
        // Exposure was roughly 1-in-30 (a 1s spin against a 30s TTL) and ONLY for requests
        // already waiting in the queue, i.e. exactly when shedding the wrong request costs
        // the most. Re-adding here means a vanished key is rebuilt at 0 and the very same
        // iteration acquires cleanly.
        //
        // This is a no-op on the overwhelmingly common path: apcu_add against a key that
        // exists returns false and changes nothing, including the TTL. It therefore does
        // NOT alter the 30s counter reset, which is a separate (and largely benign)
        // property -- see CHAT_DB_RESILIENCE_PLAN item 9, problem 3.
        apcu_add($keyGlobal, 0, $ttlGlobal);

        $count = apcu_fetch($keyGlobal);
        // $count can still be false if the key expires in the microseconds between the add
        // and the fetch. apcu_cas then fails, and the next iteration's add rebuilds it --
        // so the worst case is one 50ms sleep, not the old full-budget spin.
        if ($count === false || $count < $maxGlobal) {
            if (apcu_cas($keyGlobal, (int)$count, (int)$count + 1)) {
                $globalAcquired = true;
                break;
            }
        }
        usleep(50000);
    } while ((microtime(true) - $start) < 1.0);

    if (!$globalAcquired) {
        header("HTTP/1.1 503 Service Unavailable");
        exit;
    }
}