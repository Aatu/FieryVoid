<?php
/**
 * Ultimate Load Guard Diagnostic Tool (Spy-Vision Version)
 */

require_once 'global.php';

// Construct keys identical to server_load_guard.php
$_slg_base = dirname(__DIR__, 2); 
$_slg_prefix = 'fv_' . substr(md5($_slg_base), 0, 8) . '_';
$ip = $_SERVER['REMOTE_ADDR'];
$ipHash = md5($ip);
$keyIP = $_slg_prefix . 'server_ip_' . $ipHash;
$keySpy = $_slg_prefix . 'server_spy_' . $ipHash;
$keyGlobal = $_slg_prefix . 'server_active_requests';

header('Content-Type: text/html');

if (isset($_GET['clear'])) {
    apcu_delete($keyIP);
    apcu_delete($keySpy);
    header("Location: guard_debug.php");
    exit;
}

$cacheTest = "Failed";
if (function_exists('apcu_store')) {
    apcu_store('slg_test_key', 'OK', 10);
    $cacheTest = apcu_fetch('slg_test_key') === 'OK' ? "Working" : "Writing but not reading";
} else {
    $cacheTest = "Extension Missing";
}

$ipCount = apcu_fetch($keyIP);
$lastScript = apcu_fetch($keySpy);
$globalCount = apcu_fetch($keyGlobal);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Load Guard Spy-Vision</title>
    <style>
        body { font-family: monospace; background: #0a161c; color: #eee; padding: 20px; }
        .stat { margin-bottom: 10px; font-size: 16px; }
        .label { color: #888; display: inline-block; width: 250px; }
        .info { color: #4a90e2; font-weight: bold; }
        .success { color: #2ecc71; }
        .fail { color: #e74c3c; }
        button { padding: 8px 15px; cursor: pointer; border: none; border-radius: 4px; font-weight: bold; margin-right: 10px; }
        .btn-refresh { background: #3498db; color: white; }
        .btn-reset { background: #e74c3c; color: white; }
    </style>
</head>
<body>
    <h1>Load Guard Spy-Vision</h1>
    <hr>
    
    <div class="stat"><span class="label">APCu Status:</span> <span class="<?php echo $cacheTest == 'Working' ? 'success' : 'fail'; ?>"><?php echo $cacheTest; ?></span></div>
    <div class="stat"><span class="label">Instance Prefix:</span> <span class="info"><?php echo $_slg_prefix; ?></span></div>
    <div class="stat"><span class="label">Your IP:</span> <span class="info"><?php echo $ip; ?></span></div>
    
    <hr>
    
    <div class="stat"><span class="label">Your ACTIVE Requests:</span> <span class="info"><?php echo ($ipCount === false) ? '0 (Key Missing)' : $ipCount; ?></span></div>
    <div class="stat"><span class="label">Global Instance Load:</span> <span class="info"><?php echo ($globalCount === false) ? '0' : $globalCount; ?></span></div>
    <div class="stat" style="background:#1c313a; padding:10px; border-radius:4px; border-left:4px solid #4a90e2;">
        <span class="label">LAST SCRIPT CAUGHT:</span> <span class="success"><?php echo ($lastScript === false) ? 'None yet (Waiting for hit...)' : $lastScript; ?></span>
    </div>

    <hr>

    <p><b>Diagnostic Procedure:</b></p>
    <ol>
        <li>Keep this tab open.</li>
        <li>Open <b><code>https://fieryvoid.eu/testInstance/source/public/hang.php</code></b> in a new tab.</li>
        <li>Come back here and refresh.</li>
        <li>If 'LAST SCRIPT CAUGHT' still says 'None yet', then the Guard is not seeing <code>hang.php</code> at all!</li>
    </ol>

    <button class="btn-refresh" onclick="location.reload()">REFRESH VIEW</button>
    <button class="btn-reset" onclick="if(confirm('Force clear your IP quota?')) location.href='?clear=1'">FORCE RESET IP</button>
</body>
</html>
