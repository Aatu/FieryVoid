<?php
// Auto-Deploy (TACTICAL_AI_PLAN.md section 5.2): where the server's FleetDeployer would stand the
// player's fleet this Deployment phase. Read-only - the game page places the units itself and the
// player commits by hand. Behind the `autoDeploy` feature flag (Manager::getAutoDeployPlan).
require_once 'global.php';

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$playerid = $_SESSION['user'] ?? null;
session_write_close();

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) $input = array();

try {
    if (!$playerid) {
        $ret = '{"error":"Not logged in."}';
    } else if (!isset($input['gameid'])) {
        $ret = '{"error":"Omitting required data"}';
    } else {
        $ret = Manager::getAutoDeployPlan($input['gameid'], $playerid, $input);
    }
} catch (Throwable $e) {
    $logid = Debug::error($e);
    $ret = json_encode(array('error' => 'Auto-Deploy failed.', 'logid' => $logid));
}

if (ob_get_length()) ob_clean();

echo $ret;
exit;
