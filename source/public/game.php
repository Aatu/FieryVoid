<?php
$fv_start_timer = microtime(true);
require_once 'global.php'; // ✅ Critical dependency
session_write_close(); // Prevent Session Locking (Spam Refresh Protection)

// Never let the browser cache this HTML document. It carries a player-specific,
// point-in-time gamedata snapshot inlined into the page (gamedata.parseServerData
// below). global.php calls session_cache_limiter('') which strips PHP's default
// no-cache headers, so without this the browser is free to disk-cache the page
// and replay a STALE copy on session restore (reopening tabs after a browser or
// computer restart) — with no server round-trip and no pageshow.persisted signal
// to trigger a client re-fetch. no-store forces a fresh fetch every time.
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

	$gameid = 1;
	$thisplayer = -1;

	if (isset($_GET["gameid"])){
		$gameid = $_GET["gameid"];
	}else{
		//header('Location: games.php');
	}
	
	if (isset($_SESSION["user"])){
		$thisplayer = $_SESSION["user"];
	}
	
	$t1 = microtime(true);
	$serverdataJSON = Manager::getTacGamedataJSON($gameid, $thisplayer, null, 0, -1, true);
    $time_getTacGamedataJSON = microtime(true) - $t1;
    $error = 'null';
    $serverdata = null;

    // Check if result is valid JSON or Error
    $decoded = json_decode($serverdataJSON);
    
    if (isset($decoded->error)) {
        $error = $serverdataJSON; // It's an error object in JSON format
        $serverdataJSON = '{}';
    } elseif (isset($decoded->status) && $decoded->status == 'GENERATING') {
        // STAMPEDE PROTECTION
        echo '<html><head><meta http-equiv="refresh" content="1"></head>
        <body style="background:#000; color:red; display:flex; justify-content:center; align-items:center; height:100vh; font-family:sans-serif; font-size:24px;">
        Loading game data...
        </body></html>';
        exit;
    } else {
        $serverdata = $decoded; // Valid game data
    }
?>


<!DOCTYPE HTML>
<html>
<head>
    <title>Fiery Void</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, minimal-ui' />

    <!-- Preload critical bundles to parallelize their download with the large inline JSON payloads below -->
    <!-- jQuery stays a synchronous <script> (the inline bootstrap needs $ at parse time);
         preload it here so the browser fetches it in parallel rather than discovering it mid-head. -->
    <link rel="preload" href="<?php echo AssetLoader::getAssetUrl('client/lib/jquery-4.0.0.min.js'); ?>" as="script">
    <link rel="preload" href="<?php echo AssetLoader::getAssetUrl('client/UI/reactJs/UI.bundle.js'); ?>" as="script">
    <?php $debug = (isset($_GET['debug']) || isset($_GET['DEBUG'])); ?>
    <?php if (!$debug): ?>
    <link rel="preload" href="<?php echo AssetLoader::getAssetUrl('client/game.legacy.bundle.js'); ?>" as="script">
    <?php endif; ?>
    
    <!-- ?v=mtime: .htaccess serves CSS with "access plus 1 month", so without a
         cache-buster a stylesheet edit can take up to a month to reach returning
         players. -->
    <!-- Shared fv design tokens (roadmap item 6): MUST load before every other stylesheet. -->
    <link href="<?php echo AssetLoader::getAssetUrl('styles/tokens.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo AssetLoader::getAssetUrl('styles/tactical.css'); ?>" rel="stylesheet" type="text/css">
	<link href="<?php echo AssetLoader::getAssetUrl('styles/confirm.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo AssetLoader::getAssetUrl('styles/replay.css'); ?>" rel="stylesheet" type="text/css">
    <link href="<?php echo AssetLoader::getAssetUrl('styles/shipTooltip.css'); ?>" rel="stylesheet" type="text/css">
    <!-- The hex stack picker (client/UI/SelectFromShips.js). Its own namespace and its own
         file so it no longer shares .shipNameContainer with the hover tooltip above. -->
    <link href="<?php echo AssetLoader::getAssetUrl('styles/hexPicker.css'); ?>" rel="stylesheet" type="text/css">
<!--	styles/helper.css was deleted (roadmap item 6, Stage 5) - it was dead, see helper.php -->
    <!-- jQuery + jQuery-UI self-hosted (same-origin HTTP/2 + cache-control, no 3rd-party TLS).
         Both kept SYNCHRONOUS: jQuery for the inline $(window).on("load") bootstrap below, and
         jQuery-UI alongside it so $.fn.draggable is guaranteed present for any drag path without
         relying on defer/ready ordering (kept consistent with gamelobby.php). assetManager.js
         has no jQuery dep, so it defers. The big win here is self-hosting, not the defer. -->
    <script src="<?php echo AssetLoader::getAssetUrl('client/lib/jquery-4.0.0.min.js'); ?>"></script>
    <!-- Deploy-version cache-buster for images (see AssetManager.appendVersion). Plain
         inline <script> runs at parse time, before the deferred assetManager.js, so the
         global is guaranteed set first. Changes each deploy -> stale same-name art refetched. -->
    <script>window.assetVersion = "<?php echo AssetLoader::getDeployVersion(); ?>";</script>
    <script defer src="<?php echo AssetLoader::getAssetUrl('client/assetManager.js'); ?>"></script>
    <script src="<?php echo AssetLoader::getAssetUrl('client/lib/jquery-ui-1.14.2.min.js'); ?>"></script>
    <!-- Tree-shaken THREE r160 global shim (perf #5): replaces the 670KB vendored
         three.min.js UMD with a ~500KB build of only the symbols FV uses, installed on
         window.THREE so the legacy code + MeshLine are unchanged. Versioned (?v=mtime)
         because it regenerates per build, like the legacy bundle. Must stay before
         MeshLine + the legacy bundle (both read window.THREE); document order + defer
         preserve that. -->
    <script defer src="<?php echo AssetLoader::getAssetUrl('client/lib/three.shim.bundle.js'); ?>"></script>
    <script defer src="client/lib/THREE.MeshLine.js"></script>
    <script defer src="<?php echo AssetLoader::getAssetUrl('client/UI/reactJs/UI.bundle.js'); ?>"></script>
	<!-- replaced by php include below
    <script src="static/ships.js"></script>
	-->
    <?php		
        //include 'static/ships.php'; //Changed how staticships are loaded to help with HTTP Protocol errors - DK Dec 2025
        $shipClasses = [];
        if(isset($serverdata) && isset($serverdata->ships)){
            foreach($serverdata->ships as $ship){
                $shipClasses[] = $ship->phpclass;
            }
        }
        $t2 = microtime(true);
        $staticShips = ShipLoader::getShipsByClass($shipClasses);
        $time_getShipsByClass = microtime(true) - $t2;

        // Auto-discover dynamically spawnable ship blueprints from weapon system properties.
        // Any weapon with a $spawnableClasses array will have its listed classes preloaded here,
        // so the frontend has their full blueprint when a mine/unit is spawned mid-game.
        //
        // Hangar Ops: faction-specific default shuttles (e.g. Flyer for Minbari) live on
        // HangarOps::$factionShuttleMap rather than on every Hangar's $spawnableClasses,
        // so each game only preloads the shuttle classes of factions actually present
        // (and carrying a Hangar) — not all ~80 factions in the codebase.
        $spawnableClasses = [];
        $factionsWithHangars = [];
        foreach ($staticShips as $faction => $shipBlueprints) {
            foreach ($shipBlueprints as $blueprint) {
                foreach ($blueprint->systems as $system) {
                    if (!empty($system->spawnableClasses)) {
                        foreach ($system->spawnableClasses as $cls) {
                            $spawnableClasses[] = $cls;
                        }
                    }
                    if ($system instanceof Hangar) {
                        $factionsWithHangars[$faction] = true;
                        //Per-bay fighter-class allow-list (arch_hangar_class_allowlist):
                        //preload the carrier's reserved fighter blueprint(s) so the client
                        //can resolve the phpclass to a display name (Hangar SystemInfo
                        //"Type:" line + lobby ship window) even when no such flight is
                        //deployed yet. Without this the class is absent from staticShips
                        //and the resolver falls back to showing the raw phpclass.
                        if (!empty($system->allowedFighterClasses)) {
                            foreach ($system->allowedFighterClasses as $cls) {
                                $spawnableClasses[] = $cls;
                            }
                        }
                    }
                }
                //Category-declared default shuttles (yacht / lifeboats / medical /
                //presidential — see HangarOps::shuttlePhpclassForCategory): these
                //auto-populate into the carrier's hangar but are NOT in the faction
                //shuttle map, so the faction-shuttle preload below misses them.
                //Without preloading them, launching one leaves the client unable to
                //resolve its phpclass -> blueprint (shows "undefined" in the ini GUI).
                if (!empty($blueprint->fighters) && is_array($blueprint->fighters)) {
                    foreach ($blueprint->fighters as $category => $count) {
                        $shuttleClass = HangarOps::shuttlePhpclassForCategory($category, $blueprint);
                        if ($shuttleClass !== null) $spawnableClasses[] = $shuttleClass;
                    }
                }
            }
        }
		foreach (array_keys($factionsWithHangars) as $faction) {
			$factionShuttle = HangarOps::shuttleClassForFactionName($faction);
			if ($factionShuttle !== null) $spawnableClasses[] = $factionShuttle;
			$factionMsw = HangarOps::minesweepingShuttleClassForFactionName($faction);
			if ($factionMsw !== null) $spawnableClasses[] = $factionMsw;
		}            
        if (!empty($spawnableClasses)) {
            $spawnableStaticShips = ShipLoader::getShipsByClass(array_unique($spawnableClasses));
            foreach ($spawnableStaticShips as $faction => $classes) {
                if (!isset($staticShips[$faction])) $staticShips[$faction] = [];
                foreach ($classes as $classKey => $blueprint) {
                    $staticShips[$faction][$classKey] = $blueprint;
                }
            }
        }

        echo '<script>window.staticShips = ' . json_encode($staticShips) . ';</script>';
    ?>
    <script>
        window.Config = {
            HEX_SIZE: 50,
            MAX_CONCURRENT_IMAGES: 40 // Limit concurrent image loads to prevent HTTP/2 errors
        };
    </script>

    <?php include_once 'shaders.php'; ?>
    <script>
        $(window).on("load", function(){
            // Yield execution to allow the browser to paint whatever HTML is currently visible
            setTimeout(function() {
                // ----- Client-side load timing instrumentation -----
                // Logs a breakdown of the synchronous work between JSON-received and first
                // render, so the post-JSON pause can be measured. Entirely gated on ?perf in
                // the URL — with no ?perf, _mark is a no-op and nothing is timed or logged.
                var FV_PERF = (window.location.search.toLowerCase().indexOf('perf') !== -1);
                var _t = (window.performance && performance.now) ? function(){ return performance.now(); } : function(){ return Date.now(); };
                var _marks = [];
                var _last = FV_PERF ? _t() : 0;
                var _mark = FV_PERF
                    ? function(label){ var now = _t(); _marks.push([label, now - _last]); _last = now; }
                    : function(){};
                var _loadStart = _last;

                var serverError = <?php print($error); ?>;
                if (serverError) {
                    var errorMsg = serverError.error || JSON.stringify(serverError);
                    alert("Server Error: " + errorMsg + (serverError.logid ? " [LogID: " + serverError.logid + "]" : ""));
                }

                gamedata.parseServerData(<?php print($serverdataJSON); ?>);
                _mark('parseServerData');

                if (gamedata.thisplayer == -1){
                    $(".notlogged").show();
                    $(".waiting").hide();
                    gamedata.waiting = false;
                }

                // webglScene.init drives the heavy work: hex grid, then phaseDirector ->
                // phaseStrategy.activate -> consumeGamedata, which builds every ship icon
                // (and, currently, every ship's status window).
                webglScene.init(
                    '#webgl',
                    jQuery('#pagecontainer'),
                    new window.webglHexGridRenderer(graphics),
                    new window.phaseDirector(graphics),
                    gamedata,
                    window.coordinateConverter
                );
                _mark('webglScene.init (icons + windows)');

                window.UIManagerInstance = new window.UIManager($("body")[0]);
                window.UIManagerInstance.PlayerSettings(window.Settings);
                window.UIManagerInstance.FullScreen();
                window.UIManagerInstance.Surrender();
                window.UIManagerInstance.EwButtons();
                _mark('UIManager (React mounts)');

                $("#pagecontainer").show();
                _mark('pagecontainer.show');

                // Report (only with ?perf). Total is the blocking JS time after JSON parse;
                // images then stream in asynchronously via the throttled texture queue.
                if (FV_PERF) {
                    var _total = _t() - _loadStart;
                    var _shipCount = (gamedata.ships && gamedata.ships.length) || 0;
                    console.log('[FV load] total post-JSON blocking: ' + _total.toFixed(1) + ' ms for ' + _shipCount + ' ships');
                    _marks.forEach(function(m){ console.log('[FV load]   ' + m[0] + ': ' + m[1].toFixed(1) + ' ms'); });
                    if (window.performance && performance.timing) {
                        var nav = performance.timing;
                        console.log('[FV load]   (navigationStart -> load event: ' + (nav.loadEventStart - nav.navigationStart) + ' ms)');
                    }
                }
            }, 50);
        });


    </script>
<!--	<script src="client/helper.js"></script>-->
    <?php if ($debug): ?>
    <script defer src="client/lib/graphics.js"></script>
    <script defer src="client/lib/HexagonMath.js"></script>
    <script defer src="client/lib/AbstractCanvas.js"></script>
    <script defer src="client/Settings.js"></script>
    <script defer src="client/uiEventRelay.js"></script>
    <script defer src="client/renderer/webglHexGridRenderer.js"></script>
    <script defer src="client/renderer/canvasHexGridRenderer.js"></script>
    <script defer src="client/renderer/webglScene.js"></script>
    <script defer src="client/renderer/webglScrolling.js"></script>
    <script defer src="client/renderer/webglZooming.js"></script>
    <script defer src="client/renderer/PhaseDirector.js"></script>
    <script defer src="client/renderer/Animation.js"></script>

    <script defer src="client/renderer/shipWindowManager.js"></script>

    <script defer src="client/renderer/sprite/webglSprite.js"></script>
    <script defer src="client/renderer/sprite/HexagonSprite.js"></script>
    <script defer src="client/renderer/sprite/ShipEWSprite.js"></script>
    <script defer src="client/renderer/sprite/ShipSelectedSprite.js"></script>
    <script defer src="client/renderer/sprite/BoxSprite.js"></script>
    <script defer src="client/renderer/sprite/PlainSprite.js"></script>
    <script defer src="client/renderer/sprite/LineSprite.js"></script>
    <script defer src="client/renderer/sprite/BallisticSprite.js"></script>
    <script defer src="client/renderer/sprite/BallisticLineSprite.js"></script>
    <script defer src="client/renderer/sprite/TextSprite.js"></script>
    <script defer src="client/renderer/sprite/HexNumberSprite.js"></script>    
    
    <!-- Shared grid-overlay geometry: used by ShipIcon's arcs/EW blankets and by
         BallisticIconContainer's terrain footprints and splash areas. -->
    <script defer src="client/renderer/HexRegion.js"></script>

    <script defer src="client/renderer/icon/ShipIcon.js"></script>
    <script defer src="client/renderer/icon/FlightIcon.js"></script>
    <script defer src="client/renderer/icon/DeploymentIcon.js"></script>

    <script defer src="client/lib/coordinateConverter.js"></script>
    <script defer src="client/renderer/icon/ShipIconContainer.js"></script>
    <script defer src="client/renderer/icon/EWIconContainer.js"></script>
    <script defer src="client/renderer/icon/BallisticIconContainer.js"></script>

    <script defer src="client/renderer/animationStrategy/AnimationStrategy.js"></script>
    <script defer src="client/renderer/animationStrategy/IdleAnimationStrategy.js"></script>
    <script defer src="client/renderer/animationStrategy/ReplayAnimationStrategy.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/Animation.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/ShipMovementAnimation.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/LogAnimation.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/CameraPositionAnimation.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/ShipDestroyedAnimation.js"></script>  
    <script defer src="client/renderer/animationStrategy/animation/ShipJumpAnimation.js"></script>

    <script defer src="client/renderer/animationStrategy/animation/FireAnimationHelper.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/AllWeaponFireAgainstShipAnimation.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/HexTargetedWeaponFireAnimation.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/ParticleEmitterContainer.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/BaseParticle.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/StarParticle.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/ParticleEmitter.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/StarParticleEmitter.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/ParticleAnimation.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/ParticleEmitter.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/effects/Explosion.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/effects/LaserEffect.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/effects/BoltEffect.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/effects/MissileEffect.js"></script>    
    <script defer src="client/renderer/animationStrategy/animation/effects/TorpedoEffect.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/effects/BlinkEffect.js"></script>    
    <script defer src="client/renderer/animationStrategy/animation/effects/ShipExplosion.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/effects/SystemDestroyedEffect.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/effects/ShipJumpPoint.js"></script>
    <script defer src="client/renderer/animationStrategy/animation/effects/ImpactSparksEffect.js"></script>

    <script defer src="client/hangarShared.js"></script>
    <script defer src="client/renderer/phaseStrategy/PhaseStrategy.js"></script>
    <script defer src="client/renderer/phaseStrategy/DeploymentPhaseStrategy.js"></script>
    <script defer src="client/renderer/phaseStrategy/MineDeployment.js"></script>
    <script defer src="client/renderer/phaseStrategy/DeploymentDock.js"></script>
    <script defer src="client/renderer/phaseStrategy/WaitingPhaseStrategy.js"></script>
    <script defer src="client/renderer/phaseStrategy/InitialPhaseStrategy.js"></script>
    <script defer src="client/renderer/phaseStrategy/MovementPhaseStrategy.js"></script>
    <script defer src="client/renderer/phaseStrategy/PreFiringPhaseStrategy.js"></script>    
    <script defer src="client/renderer/phaseStrategy/FirePhaseStrategy.js"></script>
    <script defer src="client/renderer/phaseStrategy/ReplayPhaseStrategy.js"></script>
    <script defer src="client/renderer/terrain/StarField.js"></script>


    <script defer src="client/renderer/texture/HexagonTexture.js"></script>


    <script defer src="client/UI/ShipTooltip.js"></script>
    <script defer src="client/UI/SelectFromShips.js"></script>
    <script defer src="client/UI/shipTooltipMenu.js"></script>
    <script defer src="client/UI/shipTooltipInitialOrdersMenu.js"></script>
    <script defer src="client/UI/shipTooltipFireMenu.js"></script>
    <script defer src="client/UI/ShipTooltipBallisticsMenu.js"></script>
	<script defer src="client/UI/moveTooltip.js"></script>


    <script defer src="client/ShipMovementCallbacks.js"></script>


    <script defer src="client/model/hexagon/Cube.js"></script>
    <script defer src="client/model/hexagon/Offset.js"></script>
    <script defer src="client/gameRules/SimultaneousMovementRule.js"></script>

    <script defer src="client/UI/botPanel.js"></script>
    <script defer src="client/UI/replayUI.js"></script>
    <script defer src="client/gamedata.js"></script>
    <script defer src="client/windowevents.js"></script>
    <script defer src="client/mathlib.js"></script>
    <script defer src="client/lib/seedRandom.js"></script>
    <script defer src="client/ajaxInterface.js"></script>
    <script defer src="client/ew.js"></script>
    <script defer src="client/weaponManager.js"></script>
    <script defer src="client/damage.js"></script>
    <script defer src="client/combatLog.js"></script>
    <script defer src="client/declarations.js"></script>
	<script defer src="client/player.js"></script>
    <script defer src="client/ships.js"></script>
    <script defer src="client/movement.js"></script>
    <script defer src="client/criticals.js"></script>
    <script defer src="client/systems.js"></script>
    <script defer src="client/battleDamage.js"></script>
    <script defer src="client/savedFleets.js"></script>
	<script defer src="client/power.js"></script>
    <script defer src="client/UI/shipMovement.js"></script>
    <script defer src="client/UI/infowindow.js"></script>
    <script defer src="client/UI/fleetList.js"></script>
	<script defer src="client/UI/gameInfo.js"></script>
	<script defer src="client/UI/confirm.js"></script>
	<script defer src="client/model/ship.js"></script>
    <script defer src="client/model/shipSystem.js"></script>
    <script defer src="client/model/systemFactory.js"></script>
    <script defer src="client/model/system/baseSystems.js"></script>
    <script defer src="client/model/system/defensive.js"></script>
    <script defer src="client/model/weapon/ammo.js"></script>
    <script defer src="client/model/weapon/ammoWeapons.js"></script>    
    <script defer src="client/model/weapon/laser.js"></script>
    <script defer src="client/model/weapon/particle.js"></script>
    <script defer src="client/model/weapon/matter.js"></script>
    <script defer src="client/model/weapon/plasma.js"></script>
    <script defer src="client/model/weapon/special.js"></script>
    <script defer src="client/model/weapon/supportWeapons.js"></script>    
    <script defer src="client/model/weapon/torpedo.js"></script>
    <script defer src="client/model/weapon/pulse.js"></script>
    <script defer src="client/model/weapon/electromagnetic.js"></script>
    <script defer src="client/model/weapon/aoe.js"></script>
    <script defer src="client/model/weapon/molecular.js"></script>
    <script defer src="client/model/weapon/antimatter.js"></script>
    <!--<script defer src="client/model/weapon/dualWeapon.js"></script>-->
    <!--<script defer src="client/model/weapon/duoWeapon.js"></script>-->
    <script defer src="client/model/weapon/gravitic.js"></script>
    <script defer src="client/model/weapon/missile.js"></script>
    <script defer src="client/model/weapon/ion.js"></script>
    <script defer src="client/model/weapon/customs.js"></script>
    <script defer src="client/model/weapon/customSW.js"></script>
    <script defer src="client/model/weapon/customNexus.js"></script>
	<script defer src="client/model/weapon/customEscalation.js"></script>
    <script defer src="client/model/weapon/customDevelopment.js"></script>
	<script defer src="client/model/weapon/customBSG.js"></script>
	<script defer src="client/model/weapon/customTrek.js"></script>
    <script defer src="client/model/weapon/customCW.js"></script>
    <?php else: ?>
    <script defer src="<?php echo AssetLoader::getAssetUrl('client/game.legacy.bundle.js'); ?>"></script>
    <?php endif; ?>
</head>


<body>


<div id="topcontainer">
    <div id="phaseheader" class="roundedright">
        <span class="turn value"></span><span class="phase value"></span><span class="activeship value"></span><span class="waiting value"></span><span class="finished value">GAME OVER</span><span class="notlogged value">NOT LOGGED IN</span>
        <table class="uitable">
            <tr>
            <td class="committurn" style="display:none"><div class="ok" ></div></td>
            <td class="cancelturn" style="display:none"><div class="cancel" ></div></td>
            </tr>
        </table>
    </div>
</div>

<div id="backDiv" style="">
</div>

<div id="iniGui" style="">
</div>

<div id="infowindow">
<h2></h2>
<div class="container"></div>
</div>

<div id="systemtemplatecontainer" style="display:none;">
    
	<div class="structure system">
        <div class="name"><span class="namevalue">STRUCTURE</span></div>
        <div class="systemcontainer">

            <div class="health systembarcontainer">
                <div class="healthbar bar" style="width:100%;"></div>
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
                <div class="healthbar bar" style="width:100%;"></div>
            </div>
            <div class="critical systembarcontainer">
                <div class="valuecontainer"><span class="criticalvalue value">CRITICAL<span></div>
            </div>
            
        </div>
    </div>
    
    
    <div class="iconduo">
        <span class="efficiency value"></span>
            <div class="duoIconmask"></div>
<!--        <div class="iconUI">
            <div class="button holdfire"></div>
        </div>-->
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
            <!--
            <div class="systembarcontainer">
                <div class="efficiencybar bar" style="width:30px;"></div>
                <div class="valuecontainer"><span class="efficiencyvalue value">5/3<span></div>
            </div>
            -->
            
            
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

<div id="shipWindowsReact"></div>
<div id="systemInfoReact"></div>
<div id="weaponList"></div>
<div id="showEwButtons"></div>
<div id="surrender"></div>
<div id="fullScreen"></div>
<div id="playerSettings"></div>
<div id="shipThrust"></div>
<div id="pagecontainer" oncontextmenu="return false;">
    <div id="background" <?php if ($serverdata && !empty($serverdata->background)) echo 'style="background-image:url(img/maps/' . $serverdata->background . ')"'; ?>></div>
    <div id="webgl" class="tacticalcanvas"></div>


    <div id="shipNameContainer">
        <div class="namecontainer" style="border-bottom:1px solid white;margin-bottom:3px;"></div>

		<div class="fire" style=";margin:3px 0px 3px 0px; padding:2px 0px 0px 0px;border-top:1px solid white;color:#b34119;"><span>TARGETING</span></div>
		<div class="fire targeting"></div>
    </div>
	
    <div id="weaponTargetingContainer">
        <div class="infolist">
        
        </div>
    </div>
    
<!--        <div id="globalhelp" class="ingamehelppanel">
        <?php
        	$messagelocation='hex.php';
        	$ingame=true;
        	include('helper.php');
        ?>
        </div>-->
    
    
    
    <div id="shipMovementUI">
        <div id="move" class="movement-icon" data-movement-type="Move">
            <div class="centercontainer">
                <span class="speedvalue value centercontent">00</span>
            </div>
            <canvas id="movecanvas" width="50" height="50"></canvas>
        </div>
        
        <div id="turnright" class="movement-icon" data-movement-type="Turn Right">
            <canvas id="turnrightcanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="turnleft" class="movement-icon" data-movement-type="Turn Left">
            <canvas id="turnleftcanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="turnIntoPivotLeft" class="movement-icon" data-movement-type="Turn Into Pivot">
            <canvas id="turnIntoPivotLeftCanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="turnIntoPivotRight" class="movement-icon" data-movement-type="Turn Into Pivot">
            <canvas id="turnIntoPivotRightCanvas" width="40" height="40"></canvas>
        </div>

        <div id="graviticTurnRight" class="movement-icon" data-movement-type="Gravitic Turn Right">
            <canvas id="graviticTurnRightCanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="graviticTurnLeft" class="movement-icon" data-movement-type="Gravitic Turn Left">
            <canvas id="graviticTurnLeftCanvas" width="40" height="40"></canvas>
        </div>        

        <div id="slipright" class="movement-icon" data-movement-type="Slip Right">
            <canvas id="sliprightcanvas" width="30" height="30"></canvas>
        </div>
        
        <div id="slipleft" class="movement-icon" data-movement-type="Slip Left">
            <canvas id="slipleftcanvas" width="30" height="30"></canvas>
        </div>
        
        <div id="pivotright" class="movement-icon" data-movement-type="Pivot Right">
            <canvas id="pivotrightcanvas" width="40" height="40"></canvas>
        </div>


		<div id="pivotRightActive" class="movement-icon" data-movement-type="Stop Pivoting">
		    <canvas id="pivotRightActiveCanvas" width="40" height="40"></canvas>
		</div>
        
        <div id="pivotleft" class="movement-icon" data-movement-type="Pivot Left">
            <canvas id="pivotleftcanvas" width="40" height="40"></canvas>
        </div>

		<div id="pivotLeftActive" class="movement-icon" data-movement-type="Stop Pivoting">
		    <canvas id="pivotLeftActiveCanvas" width="40" height="40"></canvas>
		</div>

        <div id="rotateleft" class="movement-icon" data-movement-type="Port Rotation">
            <canvas id="rotateleftcanvas" width="40" height="40"></canvas>
        </div>

        <div id="rotateright" class="movement-icon" data-movement-type="Starboard Rotation">
            <canvas id="rotaterightcanvas" width="40" height="40"></canvas>
        </div>
        
        
        <!-- 40px bitmaps, not 16: on a coarse pointer drawShipMovementUI centres the
             16px glyph inside a 34px transparent hit box, so the backing store has to
             cover the box, not just the glyph. The parent div is overflow:hidden and
             sized to the box, so the surplus is clipped and mouse layout is unchanged. -->
        <div id="accelerate" class="movement-icon" data-movement-type="Accelerate">
            <canvas id="acceleratecanvas" width="40" height="40"></canvas>
        </div>

        <div id="deaccelerate" class="movement-icon" data-movement-type="Decelerate">
            <canvas id="deacceleratecanvas" width="40" height="40"></canvas>
        </div>
        
        <div id="morejink" class="movement-icon" data-movement-type="Increase Jinking">
            <canvas id="morejinkcanvas" width="16" height="16"></canvas>
        </div>
        
        <div id="lessjink" class="movement-icon" data-movement-type="Decrease Jinking">
            <canvas id="lessjinkcanvas" width="16" height="16"></canvas>
        </div>
        
		<div id="roll" class="movement-icon" data-movement-type="Roll">
            <canvas id="rollcanvas" width="40" height="40"></canvas>
        </div>

		<div id="rollActive" class="movement-icon" data-movement-type="Roll">
            <canvas id="rollActivecanvas" width="40" height="40"></canvas>
        </div>

		<div id="emergencyroll" class="movement-icon" data-movement-type="Emergency Roll">
		    <canvas id="emergencyrollcanvas" width="40" height="40"></canvas>
		</div>        	
		
        <div id="halfphase" class="movement-icon" data-movement-type="Half-Phase">
            <canvas id="halfphasecanvas" width="50" height="50"></canvas>
        </div>		
        
        <div id="jink" class="movement-icon" data-movement-type="Jinking">
			<div class="centercontainer">
                <span class="jinkvalue value centercontent">0</span>
            </div>
            <canvas id="jinkcanvas" width="40" height="40"></canvas>
        </div>

        <div id="contraction" class="movement-icon" data-movement-type="Contraction">
			<div class="centercontainer">
                <span class="contractionvalue value centercontent">0</span>
            </div>
            <canvas id="contractioncanvas" width="40" height="40"></canvas>
        </div>        

        <div id="morecontraction" class="movement-icon" data-movement-type="Increase Contraction">
            <canvas id="morecontractioncanvas" width="16" height="16"></canvas>
        </div>
        
        <div id="lesscontraction" class="movement-icon" data-movement-type="Decrease Contraction">
            <canvas id="lesscontractioncanvas" width="16" height="16"></canvas>
        </div>
        
        <div id="cancel" class="movement-icon" data-movement-type="Cancel Last Move">
            <canvas id="cancelcanvas" width="30" height="30"></canvas>
        </div>
        
        <div id="detach" class="movement-icon" data-movement-type="Detach" style="filter: hue-rotate(200deg) scaleX(-1);">
            <canvas id="detachcanvas" width="50" height="50"></canvas>
        </div>
        
    </div>
</div>
<!--
<div class="scrollelement left" style="left:0px;top:0px;"></div>
<div class="scrollelement side" style="right:0px;top:0px;"></div>
<div class="scrollelement side" style="left:0px;top:0px;"></div>
<div class="scrollelement side" style="left:0px;bottom:0px;"></div>
-->

<div id="templatecontainer">

    <div class="hexship">
        
        <canvas id="shipcanvas" width="0" height="0" class="hexshipcanvas"></canvas>
      
    
    </div>
    
    <div class="ewentry deletable"><span class="valueheader"></span><span class="value"></span><div class="button1"></div><div class="button2" ></div></div>

</div>
<div id="logcontainer">    
    <div id="logUI">
        <div id="logTab" data-select="#log" class="logUiEntry selected">
            <span>LOG</span>
        </div>
        <div id="infoTab" data-select="#gameinfo" class="logUiEntry">
            <span>INFO</span>
        </div>
        <div id="declarationsTab" data-select="#declarations" class="logUiEntry" style = "overflow-y: auto;"> <!-- fire and EW declarations review -->
            <span>DECLARATIONS</span>
        </div>
        <div id="fleetSaveTab" data-select="#fleetsave" class="logUiEntry"> <!-- save this game's fleet for reuse -->
            <span>SAVE FLEET</span>
        </div>
        <div id="chatTab" data-select="#chat" class="logUiEntry">
            <span>GAME CHAT</span>
        </div>
        <div id="globalChatTab" data-select="#globalchat" class="logUiEntry">
            <span>CHAT</span>
        </div>
        <div id="expandBotPanel">
            <span>Click!</span>
        </div>
<!--        <div id="settingsTab" data-select="#settings" class="logUiEntry">
            <span>SETTINGS</span>
        </div>-->
    </div>

    <div id="log" class="logPanelEntry">
    <?php include("combatLog.php"); ?>
    </div>
    
    <div id="gameinfo" class="logPanelEntry" style="display:none;">
        <div id="gameInfoButtons">
            <input type="button" value="Fiery Void FAQ" onclick="window.open('faq.php', '_blank');">
            <input type="button" value="Ammo, Options & Enhancements" onclick="window.open('ammo-options-enhancements.php', '_blank');">            
            <input type="button" value="Factions & Tiers Info" onclick="window.open('factions-tiers.php', '_blank');">
        </div>
    </div>
    <div id="declarations" class="logPanelEntry" style="display:none;"> <!-- fire and EW declarations review -->
	<?php
	    include("declarations.php");
	?>
    </div>

    <!-- Save Current Fleet (PREBATTLE_DAMAGE_PLAN.md §7.3). Colours come from
         styles/tokens.css only - no new :root block. -->
    <div id="fleetsave" class="logPanelEntry" style="display:none;">
        <div id="fleetSavePanel">
            <p>
                Saves your surviving ships, their enhancements, ammo, and their current battle
                damage and critical effects as a reusable fleet list. Load it from the game
                lobby to continue a campaign.
            </p>
            <p id="fleetSaveSummary"></p>
            <input type="button" id="fleetSaveButton" value="Save Current Fleet">
        </div>
    </div>

    <?php
    // No $chattitle on either of these: the chat already sits behind a labelled tab
    // (GAME CHAT / CHAT), so chat.php's head bar would only repeat it and cost a line
    // of the log panel. $chatcompact trims the composer for the same reason — see
    // .fv-chat-compact in styles/chat.css.
    ?>
    <div id="chat" class="logPanelEntry" style="display:none;">
        <?php
            $chatgameid = $gameid;
            $chatelement = "#chat";
            $chatcompact = true;
            include("chat.php")
        ?>
    </div>

    <div id="globalchat" class="logPanelEntry" style="display:none;">
        <?php
            $chatgameid = 0;
            $chatelement = "#globalchat";
            $chatcompact = true;
            include("chat.php")
        ?>
    </div>
 
<!--    <div id="settings" class="logPanelEntry" style="display:none;">
        <div id="helphide" class="helphide">
        <span title="Show or Hide Vir to help you">
        <img id="helphideimg" src="img/greyvir.jpg" height="30" width="30">	
        HELP
        </span>
        </div>
        <span title="Disable or Enable the extra Commit check to end a turn">
        <div id="autocommit" class="autocommit">
        <img id="autocommitimg" src="img/ok.png" height="30" width="30">	
        <span id="autocommittext" class="autocommittext">COMMIT</span>
        </span>
        </div>
    </div>-->
        
    <div class="fleetlistentry">
        <div class="fleetheader">
        </div>
        <div class="fleetlist">
                <div class="fleetlistline">
                </div>
    </div>
    
        
</div>

<div id="global-blocking-overlay" class="blocking-overlay" style="display:none;">
    <span>
        TRANSMITTING ORDERS...<br>
        <span class="blocking-warning">Do not close window</span>
    </span>
</div>
<!--
<div id="global-loading-overlay" class="blocking-overlay" style="display:none;">
    <span>
        LOADING...
    </span>
</div>-->

</body>

</html>
<!-- 
PHP Execution Diagnostics:
Total Time: <?php echo isset($fv_start_timer) ? round((microtime(true) - $fv_start_timer)*1000, 2) : 0; ?> ms
Manager::getTacGamedataJSON Time: <?php echo isset($time_getTacGamedataJSON) ? round($time_getTacGamedataJSON*1000, 2) : 0; ?> ms
  |- getTacGamedata (DB Wrapper): <?php echo isset($GLOBALS['dbg_getTacGamedata']) ? round($GLOBALS['dbg_getTacGamedata']*1000, 2) : 0; ?> ms
       |- getTacGame: <?php echo isset($GLOBALS['dbg_getTacGame']) ? round($GLOBALS['dbg_getTacGame']*1000, 2) : 0; ?> ms
       |- getSlotsInGame: <?php echo isset($GLOBALS['dbg_getSlotsInGame']) ? round($GLOBALS['dbg_getSlotsInGame']*1000, 2) : 0; ?> ms
       |- getTacShips: <?php echo isset($GLOBALS['dbg_getTacShips']) ? round($GLOBALS['dbg_getTacShips']*1000, 2) : 0; ?> ms
       |- onConstructed: <?php echo isset($GLOBALS['dbg_onConstructed']) ? round($GLOBALS['dbg_onConstructed']*1000, 2) : 0; ?> ms
  |- stripForJson: <?php echo isset($GLOBALS['dbg_stripForJson']) ? round($GLOBALS['dbg_stripForJson']*1000, 2) : 0; ?> ms
  |- json_encode: <?php echo isset($GLOBALS['dbg_json_encode']) ? round($GLOBALS['dbg_json_encode']*1000, 2) : 0; ?> ms
ShipLoader::getShipsByClass Time: <?php echo isset($time_getShipsByClass) ? round($time_getShipsByClass*1000, 2) : 0; ?> ms
-->

