<?php

/**
 * Discord turn notifications (design: TURN_NOTIFICATIONS_PLAN.md).
 *
 * Records tac_playeringame.waiting flips in-memory during the request (hooked
 * into the two DBManager waiting-flag setters) and, strictly AFTER the DB
 * commit, DMs each player the game is now waiting on. Optional channel-webhook
 * fallback for players whose privacy settings block bot DMs (Discord error
 * 50007). Entirely disabled when both $discord_bot_token and
 * $discord_webhook_url (varconfig.php) are empty — local Docker, the replay
 * harness and testInstance stay inert by default.
 *
 * Failure isolation: sends happen only after commit, every HTTP call is
 * time-capped (1s connect / 1.5s total, ~5s budget per request), all errors
 * are swallowed and flush() is wrapped in try/catch — notifications must
 * NEVER break game flow.
 */
class DiscordNotifier
{

    private static $ops = array();        // gameid => list of [playeridOrALL, bool needsToAct]
    private static $suppressed = array(); // gameid => true; game ended mid-request, never ping
    private static $deadline = null;      // request-level time budget for HTTP calls

    public static function isEnabled()
    {
        global $discord_bot_token, $discord_webhook_url;
        return !empty($discord_bot_token) || !empty($discord_webhook_url);
    }

    // Called from DBManager::setPlayerWaitingStatus / setPlayersWaitingStatusInGame.
    // $playerid === 'ALL' means every player in the game. $waiting mirrors the DB
    // column: waiting = 0 <=> the game needs this player's input.
    public static function recordWaiting($gameid, $playerid, $waiting)
    {
        if (!self::isEnabled()) return;
        self::$ops[$gameid][] = array($playerid, !$waiting);
    }

    // The in-memory gamedata's status can lag a direct DB status write (e.g. the
    // surrender branch in submitTacGamedata) — callers flag such games explicitly.
    public static function suppressGame($gameid)
    {
        self::$suppressed[$gameid] = true;
    }

    // Called on transaction rollback paths — state that never committed must not ping.
    public static function clear()
    {
        self::$ops = array();
        self::$suppressed = array();
    }

    // Called AFTER endTransaction(false). $gamedata is the request's (post-process/
    // post-advance) in-memory TacGamedata; $triggeringUserid is the submitter or
    // the poller whose request performed the advance — they're online, never pinged.
    public static function flush($dbManager, $gamedata, $triggeringUserid)
    {
        if (empty(self::$ops) || !self::isEnabled()) return;
        if (!is_object($gamedata)) { self::clear(); return; }
        $ops = self::$ops;
        $suppressed = self::$suppressed;
        self::clear();
        self::$deadline = microtime(true) + 5.0;   // hard cap on total HTTP time per request
        try {
            foreach ($ops as $gameid => $gameOps) {
                // Turn/phase/name below describe $gamedata's game only; a request
                // never legitimately flips waiting flags for any other game.
                if ($gameid != $gamedata->id) continue;
                if (!empty($suppressed[$gameid])) continue;
                if ($gamedata->status === 'FINISHED' || $gamedata->status === 'SURRENDERED') continue;

                // playerid => row{discord_id, dm_channel_id} for everyone in the game.
                $players = $dbManager->getDiscordIdsInGame($gameid);

                // Replay ops in order -> final needs-to-act set (mirrors final DB state).
                $needs = array();
                foreach ($gameOps as $op) {
                    list($pid, $needsToAct) = $op;
                    if ($pid === 'ALL') {
                        foreach ($players as $id => $row) $needs[$id] = $needsToAct;
                    } else {
                        $needs[$pid] = $needsToAct;
                    }
                }

                $recipients = array();
                foreach ($needs as $pid => $needsToAct) {
                    if (!$needsToAct) continue;
                    if ($pid == $triggeringUserid) continue;                 // they're online right now
                    if (!isset($players[$pid]) || empty($players[$pid]->discord_id)) continue; // not opted in
                    if (self::seenRecently($gameid, $pid)) continue;         // actively polling
                    $recipients[$pid] = $players[$pid];
                }
                if (empty($recipients)) continue;

                // Dedupe guard — poller races are submit-locked already; belt & braces.
                // apcu_enabled() check matters: apcu_add also returns false when the
                // extension is loaded but disabled, which would suppress every ping.
                if (function_exists('apcu_enabled') && apcu_enabled()) {
                    global $database_name;
                    $key = ($database_name ?? 'default') . "_dnotif_{$gameid}_{$gamedata->turn}_{$gamedata->phase}_"
                         . md5(implode(',', array_keys($recipients)));
                    if (!apcu_add($key, 1, 600)) continue;
                }

                $dmText = self::buildMessage($gamedata, $gameid, false);
                $failedMentions = array();
                foreach ($recipients as $pid => $row) {
                    if (!self::sendDm($dbManager, $pid, $row, $dmText)) {
                        $failedMentions[] = '<@' . $row->discord_id . '>';
                    }
                }
                if (!empty($failedMentions)) {
                    self::postWebhook(implode(' ', $failedMentions) . ' '
                                    . self::buildMessage($gamedata, $gameid, true));
                }
            }
        } catch (Throwable $e) {
            Debug::error($e);   // notifications must NEVER break game flow
        }
        self::$deadline = null;
    }

    // True when the player's client polled this game within the idle window —
    // the stamp is written at the top of gamedata.php, above the fast-poll exit.
    private static function seenRecently($gameid, $playerid)
    {
        global $database_name, $discord_notify_idle_secs;
        if (!function_exists('apcu_fetch')) return false;   // no APCu -> just ping
        $window = !empty($discord_notify_idle_secs) ? $discord_notify_idle_secs : 300;
        $t = apcu_fetch(($database_name ?? 'default') . '_seen_' . (int)$gameid . '_' . (int)$playerid);
        return $t !== false && (time() - $t) < $window;
    }

    private static function buildMessage($gamedata, $gameid, $forChannel)
    {
        global $game_base_url;
        $phaseNames = array(-2 => 'Fleet selection', -1 => 'Deployment', 1 => 'Initial orders',
                             2 => 'Movement', 5 => 'Pre-firing', 3 => 'Firing');
        $phase = isset($phaseNames[$gamedata->phase]) ? $phaseNames[$gamedata->phase] : 'Turn';
        $verb = $forChannel ? 'the game is waiting on you' : "it's your turn";
        $page = ($gamedata->phase == -2) ? 'gamelobby.php' : 'game.php';
        $base = !empty($game_base_url) ? $game_base_url : 'https://fieryvoid.eu/game/';
        // <...> around the URL suppresses Discord's link-preview embed.
        return "⚔️ **{$gamedata->name}** — Turn {$gamedata->turn}, {$phase} — {$verb}.\n"
             . "<{$base}{$page}?gameid={$gameid}>";
    }

    // Two REST calls max: create-DM-channel (once per player, then cached in
    // player.dm_channel_id) + send. Returns false on any failure incl. Discord
    // error 50007 "Cannot send messages to this user" (DMs from server members
    // disabled — the main, silent failure mode).
    private static function sendDm($dbManager, $playerid, $row, $content)
    {
        global $discord_bot_token;
        if (empty($discord_bot_token)) return false;
        $channelId = $row->dm_channel_id;
        if (empty($channelId)) {
            $resp = self::api('users/@me/channels', array('recipient_id' => $row->discord_id));
            if (!$resp || empty($resp->id)) return false;
            $channelId = $resp->id;
            $dbManager->setPlayerDmChannelId($playerid, $channelId);   // cache: next ping is one call
        }
        return self::api("channels/{$channelId}/messages", array('content' => $content)) !== false;
    }

    // DM a verification code to an as-yet-unbound Discord ID. DM-ONLY: a code must
    // never fall back to a public channel, and the whole point is that only the
    // account that owns the ID can read it. No dm_channel_id caching (the ID isn't
    // bound yet). Returns true only if the DM was delivered.
    public static function sendVerificationCode($discordId, $code)
    {
        $msg = "🔐 Your Fiery Void verification code is: **{$code}**\n"
             . "Enter it on the Notifications page to link this Discord account to your "
             . "Fiery Void account. It expires in 10 minutes. If you didn't request this, "
             . "you can safely ignore this message.";
        return self::sendDmToId($discordId, $msg);
    }

    // One-shot DM to a raw Discord ID (create channel + send, no caching).
    // Returns false on any failure, incl. 50007 when the user's DMs are closed.
    private static function sendDmToId($discordId, $content)
    {
        global $discord_bot_token;
        if (empty($discord_bot_token) || empty($discordId)) return false;
        $resp = self::api('users/@me/channels', array('recipient_id' => $discordId));
        if (!$resp || empty($resp->id)) return false;
        return self::api("channels/{$resp->id}/messages", array('content' => $content)) !== false;
    }

    // Profile-page test. Returns 'dm', 'channel', or false so the page can tell
    // the player whether DMs work or their privacy settings block them.
    public static function sendTestPing($dbManager, $playerid, $row)
    {
        $msg = "✅ Test ping from Fiery Void — you will be DM'd here when it's your turn.";
        if (self::sendDm($dbManager, $playerid, $row, $msg)) return 'dm';
        if (self::postWebhook("<@{$row->discord_id}> " . $msg
            . " (Your DMs are closed — enable 'Allow direct messages from server members'"
            . " for the Fiery Void server to get DMs instead of channel pings.)")) return 'channel';
        return false;
    }

    // Authenticated bot REST call. Decoded object on 2xx, false otherwise.
    private static function api($route, $body)
    {
        global $discord_bot_token;
        if (empty($discord_bot_token)) return false;
        return self::httpPostJson('https://discord.com/api/v10/' . $route, $body, $discord_bot_token);
    }

    private static function postWebhook($content)
    {
        global $discord_webhook_url;
        if (empty($discord_webhook_url)) return false;
        // allowed_mentions: user pings only — a game name containing @everyone stays inert.
        return self::httpPostJson($discord_webhook_url, array(
            'content' => $content,
            'allowed_mentions' => array('parse' => array('users')),
        )) !== false;
    }

    private static function httpPostJson($url, $body, $botToken = null)
    {
        // $deadline is only armed inside flush(); test pings run without a budget.
        if (self::$deadline !== null && microtime(true) > self::$deadline) return false;
        if (!function_exists('curl_init')) return false;
        $headers = array('Content-Type: application/json');
        if ($botToken !== null) $headers[] = 'Authorization: Bot ' . $botToken;
        $ch = curl_init($url);
        curl_setopt_array($ch, array(
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT_MS => 1000,
            CURLOPT_TIMEOUT_MS => 1500,
        ));
        $raw = @curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($raw === false || $code >= 300) return false;
        // Webhooks return 204 No Content; the API returns JSON.
        return ($code === 204 || $raw === '') ? new stdClass() : json_decode($raw);
    }
}
