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
<body style="background: url('./img/webBackgrounds/howtoplay.jpg') no-repeat center center fixed; background-size: cover;">

<header class="header">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <a href="games.php">Back to Game Lobby</a>
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<main class="container">
  <section class="faq-panel">
    <h2 id="top" style="margin-top: 5px">HOW TO PLAY FIERY VOID</h2>
    <ul class = index-list>
        <li><a href="#creating-account">Creating an Account</a></li>
        <li><a href="#joining-starting-game">Joining a Game</a></li>
        <li><a href="#creating-a-game">Creating a Game</a></li>
        <li><a href="#selecting-your-fleet">Selecting Your Fleet</a></li>
        <li><a href="#ship-control-sheet">Ship Tooltips & Ship Control Sheets (SCS)</a></li>                        
        <!-- Add more sections here -->
    </ul>

    <h3 id="creating-account">CREATING AN ACCOUNT</h3>
    <p>Go to the Fiery Void website.</p>
    <p>If you don't have an account, click on “Register New Player Account” at the top of the Log-in box. Create a username and password.</p>
    <p>Once you have a new account, go to the Fiery Void website and log in with your username and password.</p>
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="joining-starting-game">JOINING A GAME</h3>
    <p>From the main page, look under ‘Join Games’ for a game to join.  Click on the game, and then click on ‘Take Slot to pick an available slot. Check the Game Description to see if the creator of a game has placed any restrictions on the game (i.e. ‘tournament rules’, ‘no Minabri’ etc).</p>
    <p>Please DO NOT join any games labeled “Test”. Test games are for single players to test list builds and try to solve apparent bugs, etc.</p>
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
            <li>TEAM 1: Name: Blue; Points 3500; Deployment X -21 Y 0; Width 10; Height 30; Deploys on Turn: 1</li>
            <li>TEAM 2: Name: Red; Points 3500; Deployment X 21 Y 0; Width 10; Height 30; Deploys on Turn: 1</li>
        </ul>    
    <p>These defaults are fine for your first game so just click on the ‘Create Game’ button and you will be taken to the Fleet Selection screen.  The creator of the game is automatically assigned to the first slot. </p>
    <p>More advanced game settings:</p>
        <ul>
            <li>If you want a larger map, try clicking 'Base Assault' or unchecking 'Set Map Boundaries'.  This will give more room to maneuver, but will give ships/races with long range weapons a relative advantage.</li>            
            <li>You may click on the 'ADD SLOT' button, to add another player slot to either Team.  This can be repeated for more players or to create a reinforcement slot that will arrive for the same player later in the game.</li>
        </ul>       
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="selecting-your-fleet">SELECTING YOUR FLEET</h3>
    <p>Select your ships from the ‘Purchase Your Fleet’ section on the left, click on a Faction name to see their list of ships.</p>

    <p>The point cost of each ship is displayed beside it. You can click on ‘Details’ to bring up its Ship Control Sheet (see “SCS- Ship Control Sheet” in the section below for details on how to interpret the SCS).</p>

    <p>Left-click ‘Add to Fleet’ to begin adding a ship to your fleet. A pop-up window will appear which allows you to re-name the ship and select any options or enhancements available to that type of ship/faction (usually at a points cost). Click on the green tick to confirm and add this ship to your fleet.</p>

    <p>The purchased ship should now appear in the list of ships on the right, and the running points total should increase to reflect the points from the ship. You can view details, edit, copy or remove the purchased ship using the buttons provided.</p>

    <p>Fighter flights are selected on their own, and you can adjust the number of fighters in a flight, usually full flights are better unless you are points-trimming the end of your fleet.</p>

    <p>There are a few conventions for building fleets, which are not forced, but which should generally be adhered to in pick-up games unless specified otherwise. These are often referred to as ‘tournament rules’:</p>

    <ul>
        <li>Each side should have at least one capital ship.</li>
        <li>Each side should have at least one ship with a jump engine. Jump engines are denoted by a blue hexagonal icon in a ship’s SCS. Most capital ships have one, but sometimes you find one on Heavy Combat Vessels or even Medium Ships. You only need one ship with a jump engine, to justify how the fleet got to the fight!</li>
        <li>Fighters cannot be taken in a list that does not contain a ship(s) with hangar space capable of carrying all of them into battle.</li>
        <li>Any ship that can carry fighters should bring at least half of their maximum fighter load (e.g., an Earth Alliance Omega Destroyer can carry 24 fighters: it must bring at least 12 fighters). If you are fielding two ships that each carry one flight of fighters (e.g., two Hyperions), you may take a single full flight to satisfy the “half hangar minimum” of both ships.</li>
        <li>Special fighters (Rutarians, Thunderbolts etc.) must have a ship capable of bringing those kinds of fighters (e.g., Dargan or Balvarix for Rutarians; Omega or Warlock for Thunderbolts).</li>
        <li>Only a single flight of fighters per side may be less than 6 in number.</li>
    </ul>

    <p>To check if your fleet meets these requirements you can click on the ‘Check’ button at any time, and will see a summary of your fleet's validity.</p>

    <p>Note: it is very unlikely that you will get exactly to the maximum point value allowed, but you should be able to get within 100 points of it. You can also use enhancements for specific ships to use up any remaining points and thus maximise your fleet’s strength.</p>

    <p>Once you are satisfied with your fleet, click “Ready” in the bottom right of the screen, and confirm you wish to commit your fleet.</p>

    <p>Once all slots in a game have readied up, the game will begin.</p>

    <p>If you haven’t loaded automatically after readying, return to the main page and click on the relevant game name in the ‘Your Active Games’ list. You will see a starmap with boxes designating deployment zones, and with each of your ships / fighter flights pictured.</p>

    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="ship-control-sheet">SHIP TOOLTIPS & SHIP CONTROL SHEETS (SCS)</h3>
    <p><strong>Ship Info:</strong> Hovering the mouse over a ship's picture on the starmap gives the following tooltip information:</p>

    <ul>
        <li><strong>Ship name:</strong> The name that the player assigned to the ship during list building.</li>
        <li><strong>Forward-Aft / Side Defense Rating (current and base):</strong> This is the % chance for shots to hit that ship, before other adjustments. If the ship has any Defensive EW assigned, the “current” number will be less than the “base” number by 5% per point of Defensive EW.</li>
        <li><strong>Turn Cost and Turn Delay ratings:</strong> Multiply Cost by the ship's current speed to know how much Thrust to turn the ship one hexagon side. Multiply Delay by the ship's current speed to know how many hexes the ship must travel after turning, before it can turn again.</li>
        <li><strong>Initiative Order:</strong> The current initiative order of this ship. First in initiative order will have to move first. Initiative is rolled on d100 + any adjustments for ship type etc. Most capital ships have no bonus, but smaller ships and fighters have significant bonuses equal to the B5 Wars “initiative bonus” x 5.</li>
        <li><strong>Speed (Acceleration rating), and any current Turn Delay:</strong> The number of hexes that the ship will currently move, unless it accelerates or decelerates. In brackets is the thrust cost to increase or decrease Speed by +/- 1 hex / turn. If the ship has any carry-over turn delay from a previous turn, this will be listed in brackets here.</li>
    </ul> 

    <p><strong>Ship Control Sheet (SCS):</strong>Right-clicking on a ship brings up that ship's SCS, displaying all of its systems as icons in a schematic. The front of the ship is shown at the top of the schematic.</p>

    <p>Weapons are red, other systems are generally blue with some exceptions.</p>

    <p>The remaining structure of each system is indicated graphically by a dark green bar along the bottom of the icon. The bar turns orange for critically damaged systems whereas destroyed systems have an empty bar, and the entire icon is dimmed. Powered down systems have a lightning bolt superimposed on the icon.</p>

    <p>Weapons display their charge state as (# turns spent charging / # turns required to fully charge). A weapon is ready to fire when both numbers are equal. Weapons that are not ready have a “haze” over the entire icon.</p>

    <p>Hovering the mouse over a system brings up a detailed display of that system's information, including the effects of any critical damage.</p>

    <p>Structure blocks are simply listed in a black bar in this format: current value / maximum value (armour rating).</p>

    <p>Opening the SCS is necessary to adjust power settings in the Initial Orders phase, and to fire weapons in the Fire Orders phase.</p>

    <p>Right-clicking on a fighter flight brings up a schematic of the flight, showing the information for each fighter (structure remaining / total structure; whether the fighter was destroyed or disengaged from damage).</p>

    <p>Hovering the mouse over the weapon(s) of a fighter brings up the detailed weapon information.</p>
    <a class="back-to-top" href="#top">↩ Back to Top</a>




  </section>
</main>
</body>
</html>