<?php
/**
 * aiBenchmark.php - how long does a CPU opponent's decision take ON THIS HOST? (TACTICAL_AI_PLAN.md section 12, stage T8.)
 *
 * The decision times in the plan were measured on the Docker stack; live is lsphp on shared hosting, with its own CPU and its own
 * memory limit, and no shell. This page runs ONE bot decision on a board built in memory (AIBenchmark - no database, no game,
 * nothing written) and prints what it cost. Run it a few times (the first run after a deploy compiles the classes):
 *
 *   aiBenchmark.php?key=<maintenance_key>&fleet=ea&size=live&level=normal&phase=2&board=1
 *     fleet  ea | raiders              size   small (4 units a side) | live (10) | large (16)
 *     level  easy | normal | hard      phase  1 Initial Orders | 2 Movement (the costly one) | 3 Firing
 *     board  0 | 1 | 2 (28, 16, 9 hexes apart)        budget  game (as a game plays: the default) | 0 (none)
 *
 * Gated by MaintenanceGate like the other maintenance pages (?key= once per session). Works whether or not the cpuOpponent flag is
 * on: it plays no game. One decision a request - a live-sized Movement decision is several seconds of CPU.
 */

require_once 'global.php';
require_once __DIR__ . '/../server/lib/MaintenanceGate.php';
MaintenanceGate::requireAccess('CPU opponent decision benchmark');
if (!class_exists('AIBenchmark')) require_once __DIR__ . '/../server/ai/AIBenchmark.php';

header('Content-Type: text/html; charset=utf-8');
header('Cache-Control: no-store');
@set_time_limit(120);

$pick = function ($name, array $allowed, $default) {
    $value = isset($_GET[$name]) ? (string)$_GET[$name] : $default;
    return in_array($value, $allowed, true) ? $value : $default;
};
$fleet = $pick('fleet', array_keys(AIBenchmark::FLEETS), 'ea');
$size = $pick('size', array('small', 'live', 'large'), 'live');
$level = $pick('level', AIConfig::LEVELS, 'normal');
$phase = (int)$pick('phase', array('1', '2', '3'), '2');
$board = (int)$pick('board', array('0', '1', '2'), '1');
$budget = $pick('budget', array('game', '0'), 'game');

$result = isset($_GET['run']) ? AIBenchmark::run($fleet, $size, $level, $phase, $board, ($budget === 'game') ? null : 0) : null;
$host = AIBenchmark::host();
$e = function ($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); };
$phases = array(1 => 'Initial Orders', 2 => 'Movement', 3 => 'Firing');
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>CPU decision benchmark</title>
<style>
  body { margin: 0; padding: 16px; background: #0b1622; color: #d8e4f0; font: 14px/1.5 system-ui, sans-serif; }
  h1 { font-size: 18px; margin: 0 0 12px; }
  table { border-collapse: collapse; margin: 12px 0; }
  td, th { padding: 4px 12px 4px 0; text-align: left; vertical-align: top; }
  th { color: #8e9aa6; font-weight: 600; }
  .big { font-size: 22px; font-weight: 700; }
  .err { color: #ff8a80; }
  form { display: flex; flex-wrap: wrap; gap: 8px 14px; align-items: end; }
  label { display: flex; flex-direction: column; font-size: 12px; color: #8e9aa6; }
  select, button { font: inherit; padding: 4px 6px; }
  p.note { color: #8e9aa6; max-width: 46rem; }
</style>
</head>
<body>
<h1>CPU opponent - one decision on this host</h1>
<form method="get">
  <input type="hidden" name="run" value="1">
  <label>Fleet <select name="fleet"><?php foreach (array_keys(AIBenchmark::FLEETS) as $f) echo '<option' . ($f === $fleet ? ' selected' : '') . '>' . $e($f) . '</option>'; ?></select></label>
  <label>Size <select name="size"><?php foreach (array('small', 'live', 'large') as $s) echo '<option' . ($s === $size ? ' selected' : '') . '>' . $e($s) . '</option>'; ?></select></label>
  <label>Level <select name="level"><?php foreach (AIConfig::LEVELS as $l) echo '<option' . ($l === $level ? ' selected' : '') . '>' . $e($l) . '</option>'; ?></select></label>
  <label>Phase <select name="phase"><?php foreach ($phases as $n => $name) echo '<option value="' . $n . '"' . ($n === $phase ? ' selected' : '') . '>' . $e($name) . '</option>'; ?></select></label>
  <label>Board <select name="board"><?php foreach (AIBenchmark::DISTANCES as $n => $d) echo '<option value="' . $n . '"' . ($n === $board ? ' selected' : '') . '>' . $d . ' hexes apart</option>'; ?></select></label>
  <label>Budget <select name="budget"><option value="game"<?php echo $budget === 'game' ? ' selected' : ''; ?>>as a game plays</option><option value="0"<?php echo $budget === '0' ? ' selected' : ''; ?>>none</option></select></label>
  <button type="submit">Run</button>
</form>
<?php if ($result !== null): ?>
<?php if ($result['error'] !== null): ?>
  <p class="err"><?php echo $e($result['error']); ?></p>
<?php else: ?>
  <table>
    <tr><th>Decision</th><td><?php echo $e($phases[$phase] . ', ' . $level . ', ' . $fleet . ' ' . $size . ' (' . $result['units'] . ' units a side), board ' . $board); ?></td></tr>
    <tr><th>CPU time</th><td class="big"><?php echo sprintf('%.2f s', $result['cpu']); ?></td></tr>
    <tr><th>Wall time</th><td><?php echo sprintf('%.2f s', $result['wall']); ?></td></tr>
<?php if ($phase == 2): ?>
    <tr><th>Ship decisions</th><td><?php echo (int)$result['ships']; ?>, slowest <?php echo sprintf('%.2f s', $result['slowest']); ?>, <?php echo (int)$result['cut']; ?> stopped by their budget</td></tr>
<?php endif; ?>
  </table>
<?php endif; ?>
<?php endif; ?>
<table>
  <tr><th>PHP</th><td><?php echo $e($host['php'] . ' (' . $host['sapi'] . ')'); ?></td></tr>
  <tr><th>memory_limit</th><td><?php echo $e($host['memoryLimit']); ?></td></tr>
  <tr><th>max_execution_time</th><td><?php echo $e($host['maxExecution']); ?> s</td></tr>
  <tr><th>Peak memory (PHP)</th><td><?php echo $e($host['peakMB']); ?> MB</td></tr>
  <tr><th>Resident (VmRSS)</th><td><?php echo $host['rssMB'] === null ? 'not readable' : $e($host['rssMB']) . ' MB'; ?></td></tr>
  <tr><th>Budgets</th><td>ship <?php echo AIConfig::MOVE_DECISION_SECONDS; ?> s, submission <?php echo AIConfig::MOVE_SUBMISSION_SECONDS; ?> s (CPU); a request <?php echo AIConfig::BUDGET_SECONDS; ?> s (wall)</td></tr>
</table>
<p class="note">On the dev machine (Docker, 2026-11-22) a live-sized EA Movement decision (10 units in one activation) takes about 8.1 s
at Normal and 10.8 s at Hard with no budget; with the game's budgets a submission is held near <?php echo AIConfig::MOVE_SUBMISSION_SECONDS; ?> s of CPU and a request
to <?php echo AIConfig::BUDGET_SECONDS; ?> s of wall time - the rest of the activation is decided on the next poll.</p>
</body>
</html>
