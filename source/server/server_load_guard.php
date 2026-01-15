<?php
// ----------------------
// APCu Load Guard (Robust Version)
// ----------------------
// Protects against server overload and rapid F5/AJAX spamming
// Requirements: APCu extension enabled

if (!function_exists('apcu_fetch')) {
    return; // APCu not available, skip guard
}

// ----------------------
// Configuration
// ----------------------
$maxGlobal = 23;       // max active requests globally (Matched to user's thread limit)
$maxIP = 6;            // max active requests per IP
$maxWait = 1.0;        // max seconds to wait for a global slot
$waitStep = 0.05 + (mt_rand(0, 50) / 1000.0); //Small stutter

//$ttlIP = 10;            // seconds for per-IP counter TTL
$ttlGlobal = 30;        // fallback TTL for global counter (Increased for safety)

$keyGlobal = 'server_active_requests';
$keyIP = 'server_ip_' . md5($_SERVER['REMOTE_ADDR']);

$start = microtime(true);

// ----------------------
// State Tracking (Crucial for Shutdown)
// ----------------------
$ipAcquired = false;
$globalAcquired = false;

// Register shutdown function IMMEDIATELY to handle exits/crashes
register_shutdown_function(function() use (&$globalAcquired, $keyGlobal, &$ipAcquired, $keyIP) {
    // Only decrement if WE actually incremented it
    if ($globalAcquired) {
        $val = apcu_fetch($keyGlobal);
        if ($val !== false && $val > 0) apcu_dec($keyGlobal);
    }
    
    // Only decrement IP if WE incremented it
    if ($ipAcquired) {
        $i = apcu_fetch($keyIP);
        if ($i !== false && $i > 0) {
            $new = apcu_dec($keyIP);
            // Cleanup empty IP keys
            if ($new <= 0) apcu_delete($keyIP);
        }
    }
});

// ----------------------
// Per-IP limiter
// ----------------------

// Increment first
$ipCount = apcu_inc($keyIP, 1, $exists);
$ipAcquired = true; // Mark as acquired so shutdown will decrement it later

if (!$exists) {
    // Key did not exist yet, set TTL
    apcu_store($keyIP, 1, 10);
}

// Check limit
if ($ipCount > $maxIP) {
    // Rely on shutdown function to decrement (since $ipAcquired is true)
    header("HTTP/1.1 503 Service Unavailable"); // Changed to 503 to signal retry
    header("Retry-After: 10");
    echo json_encode(['error' => 'Too many requests from your IP']);
    exit;
}

// ----------------------
// Fast Poll Exemption
// ----------------------
// If this is a polling request that will be served from APCu, we skip the global limit.
$isFastPoll = false;

if (isset($_SERVER['PHP_SELF'])) {
    if (strpos($_SERVER['PHP_SELF'], 'chatdata.php') !== false && isset($_GET['gameid'], $_GET['lastid'])) {
        $key = 'chat_last_id_' . $_GET['gameid'];
        $lastMsgId = apcu_fetch($key);
        if ($lastMsgId !== false && $_GET['lastid'] >= $lastMsgId) {
            $isFastPoll = true;
             // DEBUG LOG
             error_log("Load Guard: Fast Poll EXEMPT (Chat) - " . $_SERVER['REMOTE_ADDR']);
        }
    } elseif (strpos($_SERVER['PHP_SELF'], 'gamedata.php') !== false && isset($_GET['gameid'], $_GET['last_time'])) {
        $key = "game_" . $_GET['gameid'] . "_last_update";
        $serverTime = apcu_fetch($key);
        // Note: serverTime might be false if expired, in which case we don't fast poll
        if ($serverTime && $serverTime <= $_GET['last_time']) {
            $isFastPoll = true;
             // DEBUG LOG
             error_log("Load Guard: Fast Poll EXEMPT (Gamedata) - " . $_SERVER['REMOTE_ADDR']);
        }
    }
}

// ----------------------
// Global limiter (atomic CAS)
// ----------------------
// Ensure key exists (safe to call repeatedly)
apcu_add($keyGlobal, 0, $ttlGlobal);

// Only enforce global limit if NOT a fast poll
if (!$isFastPoll) {
    do {
        $count = apcu_fetch($keyGlobal);
        if ($count === false) $count = 0;

        if ($count < $maxGlobal) {
            // Atomic compare-and-swap
            if (apcu_cas($keyGlobal, $count, $count + 1)) {
                $globalAcquired = true; // SUCCESS! Shutdown will now handle this decrement.
                break;
            }
        }

        usleep((int)($waitStep * 2000000));
    } while ((microtime(true) - $start) < $maxWait);

    // If no slot acquired, reject
    if (!$globalAcquired) {
        header("HTTP/1.1 503 Service Unavailable");
        header("Retry-After: " . ceil($maxWait));
        echo json_encode(['error' => 'Server busy, please retry']);
        // $globalAcquired is false, so shutdown will NOT touch global count.
        exit;
    }
}

// ----------------------
// Success
// ----------------------
// Execution continues... 
// On script finish (or error), shutdown function runs and releases both slots.
?>