<?php
/*
 * docs/docPage.php - the page around the DATA ARCHIVE viewer (client/UI/docViewer.js), for the five
 * standalone document pages: starterGuide.php, faq.php, factions-tiers.php,
 * ammo-options-enhancements.php and fleetchecker.php. Each of those sets $fvDocKey and includes
 * this file. DOCUMENT_VIEWER_PLAN.md is the record.
 *
 * Everywhere else the documents open as a window over the current screen; these pages are what an
 * old bookmark, a middle-click or a link pasted into Discord lands on - the same viewer, filling the
 * page (PAGE MODE), with the document's words embedded in a <template> so no second request is
 * needed. The words themselves live in docs/*.html; edit them there.
 */
if (!isset($fvDocKey)) {
    header('Location: ../games.php');
    exit;
}

$fvDocPages = array(
    'starter'  => array('file' => 'starter-guide.html',  'title' => 'Starter Guide',                'bg' => 'img/maps/24.PurpleArch.jpg'),
    'faq'      => array('file' => 'faq.html',            'title' => 'FAQ',                          'bg' => 'img/webBackgrounds/faq.jpg'),
    'factions' => array('file' => 'factions-tiers.html', 'title' => 'Factions & Tiers',             'bg' => 'img/maps/3.StarFormation.jpg'),
    'ammo'     => array('file' => 'ammo-options.html',   'title' => 'Ship Options',                 'bg' => 'img/webBackgrounds/aoe.jpg'),
    'fleet'    => array('file' => 'fleet-checker.html',  'title' => 'Fleet Checker Rules',          'bg' => 'img/webBackgrounds/faq.jpg')
);
$fvDocPage = $fvDocPages[$fvDocKey];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Fiery Void - <?php echo htmlspecialchars($fvDocPage['title']); ?></title>
  <!-- Shared fv design tokens (roadmap item 6): MUST load before every other stylesheet. -->
  <link href="<?php echo AssetLoader::getAssetUrl('styles/tokens.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/base.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/gamesNew.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/docViewer.css'); ?>" rel="stylesheet" type="text/css">
</head>
<body class="fvd-page-body" style="background: url('./<?php echo $fvDocPage['bg']; ?>') no-repeat center center fixed; background-size: cover;">

<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <a href="games.php">Back to Game Lobby</a>
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<div id="fvdPage" data-doc="<?php echo $fvDocKey; ?>"></div>
<template id="fvdSrc-<?php echo $fvDocKey; ?>"><?php readfile(__DIR__ . '/' . $fvDocPage['file']); ?></template>

<footer class="site-disclaimer">
  <p>
DISCLAIMER — Fiery Void is an unofficial, fan-created work based on concepts from Agents of Gaming’s Babylon 5 Wars.
It is not affiliated with, endorsed by, or sponsored by any official rights holders.
All trademarks and copyrights remain the property of their respective owners.
  </p>
</footer>

<script src="<?php echo AssetLoader::getAssetUrl('client/UI/docViewer.js'); ?>"></script>
</body>
</html>
