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

// Never cache this HTML document — it inlines a player-specific, point-in-time
// games list ($games below). Without this the browser can disk-cache the page
// and replay a stale copy on session restore (reopening tabs after a browser or
// computer restart), with no server round-trip. no-store forces a fresh fetch.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

// Fetch games for logged-in user
// Fetch games for logged-in user
$userid = (int)$_SESSION["user"];
$gamesData = Manager::getTacGames($userid);

// STAMPEDE PROTECTION
if (isset($gamesData[0]) && isset($gamesData[0]['status']) && $gamesData[0]['status'] == 'GENERATING') {
    echo '<html><head><meta http-equiv="refresh" content="1"></head>
    <body style="background:#000; color:red; display:flex; justify-content:center; align-items:center; height:100vh; font-family:sans-serif; font-size:24px;">
    Refreshing game list...
    </body></html>';
    exit;
}

$games = json_encode($gamesData, JSON_NUMERIC_CHECK);

$defaultGameName = 'GAME NAME' . $_SESSION["user"];	
$playerName = Manager::getPlayerName($_SESSION["user"]);
$defaultGameName = ucfirst($playerName) . "'s Game";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">  
  <title>Fiery Void - Games</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <!-- Shared fv design tokens (roadmap item 6): MUST load before every other stylesheet. -->
  <link href="<?php echo AssetLoader::getAssetUrl('styles/tokens.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/base.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/lobby.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/gamesNew.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/confirm.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/ladder.css'); ?>" rel="stylesheet" type="text/css">
  <!-- Page-scoped: the games panel + Recent Games window. Kept out of gamesNew.css,
       which 12 pages share. -->
  <link href="<?php echo AssetLoader::getAssetUrl('styles/gamesPanel.css'); ?>" rel="stylesheet" type="text/css">
  <script src="<?php echo AssetLoader::getAssetUrl('client/lib/jquery-4.0.0.min.js'); ?>"></script>
  <script src="client/games.js"></script>
  <script src="client/ajaxInterface.js"></script>
  <script src="client/player.js"></script>
  <script src="client/mathlib.js"></script>
  <script src="client/UI/confirm.js"></script>
  <script src="client/ladder.js"></script>
  <script src="client/recentGames.js"></script>
  
  <script>
    jQuery($ => {
      gamedata.parseServerData(<?php echo $games; ?>);
      ajaxInterface.startPollingGames();
      gamedata.thisplayer = <?php echo $_SESSION["user"]; ?>;
      gamedata.defaultGameName = "<?php echo $defaultGameName; ?>";
      gamedata.defaultBackground = "21.PurpleNebula.jpg";
    });
    // BFCache restore freshness (games list page).
    // games.php bakes its game list into the page at server-render time and
    // parses it once on jQuery ready. That handler does not re-fire when the
    // browser restores a frozen page from the back/forward cache (clicking back
    // from a game, or session restore on startup), so the lists come back
    // showing the stale render-time snapshot. There is no polling loop on this
    // page (startPollingGames is a no-op), so nothing refreshes on its own.
    // On a persisted restore: force one immediate fetch of the games list.
    // The Recent Games window needs nothing here — it fetches every time it opens.
    window.addEventListener("pageshow", function (event) {
      if (!event.persisted) return;                       // only BFCache restores
      if (typeof ajaxInterface === "undefined") return;

      ajaxInterface.submitingGames = false;               // a frozen in-flight XHR never completed
      ajaxInterface.requestAllGames();                    // refresh YOUR GAMES + JOIN GAMES
    });
  </script>
</head>

<body  style="background: url('./img/maps/14.PlanetsNear.jpg') no-repeat center center fixed; background-size: cover;">
<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <!--<a href="reg.php">Register new account</a>
    <span>|</span>-->
    <a href="chpass.php">Change password</a>
    <span>|</span>
    <a href="profile.php">Set-Up Discord Notifications</a>
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<main class="container">
  <section class="news-panel">
    <div class="fv-panel-head">
      <span>MISSION BRIEFING</span>
      <span class="fv-panel-meta">Aug 2026</span>
    </div>

    <h2>Welcome to <strong>Fiery Void!</strong></h2>
    <p class="lead">A free-to-play adaptation of the “Babylon 5 Wars” tabletop game, by Agents of Gaming!</p>

<?php // The " | " separators are gone — .quick-links lays these out as chips, which are
      // also a usable tap target on a phone. Each carries the colour of what it opens via
      // its qk-* class; a link with no class falls back to the page accent, so adding one
      // without a class still looks deliberate. ?>
<div class="resources">
  <h3>Get Started</h3>
  <div class="quick-links">
    <a class="qk-guide" href="./starterGuide.php" target="_blank" rel="noopener noreferrer">Starter Guide</a>
    <a class="qk-discord" href="https://discord.gg/4jXarWusp4" target="_blank" rel="noopener noreferrer">Discord</a>
    <a class="qk-video" href="https://www.youtube.com/playlist?list=PLTGKagm5KkMxB8oKBiIUeoBQTRYz2z0-3" target="_blank" rel="noopener noreferrer">Video Tutorials</a>
    <a class="qk-tools" href="https://fieryvoidmogwaitools.netlify.app/" target="_blank" rel="noopener noreferrer">Tool Suite</a>
    <!--<a href="https://www.facebook.com/groups/fieryvoid" target="_blank" rel="noopener noreferrer">Facebook</a>-->
  </div>
</div>


<div class="resources">
      <h3>Rules & Info</h3>
      <div class="links">     
        <div><a href="./faq.php" target="_blank" rel="noopener noreferrer">Fiery Void FAQ:</a> Aide Memoire of specific rules and differences from Babylon 5 Wars.</div>        
        <div><a href="./factions-tiers.php" target="_blank" rel="noopener noreferrer">Fiery Void: Factions & Tiers:</a> Overview of Fiery Void factions and their relative strengths.</div>
        <div><a href="./ammo-options-enhancements.php" target="_blank" rel="noopener noreferrer">Ammo, Options & Enhancements:</a> Details of all the extras available to Fiery Void units e.g. Missiles.</div>
        <div><a href="http://b5warsvault.wikidot.com/" target="_blank" rel="noopener noreferrer">Babylon 5 Wars Vault:</a> Huge repository of Babylon 5 Wars rules and info!</div>
      </div> 
    </div>

    <?php // The month moved into the panel head bar's readout, so it is no longer a date
          // that has to be edited in two places. The " - " after each title is gone too:
          // the title renders as uppercase Orbitron in the accent and the body as Arial,
          // which separates them without needing a punctuation mark.
          //
          // Per-entry highlight, unchanged: set --update-colour on the <li> and it
          // recolours the title AND tints the rail. See the commented example below. ?>
    <div class="resources">
      <h3>Latest Updates</h3>
      <ul class="updates-list">
        <!--<li style="--update-colour: #e05b52;"><span class="update-title">Merry Christmas from Fiery Void!</span></li>-->
        <li><span class="update-title">Reinforcements from Hyperspace</span>Select new 'Allow Reinforcements' option in Create Game to let part of your fleet start the battle in Hyperspace, details in FAQ.</li>        
        <li><span class="update-title">Jumping to Hyperspace</span>Ships can now open a jump point for themselves and others to exit the scenario into hyperspace, more details in FAQ.</li>
        <li><span class="update-title">Info Panel Redesign</span>Combat Log, Fleet Info and Declarations tabs are more user friendly, and offer new filter options.  Panel height can also be manually resized.</li>        
        <li><span class="update-title">Manual Ballistic Intercept</span>Updates to ship tooltips now allow you to manually intercept incoming ballistic shots, see FAQ for details.</li>                    
        <li><span class="update-title">The System</span>PaulUK (Reman) provides his vision of the System faction from Blake's 7, thanks to Geoffrey for adding to Fiery Void!</li>                             
        <li><span class="update-title">Discord Notifications</span>You can now add your Discord account details in 'Set-Up Discord Notifications' to get a message when it's your turn. See Fiery Void FAQ for details!</li>
        <!--<li><span class="update-title">General Fixes</span>Many other small bug fixes/updates. Thanks for the reports!</li>-->
      </ul>
    </div>

    <!--<p class="noteGames">Remember - When anything weird happens, press <kbd>Ctrl+F5</kbd> to reload page!  If that doesn't work report bugs via Discord link above.</p>-->
  </section>

<?php
// The RECENT ACTIVITY column moved into the Recent Games window (recentgames.php); the
// freed width went to the two lists, which are now ~460px instead of ~280px. Container
// fills/radii live in gamesPanel.css — they used to be inline style attributes.
?>
  <section class="games-panel">
    <div class="fv-panel-head">
      <span>Games</span>
    </div>

    <div class="fv-games-grid">
      <section class="fv-col" aria-labelledby="yourGamesHead">
        <div class="fv-col-head" id="yourGamesHead">
          <span>Your Games</span>
          <span class="fv-count-badge" data-fv-count="active"></span>
        </div>
        <?php // placeholder is replaced by games.js on ready; it also covers the case
              // where scripting is unavailable, so the wells never sit blank and unexplained ?>
        <div class="fv-well gamecontainer active"><div class="fv-empty">Loading games&hellip;</div></div>
      </section>

      <section class="fv-col" aria-labelledby="joinGamesHead">
        <div class="fv-col-head" id="joinGamesHead">
          <span>Join Games</span>
          <span class="fv-count-badge" data-fv-count="lobby"></span>
        </div>
        <div class="fv-well gamecontainer lobby"><div class="fv-empty">Loading games&hellip;</div></div>
      </section>

      <div class="fv-col fv-actions">
        <div class="fv-col-head"><span>Actions</span></div>
        <a class="fv-btn fv-btn--create" href="creategame.php">Create Game</a>
        <button class="fv-btn fv-btn--fleet" type="button" onclick="gamedata.submitFleetTest()">Fleet Builder</button>
        <button class="fv-btn btn-ladder" type="button" data-show-calc="false">View Ladder</button>
        <button class="fv-btn fv-btn--recent btn-recent-games" type="button">Recent Games</button>
      </div>
    </div>
  </section>

<?php
// .chat-panel is a WRAPPER around #globalchat rather than the same element. It has to
// be, because the head bar must sit outside the scrolling body — #globalchat scrolls,
// so a header inside it would scroll away with the chat log.
//
// $chatelement stays "#globalchat": chat.php's JS selects the scrolling body, which is
// still that id.
//
// This page keeps its own .fv-panel-head (so the chat matches the news and games panels
// beside it) and therefore does NOT set $chattitle — creategame.php and gamelobby.php do,
// and get chat.php's own head bar instead. Everything else about how the chat looks now
// lives in styles/chat.css and is shared by all four pages.
//
// The height moved off #globalchat's own inline style and onto the body below; it is a
// little taller than the old 200px because the composer bar now has real padding of its
// own, so less of the box is message list.
?>
  <section class="chat-panel">
    <div class="fv-panel-head">
      <span>Global Chat</span>
      <span class="fv-panel-meta">All players</span>
    </div>
    <div id="globalchat" style="height:230px;">
      <?php
      $chatgameid = 0;
      $chatelement = "#globalchat";
      include("chat.php")
      ?>
    </div>
  </section>
</main>

<footer class="site-disclaimer">
  <p>
DISCLAIMER — Fiery Void is an unofficial, fan-created work based on concepts from Agents of Gaming’s Babylon 5 Wars. 
It is not affiliated with, endorsed by, or sponsored by any official rights holders. 
All trademarks and copyrights remain the property of their respective owners.
  </p>
</footer>

</body>
<?php include("ladder.php"); ?>
<?php include("recentgames.php"); ?>
</html>
