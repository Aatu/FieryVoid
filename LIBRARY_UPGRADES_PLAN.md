# Library Upgrades — PHP, jQuery, THREE, React

A version review of the four libraries Fiery Void is built on, with a plan for the ones that are behind
and a straight answer on whether each upgrade is worth doing.

Status (2026-10-08):
- **Review done.** Everything below was measured, not inferred: throwaway PHP 8.2/8.4/8.5 containers with
  the repo mounted read-only, scratch builds of the THREE shim and the React bundle against newer versions
  (written to a temp folder), and a read-only fetch of the live UI bundle.
- **Live PHP is already 8.4.24** (LiteSpeed LSAPI, alt-php; confirmed by you). That makes most of the
  original PHP plan unnecessary, and it turns the `round()` item (§2.4) into a bug that is live now.
- **Committed (cef9d634d):** local Docker on `php:8.4-fpm` (§2.1) and the React build-mode config (§5.1).
- **BUILT 2026-10-08, uncommitted:** the `round()` fix on both sides (§2.4, options A and B), the pollStats fix (§2.3 #1),
  React 19.3 (§5.2) and THREE r186 with its two fixes (§4). Verification for each is in its section.
- Still open: §6.

## 1. Verdict at a glance

| | FV runs | Current upstream | Verdict |
|---|---|---|---|
| **PHP** | live 8.4.24; local now 8.4.26 | 8.5.11 (8.6 is at RC3) | **Done** — local matches live. Two follow-ups (§2.3); 8.4 is supported to 31 Dec 2028 |
| **jQuery** | 4.0.0 + jQuery UI 1.14.2 | 4.0.0 (Jan 2026) + UI 1.14.2 (Jan 2026) | **Up to date** |
| **THREE** | **r186** (0.186.1) | r186 (0.186.1, Sep 2026) | **Upgraded** — two fixes; pixel-identical to r160 (§4) |
| **React** | **19.3.0** | 19.3.0 (Sep 2026) | **Upgraded**, with the build mode fixed (§5) |

## 2. PHP — live is on 8.4, local now matches

### 2.1 Local Docker moved to 8.4 — BUILT 2026-10-08
`docker/php/Dockerfile:1` is now `php:8.4-fpm`. It tracks the latest 8.4.x patch, so it's 8.4.26
locally against live's 8.4.24; patch releases are bug fixes only. Verified after the rebuild:
- apcu, mysqli, zip, curl, mbstring and OPcache are all loaded; memory_limit, APCu and timezone settings
  are unchanged (they come from the bind-mounted `docker/php/php.ini`).
- `fvbuild -Check` gives exactly the 8.2 result: class map up to date, validator PASS (0 new findings),
  replay harness 118 passed with the same five known-bad games.
- game.php (game 4444) and gamelobby.php (lobby 4448) load as real players with no PHP deprecations
  logged.

Recreating the container wiped its `/tmp`, so local logins (session files) were reset.

### 2.2 What was tested before that

| Test | 8.4 | 8.5 |
|---|---|---|
| `php -l` on all 2,863 FV PHP files (random_compat excluded: a PHP 5 polyfill that loads nothing on 7+) | clean | 2 deprecations, both in `Shuttle.php` |
| Replay harness, full corpus | **identical to 8.2** | identical once fixes 3 and 6 below are in |
| phpab class map | identical to the committed map | identical |
| Grep for functions deprecated in 8.3–8.5 | 1 hit | 3 more |
| Harness wall time, same fresh image, two runs each | 40.5 s (8.2: 41.0 s) | 41.0 s |

**Why deprecations matter more in FV than elsewhere.** `global.php:23` and `Manager.php:5` turn every
diagnostic, E_DEPRECATED included, into an ErrorException; Manager's handler ignores
`error_reporting()` altogether. A deprecation that is a log line in most PHP apps is a failed request
here. That includes compile-time ones: under 8.5, a `case 'x';` throws out of the `require`.

### 2.3 Code changes

| # | Where | Change | Status |
|---|---|---|---|
| 1 | `source/public/pollStats.php:36` | `str_getcsv($line)` → `str_getcsv($line, ',', '"', '\\')` — same behaviour, escape now explicit | **DONE 2026-10-08.** Was a live bug: the page threw on 8.4 once `logs/pollstats.csv` had rows. On the container's 8.4 the old call throws under FV's handler; the new one parses a pollstats row identically |
| 2 | `source/public/client/mathlib.js:394` + `source/server/lib/HexZone.php:63` | the rounding fix, §2.4 | **A and B DONE 2026-10-08** (A fixed a live bug; B makes it PHP-version-proof) |
| 3 | `source/server/model/ships/Shuttle.php:95`, `:97` | `case '…';` → `case '…':` | before an 8.5 move |
| 4 | `source/server/lib/Debug.php:75` | `->attach($e, $logid)` → `->offsetSet($e, $logid)`. On 8.5, `Debug::error` would log and then throw, so every caught-and-reported error becomes a second, uncaught one | before an 8.5 move |
| 5 | `source/server/lib/DiscordNotifier.php:242` | delete `curl_close($ch);` (a no-op since 8.0); on 8.5 it breaks turn notifications | before an 8.5 move |
| 6 | `tests/replay/*.php` — 60 calls in 20 files, including `replayHarness.php:844` and `:867` | delete `->setAccessible(true)` (a no-op since 8.1) | before an 8.5 move (test tooling only) |
| 7 | `docker/php/Dockerfile:1` | `php:8.2-fpm` → `php:8.4-fpm` | **done** |

### 2.4 The round() change — a live mismatch today
PHP 8.4 removed `round()`'s "pre-rounding". `HexZone::line()` — used by the EDF targeting corridor
(`TacGamedata.php:2628`) and by `specialWeapons.php:11795` — rounds interpolated cube coordinates with
`round()`. The client copy, `mathlib.hexLine()`, uses a `phpRound()` that deliberately copies 8.2's
pre-rounding. Live runs 8.4, so on lines through an exact hex corner the client preview and the server
already disagree.

Measured on 100,000 seeded lines:

| | agrees with the 8.2 server | agrees with the 8.4/8.5 server |
|---|---|---|
| Server on 8.4/8.5 | 106 lines differ | — |
| JS mirror today | all 100,000 | **106 differ** |
| JS mirror with the pre-round step removed | 106 differ | all 100,000 |
| Server using the explicit helper below (on 8.2, 8.4 and 8.5 alike) | 106 differ | all 100,000 |

- **A — minimum fix:** remove the pre-rounding step from `phpRound()` (one line plus its comment). This
  makes the client match live, and local now that it's on 8.4.
- **B — A plus future-proofing:** also replace the three `round()` calls in `HexZone::cubeRound()` with
  the helper below. The server's answer then no longer depends on PHP's `round()` at all, so a future PHP
  change can't silently split client and server again. It matches native 8.4 `round()`, so it changes
  nothing on live today.

**Option A BUILT 2026-10-08.** `phpRound()` is now just `v < 0 ? -Math.round(-v) : Math.round(v)`, with
the comment rewritten. Against a fresh 100,000-line corpus generated on live's exact PHP (8.4.24), it
agrees with the server on all 100,000; against 8.2 it differs on the expected 106. The legacy watcher
rebuilt `game.legacy.bundle.js` with it. The Walkers plan and memory note carry a dated update.

**Option B BUILT 2026-10-08.** `HexZone::cubeRound()` calls a private `roundHalfAwayFromZero()` instead of
`round()`, and the file header records this as the one deliberate exception to its "verbatim move" rule:

```php
private static function roundHalfAwayFromZero($x) {
    if ($x < 0) return -self::roundHalfAwayFromZero(-$x);
    $f = floor($x);
    return ($x - $f >= 0.5) ? $f + 1 : $f;
}
```

⚠️ This replaces the version drafted earlier in this plan, which subtracted `floor()` from negative
values directly. Just above −0.5 that subtraction is inexact: −0.49999999999999994 came out at −1, where
8.4's `round()` and the JS mirror give 0. The 100,000-line corpus never hit that value, but the edge test
below did. Rounding |x| keeps the subtraction exact, and it is the same form as the JS mirror.

Verified:
- `php -l` is clean on 8.2, 8.4.24 and 8.5.
- Edge test of 102,184 values (every double within ±4 bit-steps of each .5 and whole number from −60
  to 60, plus 100,000 random ones): the helper matches native `round()` on 8.4.24 and 8.5 on all of
  them, and matches the JS mirror on all of them. Its output is byte-identical on 8.2, 8.4 and 8.5.
- The 100,000-line corpus through the real edited `HexZone::line()` is byte-identical on 8.2, 8.4.24 and
  8.5, and identical to live's current output.
- `fvbuild -Check`: class map up to date, validator PASS, harness unchanged apart from game 4229, which
  the 3-month cleanup had deleted (§7).

The JS comment now names the server helper as its pair: change one, change both.

### 2.5 The next hop (8.5 or 8.6)
8.4 is supported to the end of 2028, so there's no hurry. When the time comes, do fixes 3–6 first; they
work on 8.4 too. Also budget for 8.5's new deprecation of `null` as an array key, which fires even in
`isset()`, `??` and `array_key_exists()`. With FV's handler, any request where a key happens to be null
would fail. That depends on the data: grep can't find it, and the harness only covers game logic (it
found none there). The safe route is to run the local Docker on the new version for a while before
switching live.

## 3. jQuery — up to date
`client/lib/jquery-4.0.0.min.js` and `jquery-ui-1.14.2.min.js`, self-hosted, are the current releases.
Nothing to do. Housekeeping only: `client/lib/jquery-ui-1.8.15.custom.min.js` (211 KB, from 2011) was
dead, with a stale skip entry at `scripts/bundle-legacy.js:73` as its only mention. **Deleted 2026-10-08** (see §7).

## 4. THREE — r160 → r186

### 4.1 Findings
- All 59 symbols in `three-global-shim.src.js` still exist in r186, and the shim builds with no warnings.
  An r160 build made the same way comes out at exactly the shipped 498.6 KB, so the comparison is
  like-for-like.
- Size: 498.6 → 565.0 KB raw, 126.8 → 141.8 KB gzipped (+15 KB gzipped). That is now more than covered
  by the React build-mode saving (§5.1).
- Of the changes listed in the r161–r186 migration notes, three affect FV:
  - **r163 turns the stencil buffer off by default.** `webglScene.js:82` builds
    `WebGLRenderer({ alpha: true, antialias: true })`, and `DeploymentIcon.js:25` uses the stencil to cut
    holes in deployment zones. The holes would silently stop being cut. Fix: add `stencil: true`.
  - **r169 always generates mipmaps when `generateMipmaps` is true.** `PlainSprite.js:44` sets a
    `LinearFilter` but leaves `generateMipmaps` on, which would mean wasted work. Fix:
    `texture.generateMipmaps = false`, as `BallisticIconContainer.js:2102` already does.
  - **r163 drops WebGL 1.** Devices without WebGL2 lose the map (very few in 2026).
- Checked and clear: `updateRange`, `encoding`/`outputEncoding`, legacy lights, render targets,
  `ShaderChunk` and `onBeforeCompile` are not used at all. The custom shaders only include
  `colorspace_fragment` and `tonemapping_fragment`, which both still exist. Multiply/Subtractive
  blending is never set on a material. `ImageBitmapLoader` is used through its callback, so the r184
  change doesn't matter, and `TextureLoader.load()` still returns the texture.
- The memory note saying "don't bump THREE, the offscreen render target is r160-calibrated" refers to a
  `BloomPipeline.js` that isn't on any local branch, so it doesn't block anything today.

### 4.2 Is it worth it?
Players gain nothing visible. FV draws sprites, lines and a few shader materials in about 5 draw calls,
and three's work since r160 has gone elsewhere (WebGPU/TSL, PBR, loaders, XR). The case for doing it is
upkeep: the fix list is known and short now, and being on a maintained release means that a future
browser or driver bug costs a small bump rather than a 26-release jump made under pressure. The cost is
a visual QA pass across deployment zones, weapon FX, EW links, ballistics, the hex grid, the starfield
and replay. That is the same size whether you jump 1 release or 26, which is why chasing every release
isn't worth it either.

### 4.3 BUILT 2026-10-08 (after React)
- `three` ^0.186.1 in `package.json`, `yarn.lock` and `package-lock.json`. `yarn build:three` produces a
  565 KB shim (141 KB gzipped).
- `webglScene.js:84`: `stencil: true` on the `WebGLRenderer`. `PlainSprite.js:47`:
  `texture.generateMipmaps = false`. Both carry a one-line reason, and both also work on r160.
- Visual A/B, done headless as player 210 against the real local site with non-GET requests blocked and
  `Math.random` seeded. Only the shim differed between runs: the committed r160 shim was served in place
  of the new one at the network level.

  | Scene | r160 vs r186 | r160 vs r160 (noise) |
  |---|---|---|
  | Deployment, mine zone with its stencil hole (game 4434) | 0 px (max delta 1/255) | 0 px (max delta 1) |
  | Initial Orders (4444) | 0 px | 0 px |
  | Movement, 26 units (4447) | 0 px | — |
  | Firing replays (4372, 4069), frames after the effects finish | ≤187 px, max delta 12 | 516 px, max delta 17 |

  During the effects themselves, frames differ because the FX run in real time; there were no console
  errors, and the end-of-replay renderer stats (draw calls, programs, textures) were identical.
- **Negative control:** r186 with the stencil forced off paints the mine zone straight over the hole (7%
  of the screen changes). That proves the A/B can see the r163 regression, and that the fix removes it.

## 5. React

### 5.1 Build mode — FIXED 2026-10-08 (uncommitted)
`vite.config.js` defined `'process.env': {}` (a polyfill), and Vite's library mode leaves
`process.env.NODE_ENV` for the consumer to set. So every React package check in the bundle compiled to
`var MD={};MD.NODE_ENV==="production" ? production : development`, which always picked development. Live
players were running development React plus styled-components' development mode, and the bundle carried
both builds (confirmed in the file live serves).

Fix: `'process.env.NODE_ENV': JSON.stringify('production')` added to `define` in `vite.config.js`, then
the bundle was rebuilt with `vite build`.

| `UI.bundle.js` | raw | gzip |
|---|---|---|
| Before (React 18, development at runtime) | 787.1 KB | 226.7 KB |
| **After (React 18, production)** | **452.1 KB** | **123.8 KB** |
| For reference: React 19.3, production | 530.3 KB | 146.4 KB |

Verified: the bundle has no `NODE_ENV` checks or dev-only text. A headless run as real players on the
local site, with all non-GET requests blocked, behaved as follows:
- game.php mounted every React panel, opened React ship windows (Omega, G'Quan), and showed system info
  when a thruster was hovered.
- gamelobby.php loaded `UIManager`.
- There were no console errors, warnings or exceptions, and the screenshot shows normal styling.

Notes:
- Live gets this once `UI.bundle.js` and `vite.config.js` are deployed.
- `yarn watch` (`vite build --watch`) now also builds production React, so React's dev warnings no longer
  appear locally. If you want them back, add `--mode development` to the watch script and make the
  define depend on the mode — but then be careful not to commit a watch-built bundle.

### 5.2 Version 18 → 19
- 18.3.1 is the last 18.x and has had no patches since April 2024. The serious React vulnerabilities of
  the past year (CVE-2025-55182 and the follow-ups) were all in React Server Components. FV's React is
  client-only and unaffected.
- Compatibility: 55 files, 46 class components, no hooks, `createRoot` already, the automatic JSX runtime
  already, and none of React 19's removed APIs. The UI builds cleanly against 19.3 with **no code
  changes**.
- Gain: nothing direct. Everything 19 adds is for function components and hooks. The real value is
  staying on the maintained line, so that a library that later needs 19 doesn't force the upgrade at a
  bad moment.
- Cost: +23 KB gzipped over React 18 in production mode, which the build-mode fix more than pays for,
  plus the same headless smoke run used for §5.1.
- **BUILT 2026-10-08.** react and react-dom are ^19.3.0 (scheduler 0.28) in `package.json`, `yarn.lock` and
  `package-lock.json`; no source changes. `UI.bundle.js` is 530 KB / 146 KB gzipped, production-only.
- Verified headless as real players with non-GET requests blocked:
  - game.php in Initial Orders (4444) and Movement (4447): ship windows, including a fighter flight and
    mines, system-info hover, click, right-click and `closeAll`.
  - gamelobby.php (4448): loaded the Narn store and opened a JaStat ship window with a Twin Array
    tooltip.
  - No console errors or warnings, **including with a React 19 development build swapped in** at the
    network level, which is where React 19 would print its deprecation warnings.
  - Click and right-click behaviour matches the React 18 bundle exactly.

## 6. Decisions for you
1. ~~Round fix~~ — A and B done.
2. ~~pollStats~~ — done. ~~React 19~~ — done. ~~THREE r186~~ — done.
3. **Fixes 3–6** now, or when an 8.5 move comes up?
4. **Deploying:** the deploy copy needs `yarn install` before `yarn build` to pick up React 19 and three
   r186. Without it, the build silently ships React 18 / r160, which is still correct: every code change
   here also works on the old versions.

## 7. Noticed, not in scope
- **The local replay corpus is eroding.** `DBManager::getGamesToBeDeleted()` deletes any game where a
  player has been inactive for 3 months, and it runs on every game.php load. Game 4229 went this way
  during today's tests, as had 4213–4227 before it. As of 2026-10-08, **45 of the 137 corpus games** (ids
  4069–4277) cross that line within 30 days, and the harness can't replay a deleted game. Their baselines
  stay on disk, but the coverage goes.
  **Fixed 2026-10-08 (local only):** `docker/php/varconfig.php` sets `$keep_idle_games = true`, and
  `getGamesToBeDeleted()` then skips the 3-month rule. Idle LOBBY games still go after 5 days. Live's
  varconfig doesn't set the flag, so live deletes as before.
- Dead vendored files: `client/lib/three.min.js` (670 KB) and the jQuery UI 1.8.15 file above.
  `THREE.MeshLine.js` is still loaded by `game.php:133`, but its only user, `LineMeshSprite.js`, is
  never instantiated — and `MeshLineMaterial` would throw if anything did call it
  (`THREE.Material.call(this)` on an ES6 class).
  **Deleted 2026-10-08:** all four files (`LineMeshSprite.js` was loaded by no page at all), the
  `game.php` script tag and both stale `bundle-legacy.js` skip entries. Both legacy bundles' script lists
  are unchanged (151 / 53). A real game.php load on games 4444 and 4430 made no MeshLine request and
  logged no errors.
- Local MariaDB is 10.3.39 (end of life May 2023) and nginx is 1.15. Both are local only; the live DB
  version is recorded nowhere.
  **MariaDB done 2026-10-08.** Live is **11.4.5-MariaDB-log (FreeBSD Ports)**, `utf8mb4` /
  `utf8mb4_unicode_ci`, and its sql_mode starts with the built-in default (only the first part was shown).
  `docker/mariadb/Dockerfile` now uses `mariadb:11.4` (11.4.13) with that charset and collation, and
  `MARIADB_AUTO_UPGRADE=1`. Steps taken:
  1. Dumped B5CGM to `C:\FV_env\db_backups\B5CGM_mariadb-10.3.39_2026-10-08.sql` (SHA-256 checked
     after the copy) and copied the whole 10.3 volume to `fieryvoid_mariadb_data_10_3_backup`.
  2. Started on a fresh volume (`emptyDatabase.sql` still loads on 11.4) and restored the dump.
  3. A per-table fingerprint (rows, CRC of every column, schema, indexes) matched 10.3 on all 31 tables.
     The only differences were the database default (latin1 → utf8mb4, which nothing uses since every
     `CREATE TABLE` names its charset) and one auto-increment counter the harness had moved.
  4. The replay harness gave the same output line for line (97 passed / 20 failed both times), and
     game.php and games.php load cleanly.
  5. An in-place `MARIADB_AUTO_UPGRADE` on a copy of the 10.3 volume also gave identical data. That
     covers the DouglasChanges stack and anyone else still on a 10.3 volume.
  6. All 252 column names still parse unquoted. The container now has only the `mariadb*` commands.
  **Then closed the remaining gaps from a second live query.** Live's sql_mode is exactly the default
  and `character_set_collations` is empty, as local already was. Its clock is `CEST` / `SYSTEM`, and the
  database default is `utf8mb3_general_ci`. Changes: the image is pinned to `mariadb:11.4.5` (the user's
  edit), `ENV TZ Europe/Warsaw`, and `db/emptyDatabase.sql` creates B5CGM as `utf8mb3_general_ci`.
  Rebuilt on a fresh volume from a dump of the 11.4.13 DB
  (`db_backups\B5CGM_mariadb-11.4.13_2026-10-08.sql`). The fingerprint matched in a UTC session, since the
  one TIMESTAMP column renders per session timezone. Harness output identical, pages load. Every
  server variable queried on live now matches. Stored DATETIMEs from before the switch were written in
  UTC, so they read 2h older. All time logic is SQL `NOW()` against values written by `NOW()`, and no
  snapshot carries a timestamp, so nothing else is affected.
  **Replay harness:** the 20 failures were never a regression. 5 games are the 10-03 Kelly Phaser rework,
  which was never re-recorded. The other 15 are games played on after the 10-01 recording: 13 had player
  210 commit the current phase between 21:00 and 21:02 local on 10-08, and 4255/4256 had ships moved. The
  harness only treats a game as "advanced" when its turn or status changes. Re-recorded those 20 plus the 8
  "advanced" SKIPs, merging the manifest; the old baseline is kept at
  `db_backups\replay_baseline_before_rerecord_2026-10-08`. `fvbuild -Check` now passes: 125 passed / 0 failed,
  plus 7 SKIPs for idle-deleted games.
- Local Node is 23.8.0, an odd-numbered release out of support since mid-2025. Node 24 LTS is the
  natural choice if the toolchain is touched.
  **Done 2026-10-08:** Node 24.20.0 LTS (official MSI via `winget install OpenJS.NodeJS.LTS`, replacing
  23.8.0 in place; npm 11.19.0, Yarn 1.22.22 unchanged). `yarn build` gives byte-identical bundles on 23 and
  24 (all four, by SHA-256). No `yarn install` was needed. The README's Node/Yarn steps now say Yarn 1:
  its `corepack prepare yarn@stable` line activated Yarn 4, which rewrites the v1 `yarn.lock`, and Node 25+
  has no Corepack anyway.
- nginx 1.15 stays, and LiteSpeed in Docker was judged not worth it (2026-10-08). OpenLiteSpeed only reads
  the rewrite rules in `.htaccess`. LiteSpeed Enterprise reads all of it but needs a licence. Neither
  reproduces what has actually bitten FV on live: the host's lsphp memory limit, CloudLinux limits and the
  nginx proxy in front. `fieryvoid.eu/testInstance/` already runs that real stack, so use it for
  server-layer checks. The cheap local step is matching live's PHP limits (local `memory_limit` is 2048M).
- Vite 5.4.21 is out of support (current: 8.3.4). FV only runs `vite build`, so 2025's dev-server CVEs
  don't apply.

## 8. Reproducing the evidence
- PHP images: `fv-phpcheck:<v>` is `php:<v>-cli` plus `docker-php-ext-install mysqli`. Harness on
  another version:
  `docker run --rm --network fieryvoid_default -v C:/FV_env/FieryVoid:/usr/src/current:ro fv-phpcheck:8.5 php -d memory_limit=2048M -d date.timezone=Europe/Helsinki /usr/src/current/tests/replay/replayHarness.php check`
  (Git Bash needs `MSYS_NO_PATHCONV=1`). Its stderr carries `FV_DEBUG` lines that the fpm container
  sends to its log file; filter them before diffing.
- Lint: `find … -name '*.php' -print0 | xargs -0 -n1 -P8 sh -c 'php -l "$0"; exit 0'`. The
  `exit 0` matters — xargs aborts the whole run on PHP's exit status 255.
- Rounding: 100,000 `mt_srand(20261008)` lines, `q∈[-45,45]`, `r∈[-30,30]`, ends within ±30 of the
  start, compared hex for hex.

## Sources
- [PHP supported versions](https://www.php.net/supported-versions.php) ·
  [PHP 8.4 other changes (round())](https://www.php.net/manual/en/migration84.other-changes.php)
- [three.js migration guide](https://github.com/mrdoob/three.js/wiki/Migration-Guide)
- npm registry (`react`, `three`, `jquery`, `jquery-ui`, `vite`, `styled-components`) for versions and dates
- [CVE-2025-55182 (React Server Components)](https://osv.dev/vulnerability/CVE-2025-55182)
