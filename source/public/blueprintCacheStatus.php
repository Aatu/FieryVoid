<?php
/**
 * blueprintCacheStatus.php — is BlueprintCache working, and is it serving the right thing?
 *
 * Two questions this answers that the game page cannot:
 *   1. Is APCu actually caching? (game.php's HTML-comment timing shows the symptom; this shows
 *      the cause — whether the extension is on, what the deploy-scoped prefix is, how many
 *      blueprint entries exist and what they cost.)
 *   2. ⭐ Is the CACHED payload still byte-identical to a from-scratch build? A stale blueprint
 *      is invisible by inspection — the page looks perfectly normal while a ship has last
 *      deploy's stats. The SELF-TEST below builds both and diffs them.
 *
 * Gated by MaintenanceGate like the generators and mass_optimizer: ?key=<maintenance_key>.
 * Read-only apart from the explicit ?clear=1 action.
 */

require_once 'global.php';
require_once __DIR__ . '/../server/lib/MaintenanceGate.php';
MaintenanceGate::requireAccess('Blueprint cache status');

require_once __DIR__ . '/../server/lib/ShipCompactor.php';
require_once __DIR__ . '/../server/lib/BlueprintCache.php';

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');

$apcuOn = function_exists('apcu_enabled') && apcu_enabled();
$prefix = class_exists('Manager') ? Manager::getCachePrefix() . 'bp_' : '(no Manager)';

/* ⚠️ DEPLOY-SKEW CHECK, FIRST, because everything below assumes a matching class.
   This page needs BlueprintCache VERSION 2 ($bypassCache + $lastStats). Against version 1 the
   self-test silently degrades into comparing the cache with itself (PHP drops the extra
   argument), and reading ::$lastStats would be a fatal. Detect it and say so. */
$needVersion  = 2;
$haveVersion  = defined('BlueprintCache::VERSION') ? BlueprintCache::VERSION : 1;
$versionOK    = ($haveVersion >= $needVersion)
                && property_exists('BlueprintCache', 'lastStats');

/* ── actions ─────────────────────────────────────────────────────────────────────────────── */
$notice = '';
if (isset($_GET['clear']) && $apcuOn) {
    $n = 0;
    foreach (new APCUIterator('/^' . preg_quote($prefix, '/') . '/') as $item) {
        apcu_delete($item['key']);
        $n++;
    }
    $notice = "Cleared $n blueprint entries. The next game load rebuilds them.";
}

/* ── which classes to test ───────────────────────────────────────────────────────────────── */
$classes = [];
$source  = '';
$gameid  = isset($_GET['game']) ? (int)$_GET['game'] : 0;

if ($gameid > 0) {
    /* Its own mysqli rather than DBManager, whose query() is private — the same choice
       tests/replay/replayHarness.php made for its discovery queries. Read-only, one statement.

       ⚠️ try/catch, not `@` and not a connect_errno check. PHP 8's mysqli THROWS on a failed
       connect (mysqli_report defaults to ERROR|STRICT), so connect_errno is never reached; and
       `@` suppresses nothing here because Manager.php installs an error handler that rethrows
       every diagnostic regardless of error_reporting (arch_shiploader_cache_traps). Without this
       the whole diagnostic page fatals when the DB is unreachable — which is precisely when you
       would be looking at it. */
    try {
        $link = new mysqli($GLOBALS['database_host'], $GLOBALS['database_user'],
                           $GLOBALS['database_password'], $GLOBALS['database_name']);
        // NOTE the column is tacgameid, not gameid.
        $stmt = $link->prepare('SELECT phpclass FROM tac_ship WHERE tacgameid = ?');
        $stmt->bind_param('i', $gameid);
        $stmt->execute();
        $res = $stmt->get_result();
        while ($row = $res->fetch_assoc()) $classes[] = $row['phpclass'];
        $stmt->close();
        $link->close();
        $source = 'game ' . $gameid . ' — ' . count($classes) . ' ship rows, '
                . count(array_unique($classes)) . ' distinct classes';
    } catch (Throwable $e) {
        $source = 'could not read game ' . $gameid . ' (' . $e->getMessage()
                . ') — use ?classes=A,B,C instead';
        $classes = [];
    }
} elseif (!empty($_GET['classes'])) {
    $classes = array_filter(array_map('trim', explode(',', (string)$_GET['classes'])));
    $source  = 'explicit ?classes= list';
} elseif ($apcuOn) {
    // Fall back to whatever is already cached, so the page is useful with no parameters at all.
    foreach (new APCUIterator('/^' . preg_quote($prefix, '/') . '/') as $item) {
        $classes[] = substr($item['key'], strlen($prefix));
        if (count($classes) >= 40) break;
    }
    $source = 'sample of ' . count($classes) . ' classes already in the cache';
}

/* ── the self-test ───────────────────────────────────────────────────────────────────────── */
$test = null;
$wasCold = false;
if ($classes && empty($_GET['nocheck']) && $versionOK) {
    /* ORDER MATTERS, and getting it wrong makes the page lie. Run a priming pass first: on a cold
       cache the very first call is the one that instantiates everything, and timing THAT as "served
       from cache" reported the cache as 3x SLOWER than a fresh build. Both figures below are taken
       with PHP fully warmed (classes loaded, opcache primed) so they compare like with like. */
    $wasCold = $apcuOn && !apcu_exists($prefix . reset($classes));
    BlueprintCache::getStaticShipsJson($classes);                                        // prime

    $t = microtime(true); $cached = BlueprintCache::getStaticShipsJson($classes);        $tCached = (microtime(true) - $t) * 1000;
    $statsCached = BlueprintCache::$lastStats;
    $t = microtime(true); $fresh  = BlueprintCache::getStaticShipsJson($classes, true);  $tFresh  = (microtime(true) - $t) * 1000;
    $statsFresh = BlueprintCache::$lastStats;

    /* ⭐ Did the two runs actually do DIFFERENT things? Without this the headline check can pass
       while comparing the cache with itself — PHP discards extra arguments to a userland
       function, so a deployed BlueprintCache.php older than the $bypassCache parameter turns the
       "fresh" build into a second cached read. The tell was two identical sub-2ms timings for a
       28-class fleet, which is far too fast to have instantiated anything. */
    $conclusive = ($statsFresh['built'] > 0) && ($statsCached['hit'] > 0)
                  && !empty($statsFresh['bypassed']);

    $firstDiff = -1;
    if ($cached !== $fresh) {
        $n = min(strlen($cached), strlen($fresh));
        for ($i = 0; $i < $n; $i++) { if ($cached[$i] !== $fresh[$i]) { $firstDiff = $i; break; } }
        if ($firstDiff < 0) $firstDiff = $n;
    }
    $test = [
        'match'     => ($cached === $fresh),
        'tCached'   => $tCached,
        'tFresh'    => $tFresh,
        'bytes'     => strlen($cached),
        'validJson' => (json_decode($cached) !== null),
        'firstDiff'   => $firstDiff,
        'wasCold'     => $wasCold,
        'conclusive'  => $conclusive,
        'hitCount'    => $statsCached['hit'],
        'hitBuilt'    => $statsCached['built'],
        'freshBuilt'  => $statsFresh['built'],
        'bypassSeen'  => !empty($statsFresh['bypassed']),
        'ctx'       => $firstDiff >= 0 ? [substr($fresh, max(0, $firstDiff - 80), 200),
                                          substr($cached, max(0, $firstDiff - 80), 200)] : null,
    ];
}

/* ── cache contents ──────────────────────────────────────────────────────────────────────── */
/* Read AFTER the self-test: on a cold cache the test is what populates it, and reporting a
   pre-test count of 0 next to a passing test just looks broken. */
$entries = 0; $mem = 0; $deflated = 0; $plainBytes = 0; $oldest = null;
if ($apcuOn) {
    foreach (new APCUIterator('/^' . preg_quote($prefix, '/') . '/') as $item) {
        $entries++;
        $mem += $item['mem_size'];
        $v = $item['value'];
        if (is_array($v) && isset($v['j'])) {
            /* Inflate before measuring. Reading strlen($v['j']) straight off the entry reports the
               COMPRESSED size as if it were the JSON, which made the ratio come out at 0.9x — i.e.
               "we are spending more APCu than we are storing", the opposite of what is happening. */
            if (!empty($v['z'])) {
                $deflated++;
                try { $p = gzinflate($v['j']); } catch (Throwable $e) { $p = false; }
                $plainBytes += is_string($p) ? strlen($p) : strlen($v['j']);
            } else {
                $plainBytes += strlen($v['j']);
            }
        }
        $t = $item['creation_time'];
        if ($oldest === null || $t < $oldest) $oldest = $t;
    }
}

$fmt = function ($b) { return $b >= 1048576 ? round($b / 1048576, 2) . ' MB' : round($b / 1024, 1) . ' KB'; };
$ok  = function ($b) { return $b ? '<span class="ok">PASS</span>' : '<span class="bad">FAIL</span>'; };
?>
<!DOCTYPE html>
<html>
<head>
<title>Blueprint cache status</title>
<style>
 body{font-family:ui-monospace,Consolas,monospace;background:#0a161c;color:#dde;padding:24px;line-height:1.55}
 h1{font-size:19px;color:#7fd1ff;margin:0 0 4px} h2{font-size:15px;color:#7fd1ff;margin:26px 0 8px}
 .sub{color:#8fa3ad;margin-bottom:18px}
 table{border-collapse:collapse;margin-bottom:8px} td{padding:3px 22px 3px 0;vertical-align:top}
 td:first-child{color:#8fa3ad}
 .ok{color:#6ee787;font-weight:bold} .bad{color:#ff7b72;font-weight:bold} .warn{color:#e3b341}
 .box{border-left:3px solid #24404f;padding:10px 16px;margin:12px 0;background:#0e1e26}
 .bad-box{border-left-color:#ff7b72}
 pre{white-space:pre-wrap;word-break:break-all;font-size:12px;color:#9fb;margin:6px 0}
 a{color:#7fd1ff} .note{color:#8fa3ad;font-size:13px}
</style>
</head>
<body>
<h1>Blueprint cache status</h1>
<div class="sub">source/server/lib/BlueprintCache.php &mdash; <?php echo htmlspecialchars($source ?: 'no classes selected'); ?></div>

<?php if ($notice): ?><div class="box"><?php echo htmlspecialchars($notice); ?></div><?php endif; ?>

<?php if (!$versionOK): ?>
<div class="box bad-box">
<p class="bad">DEPLOY SKEW &mdash; this page needs BlueprintCache version <?php echo $needVersion; ?>,
the deployed class is version <?php echo $haveVersion; ?>.</p>
<p>The self-test below is disabled rather than run, because against the older class it cannot do
what it claims: PHP silently discards the extra <code>$bypassCache</code> argument, so the
"built from source" run would just be a second cached read &mdash; two identical timings and a
green PASS that proved nothing.</p>
<p>Deploy the matching <code>source/server/lib/BlueprintCache.php</code> and reload.
(The cache itself is fine and game.php is unaffected; only this diagnostic needs the newer file.)</p>
</div>
<?php endif; ?>

<h2>1. Is the cache alive?</h2>
<table>
<tr><td>APCu</td><td><?php echo $apcuOn ? '<span class="ok">enabled</span>'
    : '<span class="bad">NOT enabled</span> &mdash; every request rebuilds from source (still correct, just slow)'; ?></td></tr>
<tr><td>key prefix</td><td><?php echo htmlspecialchars($prefix); ?>
    <span class="note">(changes on every deploy &mdash; that is the invalidation)</span></td></tr>
<tr><td>blueprint entries</td><td><?php echo $entries; ?></td></tr>
<tr><td>APCu memory</td><td><?php echo $fmt($mem); ?>
    <?php if ($plainBytes): ?><span class="note">holding <?php echo $fmt($plainBytes); ?> of JSON
    (<?php echo round($plainBytes / max(1, $mem), 1); ?>x, <?php echo $deflated; ?> deflated)</span><?php endif; ?></td></tr>
<tr><td>oldest entry</td><td><?php echo $oldest ? (round((time() - $oldest) / 60) . ' min old  (TTL '
    . round(BlueprintCache::TTL / 3600) . 'h)') : '&mdash;'; ?></td></tr>
<tr><td>apc.shm_size</td><td><?php echo htmlspecialchars(ini_get('apc.shm_size')); ?>
    <span class="note">shared with the gamedata cache</span></td></tr>
</table>

<h2>2. Is it serving the right thing?</h2>
<?php if (!$test): ?>
  <div class="note">No classes to test. Add <code>?game=&lt;id&gt;</code> (best &mdash; uses a real fleet)
  or <code>?classes=Sharlin,Omega</code>.</div>
<?php else: ?>
  <div class="box <?php echo ($test['match'] && $test['conclusive']) ? '' : 'bad-box'; ?>">
  <table>
  <tr><td>cached === freshly built</td><td><?php
      echo $test['conclusive'] ? $ok($test['match']) : '<span class="warn">INCONCLUSIVE</span>'; ?>
      <span class="note">&mdash; the check that matters: a stale blueprint looks normal on the page</span></td></tr>
  <tr><td>&nbsp;&nbsp;classes compared</td><td class="note">cache run: <?php echo $test['hitCount']; ?> from APCu<?php
      if ($test['hitBuilt']) echo ' + ' . $test['hitBuilt'] . ' built'; ?>
      &nbsp;|&nbsp; fresh run: <?php echo $test['freshBuilt']; ?> built from source
      <?php if (!$test['bypassSeen']): ?><span class="bad">&mdash; bypass flag NOT honoured</span><?php endif; ?></td></tr>
  <tr><td>output is valid JSON</td><td><?php echo $ok($test['validJson']); ?></td></tr>
  <tr><td>payload</td><td><?php echo $fmt($test['bytes']); ?> inlined into game.php</td></tr>
  <tr><td>served from cache</td><td><?php printf('%.1f ms', $test['tCached']); ?></td></tr>
  <tr><td>built from source</td><td><?php printf('%.1f ms', $test['tFresh']); ?>
      <span class="note">&mdash; the cache-miss path. Slightly MORE than the pre-change code cost:
      that did getShipsByClass + a raw json_encode, and did not compact. Read it as an upper
      bound on the saving, not an exact "before".</span></td></tr>
  <tr><td>saving</td><td><?php printf('%.1f ms per game page load (%.0f%%)',
      $test['tFresh'] - $test['tCached'],
      $test['tFresh'] > 0 ? 100 * ($test['tFresh'] - $test['tCached']) / $test['tFresh'] : 0); ?>
      <?php if ($test['tCached'] > $test['tFresh']): ?>
        <span class="warn">&mdash; cache slower than a rebuild: expected on a Windows/Docker dev box
        (bind-mount stat() is ~1000x slower than on Linux), NOT expected on the server</span>
      <?php endif; ?></td></tr>
  <?php if ($test['wasCold']): ?>
  <tr><td></td><td class="note">The cache was cold when this page loaded, so it has just been
      populated for these classes. Reload for steady-state figures.</td></tr>
  <?php endif; ?>
  </table>
  <?php if (!$test['conclusive']): ?>
    <p class="warn">This comparison proved nothing &mdash; the two runs did not do different work,
    so the cache was effectively compared with itself.
    <?php if (!$test['bypassSeen']): ?>
      <strong>The deployed <code>BlueprintCache.php</code> is older than this page</strong>: it has no
      <code>$bypassCache</code> parameter, and PHP silently discards the extra argument. Deploy the
      matching <code>source/server/lib/BlueprintCache.php</code> and reload.
    <?php elseif ($test['freshBuilt'] === 0): ?>
      The from-source run built 0 classes &mdash; check that the class names really exist
      (a typo'd <code>?classes=</code> list, or a game whose hulls are no longer in the codebase).
    <?php else: ?>
      The cached run served 0 classes from APCu &mdash; the cache is not retaining anything between
      requests. Check APCu above.
    <?php endif; ?></p>
  <?php endif; ?>
  <?php if ($test['conclusive'] && !$test['match']): ?>
    <p class="bad">MISMATCH at byte <?php echo $test['firstDiff']; ?> &mdash; the cache is serving something
    a fresh build would not produce. Hit <a href="?clear=1">clear the cache</a> and reload; if it recurs,
    that is a real bug, not staleness.</p>
    <pre>fresh : ...<?php echo htmlspecialchars($test['ctx'][0]); ?></pre>
    <pre>cached: ...<?php echo htmlspecialchars($test['ctx'][1]); ?></pre>
  <?php endif; ?>
  </div>
<?php endif; ?>

<h2>3. Actions</h2>
<div class="note">
<a href="?clear=1<?php echo $gameid ? '&game=' . $gameid : ''; ?>">Clear the blueprint cache</a>
 &mdash; safe at any time; the next game load rebuilds it.<br>
Re-run against a real game: append <code>&amp;game=&lt;gameid&gt;</code> to this URL.<br>
<br>
On the game page itself, View Source and read the last line of the
<code>PHP Execution Diagnostics</code> comment at the bottom:
<code>BlueprintCache::getStaticShipsJson Time</code>. First load after a deploy is cold; the second
should drop to a few ms.
</div>

</body>
</html>
