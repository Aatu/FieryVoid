<?php
    include_once 'global.php';
    session_write_close(); // Prevent session locking for concurrent loads
	
	if (!isset($_SESSION["user"]) || $_SESSION["user"] == false){
		header('Location: index.php');
        exit;
	}
    
    	if (isset($_GET["leave"]) && isset($_GET["gameid"])){
		Manager::leaveLobbySlot($_SESSION["user"], $_GET["gameid"]);
		header('Location: games.php');
        exit;
	}
	
	
	$gameid = null;
	
	if (isset($_GET["gameid"])){
		$gameid = $_GET["gameid"];
	}
	
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

    if (!is_object($gamelobbydata) || $gamelobbydata->status != "LOBBY") {
        header('Location: games.php');
        exit;
    }
  
    // $gamelobbydataJSON is already set/cached, no need to encode again
	
	// Getting all ships in one go causes memory overload on the server.
	// Get the factions first. When a faction is opened to buy ships,
	// go bother the server for the ships of that faction only.
	
	$factions = json_encode(Manager::getAllFactions(), JSON_NUMERIC_CHECK);
	
	$ships = [];
	
	
?>

<!DOCTYPE HTML>
<html>
	<head>
		<title>Fiery Void - Gamelobby</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link href="styles/base.css" rel="stylesheet" type="text/css">
		<link href="styles/lobby.css" rel="stylesheet" type="text/css">
		<link href="styles/confirm.css" rel="stylesheet" type="text/css">
        <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">          
        <link href="styles/shipwindow.css" rel="stylesheet" type="text/css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script src="client/lib/jquery-ui-1.8.15.custom.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">        
		
		<!-- replaced by php include below
        <script src="static/ships.js"></script>
		-->
<?php		
	//include 'static/ships.php'; //Changed how staticships are loaded to help with HTTP Protocol errors - DK Dec 2025

?>
		
<!--		<script src="client/helper.js"></script>-->
        <script src="client/gamelobby.js"></script>
		<script src="client/ajaxInterface.js"></script>
		<script src="client/lobbyEnhancements.js"></script>        
		<script src="client/player.js"></script>
        <script src="client/ships.js"></script>
        <script src="client/criticals.js"></script>
        <script src="client/damage.js"></script>
        <script src="client/systems.js"></script>
        <script src="client/power.js"></script>
        <script src="client/movement.js"></script>
        <script src="client/mathlib.js"></script>
		<script src="client/UI/confirm.js"></script>
        <script src="client/UI/shipwindow.js"></script>
        <script src="client/UI/fleetList.js"></script>
        <script src="client/UI/gameInfo.js"></script>
        <script src="client/UI/flightwindow.js"></script>
        <script src="client/UI/systemInfo.js"></script>
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
        <!--<script src="client/model/weapon/dualWeapon.js"></script>-->
        <!--<script src="client/model/weapon/duoWeapon.js"></script>-->
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
		<script>
			
            window.weaponManager = 
            {
                
                mouseoverTimer: null,
                mouseOutTimer: null,
                mouseoverSystem: null,

                onWeaponMouseover: function(e){

                    if (weaponManager.mouseOutTimer != null){
                        clearTimeout(weaponManager.mouseOutTimer); 
                        weaponManager.mouseOutTimer = null;
                    }

                    if (weaponManager.mouseoverTimer != null)
                        return;

                    weaponManager.mouseoverSystem = $(this);
                    weaponManager.mouseoverTimer = setTimeout(weaponManager.doWeaponMouseOver, 150);
                },

                onWeaponMouseOut: function(e){
                    if (weaponManager.mouseoverTimer != null){
                        clearTimeout(weaponManager.mouseoverTimer); 
                        weaponManager.mouseoverTimer = null;
                    }

                    weaponManager.mouseOutTimer = setTimeout(weaponManager.doWeaponMouseout, 50);
                },

                doWeaponMouseOver: function(e){
                    if (weaponManager.mouseoverTimer != null){
                        clearTimeout(weaponManager.mouseoverTimer); 
                        weaponManager.mouseoverTimer = null;
                    }

                    systemInfo.hideSystemInfo();

                    var t = weaponManager.mouseoverSystem;

                    var id = t.data("shipid");

                    //Added button to view actual details of purchased ships, so need ot make sure right ship is called for correct tooltip info - DK 30.3.25 
                    if(gamedata.fleetWindowOpen){
                        var ship = gamedata.getFleetShipById(id);
                    }else{
                        var ship = gamedata.getShip(id);
                    }

                    var system = null;

                    if (t.hasClass("fightersystem")){
                        system = shipManager.systems.getFighterSystem(ship, t.data("fighterid"), t.data("id"));
                    }else{
                        system = shipManager.systems.getSystem(ship, t.data("id"));
                    }

                    systemInfo.showSystemInfo(t, system, ship);
                },

                doWeaponMouseout: function(){
                    if (weaponManager.mouseOutTimer != null){
                        clearTimeout(weaponManager.mouseOutTimer); 
                        weaponManager.mouseOutTimer = null;
                    }

                    systemInfo.hideSystemInfo();

                    weaponManager.mouseoverSystem = null;
                },
                hasFiringOrder: function(){return false},
                isLoaded: function(){return true},
                isSelectedWeapon: function(){return false},
                getWeaponCurrentLoading: function(weapon)
                {
                    return weapon.normalload;
                },
            }
            
            window.shipManager.movement.isRolled = function(ship)
            {
                return false;
            }
            
            
            window.shipWindowManager.addEW = function(){};            

        
        jQuery(function($){            
            var lobbyData = <?php print($gamelobbydataJSON); ?>;
            gamedata.parseServerData(lobbyData);
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

            // ✅ Unified filter logic for factions based on Tier and Custom
            window.updateTierFilter = function() {   // ✅ Now global
                const selectedTiers = $('.tier-filter:checked').map(function () {
                    return $(this).data('tier');
                }).get();

                const showCustom = $('#toggleCustom').is(':checked');
                const customMode = $('#customSelect').val();

                $('.faction').each(function () {
                    const tier = $(this).data('tier');
                    const isCustom = $(this).data('custom') === true || $(this).data('custom') === "true";

                    let isVisible = false;

                    if (selectedTiers.includes(tier)) {
                        if (showCustom) {
                            if (customMode === 'showOnlyCustom') {
                                isVisible = isCustom;
                            } else {
                                isVisible = true; // show both custom and non-custom
                            }
                        } else {
                            isVisible = !isCustom; // hide custom if toggle unchecked
                        }
                    }

                    $(this).toggle(isVisible);
                });

                // Group headers visibility stays unchanged
                $('.factiongroup-header').each(function () {
                    let header = $(this);
                    let hasVisibleFaction = false;
                    let next = header.next();
                    while (next.length && !next.hasClass('factiongroup-header')) {
                        if (next.hasClass('faction') && next.is(':visible')) {
                            hasVisibleFaction = true;
                            break;
                        }
                        next = next.next();
                    }
                    header.toggle(hasVisibleFaction);
                });
            }

            // ✅ Listen to Tier and Custom Faction checkboxes
            $('.tier-filter').on('change', updateTierFilter);

            // Combined listener for toggle and dropdown
            $('#toggleCustom, #customSelect').on('change', function () {
                var showCustom = $('#toggleCustom').is(':checked');
                // var mode = $('#customSelect').val(); // Mode no longer needed for specific warnings

                if (showCustom) {
                    $('#customDropdown').show();
                    
                    var description = lobbyData.description || "";
                    // Check if explicit permission is missing (i.e. it does NOT say "Allowed")
                    var allowed = description.match(/CUSTOM FACTIONS \/ UNITS:\s*Allowed/i);

                    if (!allowed && !customWarningShown && gamedata.rules && gamedata.rules.fleetTest !== 1) {
                         window.confirm.warning("Custom Factions and/or Units not allowed in this match. <br>Please check Scenario Description");
                         customWarningShown = true;
                    }
                } else {
                    $('#customDropdown').hide();
                }
                updateTierFilter();
                gamedata.applyCustomShipFilter();
            });

            $('#customSelect').on('change', function () {
                updateTierFilter();
                gamedata.applyCustomShipFilter();
            });


            // ✅ Initial call
            updateTierFilter();


            // ✅ Select All / None Tier checkboxes + toggle customs
            $('.tier-select-all').on('click', function () {
                $('.tier-filter').prop('checked', true);
                $('#toggleCustom').prop('checked', true).trigger('change');
                $('#customSelect').val('showCustom'); // ✅ reset custom dropdown to Show Customs                
                $('#isdFilter').val('');
                $('#nameFilter').val('');
                gamedata.applyCustomShipFilter();
                updateTierFilter();
            });

            $('.tier-select-none').on('click', function () {
                $('.tier-filter').prop('checked', false);
                $('#toggleCustom').prop('checked', false).trigger('change');
                $('#isdFilter').val('');
                $('#nameFilter').val('');
                gamedata.applyCustomShipFilter();
                updateTierFilter();
            });

            // Sanitize input on each keystroke, but don't apply filter yet
            $("#isdFilter").on("input", function () {
                let val = $(this).val().replace(/\D/g, ''); // remove non-digits
                if (val.length > 4) val = val.slice(0, 4); // limit to 4 digits
                $(this).val(val);
            });

            // Apply filter only when Enter key is pressed
            $("#isdFilter").on("keypress", function (e) {
                if (e.which === 13) {
                    gamedata.applyCustomShipFilter();
                }
            });

            // Apply filter only when Enter key is pressed (for consistency)
            $("#nameFilter").on("keypress", function (e) {
                if (e.which === 13) {
                    gamedata.applyCustomShipFilter();
                }
            });

            // Reset filters when clicking "Reset Filters"
            $(".resetFilters").on("click", function () {
                $("#isdFilter").val('');
                $("#nameFilter").val('');
                gamedata.applyCustomShipFilter();
            });

            // Optional: initialize custom ship visibility
            $("#toggleCustom").trigger("change");
        });


		</script>
	</head>
	<body style="background-image:url(img/maps/<?php print($gamelobbydata->background); ?>)">

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
		<div class="panel large lobby">
            <?php 
                $isFleetTest = false;
                // Using isset/property check instead of hasRuleName for JSON object
                if (isset($gamelobbydata->rules->fleetTest)) {
                    $isFleetTest = true;
                }
            ?>
            <div class="">
                <!--<span class="panelheader">GAME NAME: </span>-->
                <span class="panelsubheader game-name"> <?php print($isFleetTest ? '<span class="fleet-test-text">Fleet Test</span>' : $gamelobbydata->name); ?></span>
            </div>

    <div class="lobby-split-container">
        <!-- Left Column: Scenario Description -->
        <div class="lobby-description-column">


<?php
//define options list
$optionsUsed = '';

    if ($gamelobbydata->gamespace == '-1x-1'){ //open map
        $optionsUsed .= 'Open Map';
    }else{ //fixed map
        $optionsUsed .= 'Map ' . $gamelobbydata->gamespace;
    }

    $ladder = false;
    $simMv = false;
    $desperate = false;
    $friendlyFire = false;    
    $asteroids = false;
    $moons = false;
    $initiativeCategories = null;
    $desperateTeams = null;
    $asteroidsNo = 0;
    $moonData = [];


    if (isset($gamelobbydata->rules)) {

        if (isset($gamelobbydata->rules->ladder)) {
            $ladder = true;  
        }        

        if (isset($gamelobbydata->rules->initiativeCategories)) {
            $simMv = true;
            $initiativeCategories = $gamelobbydata->rules->initiativeCategories;
        }

        if (isset($gamelobbydata->rules->desperate)) {
            $desperate = true;
            $desperateTeams = $gamelobbydata->rules->desperate;     
        }

        if (isset($gamelobbydata->rules->friendlyFire)) {
            $friendlyFire = true;  
        }        

        if (isset($gamelobbydata->rules->asteroids)) {
            $asteroids = true;
            $asteroidsNo = $gamelobbydata->rules->asteroids;     
        }  

        if (isset($gamelobbydata->rules->moons)) {
            $moons = true;
            $rulesMoons = $gamelobbydata->rules->moons;
            // Convert to array if object
            if (is_object($rulesMoons)) {
                $moonData = (array)$rulesMoons;
            } else if (is_array($rulesMoons)) {
                $moonData = $rulesMoons;
            }
        }       
    }

    if ($ladder == true) { // Ladder game
        $optionsUsed .= ', Ladder Game';
    } else { 
        $optionsUsed .= '';
    }

    if ($simMv == true) { // simultaneous movement
        $optionsUsed .= ', Simultaneous Movement';
        if ($initiativeCategories !== null) {
            $optionsUsed .= ' (Brackets: ' . $initiativeCategories . ')';
        }
    } else { // standard movement
        $optionsUsed .= ', Standard Movement';
    }

    if ($desperate == true) { // Desperate rules in play
        $teamDisplay = null;
    
        if($desperateTeams == 1) {
                $teamDisplay = "Team 1";
        }else if($desperateTeams == 2){    
            $teamDisplay = "Team 2";
        }else{    
            $teamDisplay = "Both Teams";
        }
        $optionsUsed .= ', Desperate Rules ('. $teamDisplay . ')';
    } else { // standard rules
        $optionsUsed .= '';
    }

    if ($friendlyFire == true) { // Desperate rules in play
        $optionsUsed .= ', Friendly Fire';
    } else { // standard rules
        $optionsUsed .= '';
    }


    if ($asteroids == true) { // Asteroid terrain rules in play
        $optionsUsed .= ', Asteroids ('. $asteroidsNo . ')';
    }
    if ($moons == true) { // Moon terrain rules in play

        $small  = $moonData['small']  ?? 0;
        $medium = $moonData['medium'] ?? 0;
        $large  = $moonData['large']  ?? 0;

            function formatMoonCount($count, $type) {
                if ($count <= 0) return null;
                return $count . ' ' . $type;
            }

            // Build each part with pluralization
        $moonParts = array_filter([
            formatMoonCount($small,  'Small'),
            formatMoonCount($medium, 'Medium'),
            formatMoonCount($large,  'Large'),
        ]);

        $optionsUsed .= empty($moonParts)
            ? ', Moons (None)'
            : ', Moons (' . implode(', ', $moonParts) . ')';
    }

    if ($asteroids == false && $moons == false) { 
        $optionsUsed .= ', No Terrain';
    }

?>
<?php if(!$isFleetTest): ?>

<?php endif; ?>

<div class="rules-info-container <?php if($isFleetTest) echo 'fleet-test'; ?>">
<div class="lobbyheader rules-info-header">RULES & INFO</div>

<a href="./factions-tiers.php" target="_blank" class="lobby-link-blue">Fiery Void: Factions & Tiers</a> 
<span class="lobby-desc-text"> - Overview of Fiery Void factions and their approximate strengths.</span>
<br>
<a href="./ammo-options-enhancements.php" target="_blank" rel="noopener noreferrer" class="lobby-link-blue">Ammo, Options & Enhancements</a> 
<span class="lobby-desc-text"> - Details of all the extras available to Fiery Void units e.g. Missiles.</span>
<br>

<a href="https://old.wheelofnames.com/fx3-uje" target="_blank" class="lobby-link-blue">Tier 1</a> 
<strong class="lobby-separator-strong">|</strong> 
<a href="https://old.wheelofnames.com/rmq-7ds" target="_blank" class="lobby-link-blue">Tier 2</a>
<strong class="lobby-separator-strong">|</strong> 
<a href="https://old.wheelofnames.com/sgd-5zq" target="_blank" class="lobby-link-blue">Tier 3</a>
<span class="lobby-dash-span">-</span>
<span class="lobby-desc-text">Random Faction Wheels</span> 
</div> 




        <?php if (!$isFleetTest): ?>

            <div class="lobbyheader rules-info-header">SCENARIO DESCRIPTION</div>

            <div class="scenario-description">
            <?php
            $desc = $gamelobbydata->description;

            // Replace <br> tags with newlines to normalize input
            $desc = str_replace(['<br>', '<br/>', '<br />'], "\n", $desc);

            // Remove the header line if it exists
            $desc = preg_replace('/^\*{3}.*\*{3}\s*/m', '', $desc);

            // Split into lines
            $lines = preg_split("/\r\n|\n|\r/", trim($desc));

            $inAdditionalInfo = false;

            foreach ($lines as $line) {
                // Trim whitespace for safety
                $line = trim($line);
                if ($line === '') continue; // skip empty lines

                // Try to split on the first colon
                $pos = strpos($line, ':');
                if ($pos !== false) {
                    $label = trim(substr($line, 0, $pos));
                    $value = trim(substr($line, $pos + 1));

                    $isAdditionalInfo = (strcasecmp($label, 'ADDITIONAL INFORMATION') === 0 || strcasecmp($label, 'ADDITIONAL INFO') === 0);

                    if ($isAdditionalInfo) {
                        $inAdditionalInfo = true;
                        if ($value === '') {
                            $value = 'None';
                        }
                        
                        echo '<span class="scenariolabel">' . htmlspecialchars($label) . ':</span><br>' .
                             '<span class="scenariovalue">' . htmlspecialchars($value) . '</span><br>';
                    } else {
                        $inAdditionalInfo = false;
                        // Bold the label regardless of case (you can add uppercase check if you want)
                        echo '<span class="scenariolabel">' . htmlspecialchars($label) . ':</span>&nbsp; ' .
                             '<span class="scenariovalue">' . htmlspecialchars($value) . '</span><br>';
                    }
                } else {
                    // Just print line if no colon found
                    if ($inAdditionalInfo) {
                         echo '<span class="scenariovalue">' . htmlspecialchars($line) . '</span><br>';
                    } else {
                         echo htmlspecialchars($line) . '<br>';
                    }
                }
            }
            ?>
            </div>

            <?php if(!$isFleetTest): ?>
            <div><span class="scenariolabel">OPTIONS SELECTED: </span> <span class="scenariovalue"><?php print($optionsUsed); ?> </span></div>
            <?php endif; ?>

            <?php endif; ?>
        </div>

        <!-- Right Column: Map Preview -->
        <?php if(!$isFleetTest): ?>
        <div class="lobby-map-column">
            <!--<div class="createsubheader deployment-header-style"><span>DEPLOYMENT ZONE PREVIEW:</span></div>-->
            <div id="mapPreviewContainer" class="mapPreviewContainer">
                <canvas id="mapPreview" width="400" height="300" class="mapPreviewContainerBox"></canvas>
            </div>
        </div>
        <?php endif; ?>
        <div class="lobby-leave-container">
            <span class="btn btn-secondary-lobby leave lobby-leave-button">Leave Game</span>
        </div>
    </div>
    

</div>

<?php if(!$isFleetTest): ?>
<div class="panel large lobby lobby-teams-wrapper">
    <div class="lobby-teams-container">
        
        <div class="createsubheader team-header-one">TEAM 1:</div>
        <div id="team1" class="subpanel slotcontainer">
        </div>
        
        <div class="createsubheader team-header-two">TEAM 2:</div>
        <div id="team2" class="subpanel slotcontainer">
        </div>
        
    </div>
</div>
<?php endif; ?>

<div class="panel large lobby buy buy-panel-container">


    <div class="buy-header-flex">
        <div>
            <span class="panelheader buy-header-title-style">PURCHASE YOUR FLEET</span>
        </div> 
                <div>
                    <span class="remaining-points-container">
                        <span class="panelsmall points-bracket-style">(</span>
                        <span class="panelsmall remaining">0</span><span class="panelsmall remaining-points-units">pts left</span>
                        <span class="panelsmall">)</span>
                    </span>
                </div>             
    </div>

            <div class="filter-container-style">
                <div>
                    <span class="clickable tier-select-all all-filters-link">All Filters</span>
                    <span class="filter-pipe-separator">|</span>          
                    <span class="clickable tier-select-none no-filters-link">No Filters</span>
                    <span class="filter-pipe-separator">|</span>  

                    <label class="name-filter-label-style">
                        <span class="filter-by-name-text">Filter by Ship Name:</span>
                        <input type="text" id="nameFilter" value="" class="name-input-style">
                    </label>
                    <!--<span class="filter-pipe-separator">|</span>-->

                    <label class="isd-filter-label-style">
                        <span class="filter-by-isd-text">Filter by ISD:</span>
                        <input type="text" id="isdFilter" value="" class="isd-input-style">
                        <span class="clickable resetFilters reset-filters-link-style">Reset Name/ISD</span>
                    </label>
                </div>
                <div>
                    <!--<span class="remaining-points-container">
                        <span class="panelsmall points-bracket-style">(</span>
                        <span class="panelsmall remaining">0</span><span class="panelsmall remaining-points-units">pts left</span>
                        <span class="panelsmall">) </span>
                    </span>-->
                    <span class="panelsubheader current">0</span>
                    <span class="panelsubheader">/</span>
                    <span class="panelsubheader max">0</span><span class="panelsubheader max-points-units">pts</span>
                </div>
            </div>


    <div class="tier-filters-row">
        <label class="tier-label-style">Tier 1 <input type="checkbox" class="tier-filter" data-tier="Tier 1" checked></label>
        <label class="tier-label-style">Tier 2 <input type="checkbox" class="tier-filter" data-tier="Tier 2" checked></label>
        <label class="tier-label-style">Tier 3 <input type="checkbox" class="tier-filter" data-tier="Tier 3" checked></label>
        <label class="tier-label-style">Ancients <input type="checkbox" class="tier-filter" data-tier="Tier Ancients" checked></label>
        <label class="tier-label-style">Other <input type="checkbox" class="tier-filter" data-tier="Tier Other" checked></label>

        <span class="tier-pipe-separator">|</span>

        <label class="tier-label-style">Show Custom<input type="checkbox" id="toggleCustom" class="yellow-tick"></label>
        <span id="customDropdown" class="custom-dropdown-style">
            <select id="customSelect" name="customFilterMode">
                <option value="showCustom">Show Customs</option>
                <option value="showOnlyCustom">Show Only Customs</option>
            </select>
        </span>  


        <div class="fleet-loading-container">
            <label class="fleet-id-label-container">
                <span class="Load-Fleet-by-ID">Load Fleet by #ID:</span>
                <input type="text" id="fleetIdInput" value="" class="fleetIdInput">
            </label>

            <!-- Custom Saved Fleet Dropdown -->
            <div class="saved-fleet-wrapper">
                <div id="fleetDropdownButton" class="fleet-dropdown-btn">
                    Load a Saved Fleet
                </div>
                <div id="fleetDropdownList" class="fleet-dropdown-list">
                    <!-- populated dynamically -->
                </div>
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
                     fleetDropdownList.innerHTML = '<div style="text-align:center; padding:10px; color:#555;">Loading fleets...</div>';
                     
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

        // Sanitize input on each keystroke: allow only digits
        fleetInput.addEventListener("input", function() {
            // Remove any non-digit characters
            this.value = this.value.replace(/\D/g, "");
        });

        // Trigger load on Enter key
        fleetInput.addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                const fleetId = this.value.trim();
                if (fleetId !== "" && !isNaN(fleetId)) {
                    gamedata.loadSavedFleetById(parseInt(fleetId, 10));
                } else {
                    console.warn("Please enter a valid numeric Fleet ID.");
                }
            }
        });

    </script>

        <!-- Fleet selection area -->
        <table class="store store-layout-table">
            <tr>
                <td class="store-left-col">
                    <div id="store" class="subpanel"></div>
                </td>            
                <td class="store-right-col">
                    <div id="fleet" class="subpanel fleet-panel-style"></div>
                </td>
            </tr>
        </table>

			
        <div class="action-buttons-row">
            <a href="./fleetchecker.php" title="Details of fleet composition rules" target="_blank" class="fleet-checker-link-style">Fleet Checker rules</a>
            &nbsp;            
            <span class="btn btn-primary-lobby checkbutton">CHECK</span>
            &nbsp;&nbsp;
            <span class="btn btn-primary-lobby savebutton">SAVE FLEET</span>
            &nbsp;&nbsp;            
            <?php if(!$isFleetTest): ?>
            <span class="btn btn-success-lobby readybutton">READY</span>
            <?php endif; ?>
        </div>

    </div> <!-- Final closing of the .buy panel -->

        <!-- ✅ Your inserted fleetcheck panel -->
        <div id="fleetcheck" class="panel large lobby fleet-check-panel-container"><p id="fleetchecktxt" class="fleet-check-text-style"><span></div>

        <div id="globalchat" class="panel large lobby global-chat-wrapper">
        <?php
            $chatgameid = 0;
            $chatelement = "#globalchat";
            include("chat.php")
        ?>
        </div>

<!--        <div id="globalhelp" class="helppanel">
        <?php
//        	$messagelocation='gamelobby.php';
//        	$ingame=false;
//        	include("helper.php")
        ?>
        </div>-->
                    
    <div id="systemInfo">
		<div class="name"><span class="name header">test</span></div>
		<div class="datacontainer"></div>
	</div>
                    
    <div id="slottemplatecontainer" class="hidden-template-container">
        <div class="slot" >
            <div class="leaveslot">Leave Slot</div>
            <div>
                <span class="smallSize headerSpan">NAME:</span>
                <span class ="value name"></span>
                <span class="smallSize headerSpan">POINTS:</span>
                <span class ="value points"></span>
                <span class="smallSize headerSpan">PLAYER:</span>
                <span class="playername"></span><span class="status">READY</span>
                <span class="takeslot clickable">TAKE SLOT</span>
                <span class="selectslot clickable">SELECT</span>
            </div>
            <div>
                <span class="smallSize headerSpan">DEPLOYMENT:</span>
                <span>X:</span>
                <span class ="value depx"></span>
                <span>Y:</span>
                <span class ="value depy"></span>
                <!---<span>Type:</span>
                <span class ="value deptype"></span> --->
                <span>Width:</span>
                <span class ="value depwidth"></span>
                <span>Height:</span>
                <span class ="value depheight"></span>
                <span>Deploys on Turn:</span>
                <span class ="value depavailable"></span>
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

    <div id="shipwindowtemplatecontainer" style="display:none;">
        <div class="shipwindow ship">
            <div class="topbar">
                <span class="valueheader name">Name:</span><span class="value name">name here</span>
                <span class="valueheader shipclass">Class:</span><span class="value shipclass">ship type class here</span>
                <div class="close"></div>
            </div>

            <div id="shipSection_3" class="shipSection">
                <table></table>
            </div>

            <table class="divider">
                <tr>
                    <td class="col1">
                        <div class="icon"><img src="" alt="" border="" width="120" height="120"></div>
                        <!--<div class="notes"></div> -->
                    </td>                
                    <td class="col2">
                        <div id="shipSection_1" class="shipSection" style="margin-top: 15px">
                            <table></table>
                        </div>
                        <div id="shipSection_0" class="shipSection primary" style="margin-top: 25px">
                            <table></table>
                        </div>
                        <div id="shipSection_2" class="shipSection" style="margin-bottom: 20px">
                            <table></table>
                        </div>
                    </td>
                    <td class="col3">
			<div class="notes"></div> <!-- Marcin Sawicki - here more readable -->
                        <div class="EW" style="display:none;"><!-- Marcin Sawicki - at fleet selection EW window is useless! -->
                            <div class="ewheader"><span class="header">ELECTRONIC WARFARE</span></div>
                            <div class="EWcontainer">
                                <div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
                                <div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
                                <div class="ewentry BDEW"><span class="valueheader">Area defence:</span><span class="value BDEW"></span><div class="button1" data-ew="BDEW"></div><div class="button2" data-ew="BDEW"></div></div>
                            </div>
                        </div>
                        <div id="shipSection_4" class="shipSection">
                            <table></table>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="hitChartDiv"></div>     
        </div>


        <div class="shipwindow base" style="width: 450px;">
            <div class="topbar">
                <span class="valueheader name">Name:</span><span class="value name">name here</span>
                <span class="valueheader shipclass">Class:</span><span class="value shipclass">ship type class here</span>
                <div class="close"></div>
            </div>

            <div id="shipSection_3" class="shipSection" style="left: 0px; top: 180px">
                <table id="31" style="border-bottom: 8px solid #7e9dc7"></table>
                <table id="32"></table>
            </div>

            <table class="divider">
                <tr>
                    <td class="col1" style="width: 33%">
                        <div class="icon" style="margin:auto;"><img src="" alt="" border="" width="120" height="120"></div>
                    </td>                
                    <td class="col2" style="width: 33%; border-left: 3px dotted #7e9dc7; border-right: 3px dotted #7e9dc7;">
                        <div id="shipSection_1" class="shipSection" style="margin-top: 20px;">
                            <table></table>
                        </div>
                        <div id="shipSection_0" class="shipSection primary" style="border-top: 3px dotted #7e9dc7; border-bottom: 3px dotted #7e9dc7; margin-top: 35px;">
                            <table></table>
                        </div>
                        <div id="shipSection_2" class="shipSection" style="margin-top: 35px;">
                            <table></table>
                        </div>
                    </td>
                    <td class="col3" style="width: 33%">
			<div class="notes"></div> <!-- Marcin Sawicki - here more readable -->
                        <div class="EW" style="display:none;"><!-- Marcin Sawicki - at fleet selection EW window is useless! -->
                            <div class="ewheader"><span class="header">ELECTRONIC WARFARE</span></div>
                            <div class="EWcontainer">
                                <div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
                                <div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
                                <div class="ewentry BDEW"><span class="valueheader">Area defence:</span><span class="value BDEW"></span><div class="button1" data-ew="BDEW"></div><div class="button2" data-ew="BDEW"></div></div>
                            </div>
                        </div>
                        <div id="shipSection_4" class="shipSection" style="right: 75px; top: 180px">
                <table id="41" style="border-bottom: 8px solid #7e9dc7"></table>
                <table id="42"></table>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="hitChartDiv"></div>     
        </div>

        <!--
        <div class="shipwindow base">
            <div class="topbar">
                <span class="valueheader name">Name:</span><span class="value name">name here</span>
                <span class="valueheader shipclass">base:</span><span class="value shipclass">ship type class here</span>
                <div class="close"></div>
            </div>

            <div id="shipSection_3" class="shipSection">
                <table id="31"></table>
                <table id="32"></table>
            </div>

            <table class="divider">
                <tr>
                    <td class="col1">
                        <div class="icon"><img src="" alt="" border="" width="120" height="120"></div>
                        <div class="notes"></div>
                    </td>                
                    <td class="col2">
                        <div id="shipSection_1" style="margin-top: 10px;" class="shipSection">
                            <table></table>
                        </div>
                        <div id="shipSection_0" class="shipSection primary" style="margin-top: 10px;">
                            <table></table>
                        </div>
                        <div id="shipSection_2" style="margin-top: 10px;" class="shipSection">
                            <table></table>
                        </div>
                    </td>
                    <td class="col3">
                        <div class="EW">
                            <div class="ewheader"><span class="header">ELECTRONIC WARFARE</span></div>
                            <div class="EWcontainer">
                                <div class="ewentry"><span class="valueheader">Defensive:</span><span class="value DEW"></span></div>
                                <div class="ewentry CCEW"><span class="valueheader">Close combat:</span><span class="value CCEW"></span><div class="button1" data-ew="CCEW"></div><div class="button2" data-ew="CCEW"></div></div>
                                <div class="ewentry BDEW"><span class="valueheader">Area defence:</span><span class="value BDEW"></span><div class="button1" data-ew="BDEW"></div><div class="button2" data-ew="BDEW"></div></div>
                            </div>
                        </div>
                        <div id="shipSection_4" class="shipSection">
                            <table id="41"></table>
                            <table id="42" style="margin-top: 10px"></table>
                        </div>
                    </td>
                </tr>
            </table>
            <div class="hitChartDiv"></div>     
        </div>-->




        <div class="shipwindow flight">
            <div class="topbar">
                <span class="valueheader name">Name:</span><span class="value name">name here</span>
                <span class="valueheader shipclass">Class:</span><span class="value shipclass"></span>
                <div class="close"></div>
            </div>

            <table class="divider">
                <tr>
                    <td class="fightercontainer 0"></td>
                   <!---             <td class="fightercontainer 1"></td>
                    <td class="fightercontainer 2"></td>
                </tr>
                <tr>
                    <td class="fightercontainer 3"></td>
                    <td class="fightercontainer 4"></td>
                    <td class="fightercontainer 5"></td>        -->
			<td>
				<div class="notes"></div>
			</td>
                </tr>
            </table>
        </div>

        <div class="shipwindow heavyfighter">
            <div class="topbar">
                <span class="valueheader name">Name:</span><span class="value name">name here</span>
                <span class="valueheader shipclass">Class:</span><span class="value shipclass"></span>
                <div class="close"></div>
            </div>
                    <table class="divider">
                            <tr>
                                    <td class="fightercontainer"></td>
                            </tr>
                    </table>
        </div>
        
    </div>
                                    
    <div class="missileSelectItem" style="display:none">
        <span>
            <span class="selectText"></span>
            <span class="selectAmount"></span>
            <span class="selectButtons">
                <table>
                    <tr>
                        <td><span class="plusButton"></span></td>
                    </tr>
                    <tr>
                        <td><span class="minusButton"></span></td>
                    </tr>
                </table>
            </span>
        </span>
    </div>
        
    <div class="totalUnitCost" style="display: none">
        <span>
            <span class="totalUnitCostText"></span>
            <span class="totalUnitCostAmount"></span>
        </span>
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
