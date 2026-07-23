<?php

$database_host = 'mariadb';
$database_name = 'B5CGM';
$database_user = 'root';
$database_password = 'fieryvoid';

$secret_phrase='molecular pulsar';

// Discord turn notifications — LOCAL docker config. This file is the one the
// running container actually uses (source/server/varconfig.php is symlinked to
// it by docker/php/Dockerfile). Keep secrets OUT of this committed file: put the
// real bot token in docker/php/varconfig.local.php (gitignored), which overrides
// the empty defaults below. Both empty = feature disabled.
$discord_bot_token = '';
$discord_webhook_url = '';
$game_base_url = 'http://localhost/';
$discord_notify_idle_secs = 300;

// Local, uncommitted overrides (bot token etc.) — gitignored, never committed.
$__localVarconfig = __DIR__ . '/varconfig.local.php';
if (is_file($__localVarconfig)) include $__localVarconfig;
