<?php
// Load global config and classes
require_once 'global.php';

if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
    session_write_close();
    header('Location: index.php');
    exit;
}

// A private game's password, posted by the form this page shows in place of the lobby (below).
// Handled while the session is still open: a right one unlocks the game for the rest of the
// session (Manager::markGameUnlocked), then the lobby loads by GET, so a reload never re-posts.
$gamePasswordResult = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['gamePassword']) && isset($_GET['gameid'])) {
    $gamePasswordResult = Manager::checkGamePassword($_SESSION["user"], $_GET['gameid'], $_POST['gamePassword']);
    if ($gamePasswordResult === 'ok') {
        Manager::markGameUnlocked($_GET['gameid']);
        session_write_close();
        header('Location: gamelobby.php?gameid=' . (int)$_GET['gameid'], true, 303);
        exit;
    }
}

session_write_close(); // Prevent session locking for concurrent loads

// Never cache this HTML document — it inlines a player-specific, point-in-time
// lobby snapshot ($gamelobbydataJSON below). Without this the browser can
// disk-cache the page and replay a stale copy on session restore (reopening tabs
// after a browser or computer restart), with no server round-trip. no-store
// forces a fresh fetch every time.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (isset($_GET["leave"]) && isset($_GET["gameid"])){
    Manager::leaveLobbySlot($_SESSION["user"], $_GET["gameid"]);
    header('Location: games.php');
    exit;
}
	
	
	$gameid = null;
	
	if (isset($_GET["gameid"])){
		$gameid = $_GET["gameid"];
	}

	/* ── Private game (CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §3.2, Stage 8) ───────────────────────
	   A player who holds no slot in a private game and has not entered its password this session gets
	   this password form instead of the lobby. Checked BEFORE the lobby's data is built, so none of it
	   reaches them. Leaving (above) needs no password; slot.php refuses them a slot as well. */
	$gameAccess = ($gameid !== null) ? Manager::getGameAccess($_SESSION["user"], $gameid) : null;
	if ($gameAccess && $gameAccess['status'] === 'LOBBY' && $gameAccess['locked']):
		$gateGameId = (int)$gameid;
		$gateError = '';
		if ($gamePasswordResult === 'wrong') $gateError = 'That is not this game&rsquo;s password.';
		else if ($gamePasswordResult === 'throttled') $gateError = 'Too many wrong passwords. Wait a few minutes, then try again.';
?>
<!DOCTYPE HTML>
<html lang="en">
	<head>
		<title>Fiery Void - Private Game</title>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link href="<?php echo AssetLoader::getAssetUrl('styles/tokens.css'); ?>" rel="stylesheet" type="text/css">
		<link href="<?php echo AssetLoader::getAssetUrl('styles/base.css'); ?>" rel="stylesheet" type="text/css">
		<link href="<?php echo AssetLoader::getAssetUrl('styles/gamesNew.css'); ?>" rel="stylesheet" type="text/css">
		<link href="<?php echo AssetLoader::getAssetUrl('styles/gameLobby.css'); ?>" rel="stylesheet" type="text/css">
		<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
	</head>
	<body class="lb-gate-page" style="background-image:url(img/maps/14.PlanetsNear.jpg)">
		<header class="pageheader">
			<img src="img/logo.png" alt="Fiery Void Logo" class="logo">
			<div class="top-right-row">
				<a href="games.php">Back to Lobby</a>
				<a href="logout.php" class="btn btn-primary">Logout</a>
			</div>
		</header>

		<main class="lb-gate">
			<section class="lb-panel" aria-labelledby="lbGateHead">
				<h1 class="lb-panel-head" id="lbGateHead"><span><i class="fa-solid fa-lock lb-gate-lock" aria-hidden="true"></i>Private Game</span><span class="lb-panel-meta">#<?php print($gateGameId); ?></span></h1>
				<!-- autocomplete off: a browser should not offer the player's own account password here. -->
				<form class="lb-panel-body lb-gate-form" method="post" action="gamelobby.php?gameid=<?php print($gateGameId); ?>">
					<!-- The game name is player-supplied and stored unescaped, so it must be escaped here. -->
					<p class="lb-gate-name"><?php print(htmlspecialchars($gameAccess['name'])); ?></p>
					<p class="lb-gate-text">This game&rsquo;s creator has set a password. Enter it to open the lobby and take a slot.</p>
					<label class="lb-gate-label" for="gamePassword">Password</label>
					<input id="gamePassword" class="lb-input lb-gate-input" type="password" name="gamePassword"
					       maxlength="<?php print(Manager::GAME_PASSWORD_MAX_LENGTH); ?>" required autofocus
					       autocomplete="off" autocapitalize="off" spellcheck="false"<?php if ($gateError !== '') print(' aria-invalid="true" aria-describedby="lbGateError"'); ?>>
					<?php if ($gateError !== ''): ?>
					<p class="lb-gate-error" id="lbGateError" role="alert"><?php print($gateError); ?></p>
					<?php endif; ?>
					<div class="lb-gate-actions">
						<a class="lb-btn" href="games.php">Back to Games</a>
						<button type="submit" class="lb-btn lb-btn--ready">Open Lobby</button>
					</div>
				</form>
			</section>
		</main>
	</body>
</html>
<?php
		exit;
	endif;

  // Use cached JSON to reduce server load
  $gamelobbydataJSON = Manager::getGameLobbyDataJSON( $_SESSION["user"], $gameid);
  $gamelobbydata = json_decode($gamelobbydataJSON);
  
    // STAMPEDE PROTECTION: If server is generating data, tell client to wait 1s
    if (isset($gamelobbydata->status) && $gamelobbydata->status == "GENERATING") {
        echo '<html><head><meta http-equiv="refresh" content="1"></head>
        <body style="background:#000; color:red; display:flex; justify-content:center; align-items:center; height:100vh; font-family:sans-serif; font-size:24px;">
        Loading...
        </body></html>';
        exit;
    }

    if (isset($gamelobbydata->error)) {
        echo '<html><body style="background:#000; color:red; padding: 20px; font-family:sans-serif;">
        <h2>Backend Error Generating Lobby</h2>
        <pre>' . htmlspecialchars($gamelobbydata->error) . '</pre>
        <p>File: ' . htmlspecialchars($gamelobbydata->file) . ' on line ' . htmlspecialchars($gamelobbydata->line) . '</p>
        <p>Log ID: ' . htmlspecialchars($gamelobbydata->logid) . '</p>
        <a href="games.php" style="color:#aaf;">Back to Games</a>
        </body></html>';
        exit;
    }

    if (!is_object($gamelobbydata) || !isset($gamelobbydata->status) || $gamelobbydata->status != "LOBBY") {
        header('Location: games.php');
        exit;
    }
  
    // $gamelobbydataJSON is already set/cached, no need to encode again

	// Fleet Builder (FleetTestRule): a one-slot lobby with no teams, map or scenario.
	$isFleetTest = isset($gamelobbydata->rules->fleetTest);

	// Scenario Description (CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md Stage 4): the structured
	// scenario as its stored JSON TEXT, handed to the page's JS as a string for scenarioCard to
	// parse - or null for a game created before it existed, whose description is parsed below
	// instead. The JSON_HEX_* flags keep the text inert inside the inline <script>.
	// With it, the In-Service Date (Stage 6, plan §4.4): the year the Store's ISD filter is locked
	// to, or null for none. Neither is ever set on a Fleet Builder lobby.
	$scenarioInfo = $isFleetTest ? array('scenario' => null, 'inServiceDate' => null) : Manager::getGameScenario($gamelobbydata->id);
	$scenarioText = $scenarioInfo['scenario'];
	$inServiceDate = $scenarioInfo['inServiceDate'];
	$scenarioJS = json_encode($scenarioText, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
	if ($scenarioJS === false) $scenarioJS = 'null';

	// Getting all ships in one go causes memory overload on the server.
	// Get the factions first. When a faction is opened to buy ships,
	// go bother the server for the ships of that faction only.
	
	$factions = json_encode(Manager::getAllFactions(), JSON_NUMERIC_CHECK);

	// Cache-busting versions for per-faction static ship JSON.
	// The lobby fetches ships on demand from gamelobbyloader.php, which serves
	// static/json/<faction>.json (regenerated on every patch). Relying on the
	// ETag/Last-Modified revalidation alone proved unreliable on mobile/BFCache
	// (browsers skip the revalidation round-trip and serve stale ships). We emit
	// each faction file's mtime so the client can append ?v=<mtime>, making the
	// request URL change whenever the ship data changes — same approach as
	// AssetLoader::getAssetUrl() uses for the JS bundles.
	$factionVersions = [];
	$jsonDir = __DIR__ . '/static/json';
	foreach (glob($jsonDir . '/*.json') as $jsonFile) {
		$factionVersions[basename($jsonFile, '.json')] = filemtime($jsonFile);
	}
	$factionVersionsJSON = json_encode($factionVersions, JSON_UNESCAPED_UNICODE);

	$ships = [];


?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Fiery Void - Gamelobby</title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"> <!--To try and ix mobile scroll juddering caused by viewport recalculation repaints -->
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

		<!-- Preload critical bundle to parallelize download with large inline JSON payloads below -->
		<?php $debug = (isset($_GET['debug']) || isset($_GET['DEBUG'])); ?>
		<?php if (!$debug): ?>
		<link rel="preload" href="<?php echo AssetLoader::getAssetUrl('client/gamelobby.legacy.bundle.js'); ?>" as="script">
		<?php endif; ?>
		<link rel="preload" href="<?php echo AssetLoader::getAssetUrl('client/UI/reactJs/UI.bundle.js'); ?>" as="script">

		<!-- Shared fv design tokens (roadmap item 6): MUST load before every other stylesheet. -->
		<link href="<?php echo AssetLoader::getAssetUrl('styles/tokens.css'); ?>" rel="stylesheet" type="text/css">
		<link href="<?php echo AssetLoader::getAssetUrl('styles/base.css'); ?>" rel="stylesheet" type="text/css">
		<link href="<?php echo AssetLoader::getAssetUrl('styles/lobby.css'); ?>" rel="stylesheet" type="text/css">
		<link href="<?php echo AssetLoader::getAssetUrl('styles/confirm.css'); ?>" rel="stylesheet" type="text/css">
        <link href="<?php echo AssetLoader::getAssetUrl('styles/gamesNew.css'); ?>" rel="stylesheet" type="text/css">
        <link href="<?php echo AssetLoader::getAssetUrl('styles/scenarioCard.css'); ?>" rel="stylesheet" type="text/css">
        <!-- The redesigned top of the page (Stage 4). After lobby.css and gamesNew.css, which it overrides. -->
        <link href="<?php echo AssetLoader::getAssetUrl('styles/gameLobby.css'); ?>" rel="stylesheet" type="text/css">
        <!-- jQuery + jQuery-UI self-hosted (same-origin HTTP/2 + cache-control, no 3rd-party
             TLS). Both kept SYNCHRONOUS: the lobby's synchronous client/*.js scripts run
             during parse and expect $.fn.draggable present, so jQuery-UI must not defer
             here. assetManager.js has no jQuery dep, so it defers. -->
        <script src="<?php echo AssetLoader::getAssetUrl('client/lib/jquery-4.0.0.min.js'); ?>"></script>
        <!-- Deploy-version cache-buster for images (see AssetManager.appendVersion). Plain
             inline <script> runs at parse time, before the deferred assetManager.js, so the
             global is guaranteed set first. Changes each deploy -> stale same-name art refetched. -->
        <script>window.assetVersion = "<?php echo AssetLoader::getDeployVersion(); ?>";</script>
        <script defer src="<?php echo AssetLoader::getAssetUrl('client/assetManager.js'); ?>"></script>
        <script src="<?php echo AssetLoader::getAssetUrl('client/lib/jquery-ui-1.14.2.min.js'); ?>"></script>
        <!-- React UI bundle (ship window + system info) — ship-window redesign Stage 3.
             AssetLoader tag, so bundle-legacy.js skips it (same as game.php). -->
        <script defer src="<?php echo AssetLoader::getAssetUrl('client/UI/reactJs/UI.bundle.js'); ?>"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
		
		<!-- replaced by php include below
        <script src="static/ships.js"></script>
		-->
<?php		
	//include 'static/ships.php'; //Changed how staticships are loaded to help with HTTP Protocol errors - DK Dec 2025

?>
		
<!--		<script src="client/helper.js"></script>-->
    <!--		<script src="client/helper.js"></script>-->
    <?php if ($debug): ?>
    <script src="client/gamelobby.js"></script>
		<script src="client/ajaxInterface.js"></script>
		<script src="client/lobbyEnhancements.js"></script>
		<script src="client/uiEventRelay.js"></script>
		<script src="client/renderer/shipWindowManager.js"></script>
		<script src="client/player.js"></script>
        <script src="client/ships.js"></script>
        <script src="client/criticals.js"></script>
        <script src="client/damage.js"></script>
        <script src="client/systems.js"></script>
        <script src="client/battleDamage.js"></script>
        <script src="client/systemEnhancements.js"></script>
        <script src="client/savedFleets.js"></script>
        <script src="client/power.js"></script>
        <script src="client/movement.js"></script>
        <script src="client/mathlib.js"></script>
		<script src="client/hangarShared.js"></script>
		<script src="client/UI/confirm.js"></script>
        <script src="client/UI/fleetList.js"></script>
        <script src="client/UI/gameInfo.js"></script>
        <script src="client/UI/scenarioCard.js"></script>
        <script src="client/UI/mapPreview.js"></script>
        <script src="client/model/ship.js"></script>
        <script src="client/model/shipSystem.js"></script>
        <script src="client/model/systemFactory.js"></script>
        <script src="client/model/system/baseSystems.js"></script>
        <script src="client/model/system/defensive.js"></script>
        <script src="client/model/weapon/ammo.js"></script>
    	<script src="client/model/weapon/ammoWeapons.js"></script>         
        <script src="client/model/weapon/laser.js"></script>
        <script src="client/model/weapon/particle.js"></script>
        <script src="client/model/weapon/matter.js"></script>
        <script src="client/model/weapon/plasma.js"></script>
        <script src="client/model/weapon/special.js"></script>
        <script src="client/model/weapon/supportWeapons.js"></script>        
        <script src="client/model/weapon/torpedo.js"></script>
        <script src="client/model/weapon/pulse.js"></script>
        <script src="client/model/weapon/electromagnetic.js"></script>
        <script src="client/model/weapon/aoe.js"></script>
        <script src="client/model/weapon/molecular.js"></script>
        <script src="client/model/weapon/antimatter.js"></script>
        <script src="client/model/weapon/gravitic.js"></script>
        <script src="client/model/weapon/missile.js"></script>
        <script src="client/model/weapon/ion.js"></script>
    	<script src="client/model/weapon/customs.js"></script>
		<script src="client/model/weapon/customSW.js"></script>
        <script src="client/model/weapon/customNexus.js"></script>
        <script src="client/model/weapon/customDevelopment.js"></script>
        <script src="client/model/weapon/customEscalation.js"></script>		
        <script src="client/model/weapon/customBSG.js"></script>		
        <script src="client/model/weapon/customTrek.js"></script>		
        <script src="client/model/weapon/customCW.js"></script>
    <?php else: ?>
    <script defer src="<?php echo AssetLoader::getAssetUrl('client/gamelobby.legacy.bundle.js'); ?>"></script>
    <?php endif; ?>		
		<script>
			
            /* Lobby weaponManager stub: predicate/data functions the React
               SystemIcon + SystemInfo render paths call. The lobby is read-only,
               so everything reads as idle/unloaded-order and the action functions
               are no-ops (SystemIcon's action branches are additionally gated off
               by gamephase -2 / gamedata.waiting).

               ⚠ THIS LIST DRIFTS. gamelobby.php does NOT bundle weaponManager.js -
               this object is the whole of `weaponManager` on this page. Any NEW
               `weaponManager.x()` call added to reactJs/system/* that is not behind
               a `gamedata.gamephase === -2` early return has to be added here too,
               or the lobby ship window dies with "x is not a function" and React
               reports "Ship window render failed for <ship>".

               SystemInfoButtons is safe by construction: canDoAnything, hasStyledMenu
               and render all return at gamephase -2 before reading any of this.
               SystemIcon is NOT - it calls straight through on every render. */
            window.weaponManager =
            {
                hasFiringOrder: function(){return false},
                isLoaded: function(){return true},
                isLoadedAlternate: function(){return false},
                isSelectedWeapon: function(){return false},
                getFiringOrder: function(){return null},
                getCalledShotInfo: function(){return null},
                selectAllWeapons: function(){}, //right-click select-all: nothing to select pre-game
                //Manual interception (MANUAL_INTERCEPTION_PLAN.md). Both are read by
                //SystemIcon with NO phase guard in front of them:
                //  isInterceptOnly       - the green "committed to defence" icon state,
                //                          evaluated for every system on every render (§11.3).
                //  canManuallyInterceptWith - the last clause of the weapon-select gate, and
                //                          `||` reaches it once the three phase clauses ahead
                //                          of it are false, which at gamephase -2 they always
                //                          are (Stage 4, §4.7a).
                //Nothing is declared pre-game, so both are flatly false.
                isInterceptOnly: function(){return false},
                canManuallyInterceptWith: function(){return false},
                getWeaponCurrentLoading: function(weapon)
                {
                    /* Weapons enter the game fully loaded, so the icon load counter
                       shows the ready state ("1/1", or "2/2" for normalload weapons).
                       Plain normalload (the old stub) read "0/1" on standard weapons. */
                    return weapon.normalload > 0 ? weapon.normalload : weapon.loadingtime;
                },
            }

            $(function(){
                window.shipManager.movement.isRolled = function(ship)
                {
                    return false;
                }
            });


            $(function(){
                /* Lobby: your own purchased mines are always identified — there is
                   no reveal mechanic pre-game, and blueprint ships have no .team so
                   the game-side check would render every mine as an unknown "?". */
                if (window.MineStealth) {
                    MineStealth.prototype.isMineRevealed = function () { return true; };
                }
            });

        
        jQuery(function($){            
            var lobbyData = <?php print($gamelobbydataJSON); ?>;
            // Per-faction static-ship versions (file mtimes) for cache-busting
            // the gamelobbyloader.php fetch. See $factionVersions in gamelobby.php.
            window.factionVersions = <?php print($factionVersionsJSON); ?>;
            gamedata.parseServerData(lobbyData);
            // The In-Service Date cutoff, a year or null: the Store's ISD box is printed locked to it,
            // and a saved fleet loads without the units that entered service later (doLoadFleet).
            gamedata.inServiceDate = <?php print($inServiceDate === null ? 'null' : (int)$inServiceDate); ?>;
            // The structured Scenario Description (tac_game.scenario) as its raw JSON text, or null;
            // then the In-Service Date again and whether the game is private, for their Game rules chips.
            gamedata.renderScenarioPanel(<?php print($scenarioJS); ?>, gamedata.inServiceDate, <?php print(($gameAccess && $gameAccess['private']) ? 'true' : 'false'); ?>);
            gamedata.parseFactions(<?php print($factions); ?>);
            
            var customWarningShown = false; 
            var customFactionWarningShown = false;
            var customShipWarningShown = false;

            $('.readybutton').on("click", gamedata.onReadyClicked);
            $('.savebutton').on("click", gamedata.onSaveClicked)            		
            $('.checkbutton').on("click", gamedata.checkChoices); //fleet correctness check
            $('.leave').on("click", gamedata.onLeaveClicked);
            $('.leaveslot').on("click", gamedata.onLeaveSlotClicked);
            $('.selectslot').on("click", gamedata.onSelectSlotClicked);
            $('.takeslot').on("click", gamedata.clickTakeslot);

            // Start polling for updates
            ajaxInterface.startPollingGamedata();

            // The Faction Picker's tier / Show Custom / search filter (gamedata.filterFactionList).
            // Global under its old name: gamelobby.js calls it after a slot is taken or left.
            window.updateTierFilter = function () {
                gamedata.filterFactionList();
            };

            // The Purchase panel's Store and the two lobby windows (Faction Picker, Fleet Check).
            gamedata.initPurchasePanel();

            // ✅ Listen to Tier and Custom Faction checkboxes
            $('.tier-filter').on('change', updateTierFilter);

            /* "Custom Factions and/or Units not allowed" - once per page, whichever Show Custom box is
               ticked first, and never in a lobby that allows customs or in Fleet Builder. */
            function warnIfCustomsNotAllowed() {
                var description = lobbyData.description || "";
                // Check if explicit permission is missing (i.e. it does NOT say "Allowed")
                var allowed = description.match(/CUSTOM FACTIONS \/ UNITS:\s*Allowed/i);

                if (!allowed && !customWarningShown && gamedata.rules && gamedata.rules.fleetTest !== 1) {
                     window.confirm.warning("Custom Units are not allowed in this game! <br>Please read Scenario Description");
                     customWarningShown = true;
                }
            }

            // The Faction Picker's Show Custom (and its Show Customs / Show Only Customs select):
            // which FACTIONS can be picked.
            $('#toggleCustom, #customSelect').on('change', function () {
                if ($('#toggleCustom').is(':checked')) {
                    $('#customDropdown').show();
                    warnIfCustomsNotAllowed();
                } else {
                    $('#customDropdown').hide();
                }
                updateTierFilter();
            });

            // The Purchase bar's Show Custom: the CUSTOM ships an official faction carries, in the
            // Store (gamedata.applyCustomShipFilter). Its own setting since Stage 5 - Reset Filters
            // clears it and leaves the picker's alone. A custom faction's own ships always show: it
            // was picked with the picker's box.
            $('#toggleCustomShips').on('change', function () {
                if (this.checked) warnIfCustomsNotAllowed();
                gamedata.applyCustomShipFilter();
            });


            // ✅ Default both "Show Custom" checkboxes on if customs are explicitly
            // allowed in the scenario description, or this is a fleet-test lobby.
            (function () {
                var description = lobbyData.description || "";
                var customsAllowed = /CUSTOM FACTIONS \/ UNITS:\s*Allowed/i.test(description)
                var isFleetTest = !!(gamedata.rules && gamedata.rules.fleetTest);
                if (customsAllowed || isFleetTest) {
                    // Set checked + show the dropdown without triggering the change
                    // handler, so the "not allowed" warning never fires on load.
                    $('#toggleCustom, #toggleCustomShips').prop('checked', true);
                    $('#customDropdown').show();
                }
            })();

            // ✅ Initial call
            updateTierFilter();


            // Reset Filters (the Purchase bar's chip): the Store's own filters - the three text
            // fields and its Show Custom - and nothing of the Faction Picker's. An ISD box the game's
            // In-Service Date has locked (readonly) keeps its year.
            var shipFilterFields = "#isdFilter, #nameFilter, #costFilter";

            $('.resetFilters').on('click', function () {
                $(shipFilterFields).not('[readonly]').val('');
                $('#toggleCustomShips').prop('checked', false);
                gamedata.applyCustomShipFilter();
            });

            // Sanitize input on each keystroke, but don't apply filter yet
            $("#isdFilter").on("input", function () {
                let val = $(this).val().replace(/\D/g, ''); // remove non-digits
                if (val.length > 4) val = val.slice(0, 4); // limit to 4 digits
                $(this).val(val);
            });

            // Same treatment for Cost - digits only, 5 is plenty for any point value
            $("#costFilter").on("input", function () {
                let val = $(this).val().replace(/\D/g, '');
                if (val.length > 5) val = val.slice(0, 5);
                $(this).val(val);
            });

            // Apply filter only when Enter key is pressed
            $("#isdFilter, #nameFilter, #costFilter").on("keypress", function (e) {
                if (e.which === 13) {
                    gamedata.applyCustomShipFilter();
                }
            });

            /* Fleet Builder points cap (rendered only in a fleetTest lobby, so both
               selectors come back empty and these binds are no-ops elsewhere).
               gamedata.builderMaxPoints is the ONE piece of state: null = unlimited.
               Everything downstream reads it through gamedata.getMaxPoints(). */
            $('#unlimitedPointsToggle').on('change', function () {
                if ($(this).is(':checked')) {
                    gamedata.builderMaxPoints = null;
                } else {
                    var typed = parseInt($('#maxPointsInput').val(), 10);
                    if (isNaN(typed) || typed < 0) {
                        typed = 3500;
                        $('#maxPointsInput').val(typed);
                    }
                    gamedata.builderMaxPoints = typed;
                }
                gamedata.calculateFleet();
            });

            $('#maxPointsInput').on('input', function () {
                var typed = parseInt($(this).val(), 10);
                //A blank/part-typed field keeps the last good cap rather than briefly
                //reading as 0 and turning the whole fleet unaffordable.
                if (isNaN(typed) || typed < 0) return;
                gamedata.builderMaxPoints = typed;
                gamedata.calculateFleet();
            });

            $('#maxPointsInput').on('blur', function () {
                if (gamedata.builderMaxPoints === null) return;
                $(this).val(gamedata.builderMaxPoints);
            });

            // The picker's Show Customs / Show Only Customs select follows its box from the start.
            $("#toggleCustom").trigger("change");
        });


		</script>
	</head>
	<body <?php
		// The background filename arrives from the client at game creation and is
		// stored unescaped. Emit it only if it still looks like a plain image
		// filename — that closes attribute breakout, CSS url() injection and path
		// traversal in one check.
		if ($gamelobbydata && !empty($gamelobbydata->background)
			&& preg_match('/^[A-Za-z0-9._-]+\.(jpg|jpeg|png|gif|webp)$/i', $gamelobbydata->background)) {
			echo 'style="background-image:url(img/maps/' . htmlspecialchars($gamelobbydata->background, ENT_QUOTES) . ')"';
		}
	?>>

  <header class="pageheader">
    <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
    <div class="top-right-row">
      <a href="games.php">Back to Lobby</a>        
      <a href="logout.php" class="btn btn-primary">Logout</a>
    </div>
  </header>
<!--        <div class="helphide" style="float:right" onclick="window.helper.onClickHelpHide()">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        </div>-->
<main class="container"></main>        
		<?php
		/* ── The page (CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §4.1 / §4.2 / §11.3, Stage 4; §12.13) ──
		   The game's name, then two rows: Map Preview | Scenario Description over the Game Rules strip,
		   beside Teams; then Purchase Fleet | Your Fleet (§12.14). Teams and the Map Preview are
		   filled by gamelobby.js on every poll; the rule chips and the Scenario Description's
		   structured facts once, on load (gamedata.renderScenarioPanel).
		   A Fleet Builder lobby has no teams, no map and no scenario - only the reference links. */

		/* The reference pages - the same three as game.php's USEFUL LINKS. (The three off-site
		   random-faction wheels that sat under them are gone: the Faction Picker has its own
		   randomiser, plan §4.5, Stage 7.) */
		$lobbyLinks = '
		<div class="lb-links">
		  <div class="lb-links-row">
		    <span class="lb-links-label">Useful links</span>
		    <a class="lb-link" href="./faq.php" target="_blank" rel="noopener noreferrer"
		       title="Aide-memoire of specific rules and differences from Babylon 5 Wars">FAQ</a>
		    <a class="lb-link" href="./factions-tiers.php" target="_blank" rel="noopener noreferrer"
		       title="Overview of Fiery Void factions and their approximate strengths">Factions &amp; Tiers</a>
		    <a class="lb-link" href="./ammo-options-enhancements.php" target="_blank" rel="noopener noreferrer"
		       title="Details of all the extras available to Fiery Void units, e.g. missiles">Ammo &amp; Options</a>
		  </div>
		</div>';

		/* A game created before tac_game.scenario existed has only its free-text description:
		   "LABEL: value" lines, recovered by splitting each on its first colon - the old parser,
		   unchanged - and written out in scenarioCard.js's own fact-grid markup, so an old game reads
		   like a new one (plan §4.1). A line with no colon continues the fact above it (Additional
		   Info's extra lines). Everything is escaped: it is the creator's text. */
		function lobbyLegacyScenarioFacts($description) {
			$desc = str_replace(array('<br>', '<br/>', '<br />'), "\n", (string)$description);
			$desc = preg_replace('/^\*{3}.*\*{3}\s*/m', '', $desc); //the old "*** ... ***" header line

			$facts = array();
			foreach (preg_split("/\r\n|\n|\r/", trim($desc)) as $line) {
				$line = trim($line);
				if ($line === '') continue;

				$pos = strpos($line, ':');
				if ($pos !== false) {
					$facts[] = array('label' => trim(substr($line, 0, $pos)), 'value' => trim(substr($line, $pos + 1)));
				} else if (!empty($facts)) {
					$last = count($facts) - 1;
					$facts[$last]['value'] .= ($facts[$last]['value'] === '' ? '' : "\n") . $line;
				} else {
					$facts[] = array('label' => '', 'value' => $line);
				}
			}

			$html = '';
			foreach ($facts as $fact) {
				if ($fact['value'] === '') continue; //as in the structured grid: an empty fact is left out
				$isInfo = preg_match('/^ADDITIONAL INFO(RMATION)?$/i', $fact['label']) === 1;
				$html .= '<div class="fv-scn-fact' . ($isInfo ? ' fv-scn-fact--wide' : '') . '">'
					. '<dt class="fv-scn-label">' . htmlspecialchars($fact['label']) . '</dt>'
					. '<dd class="fv-scn-value' . ($isInfo ? ' fv-scn-value--multiline' : '') . '">' . htmlspecialchars($fact['value']) . '</dd>'
					. '</div>';
			}
			return $html === '' ? '' : '<dl class="fv-scn-grid">' . $html . '</dl>';
		}

		//A structured scenario is rendered by the page's JS; only a game without one is parsed here.
		$scenarioFactsHtml = ($scenarioText === null) ? lobbyLegacyScenarioFacts($gamelobbydata->description) : '';

		$mapSizeText = 'No boundaries';
		if (preg_match('/^(\d+)x(\d+)$/', (string)$gamelobbydata->gamespace, $mapSizeMatch)) {
			$mapSizeText = $mapSizeMatch[1] . ' &times; ' . $mapSizeMatch[2];
		}
		?>
		<div class="lb-top<?php if ($isFleetTest) echo ' lb-top--builder'; ?>">
			<div class="lb-titlebar">
				<!-- The game name is player-supplied and stored unescaped, so it must be escaped here. -->
				<h1 class="lb-title"><?php print($isFleetTest ? 'Fleet Builder' : htmlspecialchars($gamelobbydata->name)); ?></h1>
				<div class="lb-title-actions">
				<?php if (!$isFleetTest): ?>
					<!-- Folds the Map Preview / Scenario Description / Game Rules section away, so the Store
					     starts near the top of the page; remembered per game in this browser
					     (gamedata.initBriefToggle). -->
					<button type="button" class="lb-btn lb-brief-toggle" id="lbBriefToggle"
					        aria-expanded="true" aria-controls="lbBrief"
					        title="Show or hide the map, the scenario and the game rules">Map &amp; Scenario</button>
				<?php endif; ?>
					<button type="button" class="lb-btn lb-btn--leave leave">Leave Game</button>
				</div>
			</div>

			<!-- Two rows (plan §12.14, the user's layout): Map Preview | Scenario Description (the same
			     height) over the Game Rules strip - one container (§12.15) - beside Teams, which is never
			     taller than they are together, and scrolls inside when it has more; then Purchase Fleet
			     (as wide as its longest row) | Your Fleet, their tops level. One column on a phone: Teams,
			     the briefing, Your Fleet, the Store. -->
			<?php if ($isFleetTest): ?>
			<section class="lb-panel lb-builder" aria-labelledby="lbBuilderHead">
				<h2 class="lb-panel-head" id="lbBuilderHead"><span>Rules &amp; Info</span></h2>
				<?php print($lobbyLinks); ?>
			</section>
			<?php else: ?>
			<div class="lb-upper">
				<!-- Map Preview | Scenario Description, always the same height (user), then the Game Rules
				     chips in a strip under both. -->
				<div class="lb-brief" id="lbBrief">
					<section class="lb-panel lb-map" aria-labelledby="lbMapHead">
						<h2 class="lb-panel-head" id="lbMapHead"><span>Map Preview</span><span class="lb-panel-meta"><?php print($mapSizeText); ?></span></h2>
						<div class="lb-panel-body">
							<div class="lb-map-frame"><canvas id="mapPreview" width="545" height="390" aria-hidden="true"></canvas></div>
							<div class="lb-legend" id="lbMapLegend"></div>
						</div>
					</section>

					<section class="lb-panel lb-scenario" aria-labelledby="lbScenarioHead">
						<h2 class="lb-panel-head" id="lbScenarioHead"><span>Scenario Description</span></h2>
						<div class="lb-panel-body" id="lbScenarioFacts"><?php
							print($scenarioFactsHtml !== '' ? $scenarioFactsHtml : '<p class="lb-empty">No scenario details.</p>');
						?></div>
						<?php print($lobbyLinks); ?>
					</section>

					<section class="lb-panel lb-rules-panel" aria-labelledby="lbRulesLabel">
						<div class="lb-rules">
							<span class="lb-rules-label" id="lbRulesLabel">Game rules</span>
							<ul class="fv-rule-chips" id="lbRuleChips" aria-labelledby="lbRulesLabel"></ul>
						</div>
					</section>
				</div>

				<!-- A stretched box as tall as the briefing beside it, holding Teams at its own height up to
				     that (gameLobby.css .lb-teams-slot). -->
				<div class="lb-teams-slot">
					<section class="lb-panel lb-teams" aria-labelledby="lbTeamsHead">
						<h2 class="lb-panel-head" id="lbTeamsHead"><span>Teams</span><span class="lb-panel-meta" id="lbTeamsMeta"></span></h2>
						<div class="lb-panel-body">
							<div class="lb-teams-grid" id="lobbyTeamsContainer">
								<!-- Teams injected by JS (gamedata.createSlots) -->
							</div>
						</div>
					</section>
				</div>
			</div>
			<?php endif; ?>

			<div class="lb-lower">
				<?php
				/* ── Purchase Fleet (CREATE_GAME_GAMELOBBY_REDESIGN_PLAN.md §4.3, Stage 5; §12.13) ──────
				   The Store's filters, then the STORE, which shows ONE faction's ships at a time, picked
				   in the Faction Picker further down. The fleet being bought, the points and the fleet
				   tools are Your Fleet, beside it.
				   ⚠️ The wrapper keeps .buy (gamedata.enableBuy show()s / hide()s it for the viewer's own
				   slot) and .buy-panel-container (hidden until then). It is a plain block on purpose:
				   show() on a hidden element writes display:block, which would break a flex panel. */
				?>
				<div class="lb-buy-wrap buy buy-panel-container">
					<section class="lb-panel lb-buy" aria-labelledby="lbBuyHead">
						<h2 class="lb-panel-head" id="lbBuyHead"><span>Purchase Fleet</span></h2>

						<!-- Row 1: the Store's ship filters. The text fields apply when Enter is pressed (the
						     handlers are at the top of this file); Reset Filters clears them and this row's Show
						     Custom. The Faction Picker's filters are its own. -->
						<div class="lb-buy-bar">
							<div class="lb-buy-filters">
								<span class="lb-bar-label">Filter by:</span>
								<label class="lb-field">
									<span>Name</span>
									<input type="text" id="nameFilter" value="" class="lb-input lb-input--name">
								</label>
								<!-- Cost filter: hides anything costing MORE than the figure typed. -->
								<label class="lb-field">
									<span>Cost</span>
									<input type="text" id="costFilter" value="" class="lb-input lb-input--num"
									       inputmode="numeric" pattern="[0-9]*">
								</label>
								<?php if ($inServiceDate !== null): ?>
								<!-- Locked to the game's In-Service Date (plan §4.4, Stage 6): the same field, pre-filled
								     and readonly, so gamedata.applyCustomShipFilter filters by it exactly as by a typed
								     year, from the first faction loaded, and Reset Filters leaves it be. -->
								<label class="lb-field lb-field--locked" title="Fixed by the scenario's In-Service Date">
									<span>ISD</span>
									<input type="text" id="isdFilter" value="<?php print((int)$inServiceDate); ?>" class="lb-input lb-input--num"
									       readonly>
									<i class="fa-solid fa-lock lb-lock" aria-hidden="true"></i>
								</label>
								<?php else: ?>
								<label class="lb-field">
									<span>ISD</span>
									<input type="text" id="isdFilter" value="" class="lb-input lb-input--num"
									       inputmode="numeric" pattern="[0-9]*">
								</label>
								<?php endif; ?>
								<!-- The Store's own Show Custom (user, Stage 5): the CUSTOM ships an official faction
								     carries. The Faction Picker's box (#toggleCustom) is a separate setting - which
								     factions can be picked. A chip like the picker's Custom, round the same checkbox. -->
								<label class="lb-chip lb-chip--check lb-chip--custom"><input type="checkbox" id="toggleCustomShips" class="yellow-tick">Custom</label>
								<span class="lb-bar-sep" aria-hidden="true">|</span>
								<button type="button" class="lb-chip resetFilters">Reset Filters</button>
							</div>
						</div>

						<!-- Row 2: the Store's size categories (gamedata.showStoreCategory; data-cat is
						     parseShips' category index), in the Store's own top-to-bottom order. -->
						<div class="lb-buy-bar">
							<div class="lb-cat-chips" role="group" aria-label="Show one category of ships">
								<span class="lb-bar-label">Show:</span>
								<button type="button" class="lb-chip lb-cat-chip" data-cat="" aria-pressed="true">All</button>
								<button type="button" class="lb-chip lb-cat-chip" data-cat="6" aria-pressed="false">Mines</button>
								<button type="button" class="lb-chip lb-cat-chip" data-cat="5" aria-pressed="false">Structures</button>
								<button type="button" class="lb-chip lb-cat-chip" data-cat="4" aria-pressed="false">Capital</button>
								<button type="button" class="lb-chip lb-cat-chip" data-cat="3" aria-pressed="false">Heavy</button>
								<button type="button" class="lb-chip lb-cat-chip" data-cat="2" aria-pressed="false">Medium</button>
								<button type="button" class="lb-chip lb-cat-chip" data-cat="1" aria-pressed="false">Light Combat</button>
								<button type="button" class="lb-chip lb-cat-chip" data-cat="0" aria-pressed="false">Fighters</button>
							</div>
						</div>

						<!-- The Store: ONE faction's ships (gamedata.selectStoreFaction), under a bar naming it.
						     #store holds one .lb-store-faction per faction loaded so far - switching back to one
						     is instant - and only the chosen one is shown. -->
						<div class="lb-store">
							<div class="lb-store-bar" id="lbStoreBar">
								<div class="lb-store-current">
									<div class="lb-store-name" id="lbStoreFaction">No faction chosen</div>
									<div class="lb-store-meta" id="lbStoreMeta">Choose a faction to see its ships.</div>
								</div>
								<button type="button" class="lb-btn lb-btn--small" id="lbSwitchFaction"
								        aria-haspopup="dialog" aria-controls="lbFactionPicker">Choose Faction</button>
							</div>
							<div id="store" class="lb-store-list"></div>
						</div>
					</section>
				</div>

				<!-- Your Fleet: the points, the fleet being bought, and every fleet tool - one set now
				     (it used to be two, top and bottom of Purchase Fleet): the panel stays in view as the
				     Store scrolls, and on a phone it sits above the Store. Hidden with Purchase Fleet
				     until the viewer holds a slot - the same .buy wrapper. -->
				<div class="lb-fleet-wrap buy buy-panel-container">
					<section class="lb-panel lb-buy lb-fleetpanel" aria-labelledby="lbFleetHead">
						<div class="lb-panel-head lb-buy-head">
							<h2 class="lb-buy-title" id="lbFleetHead">Your Fleet</h2>
							<!-- ONE figure (user, §12.17 - "0 / 3500 pts · 3500 pts left" wrapped): the points LEFT, or
							     for a slot with no limit the points SPENT. gamedata.calculateFleet writes .current /
							     .remaining and shows whichever applies. Fleet Builder puts its cap controls in front. -->
							<div class="lb-buy-points">
								<?php if ($isFleetTest): ?>
								<!-- Fleet Builder only. The slot itself is always unlimited server-side,
								     so this optional cap is purely a client-side yardstick: it drives the
								     points readout, the affordability checks and the Fleet Checker's
								     bracket/hull limits (gamedata.getMaxPoints). It sits exactly where
								     the word "Unlimited" does, and calculateFleet swaps the two - the
								     value is NEVER written into .max, which is rewritten on every
								     recalculation and would eat the field mid-keystroke. -->
								<span class="max">0</span><input type="number" id="maxPointsInput" class="max-points-input" value="3500"
								       min="0" step="50" style="display:none" aria-label="Maximum fleet points"><span class="max-points-units">pts</span>
								<input type="checkbox" id="unlimitedPointsToggle" class="yellow-tick unlimited-points-toggle"
								       checked title="Unlimited points - untick to build against a fixed limit"
								       aria-label="Unlimited points">
								<span class="lb-buy-dot" aria-hidden="true">·</span>
								<?php endif; ?>
								<span class="lb-buy-spent"><span class="current">0</span> pts</span>
								<span class="remaining-points-container"><span class="remaining">0</span> <span class="remaining-points-units">pts left</span></span>
							</div>
						</div>

						<!-- ⚠️ `store` stays on the FLEET's box: lobby.css styles the bought rows through it
						     (.store .ship, .store span, .store .ship .clickable), as it did when both columns
						     sat in one table.store. The Store itself does not carry it. -->
						<div class="lb-fleet store">
							<!-- REINFORCEMENTS_PLAN.md 2.1 - the BUY MODE: everything added to the fleet while it is
							     ticked is bought as a reinforcement and waits in hyperspace.
							     ⭐ THIS CHECKBOX IS THE STATE THE WHOLE FEATURE READS - buyingReinforcement() asks it,
							     applyReinforcementRule forces it off without the rule. Its control is the MAIN FLEET /
							     REINFORCEMENTS headers in the fleet list below (gamedata.setBuyTarget writes it,
							     applyFleetGrouping reads it back). The "Buy as Reinforcement" box that was a second
							     control was removed (user, Stage 5), so it is hidden - but it stays: it IS the state. -->
							<input type="checkbox" id="reinforcementModeToggle" hidden>
							<div id="fleet" class="subpanel fleet-panel-style"></div>
						</div>

						<div class="lb-fleet-tools">
							<!-- A bare <input> gave phone keyboards a "Next" action key, because the page has
							     more focusable fields after it (the chat panel), so pressing it moved focus
							     there instead of firing the keydown handler and the fleet never loaded.
							     The <form> is what actually fixes it: an input inside its OWN single-field
							     form gets implicit submission, so the action key becomes Go/Enter rather than
							     Next — no submit button is needed for that (HTML implicit submission), and
							     there deliberately isn't one. enterkeyhint labels the key, inputmode/pattern
							     bring up the numeric pad. -->
							<form class="fleet-id-form" id="fleetIdForm" action="#" onsubmit="return false;">
								<label class="lb-field">
									<span>Load Fleet by #ID</span>
									<input type="text" id="fleetIdInput" value="" class="lb-input lb-input--id"
									       inputmode="numeric" pattern="[0-9]*" enterkeyhint="go"
									       autocomplete="off" aria-label="Load fleet by ID">
								</label>
							</form>

							<!-- Two by two: Load a Fleet · Save Fleet, Check · Ready. Check opens the Fleet
							     Correctness Report window (#fleetcheck, below), which carries the link to the Fleet
							     Checker rules. -->
							<div class="lb-fleet-btns">
								<div class="saved-fleet-wrapper">
									<button type="button" id="fleetDropdownButton" class="lb-btn fleet-dropdown-btn">LOAD A FLEET</button>
									<div id="fleetDropdownList" class="fleet-dropdown-list">
										<!-- populated dynamically -->
									</div>
								</div>
								<button type="button" class="lb-btn lb-btn--save savebutton">Save Fleet</button>
								<button type="button" class="lb-btn lb-btn--check checkbutton" aria-haspopup="dialog" aria-controls="fleetcheck">Check</button>
								<?php if(!$isFleetTest): ?>
								<button type="button" class="lb-btn lb-btn--ready readybutton">Ready</button>
								<?php endif; ?>
							</div>
						</div>
					</section>
				</div>
			</div>
		</div>

    <script>
        let cachedFleets = [];
        let fleetsLoaded = false;
        // References
        const fleetDropdownButton = document.getElementById('fleetDropdownButton');
        const fleetDropdownList = document.getElementById('fleetDropdownList');

        // Toggle dropdown visibility
        fleetDropdownButton.addEventListener('click', () => {
            if (fleetDropdownList.style.display === 'block') {
                fleetDropdownList.style.display = 'none';
            } else {
                fleetDropdownList.style.display = 'block';
                
                if (!fleetsLoaded) {
                     // Show loading state
                     fleetDropdownList.innerHTML = '<div class="lb-fleetmenu-note">Loading fleets…</div>';
                     
                     ajaxInterface.getSavedFleets(function(fleets) {
                        cachedFleets = fleets;
                        fleetsLoaded = true;
                        gamedata.populateFleetDropdown();
                    });
                }
            }
        });

        // Close dropdown if clicked outside
        document.addEventListener('click', (e) => {
            if (!fleetDropdownButton.contains(e.target) && !fleetDropdownList.contains(e.target)) {
                fleetDropdownList.style.display = 'none';
            }
        });

        const fleetInput = document.getElementById("fleetIdInput");
        const fleetIdForm = document.getElementById("fleetIdForm");

        // Sanitize input on each keystroke: allow only digits
        fleetInput.addEventListener("input", function() {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, "");
        });

        function submitFleetId() {
            const fleetId = fleetInput.value.trim();
            if (fleetId === "" || isNaN(fleetId)) {
                window.confirm.fleetNotice("Enter the numeric ID of the fleet you want to load.");
                return;
            }
            fleetInput.blur();   // dismiss the on-screen keyboard before the dialog opens
            gamedata.loadSavedFleetById(parseInt(fleetId, 10));
        }

        /* The form submit is the path that phone keyboards actually take (their GO key
           submits the form rather than emitting an Enter keydown), and it is also what the
           Load button fires. The keydown handler stays for desktop Enter and for any
           keyboard that emits Enter without submitting - submitFleetId is idempotent, and
           preventDefault stops the two firing twice for one press. */
        fleetIdForm.addEventListener("submit", function(event) {
            event.preventDefault();
            submitFleetId();
        });

        fleetInput.addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                submitFleetId();
            }
        });

    </script>

		<?php
		/* ── The two lobby windows (gameLobby.css .lb-modal; opened and closed by gamelobby.js) ──
		   A dimmed overlay with the window centred on it - full screen on a phone. Closed by ×, a
		   click on the overlay or Escape. */
		?>
		<!-- The Faction Picker (plan §4.3): the six groups, Custom Factions split into its
		     sub-groups, stopping at faction level. The tier / Show Custom boxes live here because they
		     decide which FACTIONS can be picked; they keep their old ids and classes, so the handlers
		     at the top of this file drive them as before. The list is written by gamedata.parseFactions. -->
		<div class="lb-modal lb-picker" id="lbFactionPicker" hidden>
			<div class="lb-modal-panel lb-picker-panel" role="dialog" aria-modal="true" aria-labelledby="lbPickerTitle" tabindex="-1">
				<div class="lb-modal-head">
					<h2 class="lb-modal-title" id="lbPickerTitle">Select Faction</h2>
					<button type="button" class="lb-modal-close" data-close aria-label="Close">&times;</button>
				</div>
				<div class="lb-picker-search">
					<input type="search" id="factionSearch" class="lb-input" placeholder="Filter factions…"
					       aria-label="Filter factions" autocomplete="off" enterkeyhint="go">
				</div>
				<!-- The tier and Custom filters are CHIPS, any number on at once (user): each is a label round
				     its old, visually hidden checkbox, which is still the state every handler reads and
				     the thing the keyboard toggles. All / None set every tier chip at once
				     (gamedata.setAllTierFilters) and read pressed while that is the state. -->
				<div class="lb-picker-filters">
					<button type="button" class="lb-chip lb-tier-all" id="lbTierAll" aria-pressed="true">All</button>
					<button type="button" class="lb-chip lb-tier-none" id="lbTierNone" aria-pressed="false">None</button>
					<span class="lb-bar-sep" aria-hidden="true">|</span>
					<label class="lb-chip lb-chip--check"><input type="checkbox" class="tier-filter" data-tier="Tier 1" checked>Tier 1</label>
					<label class="lb-chip lb-chip--check"><input type="checkbox" class="tier-filter" data-tier="Tier 2" checked>Tier 2</label>
					<label class="lb-chip lb-chip--check"><input type="checkbox" class="tier-filter" data-tier="Tier 3" checked>Tier 3</label>
					<label class="lb-chip lb-chip--check"><input type="checkbox" class="tier-filter" data-tier="Tier Ancients" checked>Ancients</label>
					<label class="lb-chip lb-chip--check"><input type="checkbox" class="tier-filter" data-tier="Tier Other" checked>Other</label>
					<span class="lb-bar-sep" aria-hidden="true">|</span>
					<label class="lb-chip lb-chip--check lb-chip--custom"><input type="checkbox" id="toggleCustom" class="yellow-tick">Custom</label>
					<!-- ⚠️ DIRECTLY AFTER ITS OWN CHECKBOX, and it has to stay there. This dropdown is shown
					     and hidden by #toggleCustom (it is the "which customs?" half of that one control),
					     so the two read as one thing only while they are adjacent (user report 2026-08-28). -->
					<span id="customDropdown" class="lb-custom-mode">
						<select id="customSelect" name="customFilterMode" class="lb-select" aria-label="Which factions to show">
							<option value="showCustom">Show Customs</option>
							<option value="showOnlyCustom">Show Only Customs</option>
						</select>
					</span>
				</div>
				<div class="lb-picker-list" id="factionList"></div>
				<p class="lb-picker-empty" id="factionListEmpty" hidden>No faction matches these filters.</p>
				<!-- The randomiser (plan §4.5, Stage 7): rolls one of the factions the list above shows,
				     so the tier boxes, Show Custom and the search decide what it can suggest
				     (gamedata.rollFaction). It only points the faction out - Choose, or its row, picks it. -->
				<div class="lb-picker-random">
					<div class="lb-roll">
						<span class="lb-roll-text" id="lbRollResult" aria-live="polite">Rolls one of the factions listed above.</span>
						<button type="button" class="lb-btn lb-btn--small lb-roll-choose" id="lbRollChoose" hidden>Choose</button>
					</div>
					<button type="button" class="lb-btn lb-roll-btn" id="lbRollFaction">
						<i class="fa-solid fa-dice" aria-hidden="true"></i>Pick a Random Faction
					</button>
				</div>
			</div>
		</div>

		<!-- The Fleet Correctness Report (gamedata.checkChoices writes #fleetchecktxt). -->
		<div class="lb-modal lb-fleetcheck" id="fleetcheck" hidden>
			<div class="lb-modal-panel lb-fleetcheck-panel" role="dialog" aria-modal="true" aria-labelledby="lbFleetCheckTitle" tabindex="-1">
				<div class="lb-modal-head">
					<h2 class="lb-modal-title" id="lbFleetCheckTitle">Fleet Correctness Report</h2>
					<button type="button" class="lb-modal-close" data-close aria-label="Close">&times;</button>
				</div>
				<div class="lb-modal-body">
					<!-- The Fleet Checker rules link lives here, with the report it explains (user, Stage 5). -->
					<div class="lb-modal-sub">
						<span>Based on tournament rules, modified for scalability</span>
						<a href="./fleetchecker.php" target="_blank" rel="noopener noreferrer" class="lb-link"
						   title="Details of fleet composition rules">Fleet Checker rules</a>
					</div>
					<div id="fleetchecktxt" class="lb-fleetcheck-text"></div>
				</div>
			</div>
		</div>

        <?php
        // A PANEL WRAPPER around #globalchat rather than the same element, matching
        // games.php and creategame.php: the head bar has to sit outside the scrolling
        // body or it scrolls away with the message log. .fv-chat-panel (chat.css) takes
        // over the padding and the flow; .panel.large.lobby keeps the lobby's own width,
        // border and fill.
        ?>
        <section class="panel large lobby global-chat-wrapper fv-chat-panel">
        <div id="globalchat">
        <?php
            $chatgameid = 0;
            $chatelement = "#globalchat";
            $chattitle = "Global Chat";
            $chatmeta = "All players";
            include("chat.php")
        ?>
        </div>
        </section>

<!--        <div id="globalhelp" class="helppanel">
        <?php
//        	$messagelocation='gamelobby.php';
//        	$ingame=false;
//        	include("helper.php")
        ?>
        </div>-->
                    
    <!-- React mounts (ship-window redesign Stage 3). Fixed full-viewport wrappers so
         the absolutely-positioned windows/tooltips anchor to the VIEWPORT on this
         scrolling page (the legacy equivalent was lobby.css forcing .shipwindow to
         position: fixed). pointer-events is re-enabled inside the components
         (ShipWindowContainer); the info tooltip stays click-through by design. -->
    <div id="shipWindowsReact" style="position:fixed; inset:0; pointer-events:none; z-index:10001;"></div>
    <div id="systemInfoReact" style="position:fixed; inset:0; pointer-events:none; z-index:20000;"></div>

                    
    <!-- Cloned by gamedata.createNewSlot, one per team; paintLobbyTeams sets its --rail colour. -->
    <div id="lobbyTeamTemplate" style="display:none;">
        <div class="team-section lb-team" data-team-id="">
             <h3 class="lb-team-head">Team <span class="team-number"></span></h3>
             <div class="slotcontainer lb-slots"></div>
        </div>
    </div>

    <!-- Cloned WITH its events (clone(true)) by gamedata.createNewSlot: the Take / Select / Leave
         handlers are bound to these template buttons at load, so the classes must stay. Which of
         them shows is the .taken / .ready / .selected classes plus createSlots' show() / hide(). -->
    <div id="slottemplatecontainer" class="hidden-template-container">
        <div class="slot lb-slot">
            <div class="lb-slot-main">
                <div class="lb-slot-player">
                    <span class="playername"></span><span class="lb-slot-open">[OPEN]</Open></span>
                </div>
                <div class="lb-slot-meta">
                    <span class="value name"></span>
                    <span class="lb-slot-sep" aria-hidden="true">&middot;</span>
                    <span class="value points"></span>
                    <span class="lb-slot-late"><span class="lb-slot-sep" aria-hidden="true">&middot;</span> deploys T<span class="value depavailable"></span></span>
                </div>
            </div>
            <div class="lb-slot-actions">
                <span class="status">Ready</span>
                <button type="button" class="takeslot lb-pill lb-pill--take">Take Slot</button>
                <button type="button" class="selectslot lb-pill">Select</button>
                <button type="button" class="leaveslot lb-pill lb-pill--leave">Leave Slot</button>
            </div>
        </div>
    </div>

    <div id="systemtemplatecontainer" class="hidden-template-container">

        <div class="structure system">
            <div class="name"><span class="namevalue">STRUCTURE</span></div>
            <div class="systemcontainer">

                <div class="health systembarcontainer">
                    <div class="healthbar bar health-bar-initial"></div>
                    <div class="valuecontainer"><span class="healthvalue value"></span></div>
                </div>
            </div>
        </div>

        <div class="fightersystem">
            <div class="icon">
                <span class="efficiency value"></span>
                <div class="iconmask"></div>
            </div>
        </div>

        <div class="system regular">
            <div class="systemcontainer">
                <div class="icon">
                    <div class="efficiency value"></div>
                    <div class="iconmask"></div>
                    <div class="UI">
                        <div class="button stopoverload"></div>
                        <div class="button overload"></div>
                        <div class="button plus"></div>
                        <div class="button minus"></div>
                        <div class="button off"></div>
                        <div class="button on"></div>
                        <div class="button holdfire"></div>
                        <div class="button mode"></div>
                    </div>
                </div>

                <div class="health systembarcontainer">
                    <div class="healthbar bar" style="width:40px;"></div>
                </div>
                <div class="critical systembarcontainer">
                    <div class="valuecontainer"><span class="criticalvalue value">CRITICAL<span></div>
                </div>

            </div>
        </div>

        <div class="fighter">
            <div class="destroyedtext"><span>DESTROYED</span></div>
            <div class="disengagedtext"><span>DISENGAGED</span></div>
            <div class="dockedtext"><span>DOCKED</span></div>
            <div class="systemcontainer">
                <div class="icon">
                    <table class="fightersystemcontainer 1"><tr></tr></table>
                    <div style="height:60px;"></div>
                    <table class="fightersystemcontainer 2"><tr></tr></table>
                </div>

                <div class="health systembarcontainer">
                    <div class="healthbar bar" style="width:90px;"></div>
                    <div class="valuecontainer"><span class="healthvalue value"></span></div>
                </div>
            </div>
        </div>

        <div class="heavyfighter">
            <div class="systemcontainer">
                <div class="icon">
                    <table class="fightersystemcontainer 1"><tr></tr></table>
                    <div style="height:60px;"></div>
                    <table class="fightersystemcontainer 2"><tr></tr></table>
                </div>

                <div class="health systembarcontainer">
                    <div class="healthbar bar" style="width:90px;"></div>
                    <div class="valuecontainer"><span class="healthvalue value"></span></div>
                </div>
            </div>
        </div>
        
    </div>

    </main>
    
<div id="global-blocking-overlay" class="blocking-overlay" style="display:none;">
    <span>
        TRANSMITTING ORDERS...<br>
        <span class="blocking-warning">Do not close window</span>
    </span>

</div>    

<footer class="site-disclaimer">
  <p>
DISCLAIMER — Fiery Void is an unofficial, fan-created work based on concepts from Agents of Gaming’s Babylon 5 Wars. 
It is not affiliated with, endorsed by, or sponsored by any official rights holders. 
All trademarks and copyrights remain the property of their respective owners.
  </p>
</footer>


	</body>
</html>
