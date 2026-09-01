# Chat / DB resilience — the 2026-09-01 "Too many connections" bursts

Follow-up to the live `1040 Too many connections` bursts of **2026-09-01**.

Status: **THE CAUSE IS STILL UNKNOWN.** The first diagnosis was wrong and is dissected below so it is
not rebuilt. Items 4–7 are hardening that stands on its own merits. **ITEM 0 — measurement — comes
first and nothing else should be built until it reports.**

⚠️ **Read this before starting any item below.** Items 4–7 make the app degrade gracefully when the
database is already in trouble, and close two ways it can make its own trouble worse. **Not one of
them is known to address the 2026-09-01 outage.** Do not let finishing them feel like fixing it.

---

## ⚠️ The first diagnosis was wrong — read this before re-deriving it

The original claim was that `chat` was MyISAM with only `PRIMARY KEY (id)`, so
`deleteOldChatMessages()`'s non-sargable `DELETE ... WHERE DATE_ADD(time, INTERVAL 3 DAY) < NOW()`
full-scanned it under a **MyISAM table-level write lock**, stalling every concurrent chat poll while
each held its own connection.

**That mechanism does not exist on live.** It was read out of `db/emptyDatabase.sql` — a MySQL 5.7.20
dump of the old `B5CGM` database — and then "confirmed" against the local Docker DB, which
[docker/mariadb/Dockerfile:9](docker/mariadb/Dockerfile#L9) **seeds from that same dump**. Two copies
of one stale file, mistaken for two independent sources.

The live table (phpMyAdmin export, 2026-09-01) is **already InnoDB** and holds **about 13 rows** —
the 3-day retention genuinely works. No table lock. No expensive scan. The "4950 rows → 0" measurement
was real but was taken against synthetic data on a schema live has not used for years.

⚠️ **`db/emptyDatabase.sql` is not the live schema, and the local Docker DB cannot corroborate it.**
Live is `u253336_b5cgm` on **`sql-005.webh.cloud` — a remote, shared MariaDB 11.4.5 instance**. The
only way to see the live schema from a dev box is to ask for a phpMyAdmin export.

### What survived

- **A `ChatManager` frame in a trace is the victim, not the cause.** `chatdata.php` is the
  highest-frequency endpoint, so it is statistically the one standing there when connections run out.
- **The polling commits `3b6e2afe4` / `b394590ee` are exonerated and need no revert.** They cut a
  quiet game.php tab from 20 req/min to 4, batched two chats into one request, added the APCu fast
  path, and `gamedata.php` piggybacks chat watermarks onto its fast-poll reply so the chat poller
  defers entirely while a game is live. Chat is the *best*-optimised path in the app.
- The `deleteOldChatMessages()` rewrite and `db/chatTableIndexes.sql` are kept as **hygiene**, clearly
  labelled as such. Neither is urgent.

---

## Item 0 — MEASURE, before writing any more code

**Do this first. Everything below is speculation until it reports.**

The single most useful fact is missing: **how many connections does FieryVoid actually hold, and for
how long?** Nothing in the repo can answer that.

### The one free clue already in hand

The error is **`1040`**, not `1203`. MariaDB raises `1203 ER_TOO_MANY_USER_CONNECTIONS` when a
*per-account* cap is hit, and `1040` when the **instance-wide `max_connections`** is full. Live got
`1040` — so the whole shared MariaDB instance was out of connections. FieryVoid may have been a
contributor or an outright bystander to another tenant on `sql-005`.

### What to collect

From phpMyAdmin (the account may be denied some of these on shared hosting — note which):

```sql
SHOW VARIABLES LIKE 'max_connections';
SHOW VARIABLES LIKE 'max_user_connections';
SHOW VARIABLES LIKE 'wait_timeout';
SHOW STATUS  LIKE 'Threads_connected';
SHOW STATUS  LIKE 'Max_used_connections';
SHOW STATUS  LIKE 'Connections';
```

`Max_used_connections` against `max_connections` settles it at a glance. If the ceiling is small (some
shared hosts set 30–50 for the whole instance) then a modest turn-change flurry is enough and the
answer is architectural. If it is large and FieryVoid's own usage is nowhere near it, the answer is
"ask the host", and items 4–7 are all that is worth doing in the code.

During a burst, `SHOW PROCESSLIST` (or `SHOW FULL PROCESSLIST`) is worth more than any of it — it
shows how many connections are FieryVoid's, and whether they are running a query or sitting idle. If
they are **idle**, that confirms the hypothesis below and item 8 becomes the priority.

### Ask the host

Worth a support ticket regardless: *what is `max_connections` on sql-005, is it shared across tenants,
and were there connection-exhaustion events on 2026-09-01 between 07:11 and 08:24 UTC?* They can see
what the account cannot.

---

## Item 8 — close the connection before the expensive part (NEW, promoted)

**This is now the leading code-side hypothesis, and it should be confirmed by Item 0's
`SHOW PROCESSLIST` before being built.**

Neither `Manager` nor `ChatManager` ever calls `close()`. The connection opens at the first query and
is released only at request shutdown. For `gamedata.php` that means one connection is held across the
**entire** ship-tree construction, `stripForJson()` and `json_encode()` — seconds of pure CPU with
zero database use. Concurrency is rate × duration, and this inflates duration by an order of
magnitude.

This matters far more than it would have on localhost, because **live's database is remote and
shared**. A connection held idle for four seconds on `sql-005` is four seconds of a scarce instance-wide
resource, spent on work that does not need it.

The shape of the outage fits: an APCu `last_update` bump on a turn advance invalidates **every**
player's fast poll at once, so N players simultaneously run the full heavy path, each holding a
connection throughout. The bursts hit several games at once, which is what a busy evening of
simultaneous turn advances looks like.

⚠️ **Confirm before building.** If `SHOW PROCESSLIST` during a burst shows FieryVoid connections
mostly *idle*, this is the answer. If it shows few FieryVoid connections at all, this is a dead end
and the problem is the host's. Do not implement on the strength of the story alone — that is exactly
the error that produced the MyISAM diagnosis.

If confirmed, the fix is to release the connection once the DB work is done and before serialisation
begins. `DBManager::close()` already exists but nothing calls it; the static would need to be nulled
so a later query can reconnect rather than use a closed handle.

---

## Item 4 — one connect attempt per request, not N

**Priority: highest of the four.** This is the only item that actively *deepens* an outage.

### The fault

[`ChatManager::initDBManager()`](source/server/controller/ChatManager.php#L25) assigns
`self::$dbManager` only on success:

```php
if (self::$dbManager == null)
    self::$dbManager = new DBManager(...);
```

When the connect throws, the static stays `null`. Every `ChatManager` entry point catches the
exception and returns an error *string* rather than rethrowing, so the caller carries on — and
[chatdata.php:136-137](source/public/chatdata.php#L136-L137) calls `getChatMessages()` **once per
requested chat**:

```php
foreach ($fvChats as $g => $l) {
    $parts[] = json_encode((string)$g) . ':' . ChatManager::getChatMessages($playerid, $l, $g);
}
```

So during a connection-pool outage each poll hammers the pool once per chat instead of once.

**The live log proves it, and this is the detail worth keeping:** pid `509813` logged two identical
`Too many connections` exceptions **1.1 ms apart** (`07:12:10.651422` and `07:12:10.652520`), and pid
`385342` did the same at `07:12:33.611697` / `.613115`. Two frames, one request, one per chat.

`$fvChats` is capped at 8 pairs ([chatdata.php:52](source/public/chatdata.php#L52)) and is
client-supplied, so the ceiling is 8 connect attempts per request, not 2.

### The fix

Give `ChatManager` a per-request "the database is down" latch so the second and subsequent calls fail
immediately without touching `mysqli_connect`:

- add `private static $dbUnavailable = null;`
- in `initDBManager()`, if `$dbUnavailable !== null` rethrow it (or throw a cheap clone) without
  attempting a connect
- wrap the `new DBManager(...)` so a throw stores the exception in `$dbUnavailable` before
  propagating

⚠️ **The latch must be per-request, not cached in APCu.** A shared latch would let one unlucky
request lock every other process out of a database that had already recovered. PHP statics die at the
end of the request, which is exactly the lifetime wanted here.

⚠️ **Do the same in `Manager`** — it has its own separate static `DBManager` and the identical
pattern at [Manager.php:25](source/server/controller/Manager.php#L25). It is less exposed (no
per-chat loop) but the fix is the same three lines and leaving one of the two half-done is how this
comes back.

### Verifying it

Point `docker/php/varconfig.php` at a dead port, load `chatdata.php?chats=0:0,7183:0`, and confirm
**one** `EXCEPTION` frame in the FV debug log where there are currently two. The log id in
`Debug::error` makes them easy to count.

---

## Item 5 — stop trusting the client's `lastid`

**Priority: medium.** Low likelihood, but the failure mode is that the app switches off its own
protection.

### The fault

[ChatManager.php:151-152](source/server/controller/ChatManager.php#L151-L152), on an empty result:

```php
$ttl = ($lastid > 0) ? 3600 : 30;
apcu_store($prefix . 'chat_last_id_' . $gameid, $lastid, $ttl);
```

`$lastid` is whatever the client asked for. `ctype_digit` in chatdata.php rejects negatives and
scientific notation but places no upper bound, so a logged-in player sending `chats=7183:999999`
writes `999999` into that chat's cache **with a one-hour TTL**.

The effect is not that other players stop seeing messages — the fast-poll test is
`$lastid >= $lastMsgId`, so a real client with `lastid=500` fails it and falls through. The effect is
worse in the way that matters here: **the DB-sparing fast path is disabled for that chat for an hour**,
and every poll from every player in that game goes straight to MySQL. One request, one hour, one game
chat converted back to pre-optimisation load.

It self-heals when someone posts (`submitChatMessage` stores the true `$msgId`) or when the poisoning
client polls again with a sane value, which is why it has probably never been noticed.

### The fix

The value cached for an empty result should be **the highest id that actually exists**, never a claim.
Options, cheapest first:

1. **Clamp to the previous cached value.** Read the existing entry first; if it exists and is lower
   than `$lastid`, re-store *it* rather than `$lastid`. Costs nothing, and a stale-low cache is
   harmless (it only means an extra DB read next poll). Does not help when the cache is cold.
2. **Clamp to a bound the server knows.** `apcu_fetch` the global `chat` max id maintained by
   `submitChatMessage`, and store `min($lastid, $thatMax)`.
3. **Ask the database.** `SELECT MAX(id) FROM chat WHERE gameid = ?` — now an instant lookup on the
   new `gameid_id` index, but it is a second round trip on a path whose whole purpose is avoiding
   them.

Recommend **(1)**, with a sanity ceiling: reject any `lastid` above the current cached value plus a
generous margin at the chatdata.php parse step, where the value is first seen.

⚠️ Whatever is chosen must be applied in **both** places the value is read —
[chatdata.php:64](source/public/chatdata.php#L64) (batched) and the single-chat legacy form below it.
The legacy form must stay: a browser holding game.php open across a deploy goes on sending it.

---

## Item 6 — cap `gamedata.php` concurrency

**Priority: medium. Highest risk of the four — this one can take the game down if it is got wrong.**

### The fault

[server_load_guard.php:54](source/server/server_load_guard.php#L54):

```php
$knownScripts = ['chatdata.php', 'gamedata.php', 'gamelobbyloader.php', 'allgames.php', 'games.php', 'guard_debug.php'];
```

Anything matching is `$isKnownPoll`, which skips **both** the per-IP limiter (`$maxIP = 20`) and the
23-slot global limiter. So the guard constrains cheap pages while `gamedata.php` — the single most
expensive request in the app, and the one seen dying of the LVE memory limit at `04:30:58` in the same
log — runs with no concurrency cap at all. That is why the pool can empty while the guard still
reports headroom.

The exemption is *correct* for `chatdata.php`, which fast-polls out before any DB work and must stay
cheap. It is wrong for `gamedata.php`.

### The fix

Not "remove it from the list" — that would subject gamedata to the same 23-slot pool as page loads and
make every heavy turn fight the lobby for slots. Give polls their **own, separate, higher** counter:

- a second APCu key, e.g. `..._server_active_polls`, with its own limit
- `gamedata.php` (and `gamelobbyloader.php`) acquire from that pool; `chatdata.php` stays fully exempt
- over the limit → `503`, which the client already handles

### Why 503 is safe here, and the one place it is not

[ajaxInterface.js:222](source/public/client/ajaxInterface.js#L222) retries `[503, 507]` with
exponential backoff up to 5 attempts, so a shed gamedata poll retries itself. **But check the submit
path before shipping this.** A shed *poll* is invisible; a shed **turn submission** is a player losing
their orders. Either exempt POSTs to gamedata.php from the cap entirely, or confirm the submit path
retries 503 all the way through — `submitGamedata` sets `ajaxInterface.submiting = true` and shows a
blocking overlay, and how that interacts with a 5-attempt retry needs to be read, not assumed.

⚠️ **Pick the limit from a measurement, not a guess.** Instrument first: log the observed concurrent
gamedata count for a week and set the cap above the normal peak, so it only bites during a genuine
pile-up. A cap set too low is indistinguishable to players from the outage it is meant to prevent.

---

## Item 7 — don't show players a dialog when the database is busy

**Priority: lowest. Purely cosmetic, but it was the visible symptom.**

### The fault

Two things combine.

**The error loses its identity server-side.** `DBManager.php` opens with
`mysqli_report(MYSQLI_REPORT_ERROR)`, and [ChatManager.php:5-10](source/server/controller/ChatManager.php#L5-L10)
registers a global `set_error_handler` that converts warnings to `ErrorException`. So the failure is
thrown from *inside* `mysqli_connect()` and `DBManager`'s own
`throw new Exception(..., 300)` never runs — the deliberate code-300 marker is lost, and the client
receives `code: 2` (`E_WARNING`). There is no way for the client to tell "database at capacity" from
any other error.

**The client shows every one of them.** [chat.php:729](source/public/chat.php#L729):

```php
if(slice && slice.error) window.confirm.exception(slice, function(){});
```

One dialog per chat, per poll, for as long as the outage lasts. During the 07:11–07:12 burst players
were being buried in them.

### The fix

Two halves, both small:

- **Server:** restore a stable machine-readable marker for "cannot reach the database". Either catch
  the `ErrorException` in `initDBManager()` and rethrow as the intended code 300, or narrow the
  `set_error_handler` registration so `mysqli_connect`'s warning reaches `DBManager`'s own check.
  ⚠️ That handler is registered at **file include time**, so it converts every PHP warning in the whole
  request, in any file — check what else depends on that before narrowing it.
- **Client:** treat that marker as transient. Count it, stay silent, let the poll ladder back off
  naturally, and surface something quiet and non-modal only if it persists past several polls. The
  precedent to copy is `timeCheckFailed`, which already caps its own retries and gives up silently.

---

## Not part of any item above, but in the same files

`playerChatInfo.php` ships with [`ini_set('display_errors', 1); error_reporting(E_ALL);`](source/public/playerChatInfo.php#L6-L7)
and its own comment says *"remove or adjust for production"*. On live that leaks PHP errors into a
response the client parses as JSON. It is a two-line change; do it whenever that file is next open for
item 4 or 5.

Also noted while diagnosing, both deliberate, neither a bug — recording them so they are not
"discovered" again:

- `getChatMessages` uses `ORDER BY id DESC LIMIT 25` while `ChatManager` reads the newest id with
  `end($msgs); key($msgs)`. That is only correct because `DBManager` calls `ksort($messages)` before
  returning. Correct today, but the two halves live in different files and nothing states the
  dependency.
- A client more than 25 messages behind gets the newest 25 and skips the rest, permanently. That is
  the intended trade — the `LIMIT` comment says it was added to stop memory-limit crashes on
  reconnect.
- The local Docker DB and `db/emptyDatabase.sql` list `player` as MyISAM. **That says nothing about
  live** — see the warning at the top of this file. Nobody has looked at live's engine list; if it
  ever matters, ask for an export rather than reading the dump.
