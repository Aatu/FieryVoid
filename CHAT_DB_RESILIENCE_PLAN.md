# Chat / DB resilience — the 2026-09-01 "Too many connections" bursts

Follow-up to the live `1040 Too many connections` bursts of **2026-09-01**.

Status: **Item 0 has reported (2026-09-01) and the answer is: the shared instance is chronically
saturated, and FieryVoid cannot be the primary cause.** See "Item 0 — RESULTS" below for the numbers
and the arithmetic. **Items 4, 5 and 7 are BUILT and verified. Item 6 is not built — but its
prerequisite measurement (item 6a) is, and is waiting to be deployed for a week. Item 8 is still
conditional on a `SHOW PROCESSLIST` that has not been taken.** The first diagnosis was wrong and is
dissected below so it is not rebuilt.

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

---

## Item 0 — RESULTS (collected 2026-09-01, at rest — no outage in progress)

| Metric | Value |
|---|---|
| `max_connections` | **1000** |
| `max_user_connections` | **100** |
| `wait_timeout` | **28800** (8 hours) |
| `Threads_connected` | **965** |
| `Max_used_connections` | **1001** |
| `Connections` | 340745 |

### What these say

**1. The instance is at 96.5% of capacity while nothing is wrong.** 965 of 1000 connections were held
at a routine measurement moment, not during a burst. There is essentially no headroom in normal
operation; the 2026-09-01 exhaustion was not an anomaly, it was the inevitable consequence of a
standing condition.

**2. `Max_used_connections` = 1001 means the ceiling has actually been hit.** The extra one over
`max_connections` is the slot MariaDB reserves for a `SUPER` user, which is handed out precisely when
the pool is full. This is direct evidence the instance has reached total exhaustion at least once.

**3. FieryVoid can account for at most 10% of it.** `max_user_connections` = 100 caps the account
instance-wide. FieryVoid could hold *every one* of its permitted connections and still leave 900 to
be explained by other tenants of `sql-005`.

**4. ⭐ The error code proves FieryVoid was under its own cap when it failed.** MariaDB raises
**`1203 ER_TOO_MANY_USER_CONNECTIONS`** when the *per-account* limit is hit, and **`1040`** when the
*instance-wide* `max_connections` is full. Live got **1040**. So at the moment of failure FieryVoid
held fewer than its 100 permitted connections and was refused because the **instance** was full of
somebody else's. This is the single most decisive fact collected, and it is free — it was in the
error code all along.

**5. `wait_timeout` = 28800 is why the instance stays full.** Eight hours before an idle connection is
reaped. Any tenant using persistent or pooled connections accumulates them essentially permanently.
FieryVoid is *not* such a tenant — `DBManager` uses plain `mysqli_connect` with no `p:` prefix and no
`mysqli.allow_persistent` reliance, so its connections die at request end. Whoever is holding the
other ~900 is doing something FieryVoid is not.

### The verdict, stated carefully

FieryVoid **cannot be the primary cause**: it is capped at 10% of the instance, and it was demonstrably
below that cap when it was refused.

But it is **not a pure bystander either**, and this distinction matters for what is worth building. If
the instance idles at ~900 from other tenants, then FieryVoid's own peak — a turn-advance flurry where
an APCu `last_update` bump invalidates every player's fast poll at once — is the marginal load that
crosses 1000. FieryVoid does not fill the pool, but it can be the straw. **Reducing FieryVoid's peak
concurrent connection count and its per-connection hold time therefore still has real value** (items 6
and 8), even though neither can prevent a `1040` caused by the other 90%.

⚠️ **Do not let item 8 be built on the old story.** Its premise — "FieryVoid holds connections idle
across serialisation and that inflates concurrency" — is still true and still worth fixing, but the
framing changed: it protects FieryVoid against hitting its **own** 100-connection cap (which would
raise `1203`, an error live has never seen) and reduces its marginal contribution. It cannot fix
`1040`. Build it as good citizenship, not as the cure.

### Still unmeasured: how many of the 965 are FieryVoid's

The one number that would complete the picture. On shared hosting the account almost certainly lacks
the `PROCESS` privilege, which is *convenient* here: without it `SHOW PROCESSLIST` returns **only the
account's own threads**, so a bare row count is exactly the wanted figure.

```sql
SHOW FULL PROCESSLIST;
SELECT COUNT(*) FROM information_schema.PROCESSLIST;
SELECT COMMAND, COUNT(*) FROM information_schema.PROCESSLIST GROUP BY COMMAND;
```

The third splits FieryVoid's own threads into `Sleep` (idle, holding a slot for nothing — item 8's
target) versus `Query` (actually working). Worth running at a quiet moment *and* during a turn-change
flurry; the difference between the two is the real peak.

### Ask the host

Now the highest-value action available, and the numbers above make the ticket concrete rather than
speculative: *`Threads_connected` on sql-005 sits at 965 of 1000 at rest and `Max_used_connections`
has reached 1001, so the instance is running with no headroom. `wait_timeout` is 28800, so idle
connections are held for eight hours. Our account is capped at 100 and received error 1040 (not 1203)
on 2026-09-01 between 07:11 and 08:24 UTC, meaning we were refused while under our own limit. Can you
confirm whether max_connections is shared across tenants on this instance, and whether a lower
wait_timeout or a higher max_connections is possible?*

That framing is hard to deflect: it does not ask them to investigate a vague slowness, it presents
their own instance's saturation and asks a specific question about capacity.

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

## Item 4 — one connect attempt per request, not N ✅ BUILT 2026-09-01

**Priority: highest of the four.** This is the only item that actively *deepens* an outage.

> **Status: done and verified.** A per-request `$dbUnavailable` latch was added to **all three**
> managers — `Manager`, `ChatManager` and **`HelpManager`**, which has the identical
> static-stays-null-on-failure pattern and was not named in the original write-up. The canonical
> explanatory comment lives on `Manager::$dbUnavailable`; the other two point at it.
>
> The latch rethrows the **identical exception object**, not a copy, so its trace still points at the
> real connect failure. To stop that producing one log frame per catch site (there are ~25 across the
> three managers), `Debug::error` now dedupes on exception object identity via an `SplObjectStorage`
> and returns the original log id — so every error response in a request cites one logged frame
> instead of N redundant ones. That also removes real disk I/O amplification: each frame writes the
> full REQUEST and SESSION context to `fieryvoid.log`, at exactly the moment the server is struggling.
>
> **Verified** with a throwaway CLI harness in the php container, pointing the connect at a refusing
> port and calling `getChatMessages()` twice as `chatdata.php` does:
>
> | | EXCEPTION frames | distinct log ids | elapsed |
> |---|---|---|---|
> | before | 2 | 2 | 10.42 ms |
> | after | **1** | **1** | **5.08 ms** |
>
> The baseline was measured by `git checkout`-ing the two files and re-running, so the test is known
> to discriminate rather than merely passing. The halved elapsed time is the second `mysqli_connect`
> no longer being attempted — 5 ms against a locally refusing port, but a full TCP round trip plus
> queueing against a saturated remote host.
>
> Also done while in these files: `playerChatInfo.php` no longer ships `display_errors = 1` (see the
> note at the foot of this file). Errors now log instead of rendering into a body the client parses
> as JSON.

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

## Item 5 — stop trusting the client's `lastid` ✅ BUILT 2026-09-01

**Priority: medium.** Low likelihood, but the failure mode is that the app switches off its own
protection.

> **Status: done and verified — but NOT by option (1), which turned out to be dead code.**
> See "Why option (1) cannot work" below before touching this again. The fix asks the database for
> `MAX(id)` on the empty-result path; `DBManager::getMaxChatMessageId()` is new.

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

### ⚠️ Why option (1) cannot work — it is dead code, and it looks like a fix

**Option (1) can never fire, and (2) inherits the same flaw.** The fast-poll gate at the *top* of
`getChatMessages` returns early for every request where `$lastid >= $lastMsgId`. So by the time
execution reaches the empty-result branch at the bottom, one of exactly two things is true:

- the cache was **cold** (`$lastMsgId === false`), so there is nothing to clamp against; or
- the cache was **warm and higher than the claim**, so "if the cached value is lower than `$lastid`,
  use it instead" tests a condition the gate above has already excluded.

The poisoning happens on the **cold** path — precisely the one option (1) admits it does not cover.
Written as specified it would have added code, read as a fix, changed the log, and protected nothing.
This is worth remembering as a shape: *a guard placed downstream of a gate that already implies its
condition.*

### The fix, as built

**Option (3), and its cost objection does not survive contact with the gate either.** The plan
called a second round trip wasteful "on a path whose whole purpose is avoiding them" — but this branch
is only reachable on a fast-poll **MISS**, where a query has already been paid for. The extra lookup
therefore lands roughly once per TTL per active chat, not once per poll.

- New [`DBManager::getMaxChatMessageId($gameid)`](source/server/controller/DBManager.php) —
  `SELECT MAX(id) FROM chat WHERE gameid = ?`, resolved from the `gameid_id` index alone.
- The empty-result branch stores `min($lastid, $trueMaxId)`, so the cached watermark can never exceed
  an id that really exists, whatever the client claims.
- The short 30s TTL is now chosen on `$trueMaxId > 0` rather than `$lastid > 0` — i.e. on whether the
  chat genuinely has messages, which is what the original short TTL was actually trying to express.

**The parse-step sanity ceiling was deliberately NOT added.** With the watermark now taken from the
database, clamping the claim earlier protects nothing: an inflated `lastid` can only win itself a
fast-poll exemption, which returns `[]` to the liar and affects no one else. Both entry points are
covered by construction, because both funnel through `getChatMessages` — which is a better answer to
the ⚠️ above than duplicating a check in two callers.

### Verified

Against the local DB with a probe row at a known id (39282), cold cache, client claiming `999999`:

| | cached watermark |
|---|---|
| before | `999999` — **poisoned**, 1h TTL |
| after | `39282` — the true max |

Baseline again measured by `git checkout`-ing the two files and re-running, so the test discriminates.
Both control cases were re-checked and unchanged: an honest client at `lastid=0` still receives the
message and caches the true id, and a client already up to date is still fast-polled `[]` with no DB
work.

---

## Item 6 — cap `gamedata.php` concurrency

**Priority: medium. Highest risk of the four — this one can take the game down if it is got wrong.**

> **Status: NOT built. The measurement it demands is built and ready to deploy — see
> "Item 6a — the instrument" immediately below.** Do not pick a cap until it has reported.

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

## Item 6a — the instrument ✅ BUILT 2026-09-01, ready to deploy

Answers the ⚠️ above. **Measures only — it limits nothing, sheds nothing and changes no behaviour.**

### Files

| File | Role |
|---|---|
| `source/server/lib/PollInstrument.php` | the whole instrument (new) |
| `source/server/server_load_guard.php` | +1 hook, above the limiter |
| `source/public/gamedata.php` | +1 line: `PollInstrument::markHeavy()` |
| `source/public/pollStats.php` | read-out, MaintenanceGate `?key=` (new) |
| `source/logs/pollstats.csv` | written by the app, one line per hour |

### ⭐ It measures two pools, and the difference is the finding

- **ALL** — every gamedata request. What a cap in `server_load_guard.php` would see, since the guard
  runs *before* the fast-poll check and cannot yet know which kind it is.
- **HEAVY** — only those that fell through to the full build (Manager, DB connection, ship tree,
  `stripForJson`, `json_encode`). What a cap placed *after* the fast-poll exit would see.

Most polls exit on the APCu fast path and are nearly free. If HEAVY peaks well below ALL, that is the
argument for moving the acquire point rather than capping at the guard — and it would mean the
"separate, higher counter" sketched above is solving the wrong half of the problem.

`markHeavy()` sits **outside** the fast-poll `if`, because a POST or a first load with no `last_time`
never enters that block at all and is every bit as expensive.

### ⚠️ The leak, and why the CSV has a `min_all` column

The in-flight counter is incremented at the start and decremented by a shutdown function. PHP runs
shutdown functions on a normal end, `exit()`, an uncaught exception and a fatal (including the memory
limit) — but **not** if lsphp is hard-killed, which is exactly what the LVE memory limit does to this
endpoint. Every missed decrement leaves the counter permanently one too high, which over a week would
quietly turn the peak into fiction.

So the hourly **minimum** is recorded next to the peak. An honest counter returns to 0 whenever the
site is briefly idle; a leaked one has a floor it never drops below. **Read `min_all` first:**

- `min_all = 0` → the peaks are real as they stand.
- `min_all` climbing → that many slots are stuck; the peaks are overstated by roughly that much.

At each hour boundary the floor is subtracted back off, so the error cannot compound across the week.

### Deploying it

1. Push the five files. Nothing else changes; there is no schema change and no client change.
2. Confirm `source/logs/` is writable and **outside the document root** (it is, alongside
   `fieryvoid.log`).
3. Visit `pollStats.php?key=<maintenance_key>` — "Right now" should show non-zero requests within a
   minute of any game being open.
4. Leave it for a week, ideally spanning a busy evening of simultaneous turn advances, since that
   thundering herd is the whole reason for the cap.

**Kill switch:** upload an empty `source/logs/pollstats.off` and collection stops dead. A marker file
rather than a varconfig flag on purpose — `global.php` requires the guard at line 36 and
`varconfig.php` at line 37, so no varconfig setting exists yet when the hook runs; and on a live
shared host stopping a diagnostic should be an FTP upload, not a deploy.

### Reading it, when the week is up

`pollStats.php` prints the percentiles directly. **Set the cap above the 99.9th percentile of HEAVY
concurrency**, not above the median: at the 99.9th it sheds roughly one poll in a thousand under
normal load, and `ajaxInterface.js` already retries a 503. Below the 99th it will bite on ordinary
evenings, which is precisely the outcome the ⚠️ warns against.

Also look at the duration histogram. Concurrency is arrival rate × duration, so if the tail is a few
multi-second builds, **item 8 shortens them and lowers concurrency with no cap at all** — which would
be the better fix, and the instrument is what would show it.

### Verified

Nine scenarios against real APCu in the php container, each in its own process: counter increment and
observation at depth, the `MAX_BUCKET` collapse (peak stays exact, only the histogram bucket caps),
the separate heavy pool, `markHeavy` idempotence, shutdown release, duration bucketing, the
never-negative decrement, the CSV rollup field-by-field including the leak rebase (`5 - 3 + 1 = 3`),
and the one-writer-per-hour election. Plus `APCUIterator` worker counting and the viewer's percentile
maths. All pass.

⚠️ **Still unverified, and only live can settle it:** whether the host runs this account in a single
lsphp pool. APCu is per-instance shared memory, so if there is more than one pool each counts only
itself and every figure is an undercount. The CSV's `workers` column is the check — a suspiciously
small number there means the numbers are a floor, not a measurement.

---

## Item 7 — don't show players a dialog when the database is busy ✅ BUILT 2026-09-01

**Priority: lowest. Purely cosmetic, but it was the visible symptom.**

> **Status: done and verified, both halves.** The server-side option chosen was catching in
> `initDBManager` (the `set_error_handler` was left alone, for the reason the ⚠️ below gives).

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

### As built

**Server** — `asUnavailable()` in all three managers, called from the same `catch` item 4 added:
a failed connect is rethrown as `Exception('Database unavailable', 300, $previous)`. The
`set_error_handler` was **left alone**; the ⚠️ above is the reason, and it stands.

Two decisions worth keeping:

- **The message is replaced, not passed through.** The ~25 catch sites in these classes interpolate
  `getMessage()` straight into a hand-built JSON string. A driver message containing a `"` would
  produce a malformed body, and the client would get a parse error *instead of* the marker this whole
  item exists to deliver — the failure would be worst exactly when it mattered. A fixed string is also
  one less piece of database detail on the wire.
- **`Debug::error` now logs the `getPrevious()` chain** (bounded at 5). Without it, wrapping would
  have traded the client-side marker for the loss of the only record of what actually went wrong.
  The wrapper carries the code; the chain carries the diagnosis. Confirmed in the log:
  `CAUSED BY [1]: ErrorException: mysqli_connect(): (HY000/2002) ...`. `CODE:` was added to the log
  frame at the same time, since the code is now load-bearing and was not being recorded.

**Client** — [chat.php](source/public/chat.php) `receive()` now routes a code-300 slice to
`dbUnavailable()` instead of `window.confirm.exception`. It stays **completely silent** for
`DB_DOWN_NOTICE_AFTER` (4) consecutive polls, then appends **one** `.chatSystemNotice` line inside the
panel and leaves it alone; `dbRecovered()` removes it and resets the counter on any normal reply,
including an empty `[]`. Anything that is *not* code 300 still raises the dialog, because anything
else is a real fault. The comparison is `==` — `ChatManager` stringifies the code into its hand-built
JSON while `chatdata.php`'s outer catch emits it as a number, and both were tested.

The notice only auto-scrolls if the player is already within 40px of the bottom, so it cannot yank the
panel down while they are reading back.

### Verified

Real functions extracted from `chat.php` and driven through a stubbed poll sequence:

| polls of code 300 | before (HEAD) | after |
|---|---|---|
| 3 | 3 modal dialogs | silent |
| 4 | 4 modal dialogs | 1 in-panel line |
| 24 | **24 modal dialogs** | **1 in-panel line** |

…then recovery clears it, a genuine (non-300) error still dialogs, and a numeric `300` is recognised
as well as the string. The baseline column was measured by extracting `receive()` from
`git show HEAD:source/public/chat.php`, not estimated. **Per chat** — game.php runs two, so the real
before-figure during the 07:11 burst was double.

---

## Not part of any item above, but in the same files

~~`playerChatInfo.php` ships with `ini_set('display_errors', 1); error_reporting(E_ALL);`~~
**✅ Fixed 2026-09-01 alongside item 4.** Now `display_errors = 0`, `log_errors = 1`, with
`error_reporting(E_ALL)` kept so nothing stops being *reported* — it just goes to the log rather than
into a body the client parses as JSON. Note the file's two `ob_clean()` calls only protected the paths
that pass through them; a warning raised after the JSON header was sent would still have landed inside
the payload.

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
