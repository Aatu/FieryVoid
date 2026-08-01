<?php
// JSON feed for the Recent Games window on games.php (markup lives in recentgames.php):
// the site-wide list of games with activity in the last 7 days.
//
// Replaces firePhaseGames.php, which built a full TacGamedata per row and shipped
// background/creator/description to render one line of text. That file is left in place
// for now: a browser still holding a cached copy of the previous legacy bundle would
// otherwise request a 404.
//
// This file must start with the PHP tag and carry no BOM. global.php calls
// session_cache_limiter(), which is fatal once any output has been sent.
require_once 'global.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION["user"])) {
    header('HTTP/1.1 403 Forbidden');
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array("error" => "Not logged in"));
    exit;
}

$userid = (int)$_SESSION["user"];
session_write_close(); // release the session lock early - this is a read-only query

// The list is a point-in-time snapshot of other players' activity; never let a browser
// or proxy replay a stale copy.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Content-Type: application/json; charset=utf-8');

// NOTE: no JSON_NUMERIC_CHECK here, unlike allgames.php. It would rewrite an all-digits
// game name or username into a number, and this payload is full of player-supplied text.
echo json_encode(Manager::getRecentGames($userid));
exit;
