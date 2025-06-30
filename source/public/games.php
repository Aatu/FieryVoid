<?php
include_once 'global.php';
if (!isset($_SESSION["user"]) || $_SESSION["user"] == false) {
    header('Location: index.php');
}
$games = "[]";
if (isset($_SESSION["user"])) {
    $games = json_encode(Manager::getTacGames($_SESSION["user"]), JSON_NUMERIC_CHECK);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
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
  <style>
    .top-right-links {
      position: absolute;
      top: 10px;
      right: 130px;
      font-size: 14px;
    }
    .top-right-links a {
      color: #ccf;
      text-decoration: none;
      margin-right: 10px;
    }
    .top-right-links a:hover {
      text-decoration: underline;
    }
    .games-grid.four-cols {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 20px;
    }
    .create-col .btn {
      display: block;
      margin-bottom: 10px;
    }
    .note.error {
      color: red;
      font-weight: bold;
    }
  </style>
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

<body>
<header class="header">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <a href="chpass.php">Change password</a>
    <span>|</span>
    <a href="reg.php">Register New Account</a>
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<main class="container">
  <section class="news-panel">
    <h2>Welcome to <strong>Fiery Void!</strong></h2>
    <p class="lead">An adaptation of the “Babylon 5 Wars” tabletop game, by Agents of Gaming!</p>

    <div class="resources">
      <h3>Get Started</h3>
      <a href="files/Fiery_Void_-_How_to_Play.docx">Starter Guide</a> | 
      <a href="https://www.youtube.com/...">Video Tutorials</a> | 
      <a href="https://discord.gg/...">Discord</a> | 
      <a href="https://www.facebook.com/groups/...">Facebook</a>
    </div>

    <div class="news-section">
      <h3>Latest Updates — June 2025</h3>
      <ul class="updates-list">
        <li><strong>Features & Enhancements:</strong> Fleets/ships can deploy after Turn 1; Hyach subs now have Stealth; Specialist rebalances; & more.</li>
        <li><strong>Bug Fixes:</strong> Pulsar mine fixed, tooltip/text readability improved, etc.</li>
        <li><strong>Fixes (6 Jun):</strong> Overlay colors, deployment zone tweaks, UI fixes.</li>
        <li><strong>Hotfixes (12 & 16 Jun):</strong> Movement/LOS fixes, targeting tooltip updates.</li>
        <li><strong>Hotfix (27 Jun):</strong> Optimized visuals, terrain updates, LoS checks improved.</li>
      </ul>
    </div>

    <div class="resources">
      <h3>Rules & Info</h3>
      <div class="links">     
        <div><a href="files/FV_factions.txt">Factions & Tier List:</a> Overview of rules and systems of the fleets available in Fiery Void.</div>
        <div><a href="files/FV_FAQ.txt">FAQ:</a> Differences from B5 Wars, known bugs!</div>
        <div><a href="files/enhancements_list.txt"> Enhancements:</a>Details of enhancements and other common systems e.g. Boarding / Missiles.</div>
        <div><a href="http://b5warsvault.wikidot.com/"> B5Wars Vault & Rules:</a> LOTS of other Babylon 5 Wars stuff.</div>
      </div>  
    </div>

    <p class="noteGames">Remember - If anything weird happens, press <kbd>Ctrl+F5</kbd> to reload page!  If that doesn't work report bugs via Discord or Facebook links above.</p>
  </section>

  <section class="games-panel">
    <div class="games-grid four-cols">
      <div>
        <h3>YOUR ACTIVE GAMES</h3>
        <div class="gamecontainer active subpanel">
          <div class="notfound">No active games</div>
        </div>
      </div>
      <div>
        <h3>GAMES TO JOIN</h3>
        <div class="gamecontainer lobby subpanel">
          <div class="notfound">No starting games</div>
        </div>
      </div>
      <div>
        <h3>RECENT ACTIVITY</h3>
        <div id="fireList" class="gamecontainer fire subpanel">
        </div>
      </div>
      <div class="create-col">
        <a class="btn btn-success create-game-btn" href="creategame.php">Create Game</a>
        <button class="btn btn-primary" onclick="loadFireList()">Recent Games</button>
      </div>
    </div>
  </section>

  <div id="globalchat" class="chat-panel" style="height:150px;">
    <?php
    $chatgameid = 0;
    $chatelement = "#globalchat";
    include("chat.php")
    ?>
  </div>
</main>
</body>
</html>