<?php
/**
 * APCu Load Guard (Robust & Quiet Edition)
 */

if (!function_exists('apcu_fetch')) {
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
    apcu_add($keyGlobal, 0, $ttlGlobal);
    do {
        $count = apcu_fetch($keyGlobal);
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