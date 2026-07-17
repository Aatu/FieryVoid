
# Introduction

Fiery Void is a turn based strategy game based on B5Wars tabletop game by Agents of Gaming.

# License

FV is licensed under GNU GPLv3

# Installing development environment

Make sure you have the following installed on your machine:

- Docker and Docker Compose
- Node.js and Yarn (for client-side frontend bundling) - Primarily Yarn.

### Installing Node.js and Yarn

Docker runs the server (Nginx/PHP/MariaDB), but the client-side JS bundling (`yarn build` / `yarn watch:legacy`) runs on your host machine, so Node.js and Yarn need to be installed locally.

1. Install Node.js (this also installs npm). Use the current LTS release.
   - Windows: download the LTS installer from https://nodejs.org/, or run `winget install OpenJS.NodeJS.LTS`
   - macOS: `brew install node`
   - Linux: use your distro's package manager (e.g. `sudo apt install nodejs npm`) or https://github.com/nvm-sh/nvm

2. Enable Yarn. Modern Node.js ships with Corepack, which is the recommended way to get Yarn — no separate install needed:

   corepack enable
   corepack prepare yarn@stable --activate

   (Alternatively, the classic global install still works: `npm install -g yarn`.)

3. Verify both are available by running `node -v` and `yarn -v` in a terminal. Once they report versions you're ready for the "Setup Client-Side Development (Yarn)" step below.

1. Build and Start the Environment
Open a terminal in the project root folder (c:\FV_env\FieryVoid) and run:

docker-compose up -d --build
This will build and start the Nginx, PHP, and MariaDB containers in the background.

(Note: The database is automatically initialized from db/emptyDatabase.sql on the very first start and securely saved to a persistent Docker volume).

Note: you might need to delete this from docker-compose.yml file: 

"develop:
      watch:
        
action: sync
        path: ./source
        target: /usr/src/fieryvoid/source
        ignore:
node_modules/"

2. Accessing the Application
Web Interface: Point your browser to http://localhost

Database Access: You can connect to the database from a local client at localhost:3306 with the following credentials:
User: root
Password: fieryvoid (or possibly just leave blank)
Database: B5CGM

3. Setup Client-Side Development (Yarn)
Since FieryVoid bundles legacy code, you'll need to install the Node dependencies locally so things recompile when you make edits by running these commands in project root folder.  

# First time setup
yarn install
# To watch and auto-rebuild JS files while editing
yarn watch:legacy
# To do a one-off build
yarn build

Note, these commands can be useful sometimes, but the Yarn Build step has largely been replaced by the fvbuild.ps1 process detailed below.

4. Interacting with the PHP Container
If you need to run server-side scripts (like to generate new staticship files after adding new units, or changing class variables on server) or explore the backend container, you can drop into the PHP container's bash shell:

docker-compose exec php bash

Then navigate to /usr/src/current and run 'php generateStaticShipFile.php'
e.g. 
cd...
cd current
php generateStaticShipFile.php

(Or from PowerShell on the host: `.\FieryVoid\scripts\fvbuild.ps1 -Statics` —
see "Building after a change" below.)

Note, this step has largely been replaced by the fvbuild.ps1 process detailed below.

5. Troubleshooting / Clean Rebuild
If your containers get out of sync or you need to cleanly force a rebuild of the environment, use:

docker-compose up -d --build --force-recreate

6. Set-up database connection if you need it

Current devs use SQL Workbench  here https://www.filehorse.com/download-mysql-workbench/download/

Then set-up connection using details from mariadb part of you Docker container.

Make sure the stack is up:


docker compose ps
Confirm the mariadb container shows Up and the port mapping is 0.0.0.0:3306->3306/tcp.

Find the DB credentials. They're in the Fiery Void repo — check docker-compose.yml (look for MYSQL_ROOT_PASSWORD, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE) and/or any .env file the compose file references. Whatever values are there are what Workbench needs.

In MySQL Workbench → "+" next to MySQL Connections:

Field	Value
Connection Name	Fiery Void (Docker)
Connection Method	Standard (TCP/IP)
Hostname	127.0.0.1
Port	3306
Username	from compose (often root or fieryvoid)
Password	"Store in Vault…" → paste from compose/.env
Default Schema	leave blank, or the DB name from compose
Click Test Connection → should succeed → OK.

Open the connection and the Fiery Void schema(s) will show up in the left-hand SCHEMAS panel. Browse tables, run queries, etc. — same as any local DB.



# Building after a change (scripts/fvbuild.ps1):

`source/autoload.php` (the class map consumed by `spl_autoload_register`) is a
**generated file — never edit it by hand**. It is produced by phpab
(`theseer/autoload`, the repo's only composer dependency) via `autoload.sh`,
which also carries the exclusion list, one commented reason per exclude. The
map is committed, so regenerating it produces a normal reviewable git diff.

Everything below assumes PowerShell in `c:\FV_env\FieryVoid` and Docker up
(`docker compose up -d`). None of these steps touch the database. If
PowerShell refuses to run scripts, use
`powershell -ExecutionPolicy Bypass -File .\FieryVoid\scripts\fvbuild.ps1 ...`
or allow local scripts once with `Set-ExecutionPolicy -Scope CurrentUser RemoteSigned`.

### The one command: you changed something, rebuild everything

    .\scripts\fvbuild.ps1

Runs, in order: autoload map → static ship files → `yarn build`. Fails fast
and says which step broke. Statics regeneration doubles as the deepest proof
of the autoload map — it constructs every ship class in the catalogue.

### The one you'll actually use day-to-day

    .\scripts\fvbuild.ps1 -Server

= autoload + statics, skipping `yarn build`. Use this while `yarn watch` /
`yarn watch:legacy` are running: a full `yarn build` *minifies* the legacy
bundles, which makes debugging harder until the watcher flips them back.

Note - You'll still need to restart the Docker container for server changes to show on local server.

### Individual steps

    .\scripts\fvbuild.ps1 -Autoload    # just the class map
    .\scripts\fvbuild.ps1 -Statics     # just the static ship files
    .\scripts\fvbuild.ps1 -Client      # just yarn build

Or bypass the script entirely — this is all it does (**`-w /usr/src/current`
is not optional**: without it the command runs in the container-local
throwaway copy and the output never reaches your repo):

    docker exec -w /usr/src/current fieryvoid-php-1 sh autoload.sh
    docker exec -w /usr/src/current fieryvoid-php-1 php generateStaticShipFile.php
    cd c:\FV_env\FieryVoid; yarn build

(Running these from `/usr/src/current#` in Docker Desktop's Exec tab is
exactly equivalent and stays fine.)

### Before deploying (or any time you're suspicious)

    .\FieryVoid\scripts\fvbuild.ps1 -Check

Writes nothing. Two gates: regenerates the map to a temp file and
byte-compares it against the committed one (fails if someone added a class
and forgot to regenerate), then runs the replay harness over your local
game corpus.

### You added / renamed / deleted a PHP class (ship, weapon, system, crit…)

1. Create or edit the class file as usual.
2. `.\FieryVoid\scripts\fvbuild.ps1 -Server`
3. `git diff source/autoload.php` — for one new ship you should see exactly
   one added line. More lines just mean the map is catching up with someone
   else's forgotten regeneration; sanity-check they look plausible.
4. Commit `source/autoload.php` together with your class files.

### Retiring or hiding a ship

Set `$this->variantOf = 'NONE';` in the ship file — it disappears from the
lobby but **stays loadable, so old games containing it keep working**. Never
hide a ship by keeping it out of the autoload map (the old commented-entry
practice): that breaks every existing game that contains one. phpab excludes
(in `autoload.sh`, with a reason) are reserved for genuine junk files —
dev scratch and `*_old.php` collision copies.

### The generator fails with a collision error

Two files declare the same class name; phpab names both and refuses to
generate — deliberately. Decide which is real, then delete/rename the
impostor's class or add the losing file to the excludes in `autoload.sh`
with a reason, and rerun.

### Merge conflict in source/autoload.php

Do **not** hand-merge it. Take either side
(`git checkout --theirs source/autoload.php`), finish merging the *source*
files, then `.\FieryVoid\scripts\fvbuild.ps1 -Autoload` — a scan of the
merged tree is by definition the correct map.

(`source/autoload_old.php` is a frozen copy of the last hand-maintained map,
kept as a reference while the generator beds in. It is not loaded and not
scanned; delete it once the generated map has been trusted for a few
releases.)



# Game.php and gamelobby.php js bundling:

I created a scripts/bundle-legacy.js script that bundles the ~130 legacy JavaScript files into a single file (game.legacy.bundle.js). I also updated game.php to load this bundle by default, which should drastically reduce HTTP requests and eliminate the HTTP/2 errors. You can still load individual scripts for debugging by adding &debug=1 to the URL. I updated package.json to include this in your build process (using yarn).

Because I've set up the build script to read game.php directly, you don't need to do anything special to maintain the bundle list!

How to add new files:

Just add your new script tag to game.php inside the if ($debug) block (where all the others are).
Run yarn build.
The script will automatically find your new file in game.php and bake it into the bundle.

How to rebuild:

Since I added the legacy build to your main build script in package.json, just running yarn build (or npm run build) will handle everything for you. It builds your vite app AND rebuilds this legacy bundle in one go.
I've updated the walkthrough with these instructions for future reference.

How to edit client side now:

You'll need to run yarn watch:legacy in FV folder when editing client files now, similar to how we run yarn watch when editing React files.

When uploading your branch to gitHub it's best not to include these files, as they will need to be regenerated anyway for the branch being deployed live and it makes merging easier not to have several versions of the bundles.

Minification (from 3.6.2026):
The bundler now depends on esbuild to minify the legacy bundles, (already present via Vite, so a normal yarn install covers it), and yarn watch:legacy intentionally produces un-minified output for debuggability while yarn build minifies.



# THREE.js bundle (three.shim.bundle.js) — when to rebuild:

(from 10.6.2026) THREE.js is no longer shipped as the full ~670KB vendored three.min.js. Instead, `scripts/build-three-shim.js` builds a slimmed, tree-shaken bundle (`client/lib/three.shim.bundle.js`, ~500KB) that contains ONLY the THREE features FV actually uses. It's installed onto the global `window.THREE` exactly like before, so none of the legacy client code had to change.

The important thing for devs: the shim only includes the THREE classes/constants currently in use. The full list of what's included is the `import { ... } from 'three'` block at the top of `source/public/client/lib/three-global-shim.src.js` — that's your reference for "is this already available?".

When you DO need to rebuild the THREE bundle:
- You used a `THREE.Something` that has never been used in FV before (e.g. adding `THREE.Points` for a new particle effect). Add it in TWO places in `three-global-shim.src.js` — the `import { ... }` list AND the object that's assigned to `window.THREE` — then run `yarn build:three` (or any full `yarn build`). Until you do, that symbol will be `undefined` at runtime (it's a runtime error, NOT a build error — so test the feature in-game).
- Someone bumps the `three` version in package.json (a separate, larger effort — re-test all weapon/FX visuals if so).

When you do NOT need to rebuild it (i.e. almost all the time):
- Normal work — ships, weapons, systems, game rules, tooltips, React UI, and any renderer/effect code that only uses THREE features already in the list — does NOT touch the THREE bundle. Just rebuild the legacy/UI bundle as usual (`yarn watch:legacy` / `yarn build`).

When in doubt: just run `yarn build`. It runs all three steps in order (THREE shim → Vite/React → legacy bundle), so you can never end up with a stale THREE bundle by running the full build. The standalone `yarn build:three` is only there to save time when you KNOW the THREE bundle is the only thing that changed.

(The old `client/lib/three.min.js` is now unused and can be deleted.)



# Replay regression harness (tests/replay):

(from 11.7.2026) A pre-deploy safety net that replays REAL recorded games from your local Docker database through the current server code and diffs the results against a recorded baseline. If a code change alters what the engine produces for games that were already played, the check fails and shows exactly what changed. It is entirely read-only against the database.

What it verifies, per game:
- **snapshot** — the full client gamedata JSON (stripForJson) from every player's perspective. Catches serialization/payload regressions: dropped or changed fields, notes handling, visibility masking, shared-reference mutations, autoload/constructor breakage.
- **movement** — Movement::validateThrustPayment replayed over every recorded ship-turn (with enforcement switched on in-memory only). Every legal recorded maneuver must stay legal — catches regressions in thrust/maneuver math.
- **tohit** — Weapon::calculateHitBase recomputed for every recorded direct-fire order against as-of-turn game state. Catches regressions in hit-chance math (arcs, range penalties, EW, jink, firing modes).

### How to use it, step by step:

1. Make sure the Docker environment is running (`docker compose up -d`). The harness runs inside the php container and reads the local B5CGM database.

   The examples below use the php container name `fieryvoid-php-1`. That name comes from your project folder (Docker Compose derives it from the directory the repo lives in), so if you cloned into a differently-named folder yours will differ — check with `docker ps` and substitute your `*-php-1` name. Everything else (DB name/user/password) is read from the standard docker-compose defaults, so no other setup is needed. (If your DB differs, you can override with the `FV_DB_HOST/PORT/NAME/USER/PASS` environment variables.)

2. **Record a baseline while the code is in a known-good state** (e.g. right after a deploy, or on a freshly pulled branch):

   docker exec fieryvoid-php-1 php /usr/src/current/tests/replay/replayHarness.php record

   This replays every local game with recorded play (turn >= 1, not in lobby) and writes the results to `tests/replay/baseline/` (git-ignored). Takes ~20 seconds for ~160 games.

3. Develop as normal.

4. **Before deploying, run the check:**

   docker exec fieryvoid-php-1 php /usr/src/current/tests/replay/replayHarness.php check

   - All PASS + exit code 0 → your changes don't alter engine behaviour on recorded play. Deploy away.
   - Any FAIL → the output shows exactly which game, which report, and which values changed (JSON field paths for snapshots, per-line before/after for movement/tohit). Decide:
     - **Unintended?** You found a regression before the players did. Fix it.
     - **Intentional?** (e.g. you rebalanced a weapon, changed a payload field on purpose) Re-run `record` to accept the new behaviour as the baseline.
   - SKIP means that game advanced since the baseline was recorded (someone played it locally) — re-record to include it again.

### Useful options:

- `list` — show all replayable games and whether they're in the baseline.
- `--games=3696,3671` — limit record/check to specific game ids (fast iteration while debugging a failure).
- `--checks=tohit` — run a subset of `snapshot,movement,tohit`.
- `--diff-limit=50` — show more differences per failed report (default 15).

### Good to know:

- **Add coverage by playing**: any game you play on the local server (fresh test games included) becomes corpus material — just run `record` again to fold it into the baseline. The more varied the local games (factions, weapons, terrain, fighters), the wider the net.
- **Deterministic dice**: live hit-chance calculation genuinely rolls dice (the hit LOCATION is rolled inside calculateHitBase and feeds the final chance). The harness replaces the Dice class in its own process with a seeded RNG so every run reproduces exactly. Game code is not touched.
- A couple of ancient test games fail to load (corrupt data) — their error text is recorded as part of the baseline, which is fine: if they ever start loading differently, that's a change worth seeing too. Games that are broken for legacy reasons the harness can't model can be dropped entirely by adding their id to the `EXCLUDED_GAMES` constant at the top of `replayHarness.php`.

### Multiple developers / different Docker databases:

The harness works for everyone, but **the baseline is per-developer and is never shared or committed**. Here's why and what that means:

- The harness code (`tests/replay/replayHarness.php`) is committed and portable — pull the branch and it's there.
- The **baseline** (`tests/replay/baseline/`) is git-ignored on purpose. A baseline is a snapshot of *your* local games run through *your* code — it only makes sense against the exact database it was recorded from. There is no point in one dev's baseline reaching another dev, and committing it would just create noise and conflicts.
- So the one-time setup for any dev is simply: pull the branch, then **run `record` once** against their own DB. From then on, `check` compares current code against that personal baseline. Everyone's corpus is different (different local games), which is a feature — collectively you cover more ships/weapons/situations.
- Because baselines aren't shared, a FAIL is always meaningful *to the dev who sees it*: it means the current code changed engine behaviour relative to the games in their own DB. It never reflects someone else's data.
- The `EXCLUDED_GAMES` list is shared (it's in the committed code). It's keyed by game id, so an entry that names a game another dev doesn't have simply does nothing for them — harmless. Only add ids there for games that are genuinely unmodellable, with a note saying why.

# Image Optimiser:

Images are optimised on Web Server by navigating to https://fieryvoid.eu/game/source/public/mass_optimizer.php or https://fieryvoid.eu/testInstance/source/public/mass_optimizer.php

As a result, users can work with full png images, and then this script can be run on game and testInstance server whenever required.

On local server, images are simply not optimised, although in theory you could run the mass_optimser script to do so.


# Sticky Images
If you're finding images are not updating after you changed them, you can sometimes add ‘?v=2’ after .png to force image refresh.


## VAGRANT IS NO LONGER USED! ##

Here were the old instructions for posterity

1. Install virtualbox https://www.virtualbox.org/
1. Install vagrant https://www.vagrantup.com/
1. Make sure you have ssh client and terminal
1. Type  `vagrant up`
1. Point your browser to localhost:8080/source/public

You can access the virtual box by:

1. Type `vagrant ssh`
1. Code is shared in /vagrant/ folder
1. Access database by typing: `mysql -uroot -proot`