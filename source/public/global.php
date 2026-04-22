<?php
// Universal Compression Hook
include_once __DIR__ . '/compression_helper.php';
if (PHP_SAPI !== 'cli' && !headers_sent()) {
    ob_start();
    register_shutdown_function('fv_compress_output');
}

ini_set('display_errors',1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . '/autoload.php';
require_once dirname(__DIR__) . '/server/lib/AssetLoader.php';

set_exception_handler(function ($e) {
    Debug::error($e);
    if (ini_get('display_errors')) {
        echo "<h1>Fatal Error</h1>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "<pre>" . $e->getTraceAsString() . "</pre>";
    }
});

set_error_handler(function ($errno, $errstr, $file, $line) {
    if (!(error_reporting() & $errno)) {
        return;
    }
    throw new ErrorException($errstr, $errno, 0, $file, $line);
});



// Prevent PHP from sending legacy 1981 headers if a session starts
session_cache_limiter(''); 
session_start();

require_once dirname(__DIR__) . '/server/server_load_guard.php'; // <--- add this line
require_once dirname(__DIR__) . '/server/varconfig.php';
?>
