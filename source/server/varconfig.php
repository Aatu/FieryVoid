<?php
//original configuration
$database_name='B5CGM';
$database_user='aatu';
$database_password='Kiiski';
$database_host = 'localhost';
$secret_phrase='molecular pulsar';

// APCu debug logging — local only. Defined here (not in the live config blocks
// below) so it is automatically OFF on the public/test servers. When true, the
// gamedata cache paths error_log() their hits/misses/fast-poll exits so you can
// watch APCu working on the local Docker server. See Manager::apcuLog().
if (!defined('FV_APCU_DEBUG')) define('FV_APCU_DEBUG', true);

//Marcin - new public server (fieryvoid.eu/game/) configuration
/*
$database_name='u253336_b5cgm';
$database_user='u253336_admin';
$database_password='Kiiski!php8';
$database_host ='sql-005.webh.cloud';
$secret_phrase='molecular pulsar'; 
*/

//Marcin - new test server (fieryvoid.eu/testInstance/) configuration
/*
$database_name='u253336_b5cgm_test';
$database_user='u253336_admin';
$database_password='Kiiski!php8';
$database_host ='sql-005.webh.cloud';
$secret_phrase='molecular pulsar'; 
*/
?>
