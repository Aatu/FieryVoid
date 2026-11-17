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

// Keep games whose players have been idle 3+ months (DBManager::getGamesToBeDeleted). Live
// deletes them; locally they are the replay harness corpus. Idle LOBBY games still go after 5 days.
$keep_idle_games = true;

// Feature flags (Features.php) - LOCAL docker turns part-built features ON; live leaves them out.
$feature_flags = array(
    'cpuOpponent' => true,   // CPU-controlled slots (TACTICAL_AI_PLAN.md)
    'autoDeploy' => true,    // the Deployment phase's Auto-Deploy button (TACTICAL_AI_PLAN.md 5.2)
);
$feature_beta_testers = array();

// Local, uncommitted overrides (bot token etc.) — gitignored, never committed.
$__localVarconfig = __DIR__ . '/varconfig.local.php';
if (is_file($__localVarconfig)) include $__localVarconfig;
