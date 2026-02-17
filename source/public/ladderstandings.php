<?php
include_once 'global.php';

// Simple read-only endpoint, but good to check session
if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(["error" => "Not logged in"]);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if (isset($input['action']) && $input['action'] === 'register') {
            Manager::registerLadderPlayer($_SESSION["user"]);
            echo json_encode(["success" => true]);
            exit;
        }
        
        if (isset($input['action']) && $input['action'] === 'remove') {
            Manager::removeLadderPlayer($_SESSION["user"]);
            echo json_encode(["success" => true]);
            exit;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'history') {
        $playerid = isset($_GET['playerid']) ? (int)$_GET['playerid'] : 0;
        if ($playerid > 0) {
            $history = Manager::getLadderHistory($playerid);
            echo json_encode($history);
            exit;
        } else {
             echo json_encode(["error" => "Invalid player ID"]);
             exit;
        }
    }

    $standings = Manager::getLadderStandings();
    
    // Find current user's rating in the standings
    $myRating = 100; // Default
    $myId = $_SESSION["user"];
    foreach ($standings as $player) {
        if ($player->playerid == $myId) {
            $myRating = $player->rating;
            break;
        }
    }
    
    echo json_encode([
        "standings" => $standings,
        "currentUser" => [
            "id" => $myId,
            "rating" => $myRating,
            "isRegistered" => ($myRating != 100 || $myId != 0 && $myRating == 100 && Manager::isLadderPlayer($myId))
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
