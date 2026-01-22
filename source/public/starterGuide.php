<?php
include_once 'global.php';
if (!isset($_SESSION["user"]) || $_SESSION["user"] == false) {
    header('Location: index.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Fiery Void - About the Game</title>
  <link href="styles/base.css" rel="stylesheet" type="text/css">
  <link href="styles/lobby.css" rel="stylesheet" type="text/css">
  <link href="styles/gamesNew.css" rel="stylesheet" type="text/css">    
</head>
<body style="background: url('./img/maps/24.PurpleArch.jpg') no-repeat center center fixed; background-size: cover;">

<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <a href="games.php">Back to Game Lobby</a>
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<main class="container">
  <section class="faq-panel">
    <h2 id="top" style="margin-top: 5px">HOW TO PLAY FIERY VOID</h2>

    <h3 id="contents" style="margin-top: 25px">TABLE OF CONTENTS</h3>

    <ul class = index-list>
        <li><a href="#creating-account">Creating an Account</a></li>
        <li><a href="#joining-starting-game">Joining a Game</a></li>
        <li><a href="#creating-a-game">Creating a Game</a></li>
        <li><a href="#selecting-your-fleet">Selecting Your Fleet</a></li>
        <li><a href="#ship-control-sheet">Ship Tooltip & Ship Control Sheets (SCS)</a></li>
        <li><a href="#deployment">Deployment</a></li>
        <li><a href="#game-turn">The Game Turn</a></li>
        <li><a href="#initial-orders">Phase 1: Initial Orders</a>
            <ul class="sub-list">
                <li><a href="#ew">Electronic Warfare</a></li>
                <li><a href="#ballistics">Ballistic Weapons</a></li>
                <li><a href="#power">Power Management</a></li>
            </ul>      
        </li>
        <li><a href="#movement-phase">Phase 2: Movement Orders</a>
            <ul class="sub-list">
                <li><a href="#adjustspeed">Adjust Speed</a></li>
                <li><a href="#movingforward">Moving Forward</a></li>
                <li><a href="#sliding">Sliding</a></li>
                <li><a href="#turning">Turning</a></li>
                <li><a href="#pivoting">Pivoting</a></li>
                <li><a href="#rolling">Rolling</a></li>
                <li><a href="#jinking">Jinking</a></li>
            </ul>
        </li>
        <li><a href="#prefiring-phase">Phase 3: Pre-Firing Orders</a></li>          
        <li><a href="#firing-phase">Phase 4: Firing Orders</a>  
            <ul class="sub-list">
                <li><a href="#selectingweapons">Selecting Weapons</a></li>
                <li><a href="#hitchances">Hit Chances</a></li>
                <li><a href="#designatingfire">Designating Fire</a></li>
                <li><a href="#firingmode">Firing Mode</a></li>
                <li><a href="#defensivefire">Defensive Fire</a></li>
                <li><a href="#combatpivots">Combat Pivots</a></li>
            </ul>      
      </li>                                                      
        <!-- Add more sections here -->
    </ul>

    <h3 id="creating-account" style="margin-top: 25px">CREATING AN ACCOUNT</h3>
    <p>Go to the Fiery Void website.</p>
    <p>If you don't have an account, click on “Register New Player Account” at the top of the Log-in box. Create a username and password.</p>
    <p>Once you have a new account, go to the Fiery Void website and log in with your username and password.</p>
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="joining-starting-game">JOINING A GAME</h3>
    <p>From the main page, look under ‘Join Games’ for a game to join.  Click on the game, and then click on ‘Take Slot' to pick an available slot. Check the Game Description to see if the creator of a game has placed any restrictions on the game (i.e. ‘tournament rules’, ‘no Minabri’ etc).</p>
    <p>Once you have joined a game and picked a slot, you may select your fleet.  Skip the next section and go directly to the Selecting Your Fleet section below.</p>
    <p>If there are no games listed in the Join Games section, you may wish to create your own game (see next section).</p>    
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="creating-a-game">CREATING A GAME</h3>
    <p>Click on ‘Create Game’ on the main page.</p>
    <p>Change the name of the game from “Game of …” to something more specific or related to the type of game you wish to play.  Examples:  Centauri (me) vs Narn (you);  All Welcome 5000 pts; etc.</p>
    <p>You may select a different background picture for your game in the ‘Background’ dropdown menu.</p>
    <p>Default game settings are:</p> 
        <ul>
            <li>Gamespace: 42 by 32 hexes.</li>            
            <li>Team 1 Slot: Points 3500; Deployment X: -21 Y: 0; Width 10; Height 30; Deploys on Turn: 1</li>
            <li>Team 2 Slot: Points 3500; Deployment X: 21 Y: 0; Width 10; Height 30; Deploys on Turn: 1</li>
        </ul>    
    <p>These defaults are fine for your first game so just click on the ‘Create Game’ button and you will be taken to the Fleet Selection screen.  The creator of the game is automatically assigned to the first slot. </p>
    <p>More advanced game settings:</p>
        <ul>
            <li>If you want a larger map, try clicking 'Base Assault' or unchecking 'Set Map Boundaries'.  This will give more room to maneuver, but will give ships/races with long range weapons a relative advantage.</li>            
            <li>You may click on the 'Add Sot' button, to add another player slot to either Team.  This can be used to add more players to the game, or to simply create a reinforcement slot for yourself when part of you fleet is arriving later in the game.</li>
        </ul>       
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="selecting-your-fleet">SELECTING YOUR FLEET</h3>
    <p>Select your ships from the ‘Purchase Your Fleet’ section on the left, click on any Faction name to expand it and see their list of ships. The point cost of each ship is displayed beside it, and any variants a ship has will be listed underneath in a darker font. 
      You can click on ‘Details’ to bring up its Ship Control Sheet (see “SCS- Ship Control Sheet” in the section below for details on how to interpret the SCS).</p>

    <p>Left-click ‘Add to Fleet’ to begin adding a ship to your fleet. A pop-up window will appear which allows you to re-name the ship and select any options or enhancements available 
      to that type of ship/faction (usually at a points cost). Click on the green tick to confirm and add this ship to your fleet.</p>

    <p>The purchased ship should now appear in the list of ships on the right, and the running points total should increase to reflect the points from the ship. 
      You can view details, Edit, Copy or Remove the purchased ship using the buttons provided next to the purchased ship. Fighter flights are selected on their own, and you can adjust the number of fighters in a flight, 
      usually full flights are better unless you are min-maxing the points available towards the end of your fleet building.</p>

    <p>There is also the option to load a pre-made fleet if you just want to get started quickly. Simply click on 'Load a Saved Fleet', select one of the default fleets provided there, then click ready.
     As you get more familiar with Fiery Void, you can also add your own saved fleets to this list by selecting the units you want in your fleet and hitting the 'Save Fleet' button to the left of 'Ready'.
     Your saved fleet will then be added to the dropdown list in 'Load a Saved Fleet'. 
    </p>      

    <p>There are a few conventions for building fleets, which are not forced, but which should generally be adhered to in pick-up games unless specified otherwise. 
      These are often referred to as ‘tournament rules’ and you can check if your fleet meets these requirements you can click on the ‘Check’ button at any time to see a summary of your fleet's validity.  
      The main features of these rules are:</p>

    <ul>
        <li>Each side should have at least one capital ship.</li>
        <li>Each side should have at least one ship with a jump engine. Jump engines are denoted by a blue hexagonal icon in a ship’s SCS. Most capital ships have one, but sometimes you find one on Heavy Combat Vessels or even Medium Ships. You only need one ship with a jump engine, to justify how the fleet got to the fight!</li>
        <li>Fighters cannot be taken in a list that does not contain a ship(s) with hangar space capable of carrying all of them into battle.</li>
        <li>Any ship that can carry fighters should bring at least half of their maximum fighter load (e.g., an Earth Alliance Omega Destroyer can carry 24 fighters: it must bring at least 12 fighters). If you are fielding two ships that each carry one flight of fighters (e.g., two Hyperions), you may take a single full flight to satisfy the “half hangar minimum” of both ships.</li>
        <li>Special fighters (Rutarians, Thunderbolts etc.) must have a ship capable of bringing those kinds of fighters (e.g., Dargan or Balvarix for Rutarians; Omega or Warlock for Thunderbolts).</li>
        <li>Only a single flight of fighters per side may be less than 6 in number.</li>
    </ul>

    <p>Note: it is very unlikely that you will get exactly to the maximum point value allowed, but you should be able to get within 100 points of it. You can also use enhancements for specific ships to use up any remaining points and thus maximise your fleet’s strength.</p>

    <p>Once you are satisfied with your fleet, click 'Ready' in the bottom right of the screen, and confirm you wish to commit your fleet. 
      When all slots in a game have readied up, the game should begin. If you doesn't loaded automatically after all players have readied, simply return to the main page and click on the relevant game name in the ‘Your Active Games’ list to get started.</p>

    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="ship-control-sheet">SHIP TOOLTIP & SHIP CONTROL SHEETS (SCS)</h3>

    <h4 id="shiptooltip">Ship Tooltip:</h4>
    <p style="margin-bottom: 0px;">Now that you've started a game, let's look at the main ways you'll get information about your ships.
    Hovering the mouse over a ship's or selecting it with left-click will display its tooltip, which provides the following information:</p>
    <ul>
        <li><strong>Ship name:</strong> The name that the player assigned to the ship during list building.</li>
        <li><strong>Defense Rating (Front-Aft/Sides):</strong> The % chance for shots to hit that ship, before other adjustments. If the ship has any Defensive EW assigned, the “current” number will be less than the “base” number by 5% per point of EW.</li>
        <li><strong>Turn Cost / Turn Delay:</strong> Value that is multiplied by a ship's current speed to know how much Thrust is required to turn the ship by one hexagon side, and how many hexes it'll have to before it can turn again.</li>
        <li><strong>Initiative Order:</strong> The initiative order of this ship on this turn. Initiative is rolled on d100 + any adjustments for ship type etc, with lower initiative causing ships to move earlier.</li>
        <li><strong>Speed (Acceleration rating):</strong> Current number of hexes that the ship will move during Movement Phase, unless it accelerates or decelerates. In brackets is the thrust cost to increase or decrease Speed by 1. If the ship has any carry-over turn delay from a previous turn, this will also be listed.</li>
    </ul> 

    <h4 id="shiptooltip" style="margin-top: 10px;">Ship Control Sheet (SCS):</h4>    
    <p>Right-clicking on a ship brings up its SCS, which displays all of its systems as icons in a pop-out window. The front of the ship is shown at the top of the schematic. Weapons systems are red, other systems are generally blue with some exceptions.</p>

    <p>The remaining structure of each system is indicated graphically by a dark green bar along the bottom of the icon. The bar turns orange for critically damaged systems whereas destroyed systems have an empty bar, and the entire icon is dimmed. Powered down systems have a lightning bolt superimposed on the icon.</p>

    <p>Weapons display their charge state as (# turns spent charging / # turns required to fully charge). A weapon is ready to fire when both numbers are equal. Weapons that are not ready have a “haze” over the entire icon.</p>

    <p>Hovering the mouse over a system brings up a detailed display of that system's information, including the effects of any critical damage.</p>

    <p>Structure blocks are simply listed in a black bar in this format: current value / maximum value (armour rating).</p>

    <p>Opening the SCS is necessary to adjust power settings in the Initial Orders phase, and to fire weapons in the Fire Orders phase.</p>

    <p>Right-clicking on a fighter flight will simialrly bring up a schematic of the flight, showing the information for each fighter (structure remaining / total structure; whether the fighter was destroyed or disengaged from damage).</p>

    <p>Hovering the mouse over the weapon(s) of a fighter brings up the detailed weapon information.</p>
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="deployment">DEPLOYMENT</h3>
    <p><strong>Navigating the starmap:</strong>  Zoom in and out with the scroll wheel on your mouse. Hold down the right mouse button to drag the map around.</p>

    <p>After starting a game, you will enter the Deployment Phase. To deploy a ship, select it by left-clicking on it (a circle will indicate that it has been selected) and place it in your deployment zone by left-clicking on a hex inside that zone.</p>

    <p>You may then change the ship's starting speed (betwen 0 and 10) by clicking on the + or – beside the “5” forward green arrow. You may also start in a direction other than pointed at the enemy if you like, by clicking on the curved green arrows.</p>

    <p>Fighter flights must be deployed on the map. Fiery Void does not allow ships to actually carry fighters in their hangars.</p>

    <p>Once your ships are all deployed to your satisfaction, click the ‘Ready’ green tick at the top of the screen. The title bar at the top should change from ‘Deployment’ to ‘Waiting for turn’.</p>

    <p>Once all players have deployed, the game's first turn will start. The left-hand bar will show the ships' initiative order, and the title bar will change to “Initial Orders”.</p>
    <p><strong>Note</strong> - If a slot has been set to deploy later in the game it will not participate in the usual Turn 1 deployment at all, but will generate it's own deployment phase on the appropriate turn.</p>
    
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="game-turn">THE GAME TURN</h3>
    <p>Each game turn consists of three phases:</p>

    <ul>
    <li><b>Initial Orders</b>: Adjust power, assign EW, fire ballistic weapons,</li>
    <li><b>Movement</b>: Move ships e.g. accel/decel, move, slide, turn, pivot and/or roll,</li>
    <li><b>Pre-Firing Orders</b>: Some special weapons fire before others, triggering this phase.</li>    
    <li><b>Fire Orders</b>: Assign weapons to fire.</li>
    </ul>

    <p>All players will complete each phase simultaneously, except for Movement. Movement is done one ship at a time, or in groups if Simultaneous Movement is being used.</p>

    <p>When you have completed a phase for all of your ships (or when you are done moving one ship or flight), you must click the green tick in the title bar at the top of the screen to indicate that you have completed the phase or movement. A box will pop up asking you to confirm or cancel: click the green checkmark in the popup box to confirm.</p>
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="initial-orders">PHASE 1: INITIAL ORDERS</h3>
    <p>For each ship, you may assign your sensor rating to various Electronic Warfare (EW) functions.</p>

    <p>If you wish to fire any Ballistic weapons (Missiles, Torpedoes or Energy Mines) you must fire these in the Initial Orders phase!</p>

    <p>You may also adjust what systems are turned off (for extra power), and use any extra power to boost systems such as your sensor and/or engines. By default, the power settings for each ship will remain identical to the previous turn's settings and all EW will initially be assigned to Defensive EW.</p>
    <p>Once you are finished with Initial Orders for all ships, click the green checkmark in the title bar and confirm in the popup box. The title will show “Waiting for turn”. After all players have committed, the game proceeds to the Movement Phase starting with the first initiative ship.</p>
    
    <h4 id="ew">Electronic Warfare (EW):</h4>
    <!--<p>Select (left-click) a ship. The ship's EW assignments will be displayed in it's tooltip or in a box at the right of its SCS.</p>-->
    <p>Select (double left-click) a ship. The ship's EW assignments will be displayed in it's tooltip or in a box at the right of its SCS.</p>

    <p>When you have one of your ships selected, you will see <b>Defensive EW</b> (DEW) and <b>Close Combat EW</b> (CCEW). DEW makes your ship harder to hit (by everything except fighters), while CCEW gives you a bonus to hit any fighters that come within 10 hexes. Selecting an enemy ship will show the <b>Offensive EW</b> buttons and allow you to lock onto them.</p>

    <p>You may distribute EW points among DEW, CCEW, and each of your OEW lock-ons as desired using the + and – buttons in the relevant ship tooltips.</p>

    <p>Increasing OEW gives you a bonus to hit the target ship and establishes a “lock-on” as soon as you have at least 1 point of OEW assigned. Not having a lock-on to an enemy ship makes it very hard to hit, as weapon range penalties will be doubled vs targets.</p>

    <p>Electronic Warfare Tips: 
    <ul>
    <li>It's wise to lock on to no more than 1-3 enemy ships at a time.</li>
    <li>Some captains prefer to alternate between “turtling” (full defensive EW) and 'Hard-Lock' (all EW assigned as Offensive EW to a single target), especially at long range.</li>
    <li>Others prefer a balanced approach (half Defensive EW, 1-2 points OEW on 2-3 targets).</li>
    <li>A few points of CCEW is a good idea if enemy fighters are present, even as a deterrent.</li>
    </ul></p>

    <h4 id="ballistics">Ballistic Weapons:</h4>
    <p>Right-click one of your ships/flights to open its SCS. Select any number of ballistic weapons, then hover over a target ship. If the target is in arc and range, a % chance to hit will be displayed for each weapon. Note that this % may change after EW assignments and Defensive Fire.</p>

    <p>Left-click to confirm firing. A red targeting reticule will appear next to the target and will be visible to your opponent after Initial Orders conclude.</p>

    <p>For hex-targeted weapons (like Narn Energy Mines), select the weapon then click on the target hex. Only your team will see the target hex, while the enemy will just see a firing indication.</p>

    <p>You can toggle Ballistic Lines on/off using the buttons on the right-hand side of the screen. Enemy ballistic attacks become visible after both players commit their Initial Orders.</p>

    <h4 id="power">Power Management:</h4>
    <p>This step is usually optional and new players should probably skip it in the beginning.  Power management is certainly useful when done correctly, but can backfire if done incorrectly. If you decide you want to try it anyway, here's how to go about managing a ship's power:</p>
    <ul>
      <li>Right-click on a ship to open the SCS.</li>
      <li>Left-click on systems to turn them off (using the red ‘power button’).</li>
      <li>A lightning bolt indicates powered-down systems, and the Reactor value will increase accordingly.</li>
      <li>Use the + button on boostable systems like Scanner or Engine to assign extra power to them.</li>
      <li>The number on the system increases, and Reactor reserves will decrease accordingly.</li>
    </ul>

    <p>If you have a negative power rating (from a Reactor critical hit for example, or from a ship with a built-in power deficit e.g. a White Star) then you will not be able to commit your Initial Orders until this is addressed by turning systems off until your reactor rating is 0 or more.</p>

    <p><b>Note:</b> Fighter flights cannot adjust power or EW unless they have ballistic weapons so they have nothing to do in this phase unless they have ballistic weapons equipped..</p>
    <p>Power Tips:</p>
    <ul>
      <li>Prefer turning off weapons with a recharge rate of 1 for flexibility.</li>
      <li>Turn off rear-facing weapons before frontal ones if needed.</li>
      <li>By common convetion, Jump Engines should only be powered down when they are more than 50% damaged, except in Desperate scenarios.</li>
    </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="movement-phase">PHASE 2: MOVEMENT ORDERS</h3>
    <p>When it is your ship's turn to move, the picture of your ship will be surrounded by various green arrows to indicate movement options, which have their own hover tooltips to help you identify them. The ship’s tooltip will also provide current movement information (Turn Cost, Turn Delay, Pivot Cost, Roll Cost, Acceleration Cost, Thrust Remaining etc).</p>

    <p>You must always move the number of hexes equal to your Speed, no more and no less. Once your movement is completed, the large green arrow at the front of your ship will disappear.</p>

    <p>Whenever you take an action requiring thrust (anything other than moving your ship straight forward), a window will open up at the bottom of the screen asking you to assign thrust to your various thrusters, in order to satisfy the requirements of the maneuver.</p>

    <p>Usually the game will automatically assign the thrust appropriately, and you can just click on the green arrow in the box (or you may click the red “cancel” circle to reconsider). Sometimes however, you may be required to manually assign thrust, this is usually because:</p>

    <ul>
        <li>There are options for which thrusters to use (ie pivoting, rolling),</li>
        <li>Some thrusters are critically damaged / destroyed, or</li>
        <li>If you are doing a maneuver that requires you to overthrust (e.g. assigning more thrust to a thruster than its rating, which may critically damage it at the end of the turn).</li>
    </ul>

    <p>Click on the red cancel move icon behind your ship to retract your maneuvers one at a time or, if you want a complete do-over, right-click the cancel icon to undo your entire move.</p>
    <p>Once you have commit all the necessary movement orders, a green checkmark will appear in the title bar at the top of your screen. If you are satisfied with your movement, click the checkmark to confirm your movement orders and confirm in the pop up box. The title bar will display “Waiting for turn” and the next ship in the initiative order will be allowed to take its move.</p>
    
    <h3 id="maneuvers">Maneuvers:</h3>

    <h4 id="adjustspeed">Adjusting Speed:</h4>
    <ul>
      <li>You can only adjust a ship’s speed before any other maneuvers are made. Use the + and – near the straight green arrow at the front of your ship. Note how the turn cost and turn delay change in the bottom left movement information box depending on current speed.</li> 
      <li>You may not decelerate if your bow thrusters are destroyed, and you may not accelerate if your aft thrusters are destroyed. Pivoting can help in these cases but it's not an easy maneuver to get right. Once you have performed any maneuver (including moving forward), the + and – will disappear, and you will no longer be able to accelerate nor decelerate this turn (unless you retract all of your maneuvers with the Cancel Move icon and start again).</li> 
      <li>After adjusting speed, you may move, slide, turn, pivot and/or roll your ship.</li>
    </ul>

    <h4 id="movingforward">Moving Forward:</h4>
    <ul>  
      <li>To move forward one hex, click on the larger green arrow which will usually be at the front of your ship. To quickly move the maximum number of hexes forward, right-click on the green arrow.</li>
    </ul>

    <h4 id="sliding">Sliding:</h4>
    <ul>         
      <li>To slide, left-click one of the shorter green arrows. The ship will move one hex diagonally forward without changing facing, at a thrust cost of 1/5 speed.</li> 
      <li>You cannot slide if your opposite side thruster is destroyed and must make a normal forward move before sliding again.</li>
    </ul>

    <h4 id="turning">Turning:</h4>
    <ul>   
      <li><strong>Turning:</strong> To turn, click one of the single curved arrows. You may assign extra thrust to “shorten” the turn delay incurred after this turn. Extra thrust must be assigned to the opposite side thruster first, before aft thrusters. The green turn arrow will disappear until you are allowed to turn again (i.e., have moved forward a number of hexes equal to your current turn delay). Turn delay can carry over into the following game turn.</li> 
      <li>If your opposite side thruster is destroyed, you may not turn. If all of your aft thrusters have been destroyed, you may only turn if your current speed is low enough to make your turn cost equal to 1 (by assigning the 1 thrust to the opposite side thruster).</li> 
      <li>If you are moving backwards, you must use your bow thrusters in place of your aft thrusters for turning.</li>
    </ul>

    <h4 id="pivoting">Pivoting:</h4>
    <ul>         
      <li>To start or stop a Pivot (change facing without changing movement vector), click the two overlapping curved green arrows in the direction you wish to pivot. You will need to manually assign thrust to thrusters on two adjacent sides (once you select one thruster, the game will tell you which other thruster to use to complete the pivot). This will start a pivot at one hex facing per turn, which will continue until you click on the overlapping curved blue arrows to stop the pivot.</li> 
      <li>You cannot pivot faster than one hex facing per turn unless the ship is Agile, but you may start and stop a Pivot on the same turn if desired. While pivoting, and while pivoted to 60 or 120 degrees even if the pivot is stopped, you may not accelerate, you may not decelerate unless you have a Gravitic engine (e.g., Minbari), and you may not turn.</li> 
      <li>Once you pivot either 180 degrees and stop the pivot, or pivot back to 0 degrees and stop the pivot, you will be allowed to accelerate, decelerate and turn again. Pivoting is usually used after one's bow thrusters have been destroyed, to turn 180 degrees and decelerate using aft thrusters. Note that it takes three turns to pivot all the way around, and you will only be able to begin decelerating on the 4th turn so it requires very careful planning, or desperation!</li> 
      <li>Note that Fighters may save unused thrust for “combat pivots” in the Fire Order phase to change direction after the Movement phase, however each pivot costs 2 thrust instead of the usual 1.</li>
    </ul>

    <h4 id="rolling">Rolling:</h4>
    <ul>  
      <li>Click on the circular arrow behind the ship to start roll and the blue icon to stop. To complete a roll takes a whole turn, unless the ship is Agile, so the ship’s sides will not reverse until the next turn.</li> 
      <li>While rolling, the ship will switch port and starboard facings every turn. While rolling, a ship may not normally turn, pivot nor slide but can change speed.</li> 
      <li>Ships can make an Emergency Roll while pivoting if required, however this is a risky maneuver and will result in a critical roll on the ship’s CnC as a result.</li>
    </ul>

    <h4 id="jinking">Jinking (fighters only):</h4>
    <ul>    
      <li>Fighter flights have a jagged “lightning bolt” trailing their picture, with + and – buttons. Your flight may use this icon to assign thrust to ‘jinking” with the + button.</li> 
      <li>Jinking reduces the % chance to hit the flight by 5% per +1, but it also reduces hit chance of the flight's own weapons by the same amount.</li> 
      <li>Fighters in the same hex as enemy fighters are considered “dogfighting” and will ignore the defensive contribution of any jinking of their target flight (but not if they are also jinking).</li>
    </ul>

    <a class="back-to-top" href="#top">↩ Back to Top</a>



    <h3 id="prefiring-phase">PHASE 3: PRE-FIRING ORDERS</h3> 

    <p>Some special weapons, such as the Torvalus' Transverse Drive, actually fire before all other weapons.  
      If one of your ships has a system like this, and it's ready to fire, then you will automatically be given the chance to do so in Pre-Firing Orders.
      The actual firing of these weapons is the same as the process below for Firing Phase, but the effects of weapons in Pre-Firing will fully occur before anyone gets to fire anything else.</p>
    
    <a class="back-to-top" href="#top">↩ Back to Top</a>    


    <h3 id="firing-phase">PHASE 4: FIRING ORDERS</h3>

    <h3 id="howtofire">How to Fire:</h3>

    <h4 id="selectingweapons">Selecting Weapons to Fire:</h4>
    <ul>    
      <li>Pull up details for one of your ships of fighter flights SCS with right-click as usual.  Hovering over an weapon in the SCS will display its firing arc around the ship, including its maximum effective range.  This allows you to easily select which weapons to fire at particular targets.</li> 
      <li>Click one or more weapons once each – the icons will be highlighted blue.  Alternatively, right-click on a weapon and all active weapons of that name will be selected.</li> 
    </ul>

    <h4 id="hitchances">Chance to Hit:</h4>
    <ul>    
      <li>Now hover the mouse over a target ship.  Below the ship info display, the % chance to hit for each weapon will be listed (you may need to drag the starmap up to see it, depending on the target's location).</li> 
      <li>This is the actual percentage to hit, taking into account the target’s defensive EW, your offensive EW, your weapon's fire control bonus vs that type of target, and the range penalty.  The only factor not included is any defensive fire from the enemy ship.</li> 
      <li>If any weapons have a negative chance to hit, this will be displayed as “- XX%”. If any selected weapons do not have the target in arc, do not have line of sight, or are out of range their % chance to hit will display a message instead e.g. 'Not in arc'.  These weapons will not be able to target this enemy.</li>
    </ul>

   <h4 id="designatingfire">Targeting:</h4>
    <ul>    
      <li>If you're happy with your hit chances, left-click on the target reticle in the enemy ship's tooltip.  All of your selected weapon icons will turn orange to indicate that they are now assigned to fire.</li>  
      <li>The % chance to hit will no longer be displayed after this, (except for Ballistic or Split Targeted weapons).</li>
      <li>Repeat these three steps by for each weapon you want to fire on this turn, targeting the same or different enemy ships.</li>        
    </ul>

   <h4 id="firingmode">Firing Modes:</h4> 
    <ul>    
      <li>Some weapons have alternate fire modes (e.g. a Battle Laser can fire in either Raking or Piercing mode).  Where this is the case, left-clicking on the weapon icon will show a letters at the top right of the weapon.</li>  
      <li>Click on the right-most letter to cycle through the alternate modes.</li>      
    </ul>

   <h4 id="defensivefire">Defensive Fire:</h4>
    <ul>    
      <li>Any weapons with an intercept rating, a recharge rate of 1 turn, have not been assigned to fire offensively, will automatically fire defensively against incoming enemy fire on their fire arcs.</li>  
      <li>Note that lasers and some other weapons are not interceptable.  Particle (including pulse weapons) and plasma weapons are interceptable, as are most others.</li>      
    </ul>

   <h4 id="combatpivots">Combat Pivots:</h4>
    <ul>    
      <li>Fighter flights with unused Thrust may make a Combat Pivot during the Fire Orders phase, in order to bring their weapons to bear (often on other fighters which have moved later in the initiative order).</li>  
      <li>Click the crossed curved green arrows in the direction you wish to pivot, each pivot during combat typically costs two points of thurst instead of the usual one point.  Once you are oriented correctly, proceed to assign Fire Orders in the normal fashion.</li>      
    </ul>

    <p>If you do not wish to fire any weapons (e.g. all targets are out of range / arc of all of your ships), you may skip this step by just clicking on the green check at the top of the screen  (the game will not progress unless you do this).</p>

    <p>Once all of your Fire Orders for all ships and flights have been assigned, click the green checkmark in the title bar and confirm in the pop up box.  The title bar will display “Waiting for turn”.  As with other phases, once all players have committed their Firing Orders the game will proceed although this time it will progress onto the next turn.</p>

    <p>To see the result of the Firing Phase you can click on Replay and Fiery Void will take you to the previous turn where you can see an animated version of the Firing Phase.  If you just want to quickly see what happened, the Log Tab  at the bottom of the screen contains a button that allows you to review text logs of Firing from previous turns.</p>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

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
</html>