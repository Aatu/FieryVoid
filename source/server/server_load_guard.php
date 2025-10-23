<?php
// server_load_guard.php

// ---- 1️⃣ SKIP LIGHTWEIGHT REQUESTS by adding them here.
$ignoredScripts = [];

$currentScript = basename($_SERVER['SCRIPT_NAME']);
if (in_array($currentScript, $ignoredScripts)) {
    return;
}

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
?>