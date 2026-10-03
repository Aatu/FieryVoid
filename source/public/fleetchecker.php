<?php
include_once 'global.php';
if (!isset($_SESSION["user"]) || $_SESSION["user"] == false) {
    header('Location: index.php');
}
// The Fleet Checker rules' words live in docs/fleet-checker.html. This page is the DATA ARCHIVE
// viewer filling the page; everywhere else it opens as a window over the current screen
// (client/UI/docViewer.js).
$fvDocKey = 'fleet';
include 'docs/docPage.php';
