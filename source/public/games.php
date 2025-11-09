<?php

// Load global config and classes
require_once 'global.php';

// Sessions
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if not logged in
if (empty($_SESSION["user"])) {
    header('Location: index.php');
    exit;
}

// Fetch games for logged-in user
$userid = (int)$_SESSION["user"];
$games = json_encode(Manager::getTacGames($userid), JSON_NUMERIC_CHECK);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <title>Fiery Void - Games</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <link href="styles/base.css" rel="stylesheet" type="text/css">
  <link href="styles/lobby.css" rel="stylesheet" type="text/css">
  <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">
  <link href="styles/confirm.css" rel="stylesheet" type="text/css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
  <script src="client/games.js"></script>
  <script src="client/ajaxInterface.js"></script>
  <script src="client/player.js"></script>
  <script src="client/mathlib.js"></script>
  <script src="client/UI/confirm.js"></script>
  
  <script>
    jQuery($ => {
      gamedata.parseServerData(<?php echo $games; ?>);
      ajaxInterface.startPollingGames();
      gamedata.thisplayer = <?php echo $_SESSION["user"]; ?>;
    });
    function loadFireList() {
      ajaxInterface.getFirePhaseGames();
    }
  </script>
</head>

<body  style="background: url('./img/maps/6.NebulaGreen.jpg') no-repeat center center fixed; background-size: cover;">
<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <a href="reg.php">Register new account</a>
    <span>|</span>
    <a href="chpass.php">Change password</a>    
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<main class="container">
  <section class="news-panel">
    <h2>Welcome to <strong>Fiery Void!</strong></h2>
    <p class="lead">An adaptation of the “Babylon 5 Wars” tabletop game, by Agents of Gaming!</p>

<div class="resources">
  <h3>Get Started</h3>
  <a href="./starterGuide.php" target="_blank" rel="noopener noreferrer">Starter Guide</a> | 
  <a href="https://www.youtube.com/playlist?list=PLTGKagm5KkMxB8oKBiIUeoBQTRYz2z0-3" target="_blank" rel="noopener noreferrer">Video Tutorials</a> | 
  <a href="https://discord.gg/4jXarWusp4" target="_blank" rel="noopener noreferrer">Discord</a> | 
  <a href="https://www.facebook.com/groups/fieryvoid" target="_blank" rel="noopener noreferrer">Facebook</a>
</div>


<div class="resources">
      <h3>Rules & Info</h3>
      <div class="links">     
        <div><a href="./factions-tiers.php" target="_blank" rel="noopener noreferrer">Fiery Void: Factions & Tiers:</a> Overview of Fiery Void factions and their relative strengths.</div>
        <div><a href="./faq.php" target="_blank" rel="noopener noreferrer">Fiery Void FAQ:</a> Aide Memoire of specific rules and the differences from Babylon 5 Wars.</div>
        <div><a href="./ammo-options-enhancements.php" target="_blank" rel="noopener noreferrer">Ammo, Options & Enhancements:</a> Details of all the extras available to Fiery Void units e.g. Missiles.</div>
        <div><a href="http://b5warsvault.wikidot.com/" target="_blank" rel="noopener noreferrer">Babylon 5 Wars Vault:</a> Huge repository of Babylon 5 Wars rules and info!</div>
      </div> 
    </div>

    <div class="resources">
      <h3>Latest Updates — November 2025</h3>
      <ul class="updates-list">
        <li><strong>Torvalus Speculators</strong> - Transverse Drive added to complete final system for this new Ancient Faction.</li>          
        <li><strong>Pre-Firing Phase</strong> - Whole new phase added to Fiery Void combat order, at the moment only Transverse Drive uses this to jump before Firing.</li>
        <li><strong>LoS Ruler</strong> - Ship tooltips will no longer display while the Ruler tool is active, to help with visual clarity.</li>                                            
        <li><strong>General Fixes</strong> - Many other small fixes. Thanks for the reports!</li>                                                    
        <!--<li><strong>6 Jun</strong> - Overlay colors, deployment zone tweaks, UI fixes. Pulsar mine fixed, tooltip/text readability improved.</li>-->
      </ul>
    </div>

    <p class="noteGames">Remember - When anything weird happens, press <kbd>Ctrl+F5</kbd> to reload page!  If that doesn't work report bugs via Discord link above.</p>
  </section>

  <section class="games-panel">
    <div class="games-grid four-cols">
      <div>
        <h3>YOUR GAMES</h3>
        <!--<div class="gamecontainer active subpanel"> Old version with subpanel -->
        <div class="gamecontainer active" style="background-color:#04161C; border-radius: 5px;">
          <div class="notfound">No active games</div>
        </div>
      </div>
      <div>
        <h3>JOIN GAMES</h3>
        <div class="gamecontainer lobby" style="background-color:#04161C; border-radius: 5px;">
          <div class="notfound">No starting games</div>
        </div>
      </div>
      <div>
        <h3>RECENT ACTIVITY</h3>
        <div id="fireList" class="gamecontainer fire" style="background-color:#04161C; border-radius: 5px;">
        </div>
      </div>
      <div class="create-col">
        <a class="btn btn-success create-game-btn" href="creategame.php">Create Game</a>
        <button class="btn btn-secondary" onclick="loadFireList()">Recent Games</button>
      </div>
    </div>
  </section>

  <div id="globalchat" class="chat-panel" style="height:200px;">
    <?php
    $chatgameid = 0;
    $chatelement = "#globalchat";
    include("chat.php")
    ?>
  </div>
</main>
</body>
</html>
