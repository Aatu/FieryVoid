<?php
/* Per-system critical CATALOGUE + cascade traits for ONE ship class.
   Feeds the gamelobby's pre-battle damage editor (PREBATTLE_DAMAGE_PLAN.md §11.2):
   which criticals a system may be offered, and which systems survive the destruction
   of their structure block. Both live in PROTECTED properties, so neither is in the
   static ship JSON that the lobby otherwise builds its blueprints from.

   ONE class per request, resolved through ShipLoader - never getAllShipsStatic(null),
   which is the documented cause of the deploy 503 on the live LiteSpeed workers. */
ob_start();
header('Content-Type: application/json; charset=utf-8');
require_once 'global.php';

if (session_status() === PHP_SESSION_NONE) session_start();
$playerid = $_SESSION['user'] ?? null;
session_write_close();

if (!$playerid) {
    http_response_code(401);
    if (ob_get_length()) ob_clean();
    echo json_encode(['success' => false, 'error' => 'Not logged in.']);
    exit;
}

try {
    $input = json_decode(file_get_contents('php://input'), true) ?? [];
    $phpclass = $input['phpclass'] ?? $_GET['phpclass'] ?? null;
    $flightSize = $input['flightSize'] ?? $_GET['flightSize'] ?? 1;

    if (!$phpclass) throw new Exception("Ship class missing");

    $catalogue = Manager::getSystemCriticals($phpclass, $flightSize);

    if (ob_get_length()) ob_clean();
    if (isset($catalogue['error'])) {
        echo json_encode(['success' => false, 'error' => $catalogue['error']]);
        exit;
    }

    echo json_encode(['success' => true] + $catalogue);

} catch (Exception $e) {
    $logid = Debug::error($e);
    if (ob_get_length()) ob_clean();
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage(),
        'code'    => $e->getCode(),
        'logid'   => $logid
    ]);
}

exit;
