<?php
/**
 * PollInstrument — measure how many gamedata.php requests are actually in flight at once.
 *
 * WHY THIS EXISTS
 * ---------------
 * CHAT_DB_RESILIENCE_PLAN.md item 6 wants a concurrency cap on gamedata.php, and its own
 * warning is the reason this file comes first:
 *
 *     "Pick the limit from a measurement, not a guess. A cap set too low is
 *      indistinguishable to players from the outage it is meant to prevent."
 *
 * Nothing in the app could answer "how many at once, and for how long". This can. It is a
 * DIAGNOSTIC, meant to run on live for about a week and then be removed or left dormant —
 * it changes no behaviour, sheds no requests and blocks nothing.
 *
 * ⭐ IT MEASURES TWO POOLS, AND THE DIFFERENCE IS THE POINT
 * gamedata.php answers most polls from the APCu fast path and exits before Manager, a DB
 * connection or the ship tree ever happen. Those requests are nearly free. Sizing a cap on
 * total gamedata concurrency would therefore shed the cheap requests along with the
 * expensive ones, for no benefit.
 *
 *   - ALL   — every gamedata request. What a cap in server_load_guard.php would see, since
 *             the guard runs before the fast-poll check and cannot yet know which kind it is.
 *   - HEAVY — only those that fell through to the full build. What a cap placed AFTER the
 *             fast-poll exit would see. This is the number item 6 actually cares about.
 *
 * If HEAVY peaks well below ALL, that is the argument for moving the acquire point rather
 * than capping at the guard.
 *
 * COST
 * ----
 * Six to ten apcu_* calls per request, all O(1), against an endpoint that takes 100ms–4s.
 * No database, no filesystem — except one CSV append per HOUR, by one elected process.
 *
 * ⚠️ LEAK, AND HOW IT IS MADE VISIBLE RATHER THAN HIDDEN
 * An in-flight counter is incremented at the start and decremented by a shutdown function.
 * PHP runs shutdown functions on a normal end, exit(), an uncaught exception and a fatal
 * (including the memory limit) — but NOT if lsphp is hard-killed. Every missed decrement
 * leaves the counter permanently one too high, which over a week would quietly turn the
 * peak into fiction.
 *
 * So the hourly MINIMUM is recorded alongside the peak. A counter that is honest returns to
 * 0 whenever the site is briefly idle; a counter that has leaked has a floor it never drops
 * below, and that floor appears in the CSV as min_all climbing hour after hour. At each hour
 * boundary the floor is subtracted back off (apcu_dec by the min), which corrects the leak
 * without disturbing genuinely live requests. Read min_all as "how much of peak_all is a
 * lie" — if it is 0, the peak is trustworthy as it stands.
 *
 * ⚠️ APCu is per-server-instance shared memory. If the host ever runs this account in more
 * than one lsphp pool, each pool counts only itself and every number here is an undercount.
 * The CSV records the pid set size as a rough check on that.
 *
 * NOT AUTOLOADED, for the reason MaintenanceGate gives: source/autoload.php is a generated
 * classmap marked "do not edit". Callers require_once this file explicitly —
 * server_load_guard.php does, which covers every web request, and pollStats.php does for the
 * read-out.
 */
class PollInstrument
{
    /** Highest concurrency bucket tracked exactly; everything above lands in this bucket. */
    const MAX_BUCKET = 40;

    /** Upper edges in milliseconds for the duration histogram. */
    private static $durEdges = [25, 50, 100, 250, 500, 1000, 2000, 4000, 8000];

    /** Keep an hour's counters for eight days, so a full week is always readable. */
    const HOUR_TTL = 691200;

    private static $prefix = null;
    private static $started = false;
    private static $heavy   = false;
    private static $t0      = 0.0;
    private static $hour    = '';

    /**
     * Per-install key prefix, deliberately NOT deploy-versioned.
     *
     * server_load_guard.php's idiom, and for the same two reasons: /game/ and /testInstance/
     * must not share counters, and a week-long measurement must survive the deploys that
     * happen during that week. Manager::getCachePrefix() would orphan the whole dataset on
     * every code push, which is right for a cache and useless for a measurement.
     */
    private static function prefix()
    {
        if (self::$prefix === null) {
            self::$prefix = 'fv_' . substr(md5(dirname(__DIR__, 2)), 0, 8) . '_pi_';
        }
        return self::$prefix;
    }

    /**
     * CLI is excluded so a console script can never contribute to the counters — the same
     * reasoning server_load_guard.php gives for its own CLI bail-out. Data from a generator
     * run is not data about player polling, and silently mixing the two would corrupt the
     * measurement this whole file exists to produce.
     *
     * FV_PI_TESTING is the seam the test harness defines to exercise the mechanics from the
     * command line. Never define it in application code.
     */
    private static function usable()
    {
        return (PHP_SAPI !== 'cli' || defined('FV_PI_TESTING'))
            && function_exists('apcu_enabled')
            && apcu_enabled();
    }

    /**
     * Begin measuring this request. Safe to call for scripts we do not care about — it
     * returns immediately unless $script is one we are instrumenting.
     *
     * Called from server_load_guard.php, which global.php loads on every web request. That
     * is the earliest point that still knows the script name, so the measured duration
     * covers essentially the whole request rather than starting after the expensive part.
     */
    public static function begin($script)
    {
        if (self::$started || !self::usable()) return;

        // Only the two polls item 6 is about. chatdata.php is deliberately excluded: it is
        // already the cheapest path in the app and is not a candidate for capping.
        if (strpos($script, 'gamedata.php') === false
            && strpos($script, 'gamelobbyloader.php') === false) {
            return;
        }

        // Never let a diagnostic break the game. Everything below is best-effort.
        try {
            $p = self::prefix();
            self::$t0    = microtime(true);
            self::$hour  = gmdate('Y-m-d H');
            self::$started = true;

            self::rollHourIfNeeded($p, self::$hour);

            apcu_add($p . 'inflight_all', 0);
            $now = apcu_inc($p . 'inflight_all');
            if ($now === false) $now = 1;

            $h = $p . self::$hour . '_';
            apcu_inc($h . 'n', 1, $x1, self::HOUR_TTL);
            apcu_inc($h . 'cb_' . self::bucket($now), 1, $x2, self::HOUR_TTL);
            self::raise($h . 'peak_all', $now);
            self::lower($h . 'min_all', $now);

            // Rough evidence of how many php workers share this APCu segment. If the host
            // splits the account across pools these counters are per-pool undercounts, and
            // a suspiciously small pid set is the only hint available from inside.
            apcu_store($p . 'pid_' . getmypid(), time(), 3600);

            register_shutdown_function([__CLASS__, 'finish']);
        } catch (Throwable $e) {
            self::$started = false;
        }
    }

    /**
     * This request did NOT take the fast path — it is doing the full, expensive build.
     *
     * Called from gamedata.php immediately after the fast-poll check falls through. Must be
     * called there rather than inferred here, because by the time the shutdown function runs
     * the distinction is gone.
     */
    public static function markHeavy()
    {
        if (!self::$started || self::$heavy) return;

        try {
            $p = self::prefix();
            self::$heavy = true;

            apcu_add($p . 'inflight_heavy', 0);
            $now = apcu_inc($p . 'inflight_heavy');
            if ($now === false) $now = 1;

            $h = $p . self::$hour . '_';
            apcu_inc($h . 'nheavy', 1, $x1, self::HOUR_TTL);
            apcu_inc($h . 'hb_' . self::bucket($now), 1, $x2, self::HOUR_TTL);
            self::raise($h . 'peak_heavy', $now);
        } catch (Throwable $e) {
            // Leave $heavy true: the shutdown decrement must mirror whatever the increment did.
        }
    }

    /** Shutdown handler. Public only because register_shutdown_function needs it to be. */
    public static function finish()
    {
        if (!self::$started) return;

        try {
            $p  = self::prefix();
            $ms = (int) round((microtime(true) - self::$t0) * 1000);
            $h  = $p . self::$hour . '_';

            self::dec($p . 'inflight_all');
            if (self::$heavy) self::dec($p . 'inflight_heavy');

            apcu_inc($h . 'd_' . self::durBucket($ms), 1, $x1, self::HOUR_TTL);
            if (self::$heavy) {
                apcu_inc($h . 'dh_' . self::durBucket($ms), 1, $x2, self::HOUR_TTL);
            }
            self::raise($h . 'maxdur', $ms);

            // A request the load guard shed never reaches here with a 200, so record the
            // status: a rising 503 count means the EXISTING limiter is already biting, which
            // would change how the numbers above should be read.
            $code = function_exists('http_response_code') ? (int)http_response_code() : 200;
            if ($code >= 500) apcu_inc($h . 'err', 1, $x3, self::HOUR_TTL);
        } catch (Throwable $e) {
            // Nothing useful to do in a shutdown handler.
        }
    }

    // ---------------------------------------------------------------- helpers

    private static function bucket($n)
    {
        if ($n < 0) return 0;
        return $n > self::MAX_BUCKET ? self::MAX_BUCKET : $n;
    }

    private static function durBucket($ms)
    {
        foreach (self::$durEdges as $i => $edge) {
            if ($ms < $edge) return $i;
        }
        return count(self::$durEdges);
    }

    /**
     * Raise a stored maximum, with CAS so a concurrent update cannot be lost.
     *
     * A plain fetch/compare/store loses exactly the samples that matter here: two requests
     * observing a new peak at the same instant would both read the old value and one would
     * overwrite the other's higher figure. Bounded to 5 attempts because a diagnostic must
     * never be able to spin.
     */
    private static function raise($key, $value)
    {
        for ($i = 0; $i < 5; $i++) {
            $cur = apcu_fetch($key);
            if ($cur === false) {
                if (apcu_add($key, $value, self::HOUR_TTL)) return;
                continue;
            }
            if ($value <= (int)$cur) return;
            if (apcu_cas($key, (int)$cur, $value)) return;
        }
    }

    /** Lower a stored minimum. The leak detector — see the header. */
    private static function lower($key, $value)
    {
        for ($i = 0; $i < 5; $i++) {
            $cur = apcu_fetch($key);
            if ($cur === false) {
                if (apcu_add($key, $value, self::HOUR_TTL)) return;
                continue;
            }
            if ($value >= (int)$cur) return;
            if (apcu_cas($key, (int)$cur, $value)) return;
        }
    }

    /** Decrement without ever going negative — the hourly leak rebase can race with this. */
    private static function dec($key)
    {
        $v = apcu_fetch($key);
        if ($v !== false && (int)$v > 0) apcu_dec($key);
    }

    /**
     * At an hour boundary, append the finished hour to the CSV and correct any leak.
     *
     * The CSV exists because APCu does not survive an lsphp restart, and a week-long
     * measurement that evaporates on a routine restart is worthless. One append per hour,
     * by ONE process: apcu_add is atomic, so whichever request first notices the new hour
     * wins the election and the rest skip it.
     */
    private static function rollHourIfNeeded($p, $hour)
    {
        $last = apcu_fetch($p . 'last_hour');

        if ($last === false) {
            apcu_add($p . 'last_hour', $hour);
            return;
        }
        if ($last === $hour) return;

        // Exactly one writer per finished hour.
        if (!apcu_add($p . 'rollup_' . $last, 1, 86400)) {
            apcu_store($p . 'last_hour', $hour);
            return;
        }

        self::writeCsvLine($p, $last);
        apcu_store($p . 'last_hour', $hour);

        // Subtract the floor the counter never dropped below: that many slots are stuck from
        // requests whose shutdown handler never ran. Correcting here, once an hour, keeps the
        // error from compounding across a week without disturbing live requests.
        $minAll = apcu_fetch($p . $last . '_min_all');
        if ($minAll !== false && (int)$minAll > 0) {
            for ($i = 0; $i < (int)$minAll; $i++) self::dec($p . 'inflight_all');
        }
    }

    private static function writeCsvLine($p, $hour)
    {
        $h = $p . $hour . '_';
        $g = function ($suffix, $default = 0) use ($h) {
            $v = apcu_fetch($h . $suffix);
            return $v === false ? $default : (int)$v;
        };

        $cb = $hb = $d = $dh = [];
        for ($i = 0; $i <= self::MAX_BUCKET; $i++) {
            $v = apcu_fetch($h . 'cb_' . $i); if ($v !== false) $cb[$i] = (int)$v;
            $v = apcu_fetch($h . 'hb_' . $i); if ($v !== false) $hb[$i] = (int)$v;
        }
        for ($i = 0; $i <= count(self::$durEdges); $i++) {
            $v = apcu_fetch($h . 'd_' . $i);  if ($v !== false) $d[$i]  = (int)$v;
            $v = apcu_fetch($h . 'dh_' . $i); if ($v !== false) $dh[$i] = (int)$v;
        }

        $pack = function ($a) {
            $out = [];
            foreach ($a as $k => $v) $out[] = $k . ':' . $v;
            return implode('|', $out);
        };

        $pids = 0;
        if (class_exists('APCUIterator')) {
            foreach (new APCUIterator('/^' . preg_quote($p, '/') . 'pid_/') as $ignored) $pids++;
        }

        $row = [
            $hour,                 // UTC, hour granularity
            $g('n'),               // gamedata+lobby requests this hour
            $g('nheavy'),          // of those, full builds
            $g('peak_all'),        // highest simultaneous, all
            $g('peak_heavy'),      // highest simultaneous, full builds only
            $g('min_all'),         // LEAK FLOOR — see class header
            $g('maxdur'),          // slowest request, ms
            $g('err'),             // responses >= 500
            $pids,                 // php workers seen sharing this APCu segment
            $pack($cb),            // concurrency histogram, all
            $pack($hb),            // concurrency histogram, heavy
            $pack($d),             // duration histogram, all
            $pack($dh),            // duration histogram, heavy
        ];

        $dir  = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'logs';
        if (!is_dir($dir)) @mkdir($dir, 0777, true);
        $file = $dir . DIRECTORY_SEPARATOR . 'pollstats.csv';

        if (!file_exists($file)) {
            @file_put_contents($file, self::csvHeader(), FILE_APPEND | LOCK_EX);
        }
        @file_put_contents($file, implode(',', $row) . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

    public static function csvHeader()
    {
        return 'hour_utc,requests,heavy,peak_all,peak_heavy,min_all,maxdur_ms,err5xx,'
             . 'workers,hist_all,hist_heavy,dur_all,dur_heavy' . PHP_EOL;
    }

    /** Upper edges, so the viewer can label the duration histogram consistently. */
    public static function durEdges()
    {
        return self::$durEdges;
    }

    /** Live snapshot for pollStats.php. */
    public static function snapshot()
    {
        if (!self::usable()) return null;

        $p    = self::prefix();
        $hour = gmdate('Y-m-d H');
        $h    = $p . $hour . '_';

        $get = function ($k) {
            $v = apcu_fetch($k);
            return $v === false ? 0 : (int)$v;
        };

        $cb = $hb = $d = $dh = [];
        for ($i = 0; $i <= self::MAX_BUCKET; $i++) {
            $v = apcu_fetch($h . 'cb_' . $i); if ($v !== false) $cb[$i] = (int)$v;
            $v = apcu_fetch($h . 'hb_' . $i); if ($v !== false) $hb[$i] = (int)$v;
        }
        for ($i = 0; $i <= count(self::$durEdges); $i++) {
            $v = apcu_fetch($h . 'd_' . $i);  if ($v !== false) $d[$i]  = (int)$v;
            $v = apcu_fetch($h . 'dh_' . $i); if ($v !== false) $dh[$i] = (int)$v;
        }

        return [
            'prefix'        => $p,
            'hour'          => $hour,
            'inflight_all'  => $get($p . 'inflight_all'),
            'inflight_heavy'=> $get($p . 'inflight_heavy'),
            'requests'      => $get($h . 'n'),
            'heavy'         => $get($h . 'nheavy'),
            'peak_all'      => $get($h . 'peak_all'),
            'peak_heavy'    => $get($h . 'peak_heavy'),
            'min_all'       => $get($h . 'min_all'),
            'maxdur'        => $get($h . 'maxdur'),
            'err'           => $get($h . 'err'),
            'hist_all'      => $cb,
            'hist_heavy'    => $hb,
            'dur_all'       => $d,
            'dur_heavy'     => $dh,
        ];
    }
}
