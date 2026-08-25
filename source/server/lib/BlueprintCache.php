<?php
/**
 * BlueprintCache — the `window.staticShips` payload for game.php, built once per deploy
 * instead of once per page load.
 *
 * WHAT PROBLEM THIS SOLVES
 * game.php needs the BLUEPRINT of every ship class in the game (the client merges it with the
 * live gamedata in SystemFactory.createSystemFromJson). Until now it built that from scratch on
 * every single page load: ShipLoader::getShipsByClass() instantiates each class, runs
 * beforeTurn() on every system, setEnhancementOptions() and notesFill(), and the result is
 * json_encode()d raw and inlined into the HTML.
 *
 * None of that work depends on the GAME — a blueprint is a pure function of the ship class and
 * the code. Two players reloading the same game, or two different games sharing a hull, redid
 * all of it independently. This class caches the finished per-class JSON fragment in APCu, so a
 * warm request is ~70 apcu_fetch()es, a gzinflate each and a string concat — no instantiation
 * and no big json_encode at all.
 *
 * ⚠️ MEASURE THIS ON THE SERVER, NOT ON THE DEV BOX. The Docker-Desktop-on-Windows figures this
 * file originally quoted (220 ms of getShipsByClass at 60 classes) were dominated by bind-mount
 * file I/O and overstated the real saving by roughly an order of magnitude. On the live test
 * instance, a 35-entry game measured **15.6 ms from source vs 1.7 ms cached**, and a real game
 * page reads ~1 ms for this block. So: single-digit-to-mid-teens ms of server CPU per page load,
 * not the hundreds the dev box suggested. (The from-source figure is itself a slight OVER-count
 * of the pre-change cost — it includes the ShipCompactor pass the old inline code never did.)
 *
 * It also routes each blueprint through ShipCompactor, which game.php never did (the compactor
 * only ever ran in the static-file generator, so the lobby got compacted blueprints and the game
 * screen did not). That is a further −57% on the payload: 3,684 KB → 1,574 KB at 60 classes.
 *
 * ⚠️ ON THE WIRE THAT LAST PART BARELY MATTERS, and it is worth knowing why before anyone
 * "improves" it further. Live compresses responses with brotli at quality 4
 * (compression_helper.php), whose 4 MB window swallows the whole blob — the same 60-class
 * payload is 39 KB compressed raw and 35 KB compacted, a 4 KB saving. (gzip -6, with its 32 KB
 * window, cannot see past one ship and reads 244 KB → 110 KB, which flatters the change about
 * 30x. Do not quote the gzip figure.) The compaction earns its place on the CLIENT side, where
 * the browser parses the DECOMPRESSED text: 3.60 MB → 1.54 MB of inline object literal is
 * ~41 ms → ~15 ms of parse, plus the memory it stops holding. The server-side win is the cache.
 *
 * ── FRESHNESS ────────────────────────────────────────────────────────────────────────────────
 * A stale blueprint is a serious bug (wrong stats on a real ship), so invalidation is three
 * independent mechanisms, and the first is the one that actually carries the load:
 *
 *  1. DEPLOY. The key prefix is Manager::getCachePrefix() — `<dbname>_<deployVersion>_`, the
 *     same one the gamedata cache uses — so a deploy that rebuilds the bundles orphans every
 *     entry. Belt and braces, the deploy ritual ALSO runs generateStaticShipFileWeb.php in the
 *     browser, which ends in apcu_clear_cache(). That second path is what makes this safe even
 *     for a PHP-only deploy that leaves the bundle mtime alone.
 *     ⭐ This gives game.php exactly the freshness contract the LOBBY already has: both are
 *     refreshed by the same deploy step. If ship data ships without that step, the lobby's
 *     static/json files are stale too — this cache does not add a failure mode, it joins one.
 *
 *  2. PER-CLASS FILE MTIME. Each entry records the defining file and its mtime, and a read that
 *     finds the file changed rebuilds. Catches the common case by far — someone edits a ship in
 *     place — and on the dev box it is what makes an edited ship show up on the next reload
 *     without anyone having to think about the cache.
 *     ⚠️ It does NOT catch an edit to a WEAPON or SYSTEM file, which changes the blueprints of
 *     every ship mounting it while leaving those ships' own files untouched. Mechanism 1 covers
 *     that. Do not let this one create false confidence.
 *     ⚠️ AND IT IS WHY THE DEV BOX LOOKS SLOW. 60 stat()s cost 0.13 ms on a Linux filesystem
 *     (0.0021 ms each — free, keep the check) but 136 ms through a Docker Desktop WINDOWS BIND
 *     MOUNT (2.27 ms each, a 1,000x difference, measured 2026-08-24). So the warm path reads
 *     ~4 ms on live and ~147 ms on the Windows dev box. That is an artifact of the mount, not of
 *     this code — the dev box is still faster than the 233 ms it replaced. Do not "optimise" the
 *     check away on the strength of a local profile.
 *
 *  3. TTL. A six-hour expiry so that even a botched deploy self-heals rather than serving stale
 *     blueprints indefinitely. Per-class entries mean the rebuilds spread out instead of landing
 *     as one spike.
 *
 * NOT autoloaded by contract: source/autoload.php is a generated classmap, so a brand-new file
 * is invisible to it until the generator next runs. Callers require_once it explicitly, the same
 * pattern global.php uses for AssetLoader and the generators use for ShipCompactor.
 */
class BlueprintCache
{
    /**
     * Bumped whenever the surface blueprintCacheStatus.php relies on changes.
     *
     * That page and this class are two files that get deployed independently, and a skew between
     * them is silent in the worst way: PHP discards extra arguments to a userland function, so an
     * older class turns the page's "build it fresh and compare" check into a second cached read —
     * two identical timings and a confident green PASS that compared the cache with itself. The
     * page checks this constant and says so instead of guessing.
     *
     * 1 = original. 2 = added $bypassCache + $lastStats.
     */
    public const VERSION = 2;

    /** Backstop expiry (seconds). See FRESHNESS 3. */
    public const TTL = 21600; // 6 hours

    /** Set true to error_log a HIT/MISS/STORE line per class. Noisy; local debugging only. */
    public const DEBUG = false;

    /**
     * What the LAST getStaticShipsJson() call actually did: how many classes came from APCu and
     * how many were instantiated from source. Reset per call.
     *
     * ⚠️ This exists because blueprintCacheStatus.php's headline check was able to pass while
     * testing NOTHING. It compares a cached build against a $bypassCache build — but PHP silently
     * DISCARDS extra arguments to a userland function, so against a deployed copy of this file
     * that predates the $bypassCache parameter the "fresh" build was simply a second cached read.
     * Two identical fast timings and a green PASS, comparing the cache with itself. The page now
     * asserts on `built` here instead of trusting the argument to have been honoured.
     */
    public static $lastStats = ['hit' => 0, 'built' => 0, 'bypassed' => false];

    /**
     * The complete JSON string for `window.staticShips`, given the ship classes in this game.
     *
     * Reproduces game.php's original block exactly — including the spawnable-class expansion and
     * the order everything lands in — so the output is byte-identical to what that block would
     * have produced had it called ShipCompactor. That is deliberate: it makes the change
     * verifiable by diffing two strings rather than by reasoning about it.
     *
     * $bypassCache builds everything from source, ignoring (and NOT writing) APCu. It exists so
     * blueprintCacheStatus.php can put the cached answer and the from-scratch answer side by side
     * on real data — "is what we are serving still what a fresh build would produce" is the one
     * question about this class that cannot be answered by looking at the page. Nothing in the
     * request path passes it.
     */
    public static function getStaticShipsJson(array $shipClasses, bool $bypassCache = false): string
    {
        self::$lastStats = ['hit' => 0, 'built' => 0, 'bypassed' => $bypassCache];

        $primary = self::resolve(array_values(array_unique($shipClasses)), $bypassCache);
        $grouped = self::groupByFaction($primary);

        /* Spawnable expansion. The original walked the live blueprint OBJECTS for this; the
           cache cannot afford to instantiate them, so each entry carries the classes its
           blueprint contributes ('s') and whether it mounts a Hangar ('h'), both recorded at
           fill time in the original's iteration order. The faction-shuttle pass below needs
           nothing but the faction name, so it stays a live call. */
        $spawnableClasses = [];
        $factionsWithHangars = [];
        foreach ($grouped as $faction => $entries) {
            foreach ($entries as $entry) {
                foreach ($entry['s'] as $cls) $spawnableClasses[] = $cls;
                if ($entry['h']) $factionsWithHangars[$faction] = true;
            }
        }
        foreach (array_keys($factionsWithHangars) as $faction) {
            $factionShuttle = HangarOps::shuttleClassForFactionName($faction);
            if ($factionShuttle !== null) $spawnableClasses[] = $factionShuttle;
            $factionMsw = HangarOps::minesweepingShuttleClassForFactionName($faction);
            if ($factionMsw !== null) $spawnableClasses[] = $factionMsw;
        }

        if (!empty($spawnableClasses)) {
            /* Grouped by faction BEFORE merging, not merged class by class: the original fed the
               spawnables through their own getShipsByClass() call, so a faction appearing for the
               first time here is appended in first-seen order of THAT grouping. Merging per class
               would append them in class order instead and shuffle the JSON's key order. */
            $extra = self::groupByFaction(self::resolve(array_values(array_unique($spawnableClasses)), $bypassCache));
            foreach ($extra as $faction => $entries) {
                if (!isset($grouped[$faction])) $grouped[$faction] = [];
                foreach ($entries as $cls => $entry) $grouped[$faction][$cls] = $entry;
            }
        }

        return self::assemble($grouped);
    }

    /**
     * class name => entry, in the order requested. Cache hits are taken as-is; everything left
     * is built in ONE getShipsByClass() call (it sets the Enhancements offer-list guards around
     * its loop, and going through it rather than around it is what keeps
     * arch_shiploader_cache_traps' 6.4s getFactionDirMap out of the page).
     */
    private static function resolve(array $classes, bool $bypassCache = false): array
    {
        $prefix = self::prefix();
        $hits = [];
        $missing = [];

        foreach ($classes as $name) {
            $entry = $bypassCache ? null : self::fetch($prefix, $name);
            if ($entry !== null) $hits[$name] = $entry;
            else $missing[] = $name;
        }

        $built = empty($missing) ? [] : self::build($missing);
        if (!$bypassCache) {
            foreach ($built as $name => $entry) self::store($prefix, $name, $entry);
        }

        self::$lastStats['hit']   += count($hits);
        self::$lastStats['built'] += count($built);

        /* Re-order to the requested sequence. A class that does not exist is in neither map and
           simply drops out, exactly as class_exists() in getShipsByClass makes it drop out today. */
        $out = [];
        foreach ($classes as $name) {
            if (isset($hits[$name])) $out[$name] = $hits[$name];
            elseif (isset($built[$name])) $out[$name] = $built[$name];
        }
        return $out;
    }

    /** faction => [class => entry], factions in first-seen order — getShipsByClass's own shape. */
    private static function groupByFaction(array $entries): array
    {
        $out = [];
        foreach ($entries as $name => $entry) {
            if (!isset($out[$entry['f']])) $out[$entry['f']] = [];
            $out[$entry['f']][$name] = $entry;
        }
        return $out;
    }

    /**
     * Instantiate the given classes and render each to a cache entry.
     *
     * The spawnable scan here is a line-for-line copy of what game.php used to do inline; if that
     * ever changes, this must change with it or a mid-game spawn will find no blueprint on the
     * client and render its raw phpclass.
     */
    private static function build(array $classes): array
    {
        $ships = ShipLoader::getShipsByClass($classes);
        $out = [];

        foreach ($ships as $faction => $blueprints) {
            foreach ($blueprints as $cls => $ship) {
                $spawn = [];
                $hasHangar = false;

                foreach ($ship->systems as $system) {
                    if (!empty($system->spawnableClasses)) {
                        foreach ($system->spawnableClasses as $c) $spawn[] = $c;
                    }
                    if ($system instanceof Hangar) {
                        $hasHangar = true;
                        if (!empty($system->allowedFighterClasses)) {
                            foreach ($system->allowedFighterClasses as $c) $spawn[] = $c;
                        }
                    }
                }
                if (!empty($ship->fighters) && is_array($ship->fighters)) {
                    foreach ($ship->fighters as $category => $count) {
                        $shuttleClass = HangarOps::shuttlePhpclassForCategory($category, $ship);
                        if ($shuttleClass !== null) $spawn[] = $shuttleClass;
                    }
                }

                /* JSON_PARTIAL_OUTPUT_ON_ERROR matches the static generator. Without it a single
                   malformed UTF-8 byte in one ship's notes makes json_encode return false, and
                   the whole window.staticShips assignment becomes `false` — every blueprint on
                   the page lost to one bad character. Identical output when nothing is wrong. */
                $json = json_encode(ShipCompactor::compactShipObject($ship), JSON_PARTIAL_OUTPUT_ON_ERROR);
                if ($json === false) continue;

                $file = self::definingFile($cls);
                $out[$cls] = [
                    'f' => $faction,
                    'j' => $json,
                    's' => $spawn,
                    'h' => $hasHangar,
                    'p' => $file,
                    'm' => ($file !== null && is_file($file)) ? filemtime($file) : 0,
                ];
            }
        }
        return $out;
    }

    /** Concatenate the cached fragments. Avoids re-encoding megabytes that are already JSON. */
    private static function assemble(array $grouped): string
    {
        /* An empty result stays `[]`, which is what json_encode(array()) produced here before.
           ship.js guards with `window.staticShips[json.faction]` so either shape is harmless,
           but matching the old bytes keeps "no ships" off the list of things to re-verify. */
        if (empty($grouped)) return '[]';

        $factions = [];
        foreach ($grouped as $faction => $entries) {
            $inner = [];
            foreach ($entries as $cls => $entry) {
                $inner[] = json_encode((string)$cls) . ':' . $entry['j'];
            }
            $factions[] = json_encode((string)$faction) . ':{' . implode(',', $inner) . '}';
        }
        return '{' . implode(',', $factions) . '}';
    }

    // ── APCu plumbing ────────────────────────────────────────────────────────────────────────

    private static function enabled(): bool
    {
        static $on = null;
        if ($on === null) {
            $on = function_exists('apcu_fetch') && function_exists('apcu_store')
                  && function_exists('apcu_enabled') && apcu_enabled();
        }
        return $on;
    }

    /* Shares the gamedata cache's prefix so one deploy invalidates both, and so a stray
       apcu_clear_cache() cannot leave the two disagreeing. Guarded: a context without Manager
       still works, it just loses deploy-scoped invalidation (and the TTL still applies). */
    private static function prefix(): string
    {
        static $prefix = null;
        if ($prefix === null) {
            $prefix = (class_exists('Manager') ? Manager::getCachePrefix() : 'default_v0_') . 'bp_';
        }
        return $prefix;
    }

    /**
     * Returns the entry with 'j' as plain JSON — the deflate below is invisible to callers.
     *
     * ⚠️ WHY THE FRAGMENTS ARE STORED COMPRESSED. A blueprint fragment averages 22 KB, and there
     * are 2,573 ship classes: cache them all uncompressed and this alone is 55.7 MB against an
     * apc.shm_size of 64 MB — SHARED with the per-game gamedata cache. Filling the segment does
     * not just cost us our own entries, it starts evicting theirs, and the two then thrash each
     * other. gzdeflate(6) gets a 5x ratio (a 60-class game's 71 entries hold 1.59 MB of JSON in
     * 0.32 MB of APCu), putting the every-class-in-the-codebase worst case at 11.4 MB, which
     * fits with room to spare. Costs ~2.5 ms of gzinflate on a warm 60-class request — against
     * the ~230 ms this class removes — and ~21 ms of deflate once per class per deploy.
     * Measured 2026-08-24.
     *
     * A fragment that fails to inflate is treated as a MISS and rebuilt, so a corrupt or
     * truncated entry can never reach the page as garbage.
     */
    private static function fetch(string $prefix, string $name)
    {
        if (!self::enabled()) return null;

        $ok = false;
        $entry = apcu_fetch($prefix . $name, $ok);
        if (!$ok || !is_array($entry) || !isset($entry['j'], $entry['f'])) {
            self::log('MISS ' . $name);
            return null;
        }

        /* ⚠️ try/catch, NOT `@`, on every one of the three calls below. Manager.php installs a
           set_error_handler at file scope that throws ErrorException UNCONDITIONALLY — it does
           not consult error_reporting(), so `@` suppresses nothing in any request that has
           loaded Manager, i.e. every game page. `@filemtime()` on a deleted file and
           `@gzinflate()` on a damaged fragment are both WARNINGS, and both would come out as an
           uncaught fatal on the game screen rather than the cache miss they are meant to be.
           Verified the hard way: a deliberately corrupted fragment fataled the page.
           See arch_shiploader_cache_traps. */

        // FRESHNESS 2 — the class file changed under us.
        if (!empty($entry['p'])) {
            try {
                $mtime = filemtime($entry['p']);
            } catch (Throwable $e) {
                $mtime = false;
            }
            if ($mtime === false || $mtime !== ($entry['m'] ?? 0)) {
                self::log('STALE ' . $name);
                return null;
            }
        }

        if (!empty($entry['z'])) {
            try {
                $plain = gzinflate($entry['j']);
            } catch (Throwable $e) {
                $plain = false;
            }
            if (!is_string($plain)) {
                self::log('CORRUPT ' . $name);
                return null;   // rebuilt from source; never emitted as garbage
            }
            $entry['j'] = $plain;
            $entry['z'] = false;
        }

        self::log('HIT ' . $name);
        return $entry;
    }

    private static function store(string $prefix, string $name, array $entry): void
    {
        if (!self::enabled()) return;

        $plainLen = strlen($entry['j']);
        try {
            $packed = gzdeflate($entry['j'], 6);
        } catch (Throwable $e) {
            $packed = false;
        }
        if (is_string($packed) && strlen($packed) < $plainLen) {
            $entry['j'] = $packed;
            $entry['z'] = true;
        }

        apcu_store($prefix . $name, $entry, self::TTL);
        self::log('STORE ' . $name . ' (' . $plainLen . 'B -> ' . strlen($entry['j']) . 'B)');
    }

    /* Where the class is declared, for the mtime check. Safe to call: the class is loaded by
       this point (build() has just instantiated it), so no autoload is triggered. Returns null
       for anything Reflection cannot place, which simply opts that entry out of check 2. */
    private static function definingFile(string $cls): ?string
    {
        try {
            $file = (new ReflectionClass($cls))->getFileName();
            return is_string($file) ? $file : null;
        } catch (Throwable $e) {
            return null;
        }
    }

    private static function log(string $msg): void
    {
        if (self::DEBUG) error_log('[BlueprintCache] ' . $msg);
    }
}
