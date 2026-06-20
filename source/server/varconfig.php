<?php
//original configuration
$database_name='B5CGM';
$database_user='aatu';
$database_password='Kiiski';
$database_host = 'localhost';
$secret_phrase='molecular pulsar';

// APCu debug logging toggle. When true, the gamedata cache paths error_log()
// their hits/misses/fast-poll exits/touches so you can watch APCu working
// (see Manager::apcuLog()). Leave FALSE normally — at true it logs on EVERY poll
// for EVERY active player and bloats the error log fast. Flip to true on the
// local Docker or test server only while actively investigating, then back.
// Validated working 2026-06-13 on the live test server (see arch_gamedata_polling_cache memory).
//if (!defined('FV_APCU_DEBUG')) define('FV_APCU_DEBUG', false);

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
