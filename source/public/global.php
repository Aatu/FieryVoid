<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . '/autoload.php';
session_start();
require_once dirname(__DIR__) . '/server/server_load_guard.php'; // <--- add this line
require_once dirname(__DIR__) . '/server/varconfig.php';
?>
