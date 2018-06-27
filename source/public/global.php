<?php
ini_set('display_errors',1);
error_reporting(E_ALL);
require_once dirname(__DIR__) . '/autoload.php';
ini_set('session.gc_maxlifetime', 20 * 24 * 3600);
session_set_cookie_params(20 * 24 * 3600);
session_start();
require_once dirname(__DIR__) . '/server/varconfig.php' ;
?>
