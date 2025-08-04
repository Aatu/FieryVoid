<?php
include_once 'global.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"] == false) {
    echo json_encode(["error" => "unauthorized"]);
    exit;
}

$factions = Manager::getAllFactions();

echo json_encode($factions, JSON_NUMERIC_CHECK);