# Turn Notifications via Discord Bot DMs — Design

Roadmap item 2 (2026-07 review): no turn notifications exist; client polling decays
to 30 min; async games live on "your turn" pings. This adds lightweight Discord
**direct-message** pings sent by a bot account, with an optional public-channel
webhook as a delivery fallback. Status: **IMPLEMENTED 2026-07-15** (see §0);
awaiting autoload regen, live migration + bot setup (§6/§7) and in-game testing.

---

## 0. Implementation status (2026-07-15)

All server-side pieces are built per this plan: `DiscordNotifier.php`,
`db/discordNotifications.sql` (+ `emptyDatabase.sql` mirror), the two DBManager
recordWaiting hooks + 4 new methods, flush/clear in `submitTacGamedata` /
`advanceGameState`, the gamedata.php seen-stamp, varconfig defaults, and
`profile.php` (+ "Notifications" link in games.php header). Zero client-bundle
changes, as designed.

Deviations from the sketch (all defensive):
- **`class_exists('DiscordNotifier')` guard at every hook site** — a stale
  autoload map degrades to "no pings" instead of fataling every game action.
  **The autoload entry still must be added (regen via autoload.sh / fvbuild)
  before the feature can work.**
- **`DiscordNotifier::suppressGame($gameid)`** added; called in the
  submitTacGamedata surrender branch, where the in-memory `$gdS->status` lags
  the `updateGameStatus(..., "SURRENDERED")` DB write — flush's status check
  alone would have pinged a just-ended game.
- **Dedupe guard requires `apcu_enabled()`**, not just `function_exists`:
  `apcu_add` also returns false when the extension is loaded but disabled,
  which would have silently suppressed *every* ping in that state.
- The HTTP deadline check is null-safe — profile test pings run outside
  flush's 5s budget.
- Fleet-selection (phase -2) pings link `gamelobby.php`, not `game.php`.
- `flush()` only processes ops whose gameid matches the passed gamedata (its
  turn/phase/name describe only that game).
- `emptyDatabase.sql`'s player seed INSERT now names its columns (the
  positional form breaks once the two new columns exist).

### Local testing — which varconfig (⚠️ gotcha, cost time 2026-07-15)

The running local container does **NOT** use `source/server/varconfig.php`. That
file is `.dockerignore`-excluded from the rsync, and `docker/php/Dockerfile`
symlinks `source/server/varconfig.php → docker/php/varconfig.php` inside the
container. So the app executes `docker/php/varconfig.php` (host `mariadb`,
root/fieryvoid). Putting the bot token in `source/server/varconfig.php` (the
production file that ships to live) does nothing locally — the web request sees
an empty `$discord_bot_token` and `sendDm` bails silently → "Ping failed" with
no exception.

For local testing the token goes in **`docker/php/varconfig.local.php`**
(gitignored), which `docker/php/varconfig.php` includes to override its empty
defaults. `docker/php/varconfig.php` is committed to the PUBLIC repo, so never
put the token directly in it. After editing docker/php files, force the sync
(Windows inotify is unreliable): `docker exec fieryvoid-php-1 sh -c "rsync -a
--delete --exclude-from /usr/src/current/.dockerignore /usr/src/current/
/usr/src/fieryvoid/"`. On the LIVE server the token still goes in that server's
own `source/server/varconfig.php` (no symlink there), never committed.

Verified 2026-07-15 (local Docker): php -l clean on all touched files; local
B5CGM already migrated; CLI smoke test of the whole flush pipeline against a
captured local HTTP listener — ops replay cancellation, triggering-user
exclusion, FINISHED + suppressGame skips, disabled-config no-op, dm-cache
set/clear-on-change semantics, exact webhook JSON payload
(`allowed_mentions: users` present) and ping text, test ping returning
`'channel'` when DMs are impossible; **replay harness check: 165 passed / 0
failed** with the hooks in place.

**Real bot DMs confirmed 2026-07-15/16 (local Docker, real bot token):** the whole
`sendDm` path delivers actual DMs (`Manager::sendDiscordTestPing` → `'dm'`,
`dm_channel_id` cached), and the ownership-verification flow (§9) passed a 25/25
assertion smoke test through the served tree incl. a real code DM. What remains is
purely the **server deploy** (§6 step-by-step) and the §5 in-game checklist on a
real 2-player game. Note the local container reads `docker/php/varconfig.php`, not
`source/server/varconfig.php` — see the "Local testing" note above.

---

## 1. Core idea

FV already computes "whose input is the game waiting on": the `waiting` column in
`tac_playeringame`. `waiting = 0` means "this game needs this player's input" and
is what highlights games in the lobby today. It is written in exactly **two**
methods:

- `DBManager::setPlayerWaitingStatus($playerid, $gameid, $waiting)` — DBManager.php:1320
- `DBManager::setPlayersWaitingStatusInGame($gameid, $waiting)` — DBManager.php:1342

Every phase type already flows through them: Buying, Deployment, InitialOrders,
Movement (sequential hand-off in `MovementGamePhase::setNextActiveShip`,
lines 434–435), PreFiring, Fire, and `SimultaneousMovementRule` (line 152).
So the notification trigger is: **"whenever a player's `waiting` flips to 0,
consider pinging them"** — one choke point, no per-phase logic, and notifications
can never disagree with the lobby highlight.

A key property of turn-based flow: once the game is blocked on you, no further
events fire until you act. So an offline player naturally gets **one ping per
time-the-game-blocks-on-them**, not one per phase.

### Transport: bot DMs, no bot process

Webhooks cannot DM. DMs require a **bot account** (Discord application + bot
token) — but NOT a running bot process. Sending a DM is two plain REST calls with
an `Authorization: Bot <token>` header, made from the same PHP code path. No
gateway/websocket, no daemon on the shared host. The bot shows as permanently
offline in the member list; that's normal for REST-only bots.

Constraints that come with DMs:
- Bot and player must share a server (players are on the FV Discord already).
- A player with "Allow direct messages from server members" disabled for the FV
  server causes the message send to fail with Discord error code **50007** —
  the main failure mode, and silent. Mitigated by (a) the profile-page test ping
  proving deliverability at registration time, and (b) the optional channel
  webhook fallback below.

### Fallback: channel webhook (optional)
If `$discord_webhook_url` is configured, any failed DM falls back to one batched
`<@mention>` message in #turn-pings so notifications never silently vanish.
Leave the URL empty to run pure-DM.

---

## 2. Components

### 2.1 Config — `source/server/varconfig.php`
```php
// Discord turn notifications. Both empty = feature disabled (local/dev default).
$discord_bot_token = '';                          // primary transport: bot DMs
$discord_webhook_url = '';                        // optional fallback: #turn-pings mention on DM failure
$game_base_url = 'https://fieryvoid.eu/game/';    // used to build game links
$discord_notify_idle_secs = 300;                  // don't ping players seen polling within this window
```
Set the real token **only in the live varconfig** (it's a full bot account —
treat like the DB password; don't commit it to the public repo). Optionally a
separate bot + channel for testInstance, or leave testInstance empty.

### 2.2 DB migration — `db/discordNotifications.sql`
```sql
ALTER TABLE `player`
  ADD COLUMN `discord_id` VARCHAR(32) NULL DEFAULT NULL,
  ADD COLUMN `dm_channel_id` VARCHAR(32) NULL DEFAULT NULL;
```
- `discord_id`: NULL = not opted in; presence of a value = opted in. Snowflakes
  are 17–20 digit numbers; store as string.
- `dm_channel_id`: cache of the bot↔player DM channel, filled on first send so
  each notification is one HTTP call, not two. Cleared whenever `discord_id`
  changes or is cleared.
- Mirror both columns into the `player` definition in `db/emptyDatabase.sql`.

### 2.3 New class — `source/server/lib/DiscordNotifier.php`
Static class, same style as `Debug`. Needs an autoload entry (lib classes are in
the map — AssetLoader etc.).

Responsibilities:
1. **Record** waiting-flag changes in an in-memory ops list during the request
   (never send mid-transaction).
2. **Flush** after DB commit: replay ops to the final needs-to-act set per game,
   apply suppression rules, DM each eligible player; batch DM failures into one
   fallback webhook message.
3. **Test ping** for the profile page (reports which transport worked).

Sketch:
```php
class DiscordNotifier {

    private static $ops = [];   // gameid => list of [playeridOrALL, bool needsToAct]
    private static $deadline;   // request-level time budget for HTTP calls

    public static function isEnabled() {
        global $discord_bot_token, $discord_webhook_url;
        return !empty($discord_bot_token) || !empty($discord_webhook_url);
    }

    // Called from the two DBManager setters. $playerid === 'ALL' for the whole game.
    public static function recordWaiting($gameid, $playerid, $waiting) {
        if (!self::isEnabled()) return;
        self::$ops[$gameid][] = [$playerid, !$waiting];
    }

    // Called on transaction ROLLBACK paths.
    public static function clear() { self::$ops = []; }

    // Called AFTER endTransaction(false). $gamedata = post-advance state.
    public static function flush($dbManager, $gamedata, $triggeringUserid) {
        if (empty(self::$ops) || !self::isEnabled()) return;
        $ops = self::$ops;
        self::$ops = [];
        self::$deadline = microtime(true) + 5.0;   // hard cap on total HTTP time per request
        try {
            foreach ($ops as $gameid => $gameOps) {
                if ($gamedata->status === 'FINISHED' || $gamedata->status === 'SURRENDERED') continue;

                // playerid => row{discord_id, dm_channel_id}, one query:
                // SELECT p.id, p.discord_id, p.dm_channel_id FROM player p
                //   JOIN tac_playeringame pig ON pig.playerid = p.id WHERE pig.gameid = ?
                $players = $dbManager->getDiscordIdsInGame($gameid);

                // Replay ops in order -> final needs-to-act set (mirrors final DB state).
                $needs = [];
                foreach ($gameOps as [$pid, $needsToAct]) {
                    if ($pid === 'ALL') {
                        foreach ($players as $id => $row) $needs[$id] = $needsToAct;
                    } else {
                        $needs[$pid] = $needsToAct;
                    }
                }

                $recipients = [];
                foreach ($needs as $pid => $needsToAct) {
                    if (!$needsToAct) continue;
                    if ($pid == $triggeringUserid) continue;               // they're online right now
                    if (empty($players[$pid]->discord_id)) continue;       // not opted in
                    if (self::seenRecently($gameid, $pid)) continue;       // actively polling
                    $recipients[$pid] = $players[$pid];
                }
                if (empty($recipients)) continue;

                // Dedupe guard (poller races are submit-locked already; belt & braces):
                // apcu_add("<db>_dnotif_{$gameid}_{$gamedata->turn}_{$gamedata->phase}_"
                //          . md5(implode(',', array_keys($recipients))), 1, 600) -> skip if present.

                $dmText = self::buildMessage($gamedata, $gameid, false);
                $failedMentions = [];
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
        } catch (Exception $e) {
            Debug::error($e);   // notifications must NEVER break game flow
        }
    }

    private static function seenRecently($gameid, $playerid) {
        global $database_name, $discord_notify_idle_secs;
        if (!function_exists('apcu_fetch')) return false;   // no APCu -> just ping
        $t = apcu_fetch("{$database_name}_seen_{$gameid}_{$playerid}");
        return $t !== false && (time() - $t) < $discord_notify_idle_secs;
    }

    private static function buildMessage($gamedata, $gameid, $forChannel) {
        global $game_base_url;
        $phaseNames = [-2 => 'Fleet selection', -1 => 'Deployment', 1 => 'Initial orders',
                        2 => 'Movement', 5 => 'Pre-firing', 3 => 'Firing'];
        $phase = $phaseNames[$gamedata->phase] ?? 'Turn';
        $verb = $forChannel ? 'the game is waiting on you'
                            : "it's your turn";
        // <...> around the URL suppresses Discord's link-preview embed.
        return "⚔️ **{$gamedata->name}** — Turn {$gamedata->turn}, {$phase} — {$verb}.\n"
             . "<{$game_base_url}game.php?gameid={$gameid}>";
    }

    // Two REST calls max: create-DM-channel (once per player, then cached) + send.
    // Returns false on any failure incl. 50007 "Cannot send messages to this user".
    private static function sendDm($dbManager, $playerid, $row, $content) {
        global $discord_bot_token;
        if (empty($discord_bot_token)) return false;
        $channelId = $row->dm_channel_id;
        if (empty($channelId)) {
            $resp = self::api('users/@me/channels', ['recipient_id' => $row->discord_id]);
            if (!$resp || empty($resp->id)) return false;
            $channelId = $resp->id;
            $dbManager->setPlayerDmChannelId($playerid, $channelId);   // cache for next time
        }
        return self::api("channels/{$channelId}/messages", ['content' => $content]) !== false;
    }

    // Profile-page test. Returns 'dm', 'channel', or false so the page can tell
    // the player whether DMs work or their privacy settings block them.
    public static function sendTestPing($dbManager, $playerid, $row) {
        $msg = "✅ Test ping from Fiery Void — you will be DM'd here when it's your turn.";
        if (self::sendDm($dbManager, $playerid, $row, $msg)) return 'dm';
        if (self::postWebhook("<@{$row->discord_id}> " . $msg
            . " (Your DMs are closed — enable 'Allow direct messages from server members'"
            . " for the Fiery Void server to get DMs instead of channel pings.)")) return 'channel';
        return false;
    }

    // Authenticated bot REST call. JSON POST, decoded object on 2xx, false otherwise.
    private static function api($route, $body) {
        global $discord_bot_token;
        if (microtime(true) > self::$deadline) return false;   // budget exhausted
        $ch = curl_init("https://discord.com/api/v10/{$route}");
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json',
                                   'Authorization: Bot ' . $discord_bot_token],
            CURLOPT_POSTFIELDS => json_encode($body),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT_MS => 1000,
            CURLOPT_TIMEOUT_MS => 1500,
        ]);
        $raw = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);
        if ($raw === false || $code >= 300) return false;
        return $code === 204 ? new stdClass() : json_decode($raw);
    }

    private static function postWebhook($content) {
        global $discord_webhook_url;
        if (empty($discord_webhook_url)) return false;
        // plain curl POST of {"content":..., "allowed_mentions":{"parse":["users"]}}
        // to $discord_webhook_url, same timeouts as api(), swallow all errors.
    }
}
```
curl ext is present in the Docker `php:8.2-fpm` image by default and on
essentially all shared hosting. All failures are swallowed; `self::$deadline`
caps total HTTP time per request at ~5s even if Discord hangs on every call
(steady state is one 1.5s-capped call per recipient, usually one recipient).

### 2.4 Hook points (5 edits, all server-side)

| Where | Edit |
|---|---|
| `DBManager::setPlayerWaitingStatus` (DBManager.php:1320) | first line: `DiscordNotifier::recordWaiting($gameid, $playerid, $waiting);` |
| `DBManager::setPlayersWaitingStatusInGame` (DBManager.php:1342) | first line: `DiscordNotifier::recordWaiting($gameid, 'ALL', $waiting);` |
| `Manager::submitTacGamedata` — after `endTransaction(false)` (Manager.php:1016) | `DiscordNotifier::flush(self::$dbManager, $gdS, $userid);` — catches movement hand-offs (`setNextActiveShip` runs inside `process()`). In-memory `$gdS` already has the updated activeship. Add `DiscordNotifier::clear();` in the catch block. |
| `Manager::advanceGameState` (live def, Manager.php:1100) — after `endTransaction(false)` (≈1174) | `DiscordNotifier::flush(self::$dbManager, $gamedata, $playerid);` — catches all phase/turn boundaries, incl. the multi-phase no-deployed-ships while-loop (ops replay collapses it to one send per player with the final state). Add `clear()` in the catch block. |
| `gamedata.php` — top of GET handling, **before** the fast-poll APCu exit | `if (function_exists('apcu_store')) apcu_store("{$database_name}_seen_{$gameid}_{$userid}", time(), 900);` — the "player is actively polling" stamp. Must be above the fast-poll exit or it never fires on cache hits (see arch_gamedata_polling_cache). |

Note `advanceGameState` runs inside whichever player's *poll* arrives first after
readiness — so `$triggeringUserid` for phase advances is that poller, who is by
definition online and correctly excluded.

### 2.5 Profile page — new `source/public/profile.php`
Clone chpass.php's layout. Session-gated (`$_SESSION['user']`), redirect to
index.php when not logged in. Contents:
- Text input: **Discord User ID** (pre-filled from `player.discord_id`).
- Save: validate `/^\d{17,20}$/` or empty; empty saves NULL (= opt out).
  `Manager::setPlayerDiscordId($userid, $id)` → prepared UPDATE in DBManager.
  **Any change or clear also NULLs `dm_channel_id`** (stale cache would DM the
  wrong account).
- Button: **Send test ping** → `Manager` → `DiscordNotifier::sendTestPing()`.
  Show the result inline:
  - `'dm'` → "DM sent — check your Discord DMs."
  - `'channel'` → "Couldn't DM you — your privacy settings block DMs from
    server members. Enable them for the Fiery Void server (right-click the
    server icon → Privacy Settings) and test again. A fallback ping was posted
    in #turn-pings."
  - `false` → "Ping failed — check the ID and that you're a member of the FV
    Discord server."
- Link to it from games.php header (plain PHP page — no bundle rebuild needed).

New DBManager methods (all prepared statements):
`getPlayerDiscordRow($playerid)`, `setPlayerDiscordId($playerid, $discordId)`
(clears dm_channel_id), `setPlayerDmChannelId($playerid, $channelId)`,
`getDiscordIdsInGame($gameid)`.

---

## 3. Spam control (summary)

1. **Never ping the request's own user** — the submitter/poller is online.
2. **Never ping a player seen polling this game within `$discord_notify_idle_secs`**
   (default 5 min) — two players trading moves in real time generate zero pings.
3. **Turn-based blocking** bounds offline players to ~one DM per block-on-you.
4. **One DM per recipient per request**, typically one recipient after
   suppression; Discord's per-route rate limits are far above FV's event rate,
   and opt-in one-at-a-time DMs are nowhere near DM-spam heuristics.
5. APCu dedupe key as re-entrancy insurance.

## 4. Failure isolation

- Feature entirely disabled when both `$discord_bot_token` and
  `$discord_webhook_url` are empty → local Docker, the replay harness, and
  testInstance are unaffected by default.
- Sends happen only **after** DB commit; ops list cleared on rollback → no pings
  for state that didn't happen.
- Every HTTP call has 1s connect / 1.5s total timeout, all errors swallowed,
  flush wrapped in try/catch, and a ~5s total budget per request → a Discord
  outage can never fail a submit or advance.
- Failed DMs (50007 closed DMs, player left the server, deleted account) fall
  back to the channel webhook when configured; otherwise they're dropped
  silently — the test-ping button is the player's deliverability check.

## 5. Edge cases / verification checklist

- [ ] 1v1 movement: A submits move → B (idle) gets exactly one DM; B submits →
      A pinged, B (the trigger) not.
- [ ] Both players actively polling: zero pings across a full turn.
- [ ] Phase advance (all submitted, third-party poll triggers advance): both
      players evaluated, only idle+opted-in ones DM'd.
- [ ] Multi-phase while-loop advance (no deployed ships): exactly one DM per player.
- [ ] Game end (FINISHED/SURRENDERED): no ping.
- [ ] Player with no discord_id: skipped silently.
- [ ] First DM to a player: two API calls, `dm_channel_id` cached; second DM: one call.
- [ ] Player with DMs disabled: sendDm fails → mention appears in #turn-pings
      (when fallback configured); test-ping button reports 'channel' with guidance.
- [ ] Changing/clearing discord_id clears dm_channel_id.
- [ ] HK/CPU-controlled ships (AutomatedMovement): confirm auto-moved ships don't
      generate a ping for an action that resolves itself.
- [ ] Simultaneous-movement-rule game: pings follow SimultaneousMovementRule.php:152.
- [ ] varconfig both empty: recordWaiting no-ops (fast), no behavior change —
      replay harness `check` run stays green.
- [ ] Test ping from profile.php works; invalid ID (letters, 5 digits) rejected.

## 6. Deploy runbook (step-by-step)

Deploy to **testInstance first**, confirm end-to-end, then repeat for **live**.
The only per-server differences are which DB you migrate and the `$game_base_url`
in that server's varconfig. Server-side only — no `yarn build`, no client bundle.

### 6.0 — One-time: create the Discord bot (see §7 for detail)
- Create the bot application, copy its **token**, invite it to the Fiery Void
  Discord server. Optionally create a **#turn-pings** webhook for the DM-failure
  fallback.
- **One bot/token serves both test and live** (DMs just go to whoever opted in on
  that instance). A separate bot for test is optional.

### 6.1 — Local prep (once, before deploying anywhere)
1. **Autoload**: confirm `source/autoload.php` contains the
   `'discordnotifier' => '/server/lib/DiscordNotifier.php'` entry (it does as of
   2026-07-15). If not, regenerate with `./autoload.sh` (or `scripts\fvbuild.ps1
   -Check`) — never hand-edit the map. See [[project_autoload_generator]].
2. **No secrets in git**: `source/server/varconfig.php` (tracked, public repo)
   must keep `$discord_bot_token = ''`. The real token goes only in each
   *server's own* varconfig (§6.2 step 3), never committed. `docker/php/
   varconfig.local.php` is already gitignored.
3. **Commit + push to `DouglasChanges`** the feature: new files
   `source/server/lib/DiscordNotifier.php`, `source/public/profile.php`,
   `db/discordNotifications.sql`, `db/discordVerification.sql`; edits to
   `DBManager.php`, `Manager.php`, `source/public/gamedata.php`,
   `source/public/games.php`, `db/emptyDatabase.sql`, and `source/autoload.php`.

### 6.2 — Deploy to TEST (`fieryvoid.eu/testInstance/`)
1. **Deploy the code** to the test server the usual way (the change is all under
   `source/` + `db/*.sql` + `autoload.php`). LiteSpeed OPcache revalidates every
   30s, so new `.php` is picked up within ~30s — no `opcache_reset()` needed
   ([[reference_fv_live_litespeed]]).
2. **Migrate the TEST DB** (`u253336_b5cgm_test`, on sql-005.webh.cloud) via
   phpMyAdmin, **in order**:
   1. `db/discordNotifications.sql` — adds `discord_id`, `dm_channel_id`.
   2. `db/discordVerification.sql` — adds `discord_verify_*` + the
      `uniq_discord_id` unique index, and NULLs any pre-verification bindings.
   (If the columns already exist from an earlier partial run, the ALTER errors
   harmlessly — check `DESCRIBE player;`.)
3. **Set the token in the TEST server's `source/server/varconfig.php`** (the
   server-specific copy where the test DB block is already uncommented — the
   same file you edit for DB creds, and the one your deploy must NOT overwrite).
   Add / fill:
   ```php
   $discord_bot_token   = 'YOUR_BOT_TOKEN';                     // real token
   $discord_webhook_url = '';                                   // optional #turn-pings URL
   $game_base_url       = 'https://fieryvoid.eu/testInstance/'; // TEST instance URL
   $discord_notify_idle_secs = 300;
   ```
   ⚠️ `$game_base_url` must point at **testInstance** here, or test pings will link
   to live. Set the token **last** (after the migration): while it's empty the
   whole feature is inert, so a code-deployed-but-not-yet-migrated window is safe.
4. **Verify** at `/testInstance/profile.php`: enter your Discord ID → **Send
   verification code** → read the code the bot DMs you → enter it → **Verify** →
   **Send test ping** (expect a DM). If the code never arrives, enable "Allow
   direct messages from server members" for the FV server and retry.
5. **End-to-end**: from a second test account, take a move in a shared game so the
   game blocks on the first (idle) account; confirm that account gets one DM.

### 6.3 — Deploy to LIVE (`fieryvoid.eu/game/`)
Repeat 6.2 against live once test looks good:
1. Deploy the same code to the live server.
2. Migrate the **LIVE DB** (`u253336_b5cgm`) with both SQL files, same order.
3. In the **live** server's `source/server/varconfig.php`, set the token and
   `$game_base_url = 'https://fieryvoid.eu/game/'` (this is already the committed
   default, but confirm on the server). Optionally set `$discord_webhook_url`.
4. Verify at `/game/profile.php` (Send code → Verify → Send test ping).
5. Play a test move between two accounts and watch the DM.
6. **Announce** to players (§8).

### 6.4 — Disable / rollback (instant, no redeploy)
Blank `$discord_bot_token` (and `$discord_webhook_url`) in that server's
varconfig → the feature goes fully inert within ~30s (all hooks are
`isEnabled()`/`class_exists`-guarded). The DB columns, `profile.php`, and the
autoload entry are harmless when the token is empty, so there is nothing else to
undo. To remove entirely, also drop the columns/index, but that's rarely needed.

## 7. Discord setup (admin)

**Bot (primary transport):**
1. https://discord.com/developers/applications → **New Application** → name
   "Fiery Void".
2. **Bot** tab → **Reset Token** → copy it (shown once). Turn **Public Bot OFF**
   so only you can invite it. No privileged intents needed.
   ⚠️ If the toggle refuses with "Private application cannot have a default
   authorisation link": new apps get a default Install Link automatically.
   Go to the **Installation** tab → untick **User Install** under Installation
   Contexts → set **Install Link** to **None** → Save, then flip Public Bot
   off again. (The URL-Generator invite in step 3 still works for the app
   owner with Public Bot off — the toggle only blocks *others* inviting it.)
3. **OAuth2 → URL Generator**: tick scope `bot`, leave permissions at none
   (DMs need no guild permissions), open the generated URL, invite it to the FV
   server. It will sit at the bottom of the member list showing offline — normal.
4. Token goes in `$discord_bot_token` in the **live** varconfig only. It's a
   full bot credential — never commit it to the public repo. If it ever leaks,
   Reset Token in the portal invalidates the old one.

**Fallback webhook (recommended, optional):**
5. Create **#turn-pings**; @everyone can View + Read History, **deny Send
   Messages** (webhook posts bypass this).
6. Channel gear → **Integrations → Webhooks → New Webhook** → name "Fiery
   Void", target #turn-pings → **Copy Webhook URL** → `$discord_webhook_url`.
   Only DM failures land here, so it stays near-silent.

## 9. Ownership verification (DM challenge-code) — BUILT 2026-07-15

A Discord user ID is semi-public (anyone in a shared server can Copy User ID), so
without a check anyone could bind *your* ID to *their* FV account and make the bot
DM you their games' turn pings (a nuisance/spam vector — no account or data
access). Fixed by proving control of the Discord account before binding.

**Flow (profile.php has three states):**
1. *Not linked* — enter Discord ID → **Send verification code**. Server validates
   `^\d{17,20}$`, generates a 6-digit code (`random_int`, CSPRNG), stores it in
   `discord_verify_*` with a 10-min expiry, and DMs it to that ID (DM-ONLY — a code
   never falls back to the public webhook). `discord_id` is NOT written yet.
2. *Awaiting code* — enter the code → **Verify**. On match+unexpired, bind and
   clear the challenge. **Cancel** drops the pending challenge (keeps any existing
   link).
3. *Linked* — **Send test ping** / **Unlink**.

**Why it's safe:** the code is delivered only to the Discord account that owns the
ID, so someone entering your ID causes the code to be DM'd to *you*, not them —
they can't complete the bind. Because `discord_id` is only ever written after
verification, the ping path (`getDiscordIdsInGame`) is automatically
verified-only; no flush changes were needed.

**Anti-abuse:**
- **Send cooldown** — one code request per account per 30s (APCu, best-effort) so
  the sender can't be looped to DM-bomb a target.
- **Attempt limiter** — wrong guesses against a live code are capped
  (`$discordVerifyMaxAttempts = 5`, APCu counter); exceeding it clears the
  challenge. No APCu (e.g. CLI) → clear on first wrong guess. With the 30s send
  cooldown this makes brute-forcing a 6-digit code infeasible.
- **Uniqueness** — `uniq_discord_id` unique index: one Discord ID ↔ at most one FV
  account (NULLs exempt). On verify, `bindVerifiedDiscordId` first transfers the ID
  off any other account (the verifier just proved ownership), so a squatter can't
  permanently block the real owner, and the index never conflicts on legitimate
  re-binding.
- The pending **code is never sent to the page** (`Manager::getPlayerDiscordRow`
  strips `discord_verify_code`).

**New methods:** `DiscordNotifier::sendVerificationCode`/`sendDmToId`;
`Manager::startDiscordVerification` / `verifyDiscordCode` / `cancelDiscordVerification`
/ `unlinkDiscord` (replaced the old unverified `setPlayerDiscordId`); DBManager
`setPlayerDiscordVerification` / `bindVerifiedDiscordId` /
`clearPlayerDiscordVerification` / `clearPlayerDiscord`, and `getPlayerDiscordRow`
extended with the verify columns. Note: `player` is MyISAM, so
`bindVerifiedDiscordId`'s transaction isn't truly atomic — fine for this
single-user verify path (transfer-then-set avoids any unique conflict).

Verified 2026-07-15 (local Docker): 25/25 assertion smoke test through the served
tree — format validation, real code DM (`sent`), code hidden from page row,
mismatch/expired/none, successful bind + field clear, ownership transfer off a
prior account, unique index rejecting a raw duplicate, cancel keeping the link,
unlink clearing it; replay harness 165/0.

## 8. Player instructions (copy-paste announcement)

See the announcement text kept alongside this plan (or the session that produced
it). Key content: enable Developer Mode → Copy User ID → paste on `profile.php`
→ Send verification code → read the code the Fiery Void bot DMs you → enter it →
Verify; enable "Allow direct messages from server members" for the FV server if
the code doesn't arrive; pings only when the game is waiting on you and you're not
actively in it; **Unlink** on profile.php to opt out.
