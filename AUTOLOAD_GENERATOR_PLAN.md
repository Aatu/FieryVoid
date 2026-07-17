# Autoload Generator — Implementation Plan

Roadmap item 3 (2026-07 dev roadmap, Tier 1): stop hand-maintaining
`source/autoload.php` (4,134 lines) and generate it from the source tree,
run like `generateStaticShipFile.php`.

Designed 2026-07-14. Status: **BUILT 2026-07-15** — all acceptance criteria
pass (idempotent generation, stale-gate proven both ways, statics regenerated
clean, replay harness 167/167). Awaiting user smoke test (lobby/game/replay)
and commit.

Implementation deviations from the plan (2026-07-15):

- **Triage closed by the user**: all 20 §1d decision ships got `variantOf`
  sentinels (VreeSalvageZorth was instead deliberately made playable and
  registered, commit 90db360c9). So everything on disk just registers.
- **`NexusCraytan/CraytanLopin.php` deleted** (git rm), not class-stripped:
  by implementation time it declared *only* the duplicate `CraytanLopinb`
  (a stale near-copy of `CraytanLopinb.php`, which the active map points at
  and which stays). The real `CraytanLopin` lives in `NexusCraytan_old/` and
  is now registered.
- **`start.sh` no longer runs phpab at boot** (it kept composer install).
  Rationale: a boot-time regen into the served copy would mask a stale
  *committed* map during local testing — the opposite of what `-Check` wants.
  The committed map is authoritative; regeneration is an explicit dev action.
- **`composer.lock` regenerated** (was ancient PHP-7-era, blocked
  `composer install` on PHP 8.2). The repo-root `composer.phar` is still
  Composer 1; `autoload.sh`'s bootstrap therefore prefers a PATH `composer`
  or the image's self-updated phar (`/usr/src/fieryvoid/composer.phar`).
- **phpab writes map paths relative to the output file**, so the `-Check`
  byte-compare temp file must be generated *beside* the real map
  (`source/autoload.check.tmp`), not in `/tmp`.
- **`.gitattributes`**: `source/autoload.php` + `source/autoload_old.php`
  pinned `text eol=lf` (autocrlf would otherwise flip the checkout to CRLF
  and break the byte-compare; `*.sh` was already pinned).
- The dead hand-map key `'diffusertendrilFtr'` (uppercase F — could never
  match the runtime's `strtolower` lookup) is now correctly registered as
  `diffusertendrilftr`. Final numbers: 3,674 entries; 36 stale keys removed
  (each verified class-not-on-disk), 132 added, 0 path changes.

---

## 1. Why (and proof it's needed)

`source/autoload.php` is a hand-edited map of `lowercase classname =>
source-relative path` consumed by `spl_autoload_register` (included from
`source/public/global.php`). Every new ship/weapon/system class needs an
entry; forgetting one is a recurring bug class (the Kirishiac Orbitals
pending item was exactly this, as were the 2026-07-12 shuttle phpclass-typo
fixes).

A prototype scanner was dry-run against the real tree on 2026-07-14
(token-based declaration scan over the five mapped subtrees, compared to the
hand map). Findings — **all of these are live today**, re-measured after the
2026-07-14 hand stop-gap fixes:

| Finding | Count |
|---|---|
| Active map entries | 3,585 |
| Commented-out entries | 151 |
| Classes on disk, **not registered at all** | **84** |
| Classes on disk, only present as commented entries | 41 |
| Stale entries (class not found where the map says) | **37** — 25+ point at files that no longer exist (latent fatal `require` if ever requested) |
| Same lowercase class name in two files (collisions) | 8 |
| Wrong-path entries | 2 |
| Case-only path mismatches (would 404 on live Linux) | 0 ✅ |

**Already hand-fixed as a stop-gap (2026-07-14), confirmed clean by re-scan** —
kept here as the historical case *for* the generator, since every one of them
is a bug a scanner makes structurally impossible:

- `'graviticminehandler '` — trailing space in the key, so
  `GraviticMineHandler` was effectively unregistered.
- `'minecontrollerdew' => '/server/model/weapons/baseSystems.php'` — that
  file does not exist (class lives in `systems/baseSystems.php`).
- Typo pairs `shuttlegvalheru` / `shuttlegbarada` /
  `minesweepingshuttlemakarealy` / `fusiocutter` vs. the real class names.

Still open, to be resolved by the migration:

- `ContainmentBreach` (a critical class in `cricialClasses.php`) is
  unregistered; its siblings (`AmmoExplosion`, …) are registered.
- `CraytanLopin.php` defines **both** `CraytanLopin` and `CraytanLopinb`
  while `CraytanLopinb.php` also defines `CraytanLopinb` — duplicate class
  definition across files (works only because both are never loaded in one
  request).
- Duplicate keys inside the hand map itself (later literal silently wins) —
  impossible to spot by eye in 4k lines, impossible by construction in a
  generated file.

A generator eliminates the whole bug class and turns "add a ship" into
"create the file, run one command, review a one-line diff."

---

## 1b. ⚠️ PLAN-CHANGING DISCOVERY (2026-07-14): the generator already exists

While verifying the Docker paths, `docker/php/start.sh:15` turned up this:

```sh
vendor/bin/phpab -e autoload.php -o source/autoload.php source
```

…which is also the entire contents of `autoload.sh` at the repo root, and
`theseer/autoload` (**phpab**, the PHP Autoload Builder) is the **only**
dependency in `composer.json`.

**`source/autoload.php` was never hand-written. It is a phpab output that has
been hand-edited for years** — which is why it still carries phpab's own
header, `// this is an autogenerated file - do not edit`.

Verified live in the container (2026-07-14):

- phpab is installed and runs (`vendor/bin/phpab`).
- Run against the current tree it **fails hard**, with exactly the 8
  collisions my prototype scanner found:
  *"Multiple declarations of trait(s), interface(s) or class(es). Could not
  generate autoload map."*
- Add `-e '*_old.php'` plus two more excludes and it generates cleanly:
  3,674 entries, correctly sorted, **identical output shape to the current
  file** (same header, same `spl_autoload_register` + static array +
  `strtolower` + `require __DIR__ . $classes[$cn]`, same `true, false`).

**This almost certainly *is* the history of the bug**: someone added an
`_old.php` copy of a ship, phpab began failing on the duplicate class, and
rather than resolve the collision the map got hand-maintained from then on.
The tool has been sitting in `composer.json` failing silently ever since —
`start.sh` still runs it on every container start, but writes into the
container-local copy that the next rsync overwrites (see §1c), so nobody
ever sees it fail.

### Consequence: pivot the plan

**Do not write `generateAutoload.php`. Restore phpab instead.** It already
provides, natively, the things §2 was going to hand-roll:

| §2 design goal | phpab |
|---|---|
| token-accurate class scan | ✅ its whole job |
| deterministic, sorted, timestamp-free output | ✅ (verified: same shape as today's file) |
| **fail hard on collisions** | ✅ native, verified |
| file/dir exclusions | ✅ repeatable `-e <glob>` |
| lowercase-keyed map, `require __DIR__ . path` | ✅ identical to current runtime |

What phpab does **not** give us, and how to cover it:

- **`--check`** (is the committed map stale?) — trivial in `fvbuild.ps1`:
  generate to a temp path, byte-compare, exit 1 on difference.
- **`--verify`** (does every entry actually load?) — a ~15-line PHP script,
  *or* skip it: regenerating statics constructs every ship class and is a
  strictly deeper proof (§3.2).
- **Excluding a single class from a multi-class file** — phpab excludes
  *files*, not classes. This only matters for the ~41 currently-commented
  entries; most are one-class ship files (exclude the file) but a few are
  weapons inside `customNexus.php`. **Open question for triage**: confirm
  whether commenting the map entry was ever actually how a ship/weapon got
  hidden from players, or whether the lobby is driven purely by
  `shipLoader`'s directory scan — in which case those comments were never
  load-bearing and the entries can simply be registered.

The `-e`/exclusion list replaces the `source/autoload.exclusions.php` file
from §2.2. Keep the *spirit* of it — a single, commented, version-controlled
place listing every exclusion with a reason — by putting the phpab
invocation and its excludes in `autoload.sh` (or in `fvbuild.ps1`) with a
comment per exclude.

Sections 2.1–2.3 below are **superseded** by this and retained only as the
record of what was designed before the discovery. §2.4 (autoload_old.php),
§3 (fvbuild.ps1), §4 (migration) and §5 (usage) all still stand — the
migration triage and the collision decisions are needed either way; only the
*tool* changes.

---

## 1c. ⚠️ Docker path trap: `docker exec` does NOT land in the repo

This bites any script that shells into the php container, and it is why the
question "will statics go to the right place?" has a non-obvious answer.

In the **php** container there are two trees (`docker-compose.yml:18`,
`docker/php/Dockerfile:22`, `docker/php/start.sh:12,25`):

| Path | What it is |
|---|---|
| `/usr/src/current` | **bind mount of the host repo** (`c:\FV_env\FieryVoid`). Real. Writes here land on your disk. |
| `/usr/src/fieryvoid` | **container-local copy**, rsync'd one-way from `/usr/src/current` by `start.sh` (initial `rsync -a --delete`, then an `inotifywait` loop repeating it on every change). A throwaway. Writes here are invisible to the host and are **deleted by the next rsync** (`--delete`). This is also the Dockerfile's `WORKDIR`. |

(That copy exists as a Windows-bind-mount performance workaround: php-fpm
executes from the fast container-local tree. It also explains why the `.md`
plan files are missing from it — the rsync honours `.dockerignore`.)

So:

- **`docker exec fieryvoid-php-1 <cmd>` starts in `/usr/src/fieryvoid`** —
  the throwaway copy — *not* the repo. Verified: `docker exec … pwd` →
  `/usr/src/fieryvoid`.
- `generateStaticShipFile.php` uses **relative** paths
  (`include_once './source/public/global.php'`, `$fileBase =
  './source/public/static/ships'` — lines 8, 97, 114). It is therefore
  **CWD-dependent**.
- Naively running `docker exec fieryvoid-php-1 php
  /usr/src/current/generateStaticShipFile.php` would *appear to succeed*
  while including the stale copy's code and writing the statics into
  `/usr/src/fieryvoid/source/public/static/` — the throwaway. Nothing would
  reach the host repo, and the output would be wiped on the next file change.
  A silent, invisible wrong-place success: the worst kind.

**Rules for `fvbuild.ps1` (and for any `docker exec` in this repo):**

1. **Always pass `-w /usr/src/current`.** Verified working:
   `docker exec -w /usr/src/current fieryvoid-php-1 pwd` → `/usr/src/current`.
   Every command in §5 uses it.
2. **Make new scripts CWD-independent** — `generateAutoload.php` (or the
   phpab wrapper) must resolve paths from `__DIR__`, never `./`.
3. **Cheap hardening, worth doing:** give `generateStaticShipFile.php` the
   same treatment (`__DIR__` instead of `./`), so it becomes impossible to
   run it in the wrong place. Its current relative paths are a loaded gun.

**Your existing manual habit is correct and stays correct**: you run it from
`/usr/src/current#` in the Docker Exec tab, so CWD is the bind mount and the
relative paths resolve to the host repo. Nothing about your workflow needs to
change — the trap only appears when a *script* execs in without setting the
working directory.

And your read on live is right: statics are regenerated server-side via
`generateStaticShipFileWeb.php` and never pushed, so only the bundles and
`autoload.php` need to be correct in the repo.

---

## 1d. How ships are hidden — and why commenting autoload is the WRONG way

Confirmed in code (2026-07-14), because it decides the whole triage.

**There are two hide mechanisms, and they are not equivalent:**

**(1) `variantOf` sentinel — the correct one.** The lobby lists only base
designs (`gamelobby.js:2254`: `if (ship.variantOf != '') continue;`) and then
nests variants under their base hull (`gamelobby.js:2279`: `if (shipV.variantOf
!= ship.shipClass) continue;`). So a ship whose `variantOf` matches **no**
existing `shipClass` — `'NONE'`, `'OBSOLETE'`, `'OBSELETE'` (all three
spellings are in use) — is neither a base nor a locatable variant, so it
renders **nowhere**. 138 ship files use this idiom. Crucially **the class stays
loadable**, so old live games containing that ship still work. This is the
established practice the user describes: on a ship update, add the new class,
register it, and mark the old one obsolete.

**(2) Commenting out the autoload entry — the crude one.** `ShipLoader`
enumerates ship classes **by filename** and gates each on `class_exists()`
(`shipLoader.php:140`), which fires the autoloader. No map entry →
`class_exists` false → ship silently skipped from the faction list. It works,
**but it also makes the class unloadable**, so any old game still containing
that ship breaks. It's a foot-gun, and it is the mechanism the generator is
about to take away — which is *good*, provided we migrate each case to (1).

> **Rule going forward:** to retire a ship, set `variantOf = 'NONE'` in the
> ship file. **Never** hide a ship by removing it from the autoload map.
> phpab excludes are reserved for files that are genuine junk (dev scratch,
> `*_old.php` collision copies), not for retiring playable content.

### The triage, computed

Applying that to the 124 unregistered/commented classes (script:
`c:\tmp\autoload_triage.php`):

| Bucket | Count | Action |
|---|---|---|
| Not lobby-visible at all (weapons, systems, crits, multi-class files — `ShipLoader` never lists them because filename ≠ classname) | **65** | **Just register.** Pure bug-fix. Includes `ContainmentBreach`, the 7 `Ammo*` systems, ~48 `customNexus.php` weapons, `combattransporter`, `lightparticlegun`. |
| Ships already hidden by a `variantOf` sentinel | **39** | **Just register.** Changes nothing in the lobby *and fixes old games that contain them.* Includes all the `zzunoff*`, `rogolon*`, `Salbez*Refit`, `ishtakaton`, `enlightenment`, `atlasStarfury`. |
| **Ships with no `variantOf` — would genuinely APPEAR in the faction list** | **20** | ⚠️ **The only real decisions.** |

So the scary "84 unregistered + 41 commented" reduces to **20 ships to rule
on**:

```
choukacovenantminelayer   choukaraiderreclumafighter   craytanlopin
craytanlopinrefit         craytanpoltenrefit2          craytansoren
cylonbasestar_old         cylonheavyraider             dtarngunboat
enbrak                    ltviper                      makartolmor
mcmine                    medviper                     pillum
qomyominauxdestroyeralt   testmine                     variablehangarsize
velraxnareshguard         vreesalvagezorth
```

For each, one question: **is it meant to be playable?**

- **Yes** → register it. It was hidden by accident (a forgotten map entry) and
  this is the bug the whole roadmap item exists to kill.
- **No** → set `variantOf = 'NONE'` in the ship file (preferred — keeps old
  games loadable). Only phpab-exclude the file if it is *truly* junk:
  `testMine`, `MCmine`, `LtViper`, `medViper`, `VariableHangarSize` (all in
  `customs/`) and `cylonbasestar_old` look like dev scratch / superseded
  copies.

Two things to eyeball while you're in there:

- `cylonbasestar_old` is caught by the `-e '*_old.php'` exclude anyway (the
  glob matches the *filename*), so it needs no separate decision. Note the
  glob does **not** exclude `*_old/` **directories** — and it must not: those
  hold live, lobby-visible ships (`EA_old/atlasStarfury`, and `artemisalpha_early`
  is registered and playable today).
- Several `Salbez*Refit` files carry truncated sentinels (`variantOf='Av'`,
  `'Jer'`, `'Rev'`, `'Shv'`). If no hull has that `shipClass` they're hidden —
  possibly by accident rather than intent. Worth a glance; not a blocker.

---

## 2. Design (2.1–2.3 SUPERSEDED by §1b — kept for the record)

### 2.1 The generator: `generateAutoload.php` (repo root)

A dependency-free PHP CLI script, sited and run exactly like
`generateStaticShipFile.php` (inside the php container; no DB access needed —
it is pure filesystem):

```
docker exec fieryvoid-php-1 php /usr/src/current/generateAutoload.php [mode]
```

**Why PHP and not Node:** `token_get_all()` is a real PHP parser — it
correctly handles multiple classes per file, comments, strings containing
the word "class", `::class` constants, abstract classes, and anonymous
classes. Regex-parsing PHP from Node would be fragile. PHP CLI is already
the established pattern (`generateStaticShipFile.php`).

**Scan logic:**

1. Recursively walk the scan dirs (from config; initially the five subtrees
   the current map covers): `/server/model`, `/server/Phase`,
   `/server/controller`, `/server/handlers`, `/server/lib`.
2. For every `*.php` file not excluded by config, tokenize and collect
   `class` / `interface` / `trait` / `enum` declarations (skipping
   `::class` constants and anonymous classes). Interfaces matter: the
   current map registers `SpecialAbility` and `DefensiveSystem`.
3. Build `strtolower(name) => '/server/...'` (path with exact on-disk case —
   live runs on a case-sensitive filesystem; the scanner gets this right by
   construction, unlike hand edits).
4. Apply config exclusions, then **fail hard on any remaining collision**
   (same lowercase name in two files) with a message listing both paths and
   how to resolve it. No silent first-wins.
5. Fail hard if a `namespace` declaration is ever encountered (the map is
   global-name keyed; namespaced code would need a design change).
6. Emit the output file: identical runtime shape to today
   (`spl_autoload_register` + static array + `strtolower` lookup +
   `require __DIR__ . $path`), entries sorted alphabetically by key, one per
   line, LF line endings, **no timestamp** (byte-stable output is what makes
   `--check` and git diffs work). Header comment: "AUTOGENERATED by
   generateAutoload.php — do not edit; see AUTOLOAD_GENERATOR_PLAN.md".

`.php.old` / `.bak` files are ignored automatically (extension filter).
`*_old/` ship **directories stay scanned** — old saved games still reference
those classes; only specific colliding files get excluded.

**Modes:**

| Mode | Behavior |
|---|---|
| *(default)* | Regenerate `source/autoload.php` in place. Prints a summary of added/removed/changed entries vs. the previous content. |
| `--diff` | Dry run: print what would change, write nothing. |
| `--check` | Exit 0 if regenerating would produce a byte-identical file, exit 1 with the diff summary otherwise. For pre-deploy/CI use. Writes nothing. |
| `--verify` | After (or without) generating: `class_exists()` every mapped entry, proving every path resolves and every file parses. ~3,600 loads, seconds in CLI. |

### 2.2 The config: `source/autoload.exclusions.php`

The one hand-maintained file left, small and diffable. This is where the
"deliberate control of class registration" moves to — instead of controlling
registration by editing (or forgetting to edit) a 4k-line map, you control
it by exception, with a required reason string per entry:

```php
<?php
return [
    // Directories the scanner walks (source-relative)
    'scanDirs' => ['/server/model', '/server/Phase', '/server/controller',
                   '/server/handlers', '/server/lib'],

    // Any path containing one of these substrings is skipped entirely
    'excludePathContains' => ['random_compat'],   // self-loading polyfill

    // Specific files to skip — value is the mandatory reason
    'excludeFiles' => [
        '/server/model/ships/customs/layoutTest.php' => 'dev scratch; duplicates VelraxSivrinGunship',
        '/server/model/ships/kirishiac/kirishiacWarrior_old.php' => 'superseded, class name collides',
        // ... (seeded during migration triage, section 3)
    ],

    // Specific classes to leave unregistered even though they exist on disk
    // (successor to the commented-out entries) — value is the reason
    'excludeClasses' => [
        'testmine' => 'not ready for play',
        // ... (seeded during migration triage)
    ],

    // Escape hatch for entries the scan can't produce. Expected to stay empty.
    'manualEntries' => [],
];
```

### 2.3 What happens to the current map's quirks

- **151 commented-out entries** → deleted. The ~41 whose classes exist on
  disk are triaged into `excludeClasses` (if deliberately disabled) or just
  registered (if someone simply forgot to uncomment). The ~110 pointing at
  nonexistent classes/files vanish — git history keeps them.
- **`'error'` / `'typeerror'` → random_compat polyfill** → dropped. On
  PHP 8 these are built-in classes; the autoloader is never asked for them.
- **Grouping comments** ("Kelly's Star Trek", "For the future", …) → gone
  from the generated file; alphabetical order + git blame on the exclusions
  file replace them.
- **`source/autoload.php` stays committed** (unlike the JS bundles): live
  shared hosting needs the file, and a generated-but-committed file with
  deterministic output gives reviewable one-line diffs. It also stays
  byte-identical for everyone, so it stops being a merge-conflict magnet —
  and when it *does* conflict, the resolution is "regenerate", never
  hand-merge (see §5.4).

### 2.4 Backup of the hand map: `source/autoload_old.php`

The final hand-maintained map is copied to `source/autoload_old.php` and
committed as part of the migration commit, as a belt-and-braces reference
while the generator beds in.

Safety properties (checked, not assumed):

- **It is never loaded.** Only `source/public/global.php` includes
  `autoload.php`, by exact name. Nothing includes `autoload_old.php`.
- **It is never scanned.** The generator's `scanDirs` are the five
  `/server/...` subtrees; `source/` root is not among them, so the backup
  cannot feed its own (stale) entries back into a future generation, and its
  presence cannot create phantom "collisions".
- It carries a header comment: *"Frozen copy of the last hand-maintained
  autoload map (pre-generator, 2026-07-14). Reference only — not loaded, not
  scanned. Delete once the generator has been trusted for a few releases."*

Suggested retirement: delete it once a couple of releases have shipped on
generated maps. Git history keeps it forever regardless; this file exists
purely so the old map is one click away rather than one `git log` away.

---

## 3. Unified build script: `scripts/fvbuild.ps1`

Requested: one command that runs **autoload generation + static ship files +
`yarn build`**, keeping each usable on its own, as the precursor to a real
pre-deploy script.

### 3.1 The wrinkle that shapes the design

The three steps do **not** run in the same place:

| Step | Where it runs | Why |
|---|---|---|
| phpab (autoload map) | **inside** `fieryvoid-php-1`, **`-w /usr/src/current`** | needs PHP + vendor/bin |
| `generateStaticShipFile.php` | **inside** `fieryvoid-php-1`, **`-w /usr/src/current`** | needs PHP + the class tree; `memory_limit -1` |
| `yarn build` | **on the Windows host** | node/yarn/esbuild/vite live there, not in the container |

So the orchestrator must be a **host-side PowerShell script** that shells into
Docker for the PHP halves and calls `yarn` locally. It cannot be a PHP script
(can't reach yarn) or an npm script (can't cleanly reach the container). Hence
`scripts/fvbuild.ps1` next to the existing `scripts/*.js` build helpers.

**Every `docker exec` in the script MUST pass `-w /usr/src/current`** — see
§1c. Without it the PHP steps run in a throwaway container-local copy and
write their output where nobody will ever see it.

### 3.2 Step order (it matters)

1. **Autoload** — first, because step 2 instantiates every ship class and will
   fatal on a missing entry.
2. **Static ship files** — and this is a *bonus*: `generateStaticShipFile.php`
   walks and constructs the whole ship catalogue, so a successful run is a
   far deeper proof of the autoload map than `--verify` alone. If the map is
   wrong, statics generation is where it screams.
3. **Client bundles** (`yarn build` → build:three + vite + bundle-legacy).

Fail-fast between steps. Note PowerShell does **not** fail automatically on a
native exe's non-zero exit, so the script checks `$LASTEXITCODE` after every
`docker exec` / `yarn` call explicitly.

### 3.3 What each step produces (and what you commit)

| Output | Tracked? | Commit it? |
|---|---|---|
| `source/autoload.php` | tracked | **yes**, with your class files |
| `source/public/static/**` (json + shipsCombined.js) | **gitignored** (`.gitignore` line 17) | n/a — deploy artifact, upload it |
| `source/public/client/*.legacy.bundle.js`, `UI.bundle.js` | untracked | **never** — per repo convention |

Pleasingly, that means the script's entire output is "one reviewable tracked
file + the exact set of deploy artifacts".

### 3.4 Flags

| Invocation | Does |
|---|---|
| `.\fvbuild.ps1` | all three steps, in order (the default "I changed something, rebuild everything") |
| `.\fvbuild.ps1 -Autoload` | autoload map only |
| `.\fvbuild.ps1 -Statics` | static ship files only |
| `.\fvbuild.ps1 -Client` | `yarn build` only |
| `.\fvbuild.ps1 -Server` | autoload + statics, skip `yarn build` (**the everyday one** while watchers are running — see §3.5) |
| `.\fvbuild.ps1 -Check` | **writes nothing.** `generateAutoload.php --check` + `--verify` + replay-harness `check`. The pre-deploy gate. |

Preflight: the script verifies `fieryvoid-php-1` is running and prints
`docker compose up -d` as the remedy rather than letting `docker exec` fail
cryptically.

### 3.5 Two gotchas the script must warn about (not silently do the wrong thing)

- **`yarn build` minifies the legacy bundles; `watch:legacy` does not** (it
  sets `FV_NO_MINIFY=1` — see `scripts/bundle-legacy.js:31`). You keep
  `yarn watch` + `yarn watch:legacy` running permanently, so running the full
  script mid-session swaps your readable bundles for minified ones until the
  next watcher trigger flips them back. Harmless (bundles are never
  committed), but it makes debugging confusing for a minute. **This is why
  `-Server` exists** and why it, not the bare default, is the right daily
  driver for you. The script prints a one-line notice if it detects it is
  about to minify over a watcher's output.
- **Statics take real time and memory** (the streaming generator peaks
  ~168 MB; see the static-generator notes). Deterministic output, though — if
  nothing ship-related changed, the regenerated files are identical, so
  running it "just in case" costs time but never noise.

### 3.6 Relationship to the future deploy script

`-Check` is deliberately the same shape as the pre-deploy routine that roadmap
item 11 (repo/deploy hygiene) wants. When that lands, the deploy script's first
act is `fvbuild.ps1 -Check`, then `fvbuild.ps1`, then upload. Building the
flags this way now means the deploy script is later a thin wrapper, not a
rewrite.

---

## 4. Migration (one-time, do this carefully)

The point of the migration is that the **first generated file must be a
reviewed, intentional change**, not a blind dump. Order of work:

**Stage 1 — Restore the tool (phpab), don't build one.** Per §1b:
`composer install` in the container, fix `autoload.sh` to carry the exclude
list (one commented line per exclude), confirm it emits the same shape as the
current file. No new PHP generator gets written. (The prototype scanner
`c:\tmp\autoload_scan_proto.php` stays as the *reporting* tool for triage —
phpab tells you it failed, the prototype tells you the full added/removed/
stale picture.)

**Stage 2 — Triage session (user decision required per item).** Using the
`--diff` report, walk the four finding lists from §1:

1. *8 collisions* — **phpab refuses to generate until every one is
   resolved**, so this is mandatory, not optional. Six are `*_old.php`
   duplicates (`kirishiacwarrior`, `kirishiaclordship`, `altarus`,
   `balvarus`, `choukaraiderheresy`, `choukaraideroathbreaker`) — covered by
   a single `-e '*_old.php'` exclude (verified: with it, phpab generates
   cleanly). The seventh is `customs/layoutTest.php` vs
   `NexusVelrax_old/VelraxSivrinGunship.php` — exclude `layoutTest.php`
   (the active map points at the other). The eighth, `craytanlopinb`, is a
   **genuine content bug**: `CraytanLopin.php` declares *both* `CraytanLopin`
   and `CraytanLopinb`, colliding with `CraytanLopinb.php`. Exclusion can't
   fix it without losing `CraytanLopin` — delete the stray duplicate class
   from `CraytanLopin.php`.
2. *84 unregistered + 41 commented* — **resolved by §1d**: 65 aren't
   lobby-visible and 39 are already `variantOf`-hidden, so **104 of them just
   register** (pure bug-fix, and it *un-breaks* old games holding the
   `variantOf`-hidden ships). Only the **20 base-design ships listed in §1d**
   need a ruling: playable → register; retire → set `variantOf = 'NONE'` in
   the ship file; genuine junk → phpab-exclude.
3. *Retire the "comment it out of autoload" practice.* It hides a ship but
   also makes it unloadable, breaking old games (§1d). Any existing case gets
   migrated to a `variantOf` sentinel.
4. *37 stale + 2 wrong-path entries* — all disappear automatically. Nothing
   to do beyond reviewing the diff.

**Stage 3 — First generation + verification.**

1. Copy the hand map to `source/autoload_old.php` and add the header comment
   (§2.4) — do this *before* the first generation overwrites it.
2. Run the generator; review the full diff of `source/autoload.php`.
3. `--check` → green; run twice → byte-identical (idempotence).
4. `--verify` → every entry loads (this alone re-proves all 3,600+ paths).
5. Regenerate statics — the deepest map proof there is (§3.2).
6. Replay harness `check` — real recorded games through the new map.
7. Manual smoke: open the game lobby (fleet buy list instantiates broadly
   via shipLoader), open a live game, open a replay.
8. Commit generator + exclusions + regenerated `autoload.php` +
   `autoload_old.php` together. (Per repo convention the legacy bundles stay
   uncommitted; statics are gitignored.)

**Stage 4 — Build script + docs.**

- Write `scripts/fvbuild.ps1` (§3).
- README: add the "Building after a change" section (§5 below, ready to
  paste); update the existing "Register it in the autoload classmap — do not
  skip this" instruction (README ~line 253) to "run the generator".
- Deferred (don't build now): git pre-commit hook, `yarn autoload` alias
  (would couple yarn to a running Docker container).

---

## 5. Usage instructions (for all devs — copy into README in Stage 4)

Prerequisite for everything below: Docker is up (`docker compose up -d`).
None of these steps touch the database.

**All commands are written to be run from `c:\FV_env` in PowerShell**, which
is where you sit. They work from any directory — the script resolves the repo
root from its own location — but the paths below assume `c:\FV_env`.

### 5.1 The one command: you changed something, rebuild everything

```powershell
.\FieryVoid\scripts\fvbuild.ps1
```

That runs, in order: autoload map → static ship files → `yarn build`
(three shim + React/Vite + legacy bundles). Fails fast and tells you which
step broke.

If PowerShell blocks the script (`running scripts is disabled on this
system`), either run it once as:

```powershell
powershell -ExecutionPolicy Bypass -File .\FieryVoid\scripts\fvbuild.ps1
```

or permanently allow local scripts for your user:

```powershell
Set-ExecutionPolicy -Scope CurrentUser RemoteSigned
```

### 5.2 The one you'll actually use day-to-day

Because you keep `yarn watch` and `yarn watch:legacy` running, the client
bundles are already being rebuilt for you — and a full `yarn build` would
*minify* them, which just makes your next debugging session harder until the
watcher flips them back. So during normal work, skip the client step:

```powershell
.\FieryVoid\scripts\fvbuild.ps1 -Server
```

= autoload + statics only.

### 5.3 Individual steps (all still available on their own)

```powershell
.\FieryVoid\scripts\fvbuild.ps1 -Autoload    # just the class map
.\FieryVoid\scripts\fvbuild.ps1 -Statics     # just the static ship files
.\FieryVoid\scripts\fvbuild.ps1 -Client      # just yarn build
```

And the underlying commands, if you ever want to bypass the script entirely
(this is all it does). **Note the `-w /usr/src/current` — it is not optional;
see §1c:**

```powershell
docker exec -w /usr/src/current fieryvoid-php-1 ./autoload.sh
docker exec -w /usr/src/current fieryvoid-php-1 php generateStaticShipFile.php
cd c:\FV_env\FieryVoid; yarn build
```

(Running `php generateStaticShipFile.php` yourself from `/usr/src/current#` in
Docker Desktop's Exec tab — as you do today — is exactly equivalent and stays
fine.)

### 5.4 Before deploying (or any time you're suspicious)

```powershell
.\FieryVoid\scripts\fvbuild.ps1 -Check
```

Writes nothing. Runs three gates:

- `generateAutoload.php --check` — fails if the committed map is out of date
  with the source tree (i.e. someone added a class and forgot to regenerate).
- `generateAutoload.php --verify` — actually `class_exists()`-loads every
  mapped entry, catching bad paths and parse errors in one sweep.
- the replay-harness `check` — real recorded games through the current code.

### 5.5 You added / renamed / deleted a PHP class (ship, weapon, system, crit…)

1. Create or edit the class file as usual, e.g.
   `source/server/model/ships/vree/myNewShip.php`.
2. `.\FieryVoid\scripts\fvbuild.ps1 -Server`
3. Check the summary it prints (and/or `git diff source/autoload.php`). For
   one new ship you should see exactly **one added line**. If you see more,
   someone previously forgot to regenerate — that's fine, the extra lines are
   the map catching up; just sanity-check they look plausible.
4. Commit `source/autoload.php` together with your class files.

**Never edit `source/autoload.php` by hand** — any hand edit is overwritten
by the next run. (`source/autoload_old.php` is the frozen pre-generator copy,
kept as a reference only. It is not loaded and not scanned.)

### 5.6 You want a class to exist on disk but NOT be loadable

The old practice was commenting out its map line. Now: add it to
`excludeClasses` in `source/autoload.exclusions.php`, with a reason:

```php
'mynewship' => 'WIP, not balanced yet',
```

then regenerate. Same idea for whole files (`excludeFiles`) or directories
(`excludePathContains`).

### 5.7 The generator fails with a collision error

Two files declare the same class name; the error names both paths. Decide
which is real, then either delete/rename the impostor's class or add the
losing file to `excludeFiles` with a reason, and rerun. The build failing
here is deliberate — silently picking one would reintroduce exactly the
ambiguity the generator exists to kill.

### 5.8 You hit a merge conflict in `source/autoload.php`

Do **not** hand-merge it. Take either side (`git checkout --theirs
source/autoload.php`), finish merging the *source* files, then regenerate —
a scan of the merged tree is by definition the correct map:

```powershell
.\FieryVoid\scripts\fvbuild.ps1 -Autoload
```

Conflicts in `source/autoload.exclusions.php` are normal hand-merges (it's a
small hand-maintained file).

### 5.9 Just seeing what would change

```powershell
docker exec fieryvoid-php-1 php /usr/src/current/generateAutoload.php --diff
```

Prints added/removed/changed entries without writing anything.

---

## 6. Risks & edge cases

- **Registering previously-unregistered classes changes nothing at runtime
  until something requests them** — autoload is lazy. Risk of the migration
  is therefore concentrated in *removals* (stale entries whose files are
  gone would have fataled anyway) and the 8 collision resolutions (each
  matched to what the active hand map already does). The replay harness +
  lobby smoke cover the load paths.
- **Live in-flight games**: no exclusion may remove a currently-*active*
  map entry during triage. Additions and stale-removal only. (Commented
  entries are already unloadable today, so excluding them regresses
  nothing.)
- **Conditional declarations** (`if (!class_exists(...)) { class X {} }`)
  are still found by the token scan and registered — acceptable; none exist
  in the scanned tree today outside random_compat (excluded).
- **Windows line endings**: generator writes LF explicitly regardless of
  host, so output is byte-stable across dev machines and `--check` can
  byte-compare.
- **PHP version drift**: `T_ENUM` guard (`defined('T_ENUM')`) so the script
  runs on pre-8.1 CLIs too, should anyone run it outside the container.
- **OPcache on live**: autoload.php is a deployed file like any other;
  LiteSpeed's 30s revalidate picks it up. No `opcache_reset()` (see
  live-server memory).

## 7. Acceptance criteria

1. `generateAutoload.php` regenerates a byte-identical file when run twice.
2. `--check` green on a clean tree; red (exit 1, useful diff) after adding
   a class file without regenerating.
3. `--verify` loads all entries with zero errors.
4. Static ship generation succeeds against the regenerated map (the deep
   proof — it constructs every ship class).
5. Replay harness `check` passes against the regenerated map.
6. Lobby + game + replay smoke pass.
7. `fvbuild.ps1` works from `c:\FV_env`; each of `-Autoload` / `-Statics` /
   `-Client` / `-Server` / `-Check` does exactly its step and nothing else;
   a failure in any step stops the run with a readable message; missing
   Docker gives the `docker compose up -d` hint, not a raw `docker exec`
   error.
8. `source/autoload_old.php` exists, is committed, is not loaded by anything,
   and is not picked up by the scanner.
9. README updated (§5 content, incl. the exact `c:\FV_env` commands); the
   "do not skip this" manual-autoload-entry instruction is gone.
10. `ContainmentBreach` and the `CraytanLopinb` duplicate are resolved by the
    regenerated map (the other §1 bugs were hand-fixed 2026-07-14).
