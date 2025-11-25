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
$maxGlobal = 20;       // max active requests globally
$maxIP = 8;            // max active requests per IP
$maxWait = 5.0;        // max seconds to wait for a global slot
$waitStep = 0.05 + (mt_rand(0, 50) / 1000.0); //Small stutter

$ttlIP = 10;            // seconds for per-IP counter TTL
$ttlGlobal = 5;         // fallback TTL for global counter

$keyGlobal = 'server_active_requests';
$keyIP = 'server_ip_' . md5($_SERVER['REMOTE_ADDR']);

$start = microtime(true);
$locked = false;

// ----------------------
// Per-IP limiter
// ----------------------
$ipCount = apcu_inc($keyIP, 1, $exists);
if (!$exists) {
    // Key did not exist yet, create with TTL
    apcu_store($keyIP, 1, $ttlIP);
}

// Reject if per-IP limit exceeded
if ($ipCount > $maxIP) {
    apcu_dec($keyIP);
    header("HTTP/1.1 429 Too Many Requests");
    header("Retry-After: $ttlIP");
    echo json_encode(['error' => 'Too many requests from your IP']);
    exit;
}

// ----------------------
// Global limiter (atomic)
// ----------------------
// Ensure key exists for CAS to function
apcu_add($keyGlobal, 0, $ttlGlobal);

// Global limiter (atomic)
do {
    $count = apcu_fetch($keyGlobal);
    if ($count === false) $count = 0;

    if ($count < $maxGlobal) {
        if (apcu_cas($keyGlobal, $count, $count + 1)) {
            $locked = true;
            break;
        }
    }

    usleep((int)($waitStep * 2000000));
} while ((microtime(true) - $start) < $maxWait);

// If no slot acquired, reject politely
if (!$locked) {
    header("HTTP/1.1 503 Service Unavailable");
    header("Retry-After: $maxWait");
    echo json_encode(['error' => 'Server busy, please retry']);
    apcu_dec($keyIP); // release per-IP slot
    exit;
}

// ----------------------
// Release slots on shutdown
// ----------------------
register_shutdown_function(function() use ($keyGlobal, $keyIP) {
    if (($g = apcu_fetch($keyGlobal)) !== false && $g > 0) apcu_dec($keyGlobal);
    if (($i = apcu_fetch($keyIP)) !== false && $i > 0) {
        $new = apcu_dec($keyIP);
        if ($new <= 0) apcu_delete($keyIP);
    }
});

/*
//Old server lock version, using server-side concurrency limiter, replaced with Redis method above 10.11.25 DK
// ---- 1️⃣ SKIP LIGHTWEIGHT REQUESTS by adding them here.
$ignoredScripts = [];

$currentScript = basename($_SERVER['SCRIPT_NAME']);
if (in_array($currentScript, $ignoredScripts)) {
    return;
}
/*
// per-IP limit - OPTIONAL
$lockDir = __DIR__ . '/locks';
if (!is_dir($lockDir)) mkdir($lockDir, 0755, true);

$ip = $_SERVER['REMOTE_ADDR'];
$ipMax = 10; // max concurrent requests per IP
$ipFile = $lockDir . "/ip_" . md5($ip) . ".cnt";

if (!file_exists($ipFile)) file_put_contents($ipFile, '0', LOCK_EX);

$fp = fopen($ipFile, 'c+');
if ($fp && flock($fp, LOCK_EX)) {
    $ipCount = (int)fread($fp, 10);
    if ($ipCount >= $ipMax) {
        flock($fp, LOCK_UN);
        fclose($fp);
        header("HTTP/1.1 429 Too Many Requests");
        echo json_encode(['error'=>'too many requests from your IP']);
        exit;
    }
    ftruncate($fp,0);
    rewind($fp);
    fwrite($fp, $ipCount + 1);
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    register_shutdown_function(function() use ($ipFile) {
        $fp = fopen($ipFile, 'c+');
        if ($fp && flock($fp, LOCK_EX)) {
            $c = (int)fread($fp, 10);
            $c = max(0, $c - 1);
            ftruncate($fp,0);
            rewind($fp);
            fwrite($fp, $c);
            fflush($fp);
            flock($fp, LOCK_UN);
        }
        if ($fp) fclose($fp);
    });
}//end of per IP limiting
*//*

// ---- 2️⃣ BASIC LOAD CONTROL ----
$lockFile = __DIR__ . '/server_load.lock';
$maxConcurrent = 23;        // adjust for your hosting limits, hosting limit is 25.
$waitInterval = 200000;     // 0.2s between retries
$maxWait = 5000000;         // wait up to 5s

$locked = false;
$start = microtime(true);

// create lock file if missing
if (!file_exists($lockFile)) {
    file_put_contents($lockFile, 0);
}

do {
    $fp = fopen($lockFile, 'c+');
    if ($fp && flock($fp, LOCK_EX)) {
        $active = (int)fread($fp, 10);
        if ($active < $maxConcurrent) {
            ftruncate($fp, 0);
            rewind($fp);
            fwrite($fp, $active + 1);
            fflush($fp);
            flock($fp, LOCK_UN);
            fclose($fp);
            $locked = true;
            break;
        }
        flock($fp, LOCK_UN);
        fclose($fp);
    }
    usleep($waitInterval);
    usleep(rand(0,50000)); // <-- add this random extra 0–0.05s    
} while ((microtime(true) - $start) < ($maxWait / 1000000.0));

if (!$locked) {
    header("HTTP/1.1 503 Service Unavailable");
    header("Content-Type: application/json");
    echo json_encode(['error' => 'Server busy, please retry']);
    exit;
}

// ---- 3️⃣ RELEASE SLOT ON EXIT ----
register_shutdown_function(function () use ($lockFile) {
    $fp = fopen($lockFile, 'c+');
    if ($fp && flock($fp, LOCK_EX)) {
        $active = (int)fread($fp, 10);
        $active = max(0, $active - 1);
        ftruncate($fp, 0);
        rewind($fp);
        fwrite($fp, $active);
        fflush($fp);
        flock($fp, LOCK_UN);
    }
    if ($fp) fclose($fp);
});
*/
?>