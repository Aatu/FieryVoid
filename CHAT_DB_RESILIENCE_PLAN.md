# Chat / DB resilience — the 2026-09-01 "Too many connections" bursts

Follow-up to the live `1040 Too many connections` bursts of **2026-09-01**.

Status: **effectively closed as of 2026-09-17. Every measurement this plan asked for has been taken,
and between them they ended the two items that were still open.**

| item | | |
|---|---|---|
| 0 | measure the pool | ✅ reported 2026-09-01 — the shared instance is chronically saturated and FieryVoid cannot be the primary cause |
| 4 | one connect attempt per request | ✅ **BUILT** and verified 2026-09-01 |
| 5 | stop trusting the client's `lastid` | ✅ **BUILT** and verified 2026-09-01 |
| 6 | cap `gamedata.php` concurrency | ❌ **DO NOT BUILD** — 2026-09-17. Peak concurrency over two weeks was **4**; 99.4% of heavy requests are alone in flight. There is nothing to cap. |
| 6a | the instrument | ✅ BUILT, ran on live for 336 hours, **reported 2026-09-17**. It killed items 6 *and* 8. Turn it off with the kill switch. |
| 7 | no dialog when the DB is busy | ✅ **BUILT** and verified 2026-09-01 |
| 8 | close the connection before serialisation | ❌ **DO NOT BUILD** — 2026-09-17. The window is **5.8 ms of a 420 ms request (1.4%)**, not "seconds of pure CPU". One real bug found and fixed in passing: `DBManager::close()` was not idempotent and would have fataled every heavy request. |
| 9 | is the load guard still fit for purpose? | 📋 **REVIEWED. Option 1 BUILT 2026-09-17** — the global limiter could spin its full budget and 503 for nothing whenever its counter key expired mid-wait; fixed, with the three control cases proved unchanged. Options 2–5 still need a decision. |

⚠️ **Read this before starting any item below.** Items 4–7 make the app degrade gracefully when the
database is already in trouble, and close two ways it can make its own trouble worse. **Not one of
them is known to address the 2026-09-01 outage.** Do not let finishing them feel like fixing it.

⚠️⚠️ **The most valuable thing in this file is the record of three plausible stories that measurement
refuted** — the MyISAM table lock (below), item 6's concurrency pile-up, and item 8's idle connection.
All three were persuasive, none survived a number. The one action still outstanding that could
actually change anything is not in the code at all: **ask the host** (item 0, last section).

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

## Item 8 — close the connection before the expensive part ❌ NOT BUILT — premise measured and wrong (2026-09-17)

> **Status: the gate below was honoured, the measurement was taken, and it says don't build this.**
> The window item 8 wanted to close is **5.8–6.7 ms out of a 420–480 ms request (1.2–1.6%)**, not the
> "seconds of pure CPU" the original reasoning assumed. The one thing that *was* built is the
> `DBManager::close()` bug this investigation exposed — see "What was built" at the foot of this item.
>
> ⚠️ **Do not re-promote item 8 from the story below.** The story is still superficially persuasive
> and the numbers are three commands away. They are recorded here so nobody has to re-derive them.

### The original hypothesis (kept, because it reads as obviously true and isn't)

Neither `Manager` nor `ChatManager` ever calls `close()`. The connection opens at the first query and
is released only at request shutdown. For `gamedata.php` that means one connection is held across the
**entire** ship-tree construction, `stripForJson()` and `json_encode()` — seconds of pure CPU with
zero database use. Concurrency is rate × duration, and this inflates duration by an order of
magnitude.

This matters far more than it would have on localhost, because **live's database is remote and
shared**. A connection held idle for four seconds on `sql-005` is four seconds of a scarce instance-wide
resource, spent on work that does not need it.

⚠️ The gate attached to it: *"Confirm before building. Do not implement on the strength of the story
alone — that is exactly the error that produced the MyISAM diagnosis."*

### What the measurement says

`SHOW PROCESSLIST` during a burst was never obtainable, but it turned out not to be the limiting
evidence. The question "how long is the connection held with nothing using it, and where in the
request is that time?" is answerable directly, on the dev box, and that is what was done: the biggest
game in the local database (4277, **74 ships, 126 KB of gamedata JSON**), driven through exactly the
call sequence `gamedata.php` runs.

| phase | | ms |
|---|---|---|
| `getTacGamedata` | DB phase — connection in use | **393–449** |
| `prepareForPlayer` | DB phase | 20–25 |
| `stripForJson` | ← item 8's window | 3.1 |
| `json_encode` | ← item 8's window | 0.4–1.9 |
| `md5` (ETag, in `fv_compress_output`) | ← item 8's window | 0.2 |
| `gzencode` | ← item 8's window | 1.5 |
| | **item 8's window, total** | **5.8–6.7 ms = 1.2–1.6% of the request** |

**The ship tree is not built after the DB phase. It is built *inside* it.** `getTacShips` constructs
ships as it walks the result rows, so "ship-tree construction" — the expensive part the original
reasoning wanted to move the connection out of — is interleaved with the queries and has no seam in
front of it. What actually remains after the last query is `stripForJson` + `json_encode` + the
shutdown compression, and that is single-digit milliseconds even on the largest game in the corpus.

### ⭐ The one part of the story that IS true — and why it still doesn't rescue item 8

The connection really is held idle for most of a heavy request. Same game, same process, measuring
the queries themselves via MariaDB's global `Questions` counter:

| | ms | queries |
|---|---|---|
| `getTacGamedata`, cold process | 410.79 | 21 |
| `getTacGamedata`, warm process (identical SQL) | 12.55 | 19 |
| **difference = PHP work done with the connection open and idle** | **398.23 (96.9%)** | — |

So ~97% of a cold heavy request is PHP, and the connection is open and unused throughout. The
instinct behind item 8 is sound. **But that idle time is distributed *through* `getTacGamedata`,
between its 21 queries — not after it.** Closing the connection where item 8 proposes captures the
5.8 ms tail and none of the 398 ms. Capturing the rest would mean restructuring `getTacShips` so
construction happens after the reads, which is a deep change to the app's hottest path.

Two further notes on those numbers before anyone re-reads them as worse than they are:

- **21 queries is not a round-trip problem.** A remote DB at even 3 ms RTT adds ~60 ms, which is
  consistent with live's observed heavy median of 100–250 ms (item 6a's corpus). There is no N+1 here.
- **The 398 ms is cold-process PHP** — autoload compile plus `ShipLoader`/`SystemFactory` static
  warm-up. On live, opcache removes the compile share, which is why live's heavy median is 100–250 ms
  rather than 420 ms. The *shape* holds (mostly PHP, connection idle); the magnitude is smaller.

### `game.php` was checked too, because it holds the connection across far more

It is the biggest request in the app, and everything after `Manager::getTacGamedataJSON()` at
[game.php:29](source/public/game.php#L29) — `json_decode` of the payload, `BlueprintCache`, the whole
HTML render, then md5 + gzip of the assembled document — runs with the connection still open.

| | ms |
|---|---|
| DB phase | 451.45 |
| `json_decode` of the payload | 1.30 |
| `BlueprintCache::getStaticShipsJson` | 126.07 **cold** / ~4 warm on live |
| md5 (whole page) | 0.65 |
| `gzencode` (whole page) | 6.67 |
| **tail, warm-cache equivalent** | **~13 ms** |

Still not worth it — and `BlueprintCache` issued **one further DB query**, so closing the connection
before it would force a reconnect, trading a saving for an extra connect against the same scarce pool.

### The verdict, and how it squares with item 0

Item 0 already established that FieryVoid is capped at **100** connections by `max_user_connections`,
was refused with **1040** (instance-wide) rather than **1203** (per-account), and therefore was under
its own cap when it failed. Item 6a's corpus now puts a number on how far under: **peak concurrency of
4, once, in two weeks** (see item 6). Four connections against a cap of 100.

Shaving 1.5% off the hold time of a request that is one of at most four is not a saving that exists.
Item 8 was promoted as "good citizenship, not the cure" — and at this size it is not even that.

### What WAS built: `DBManager::close()` was broken, and item 8 would have fataled on it ✅

Item 8's sketch said "`DBManager::close()` already exists but nothing calls it; the static would need
to be nulled". That understates it. `close()` did:

```php
public function close() { mysqli_close($this->connection); }
```

— unconditionally, and `__destruct()` calls `close()`. So **any** explicit `close()` was followed by a
second one at destruct time, and under PHP 8 `mysqli_close()` on an already-closed handle throws
`Error: mysqli object is already closed`. Not a warning — an uncaught `Error`.

Item 8 as specified would therefore have fataled **every heavy `gamedata.php` request**, at shutdown,
after the response was already composed. Observed directly while measuring:

```
close() behaviour, as item 8 would use it:
  explicit close()            OK
  second close()              THREW: Error: mysqli object is already closed
  now letting __destruct run:
PHP Fatal error:  Uncaught Error: mysqli object is already closed in DBManager.php:134
  #1 DBManager.php(41): DBManager->close()   ← __destruct
```

`close()` is now idempotent and nulls the handle, so a second call returns, `__destruct` is safe, and
a later query gets `query()`'s own clean "connection failed" exception rather than an `Error` from
inside mysqli. After the fix the same harness prints `explicit close() OK / second close() OK /
survived __destruct OK`.

Nothing calls `close()` today, so this changes no behaviour — it removes a trap that was armed and
pointing at exactly the change item 8 proposed. `fvbuild.ps1 -Check` green afterwards (autoload map
current, ship-data validator clean, replay harness **133 passed, 0 failed**).

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

## Item 6 — cap `gamedata.php` concurrency ❌ DO NOT BUILD — the instrument reported (2026-09-17)

> **Status: resolved by measurement, and the answer is no.** Item 6a ran on live from **2026-09-02 14:00
> to 2026-09-17 05:00 UTC** — 336 hours, **44,363** instrumented requests, **8,593** of them heavy.
> There is no concurrency to cap. The instrument did its job: it talked the plan out of its own
> highest-risk item.

### What the corpus says

`min_all` is **1 in all 336 rows**, lowest and highest. That is the leak detector reading clean, so
every peak below is real as it stands. (Note for anyone re-reading item 6a's header: it predicts
`min_all = 0` for an honest counter, but the floor is structurally **1** — `lower()` is called *after*
`apcu_inc`, so the incrementing request is always counted. `min_all = 1` is the clean reading; a leak
would show as `2, 3, 4…` climbing hour on hour. See "the hourly rebase" below.)

**Concurrency, every instrumented request in the corpus:**

| simultaneous | ALL requests | | HEAVY (full build) | |
|---|---|---|---|---|
| 1 | 43,898 | 98.952% | 8,545 | 99.441% |
| 2 | 458 | 1.032% | 45 | 0.524% |
| 3 | 2 | 0.005% | 2 | 0.023% |
| 4 | 5 | 0.011% | 1 | 0.012% |

**99.44% of heavy `gamedata.php` requests are alone in flight.** Across two weeks, exactly **three**
heavy requests were ever observed at a depth of 3 or more. Peak concurrency, all fourteen days, all
hours: **4**, reached once.

Per-hour peaks agree: `peak_heavy` was 0 or 1 in **308 of 336 hours**, 2 in 26, and 3 or 4 in one hour
each. `err5xx` is **0** for the entire corpus.

### Why the plan's own sizing rule refuses to produce a cap

The rule was: *"Set the cap above the 99.9th percentile of HEAVY concurrency."* The 99.9th percentile
of heavy concurrency is **2**. So the rule yields a cap of **3** — and a cap of 3 would have fired
three times in two weeks, every one of them during an incident (below). A cap high enough to be safe
is a cap that can never fire.

Little's Law says the same thing from the other direction. The busiest hour in the corpus is
2026-09-04 20:00 with 658 requests — **0.18 requests/second**. At the measured median heavy duration
of ~150 ms, expected concurrency is **0.027**. The observed peaks of 2–4 are ordinary Poisson
clustering around a mean of essentially zero. **Reaching a cap of even 5 would need ~40× the current
traffic.**

### ⭐ The finding that actually matters: concurrency here is a *duration* symptom, not a *rate* one

The only hours that reached depth 3 or 4 are the hours containing absurdly slow requests:

| hour (UTC) | requests | heavy | peak | slowest request |
|---|---|---|---|---|
| 2026-09-15 18 | **55** | 30 | **4** | **125,802 ms** |
| 2026-09-15 10 | **11** | 11 | 2 | **102,126 ms** |
| 2026-09-15 15 | 125 | 27 | 2 | **94,607 ms** |
| 2026-09-14 11 | 165 | 50 | 2 | **85,183 ms** |
| 2026-09-04 16 | 617 | 318 | 3 | 1,353 ms |

The worst hour in the entire fortnight carried **55 requests** — one of the quietest hours in the
corpus — and still hit depth 4, because four of its requests ran for more than eight seconds and one
ran for over two minutes. Meanwhile 2026-09-04 16:00, with **eleven times the traffic**, peaked at 3.

**A cap sized against arrival rate would never have fired; a cap low enough to fire would have shed
requests during an incident it could not have caused.** In those hours something else — the shared
`sql-005` instance, or the host — was already sick. Shedding FieryVoid's own polls at that moment
takes away the retry ladder's work and adds nothing.

### Duration, for the record — and the number item 8 needed

| | ALL (n=44,361) | HEAVY (n=8,593) |
|---|---|---|
| < 25 ms | 84.90% | 22.05% |
| 50–100 ms | 4.86% | 25.07% |
| 100–250 ms | 6.63% | 34.23% |
| 250–500 ms | 2.47% | 12.73% |
| 0.5–1 s | 0.87% | 4.48% |
| ≥ 1 s | 0.28% | **1.44%** |
| ≥ 4 s | 0.057% | **0.29%** (25 requests in two weeks) |

The fast path works: **85% of all gamedata requests finish in under 25 ms.** A heavy request's median
lands in the **100–250 ms** band and 94% finish inside half a second. This is the number that
demolished item 8's "seconds of pure CPU" premise — see that item.

(22% of *heavy* requests also land under 25 ms. `markHeavy()` fires on everything that misses the
fast poll, which includes POSTs and first loads that have little to build — a WAITING player is served
no ship data at all. Not an anomaly.)

### The `workers` column — the one caveat item 6a flagged, discharged

Item 6a warned that a small pid set would mean the host splits the account across lsphp pools and every
figure is a per-pool undercount. `workers` tracks `requests` closely across the corpus (617 workers for
617 requests in the busiest hour). The APCu segment is shared widely; the numbers are not a per-pool
fragment. Hours reading `workers=0` are hours whose rollup was written more than an hour later, after
the 3600 s `pid_` keys had expired — an artefact of the gap, not evidence of a small pool.

### What to do with the instrument

It has answered its question and it costs 6–10 `apcu_*` calls on every `gamedata.php` request. **Stop
it with the kill switch** — upload an empty `source/logs/pollstats.off`. That needs no deploy, keeps
`pollstats.csv` intact, and leaves the whole apparatus in place if a future incident makes the question
live again. Deleting the code is not worth the diff.

⚠️ If it is ever restarted, fix the hourly rebase first: `rollHourIfNeeded` subtracts `min_all` from
`inflight_all` at each boundary, and with `min_all` structurally 1 that decrements the live counter by
one every hour whether or not anything leaked. It is self-limiting (`dec()` floors at 0) and did not
distort this corpus, but it would mask a genuine one-per-hour leak.

### Two things settled along the way, worth keeping

- **The submit path does retry a 503.** Item 6's ⚠️ said to check this before shipping any cap.
  `submitGamedata` at [ajaxInterface.js:338](source/public/client/ajaxInterface.js#L338) goes through
  `ajaxWithRetry` with no `maxAttempts` override, so it takes the default **5** attempts against
  `retryCodes = [503, 507]` with exponential backoff (~400/800/1600/3200 ms). A shed turn submission
  would have retried, not been lost. The blocking overlay stays up across the retries, so the player
  would have seen a longer "TRANSMITTING ORDERS" and nothing worse.
  (Note there is a *second* `ajaxWithRetry` at line 301 that retries only 507 — it is inside a
  `/* … */` block and is dead. Don't read it as the live one.)
- **The original fault statement is right but incomplete.** Item 6 said the guard "constrains cheap
  pages while `gamedata.php` runs with no concurrency cap at all". True — but the pages it constrains
  are not all cheap, and the ones it exempts are not all polls. That is now item 9.

---

## Item 6a — the instrument ✅ BUILT 2026-09-01 · ✅ RAN ON LIVE · ✅ REPORTED 2026-09-17

> **It worked, and it earned its keep.** 336 hours of live data (2026-09-02 → 2026-09-17), 44,363
> requests, no leak, no 5xx. It killed item 6 outright and supplied the duration distribution that
> killed item 8 as well. The results and their reading are in item 6 above; what follows is how it was
> built, which is still worth having if it is ever restarted. **Turn it off with the kill switch** —
> see "What to do with the instrument" in item 6.

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

## Item 9 — is `server_load_guard.php` still fit for purpose? (REVIEW, 2026-09-17 — nothing changed yet)

**Why this is being asked now.** The guard was written for a condition that no longer exists. It went
in when FieryVoid had just moved hosts and **APCu was not yet enabled**, so the site was throwing
"too many threads" errors and the guard was deliberately made very strict. APCu is now on, and with it
the whole `BlueprintCache` / gamedata JSON cache / fast-poll layer. Item 6a has since measured the
traffic the guard is nominally protecting against. This is the review of whether its settings still
describe reality.

**Summary: it is not doing harm, and it is not doing much good either.** Nothing in two weeks of data
came within an order of magnitude of any of its thresholds. What it *does* have are three
specification problems that would matter the day it ever engaged — which is to say, during an
incident, which is the only time anyone would find out.

### What it actually limits today, which is not what the list looks like

Matching is `strpos($script, $ks) !== false` — substring, not filename. Deriving the true exempt set
from the code rather than reading the array:

| exempt | why |
|---|---|
| `chatdata.php` | listed — correct, it is the cheapest path in the app |
| `gamedata.php` | listed — the poll |
| `gamelobbyloader.php` | listed — the poll |
| `allgames.php` | listed |
| `games.php` | listed — but this is a **page load**, not a poll |
| `gamelobby.php` | special-cased — also a **page load** |
| `guard_debug.php` | listed — the diagnostic, correctly exempt |
| **`recentgames.php`** | **not listed — exempt by accident, because its name contains `games.php`** |

Everything else is limited, and that set includes **`game.php`** — by some distance the heaviest
request in the app: a full heavy gamedata build, plus `BlueprintCache`, plus ~1.6 MB of inlined
blueprint JSON, plus md5 and gzip over the assembled document. Also `replay.php`, `saveFleet.php`,
`loadSavedFleet.php`, `slot.php`.

So the shape is upside down relative to cost: **the endpoint with no cap (`gamedata.php`) is cheaper
than the one with a cap (`game.php`), and three page loads are exempt while the heaviest page is not.**
Item 6's framing — "the guard constrains cheap pages while `gamedata.php` runs with no cap" — had the
right instinct and the wrong example.

`recentgames.php` being exempt by substring accident is the kind of thing that is harmless right up
until someone adds `deletegames.php` or `archivedgames.php` and silently exempts it too.

### Three specification problems, in the order they would bite

**1. ⭐ The spin holds an lsphp worker for a full second before shedding.**

```php
do {
    $count = apcu_fetch($keyGlobal);
    if ($count === false || $count < $maxGlobal) { ... break; }
    usleep(50000);
} while ((microtime(true) - $start) < 1.0);
if (!$globalAcquired) { header("HTTP/1.1 503 ..."); exit; }
```

Under contention a request sleeps in 50 ms steps for up to a second and *then* 503s. On LiteSpeed the
scarce resource is the worker pool ([[reference_fv_live_litespeed]]), so a genuine pile-up is converted
into *more* workers held, each doing nothing, for a second each — and the client is told to go away
anyway. If the intent is to shed load, shedding immediately is strictly better; the waiting room only
helps if slots free up quickly, and a request that is holding a slot is by definition one that has not.

**2. ⭐ If the global key expires mid-loop, the request is guaranteed to 503 for no reason.**

`apcu_add($keyGlobal, 0, $ttlGlobal)` runs **once, before** the loop. `apcu_cas()` fails on a key that
does not exist, and nothing inside the loop recreates it. So if the key expires between the `apcu_add`
and a later iteration, every subsequent `apcu_fetch` returns `false`, every `apcu_cas` fails, and the
request spins the full second and 503s — while the counter is, in truth, empty.

The window is microseconds under no contention (the loop breaks on its first iteration). But under
contention a request sits in that loop for up to a second against a 30-second TTL, so the exposure is
roughly **1 in 30 — precisely when the limiter is engaged**. Latent today because nothing reaches the
limit; a real bug the first time anything does.

**3. The global counter silently resets every 30 seconds.** `apcu_add` sets the TTL only on creation
and `apcu_cas` does not refresh it, so `..._server_active_requests` expires 30 s after it is created
regardless of traffic, and the count restarts at 0. In-flight requests then decrement a counter that
knows nothing about them (floored at 0 by the guard's own check). The effect is that the limiter
systematically **under**-counts — which also means it self-heals any leaked slot, so this one is closer
to an accidental feature than a bug. Worth knowing before anyone tunes `$maxGlobal` against observed
values from `guard_debug.php`: those values are a floor.

> **Both 2 and 3 were verified against real APCu in the php container, not reasoned from the docs:**
>
> ```
> apcu_cas('k', 0, 1) on missing key => false        (and the key still does not exist)
> apcu_add on an EXISTING key        => false        (no-op — does NOT refresh the TTL)
> apcu_cas on an existing key        => value bumped, TTL untouched
> at 2.2s against a 2s TTL           => key gone, despite add+cas at 1.2s
> ```
>
> So the loop genuinely cannot recover from a vanished key, and the counter genuinely expires from
> **creation** rather than from last use.

Two smaller notes, neither worth acting on alone: the IP counter does `apcu_inc` then
`apcu_store($keyIP, $ipCount, 20)`, which is a lost-update race under concurrency (and the `store` is
what refreshes that TTL, so the IP limiter does not have problem 3); and `guard_debug.php?clear`
clears the IP keys but not the global one, so it cannot unstick the counter it most usefully would.

### Is it still fit for purpose?

**Its original purpose is gone.** It was sized for a pre-APCu site that was falling over. Every layer
that made that site fragile has since been built: the gamedata JSON cache, the fast poll, the chat
watermark piggyback, `BlueprintCache`. 85% of gamedata requests now finish in under 25 ms.

**Its current purpose is unclear, and the measurements do not supply one.**

- Against the **database**: item 0 established FieryVoid is capped at 100 connections by
  `max_user_connections`, and was refused with `1040` (instance-wide) not `1203` (per-account), so it
  was under its own cap when it failed. Item 6a now bounds the peak: **4 concurrent gamedata requests
  in two weeks**. Even adding page loads generously, FieryVoid is nowhere near 100. A 23-slot request
  limiter is not what stands between the app and the connection pool.
- Against the **lsphp worker pool / LVE memory limit**: this is the constraint that is actually real on
  live — but it is a **per-process memory** limit, and a concurrency counter does not address it. One
  `game.php` load on a 130-ship game can trip it on its own, with the guard reporting full headroom.
  (That is the failure mode `arch_static_generator_streaming` was written about.)
- Against **abuse / a scraper**: the per-IP limiter is the part with an ongoing rationale, and `$maxIP
  = 20` concurrent non-poll requests from one address is generous but sane. Worth keeping.

**Verdict: keep the per-IP limiter, and the global limiter is doing nothing measurable.** It should
either be given a threshold that means something, or have its two bugs fixed so it behaves correctly on
the day it engages. What it should *not* do is stay as it is on the assumption that it is protecting
something — the evidence is that it is not.

### Options, cheapest first — **option 1 BUILT 2026-09-17; 2–5 still need a decision**

Option 1 is done (see "Option 1, as built" below). Options 2–5 have **not** been implemented. The guard
is the one file in this plan flagged as able to take the game down if got wrong, and each of those
changes live load-shedding behaviour.

1. ✅ **BUILT.** **Fix problem 2 only** (re-`apcu_add` inside the loop). Pure bug fix, no threshold change, strictly
   fewer spurious 503s. The safest thing on this list and the only one with no behavioural downside.
2. **Drop the spin** — try once, and on failure serve the 503 immediately. Frees a worker a second
   earlier per shed request. Client-side this is invisible: `ajaxWithRetry` already backs off 503s.
3. **Add `game.php` and `replay.php` to the exempt list, and remove `games.php` / `gamelobby.php` /
   `allgames.php` from it.** Makes the exemptions describe *polls*, which is what the variable
   `$isKnownPoll` claims. ⚠️ This is the one that inverts real behaviour on live: it uncaps the app's
   heaviest page and caps three pages that currently run free. Do not do it without deciding what the
   cap is *for* first.
4. **Anchor the matching** — compare `basename($script)` against an exact list instead of `strpos`.
   Removes the `recentgames.php` accident and the trap for future filenames.
5. **Raise `$maxGlobal`.** There is no evidence-based number to raise it *to*; item 6a never
   instrumented the non-exempt population, so the limited set is the one part of the app with no data.
   If the global limiter is to be kept and tuned rather than fixed or dropped, that measurement comes
   first — the same discipline item 6a applied to `gamedata.php`, and it is the reason item 6 ended in
   a defensible "no" instead of a guessed cap.


### Option 1, as built ✅ 2026-09-17

One line moved, from above the `do` to the top of the loop body, plus the comment explaining why it
has to live there. `$maxGlobal`, `$maxIP`, the 1-second budget, the 50 ms sleep and the exempt list are
all **untouched** — this changes what happens when the counter's key vanishes, and nothing else.

```php
do {
    apcu_add($keyGlobal, 0, $ttlGlobal);   // ← was above the loop, where it could not help
    $count = apcu_fetch($keyGlobal);
    if ($count === false || $count < $maxGlobal) {
        if (apcu_cas($keyGlobal, (int)$count, (int)$count + 1)) { $globalAcquired = true; break; }
    }
    usleep(50000);
} while ((microtime(true) - $start) < 1.0);
```

The `$count === false` branch is deliberately left in place. It is now nearly unreachable — the add
above it guarantees the key exists — but it still covers the microsecond race where the key expires
between the add and the fetch. In that case `apcu_cas` fails, the loop sleeps once, and the next
iteration's add rebuilds it. Worst case one 50 ms sleep, against the old worst case of the full budget.

**Problems 1 and 3 are untouched and still stand.** The spin still holds a worker for up to a second
before shedding (problem 1), and the counter still resets every 30 s because `apcu_add` on an existing
key does not refresh the TTL (problem 3) — that is *why* this fix is safe: the no-op add changes no
timing.

#### Verified — and the test discriminates

The BEFORE loop was taken verbatim from `git show HEAD:source/server/server_load_guard.php`, not
retyped, so the baseline is the committed code. Both variants were driven against real APCu in the php
container with the limiter **engaged** (counter at `$maxGlobal`, so the request must queue) and the key
pre-aged to a 1 s TTL then slept on for 0.9 s, so it expires ~0.1 s into a real 1.0-second budget.

| | BEFORE (HEAD) | AFTER |
|---|---|---|
| **1. limiter engaged, key expires mid-spin** | **503 after 1002.1 ms, 20 iterations** | **acquired at 200.4 ms, 5 iterations** |
| 2. quiet site, counter empty | acquired at 0.0 ms, counter → 1 | acquired at 0.0 ms, counter → 1 |
| 3. counter genuinely full, key not expiring | 503 after 1001.9 ms, 20 iterations | 503 after 1001.9 ms, 20 iterations |
| 4. one slot free below the cap | acquired at 0.0 ms, counter → 23 | acquired at 0.0 ms, counter → 23 |

Row 1 is the bug and it is fixed. **Rows 2–4 are identical in both columns**, which is the part worth
keeping: case 3 in particular proves the fix does not weaken the limiter — a request that *should* be
shed is still shed, at the same moment, for the same reason.

⚠️ **`fvbuild.ps1 -Check` does not exercise this and cannot.** The guard's first statement is
`if (PHP_SAPI === 'cli' || !apcu_enabled()) return;`, so the replay harness never reaches the limiter.
The gate was run and is green (autoload current, ship-data validator clean, replay harness 133 passed /
0 failed), but it is a regression check on everything else — the table above is the evidence for this
change.

⚠️ **The item 6 lesson applies to the guard itself.** A limit chosen without a measurement of the
population it governs is how you get a threshold that is invisible until the worst possible moment.
`$maxGlobal = 23` and `$maxIP = 20` have never been checked against live numbers; they were chosen for
a site that no longer exists.

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
