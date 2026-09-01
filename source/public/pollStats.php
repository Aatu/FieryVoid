<?php
/**
 * pollStats.php — read out what PollInstrument has measured.
 *
 * Groundwork for CHAT_DB_RESILIENCE_PLAN item 6: it answers "how many gamedata.php requests
 * are actually in flight at once, and for how long", which is the measurement item 6 refuses
 * to be built without.
 *
 * Two sources, and the difference between them matters:
 *   - the CURRENT hour, live from APCu (not yet written to the CSV);
 *   - every completed hour, from source/logs/pollstats.csv, which survives the lsphp
 *     restarts that wipe APCu.
 *
 * Gated by MaintenanceGate like the generators and blueprintCacheStatus: ?key=<maintenance_key>.
 * Strictly read-only apart from ?reset=1, which clears the collected data.
 */

require_once 'global.php';
require_once __DIR__ . '/../server/lib/MaintenanceGate.php';
MaintenanceGate::requireAccess('Poll statistics');

require_once __DIR__ . '/../server/lib/PollInstrument.php';

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');

$snap = PollInstrument::snapshot();
$csv  = dirname(__DIR__) . '/logs/pollstats.csv';
$off  = dirname(__DIR__) . '/logs/pollstats.off';

$rows = [];
if (is_file($csv)) {
    $lines = file($csv, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    array_shift($lines);                       // header
    foreach (array_reverse($lines) as $line) { // newest first
        $rows[] = str_getcsv($line);
    }
}

/** "3:120|4:44" -> [3=>120, 4=>44] */
function pi_unpack($s)
{
    $out = [];
    foreach (explode('|', (string)$s) as $pair) {
        if ($pair === '') continue;
        $kv = explode(':', $pair, 2);
        if (count($kv) === 2) $out[(int)$kv[0]] = (int)$kv[1];
    }
    return $out;
}

/**
 * The number item 6 needs: the concurrency level at or below which $pct of requests arrived.
 * A cap belongs ABOVE this, so it only engages during a genuine pile-up rather than during
 * ordinary busy moments — which is exactly the failure mode the plan warns about
 * ("indistinguishable to players from the outage it is meant to prevent").
 */
function pi_percentile($hist, $pct)
{
    $total = array_sum($hist);
    if ($total === 0) return 0;
    ksort($hist);
    $target = $total * $pct / 100;
    $run = 0;
    foreach ($hist as $level => $count) {
        $run += $count;
        if ($run >= $target) return $level;
    }
    return array_key_last($hist);
}

function pi_dur_labels()
{
    $edges = PollInstrument::durEdges();
    $labels = [];
    $prev = 0;
    foreach ($edges as $e) { $labels[] = $prev . '-' . $e . 'ms'; $prev = $e; }
    $labels[] = $prev . 'ms+';
    return $labels;
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

/* Aggregate the whole collection period — the figure a cap is actually chosen from. */
$allHist = [];
$heavyHist = [];
$peakAll = $peakHeavy = $maxMin = 0;
$totalReq = $totalHeavy = 0;
foreach ($rows as $r) {
    $totalReq   += (int)($r[1] ?? 0);
    $totalHeavy += (int)($r[2] ?? 0);
    $peakAll     = max($peakAll,   (int)($r[3] ?? 0));
    $peakHeavy   = max($peakHeavy, (int)($r[4] ?? 0));
    $maxMin      = max($maxMin,    (int)($r[5] ?? 0));
    foreach (pi_unpack($r[9]  ?? '') as $k => $v) $allHist[$k]   = ($allHist[$k]   ?? 0) + $v;
    foreach (pi_unpack($r[10] ?? '') as $k => $v) $heavyHist[$k] = ($heavyHist[$k] ?? 0) + $v;
}
?>
<!doctype html>
<meta charset="utf-8">
<title>Poll statistics — gamedata concurrency</title>
<style>
  body { background:#0d1520; color:#c8dced; font:13px/1.5 system-ui, sans-serif; margin:24px; }
  h1 { font-size:18px; color:#7fd1ff; margin:0 0 4px; }
  h2 { font-size:14px; color:#7fd1ff; margin:26px 0 8px; }
  p.sub { color:#8ca5c0; margin:0 0 18px; }
  table { border-collapse:collapse; margin:8px 0 4px; font-variant-numeric:tabular-nums; }
  th, td { border:1px solid #24384f; padding:3px 9px; text-align:right; }
  th { background:#152539; color:#8ca5c0; font-weight:600; }
  td.l, th.l { text-align:left; }
  .big { font-size:26px; color:#7fd1ff; font-weight:700; }
  .warn { color:#ffd84d; }
  .ok { color:#7bd88f; }
  .note { color:#8ca5c0; max-width:70ch; }
  .bar { display:inline-block; height:9px; background:#2b6ea8; vertical-align:middle; }
  code { background:#152539; padding:1px 5px; border-radius:3px; }
</style>

<h1>Poll statistics — gamedata.php concurrency</h1>
<p class="sub">Groundwork for CHAT_DB_RESILIENCE_PLAN item 6. Measures only; limits nothing.</p>

<?php if ($snap === null): ?>
  <p class="warn">APCu is not available — nothing is being measured.</p>
<?php else: ?>

<?php if (is_file($off)): ?>
  <p class="warn">⚠️ Kill switch present (<code>logs/pollstats.off</code>) — collection is STOPPED.
     Delete that file to resume.</p>
<?php endif; ?>

<h2>Right now</h2>
<table>
  <tr><th class="l">In flight, all gamedata</th><td class="big"><?= (int)$snap['inflight_all'] ?></td></tr>
  <tr><th class="l">In flight, full builds only</th><td class="big"><?= (int)$snap['inflight_heavy'] ?></td></tr>
  <tr><th class="l">This hour (<?= h($snap['hour']) ?> UTC), requests</th><td><?= (int)$snap['requests'] ?></td></tr>
  <tr><th class="l">…of which full builds</th><td><?= (int)$snap['heavy'] ?></td></tr>
  <tr><th class="l">Peak this hour, all</th><td><?= (int)$snap['peak_all'] ?></td></tr>
  <tr><th class="l">Peak this hour, full builds</th><td><?= (int)$snap['peak_heavy'] ?></td></tr>
  <tr><th class="l">Slowest this hour</th><td><?= (int)$snap['maxdur'] ?> ms</td></tr>
  <tr><th class="l">5xx this hour</th><td><?= (int)$snap['err'] ?></td></tr>
</table>

<h2>Collected so far — <?= count($rows) ?> complete hours</h2>
<?php if (!$rows): ?>
  <p class="note">No completed hours yet. The first CSV line is written when the hour rolls
     over and a request arrives to notice it.</p>
<?php else: ?>
<table>
  <tr><th class="l">Requests measured</th><td><?= number_format($totalReq) ?></td></tr>
  <tr><th class="l">…full builds</th><td><?= number_format($totalHeavy) ?>
      (<?= $totalReq ? round(100 * $totalHeavy / $totalReq) : 0 ?>%)</td></tr>
  <tr><th class="l">Highest concurrency, all</th><td class="big"><?= $peakAll ?></td></tr>
  <tr><th class="l">Highest concurrency, full builds</th><td class="big"><?= $peakHeavy ?></td></tr>
  <tr><th class="l">Worst leak floor seen (min_all)</th>
      <td class="<?= $maxMin > 0 ? 'warn' : 'ok' ?>"><?= $maxMin ?></td></tr>
</table>

<p class="note">
  <strong>Read the leak floor first.</strong> If it is <span class="ok">0</span>, the counter
  returned to zero during every hour and the peaks above are real. If it is climbing, that
  many slots were never released (a hard-killed lsphp skips the shutdown handler) and the
  peaks are overstated by roughly that much.
</p>

<h2>Where to put the cap</h2>
<?php
  $p50 = pi_percentile($heavyHist, 50);
  $p95 = pi_percentile($heavyHist, 95);
  $p99 = pi_percentile($heavyHist, 99);
  $p999 = pi_percentile($heavyHist, 99.9);
?>
<table>
  <tr><th class="l">Concurrency (full builds)</th><th>level</th></tr>
  <tr><td class="l">median arrival saw</td><td><?= $p50 ?></td></tr>
  <tr><td class="l">95th percentile</td><td><?= $p95 ?></td></tr>
  <tr><td class="l">99th percentile</td><td><?= $p99 ?></td></tr>
  <tr><td class="l">99.9th percentile</td><td><?= $p999 ?></td></tr>
  <tr><td class="l">observed maximum</td><td><?= $peakHeavy ?></td></tr>
</table>
<p class="note">
  A cap set at the 99.9th percentile sheds roughly one poll in a thousand under normal load —
  and those retry themselves. Below the 99th it will bite during ordinary evenings, which is
  the outcome item 6 explicitly warns against. Compare the two peak figures as well: if full
  builds peak far below all requests, that is the argument for acquiring the slot AFTER the
  fast-poll exit rather than in server_load_guard.php.
</p>

<h2>Concurrency histogram (full builds)</h2>
<?php
  $maxCount = $heavyHist ? max($heavyHist) : 1;
  ksort($heavyHist);
?>
<table>
  <tr><th>concurrent</th><th>requests</th><th class="l">share</th></tr>
<?php foreach ($heavyHist as $level => $count): ?>
  <tr>
    <td><?= $level >= PollInstrument::MAX_BUCKET ? $level . '+' : $level ?></td>
    <td><?= number_format($count) ?></td>
    <td class="l"><span class="bar" style="width:<?= max(1, round(260 * $count / $maxCount)) ?>px"></span></td>
  </tr>
<?php endforeach; ?>
</table>

<h2>Per hour (newest first)</h2>
<table>
  <tr>
    <th class="l">hour (UTC)</th><th>req</th><th>heavy</th><th>peak all</th>
    <th>peak heavy</th><th>leak</th><th>max ms</th><th>5xx</th><th>workers</th>
  </tr>
<?php foreach (array_slice($rows, 0, 200) as $r): ?>
  <tr>
    <td class="l"><?= h($r[0] ?? '') ?></td>
    <td><?= (int)($r[1] ?? 0) ?></td>
    <td><?= (int)($r[2] ?? 0) ?></td>
    <td><?= (int)($r[3] ?? 0) ?></td>
    <td><strong><?= (int)($r[4] ?? 0) ?></strong></td>
    <td class="<?= (int)($r[5] ?? 0) > 0 ? 'warn' : '' ?>"><?= (int)($r[5] ?? 0) ?></td>
    <td><?= (int)($r[6] ?? 0) ?></td>
    <td><?= (int)($r[7] ?? 0) ?></td>
    <td><?= (int)($r[8] ?? 0) ?></td>
  </tr>
<?php endforeach; ?>
</table>

<h2>Duration histogram (full builds, whole period)</h2>
<?php
  $durTotals = [];
  foreach ($rows as $r) {
      foreach (pi_unpack($r[12] ?? '') as $k => $v) $durTotals[$k] = ($durTotals[$k] ?? 0) + $v;
  }
  $labels = pi_dur_labels();
  $durMax = $durTotals ? max($durTotals) : 1;
  ksort($durTotals);
?>
<table>
  <tr><th class="l">duration</th><th>requests</th><th class="l">share</th></tr>
<?php foreach ($durTotals as $i => $count): ?>
  <tr>
    <td class="l"><?= h($labels[$i] ?? ('bucket ' . $i)) ?></td>
    <td><?= number_format($count) ?></td>
    <td class="l"><span class="bar" style="width:<?= max(1, round(260 * $count / $durMax)) ?>px"></span></td>
  </tr>
<?php endforeach; ?>
</table>
<p class="note">
  Duration is the other half of the cap decision: concurrency is arrival rate times duration,
  so if the long tail here is dominated by a few multi-second builds, shortening those (item 8
  — releasing the DB connection before serialisation) reduces concurrency without any cap.
</p>

<?php endif; ?>

<h2>Raw CSV</h2>
<p class="note">
  <code>source/logs/pollstats.csv</code> — one line per hour, appended by a single elected
  process. Download it for analysis elsewhere; it is outside the document root, so fetch it
  over FTP rather than by URL.
</p>

<?php endif; ?>
