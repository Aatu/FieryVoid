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
<body style="background: url('./img/webBackgrounds/faq.jpg') no-repeat center center fixed; background-size: cover;">

<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <a href="games.php">Back to Game Lobby</a>
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<main class="container">
  <section class="faq-panel">
    <h2 id="top" style="margin-top: 5px">FIERY VOID FAQ</h2>

    <h3 id="contents" style="margin-top: 25px">TABLE OF CONTENTS</h3>

    <ul class = index-list>
        <li><a href="#general">GENERAL NOTES</a> </li>
        <li><a href="#differences">DIFFERENCES FROM BABYLON 5 WARS</a></li>                        
        <li><a href="#mechanics">ADVANCED MECHANICS</a>
           <ul class="sub-list">
                <li><a href="#boarding">Boarding Actions & Marines</a></li>
                <li><a href="#called">Called Shots</a></li>
                <li><a href="#delayed">Delayed Deployment</a></li>                
                <li><a href="#enormous">Enormous Units</a></li>
                <li><a href="#jump">Jump Drives</a></li>
                <li><a href="#ladder">Online Ladder</a></li>                
                <li><a href="#ruler">Ruler Tool</a></li>
                <li><a href="#savedfleets">Saved Fleets</a></li>
                <li><a href="#skindancing">Skin Dancing</a></li>                                                       
                <li><a href="#stealth">Stealth Ships</a></li>
                <li><a href="#terrain">Terrain</a></li>  
                <li><a href="#useful">Useful Controls</a></li>
                <li><a href="#disclaimer">Disclaimer</a></li>                                                                      
            </ul>     
      </li>                                                                 
        <!-- Add more sections here -->
    </ul>

<h3 id="general" style="margin-top: 30px;">GENERAL NOTES</h3>
    <p>This is an online adaptation of the <strong>Babylon 5 Wars</strong> tabletop game, by Agents of Gaming (bowing heads to you, AoG!).</p>
    
    <p>Therefore, there is no game manual for Fiery Void itself — all rules are readily available under the name <strong>Advent of Galactic Wars</strong>, 
    hosted at <a href="http://b5warsvault.wikidot.com/" target="_blank" rel="noopener noreferrer">B5Wars Vault</a> (see Links section!). 
        If you know these rules, you should essentially be able to play Fiery Void (after coming to grips with the online interface).</p>
    
    <p>For new players the game will seem very complex at first, but don’t worry. If you start with the basics and dig deeper at your own speed, you’ll be a lion of the galaxy soon enough.</p>
    
    <p>For your first game, we suggest asking for a tutorial from more experienced players via the in-game chat function, or by joining us on our <a href="https://discord.gg/4jXarWusp4" target="_blank" rel="noopener noreferrer">Discord group</a> 
        (link available in the lobby). It shouldn’t be difficult to find a volunteer to explain the basics. </p>
    
    <p>If you have any questions about the game, feel free to ask on our Discord channel too!</p>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h2 id="differences">DIFFERENCES FROM BABYLON 5 WARS</h2>
        <ul style="margin-top: 10px;">
            <li>Alot has been automated in Fiery Void compared to B5 Wars, such as dice rolling, and players have a little less control over certain minutiae, but overall the game is more streamlined.</li>
            <li>By default the game is played in a fixed, rectangular map. It does not enforce anything about the boundaries — it’s up to the players to ensure ships leaving the map behave as disengaged.</li>
            <li>Fiery Void does not enforce fleet design rules. Fleet requirement rules can be checked using the Check button during Fleet Selection.</li>
            <li>The game mechanics are based on d100 rolls (instead of d20), so most values are displayed as percentages (e.g., 1 point on a d20 equates to 5%).</li>
            <li>There are no separate Power and Electronic Warfare (EW) phase. These are combined into a single Initial Orders phase, along with ballistic firing, which happens after the Initiative roll.</li>
        </ul>

        <h3>Electronic Warfare (EW)</h3>
        <ul>
            <li>CCEW: Provides a lock on all enemy fighters within 10 hexes.</li>
            <li>Most activities requiring multiple EW points can now be done in fractions. For example, with Blanket DEW (5% per 4 points allocated), you may allocate 3 points and get around 4% Blanket DEW.</li>
            <li>Disruption EW: Enemy target locks are affected equally by the fraction of DEW used E.g. using 1 point of DIST against 3 separate OEW locks reduces each by 0.33 points.</li>
            <li>Disruption EW: CCEW is treated as one OEW target and affected appropriately.</li>
            <li>Disruption EW: Target locks weaker than 1 point but worth at least half a point provide half-lock (e.g., range penalties multiplied by 1.5 instead of being doubled).</li>
            <li>DEW Below 0: The B5 Wars rule about only a ship’s own DEW being able to bring its profile below 0 does not apply in Fiery Void.</li>
        </ul>

        <h3>Movement</h3>
        <ul>
            <li>Snap turn: Implemented as consecutive but separate turns. Turn delay is taken from the first turn only (shorten the first segment to shorten the snap turn).</li>
            <li>Fighter Pivots: Fighters do not automatically return from pivots.</li>
        </ul>

        <h3>Firing and Weapons</h3>
        <ul>
            <li><strong>Firing Order of Weapons:</strong> Each weapon has a priority number determining firing order, except ramming attacks which always fire first. Players cannot influence this order.</li>
            <li><strong>Choosing Target Section:</strong> Automatically chosen based on target profiles and remaining structure. Fire from the same ship always hits same section, primary sections are avoided if alternatives exist.</li>
            <li><strong>Choosing System Hit:</strong> In-arc systems are prioritized before off-arc ones. Direction of damage is assumed to match the direction of the shot.</li>
            <li><strong>Targeting Fighters:</strong> The algorithm minimizes expected overall damage. You can manually target individual fighters, but it counts as a called shot with a -40% to-hit penalty.</li>
            <li><strong>Fighter Dropouts:</strong> Occur during critical hits resolution (after firing at ships).</li>
            <li><strong>Interception:</strong> Handled automatically, prioritizing the most powerful incoming shots. 1-turn recharge weapons intercept automatically if not fired; others require manual selection.</li>
            <li><strong>Ballistic Damage:</strong> Resolved within relevant subphases instead of a separate ballistic damage subphase.</li>
            <li><strong>Fighter Escorts:</strong> Fighters can intercept ballistics for ships they are escorting if they start and end their movement in the same hex (on Turn 1, any fighter may escort any ship).</li>
            <li><strong>Ballistics & Jammers:</strong> Power and missile launches are simultaneous. Disabling jammers affects missile launches from the next turn, not the current one.</li>
            <li><strong>Multi-mode Weapons:</strong> Simplified — may switch freely (Guardian Array) or can be boosted for free during Initial Orders (EA Interceptors).</li>
            <li><strong>Piercing Attacks:</strong> Damage is split into 3 parts (or 2 if entry and exit are through the same section). Piercing vs. MCVs is reduced by 10%. EW penalties are already included in fire control values.</li>
            <li><strong>Unit Positions:</strong> Range 0 targeting is allowed to compensate for imperfect relative positioning within the same hex.</li>
            <li><strong>Firing Modes:</strong> Always visible, meaning opponents can see missile types immediately.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h2 id="mechanics">INFO ON ADVANCED MECHANICS</h2>

        <h3 id="boarding" style="margin-top: 15px;">Boarding Actions & Marines</h3>
        <ul>
            <li>Many factions have access to Breaching Pods, which come equipped with a marines that can undertake boarding actions.</li>
            <li>During the Firing Phase, Pods can attempt to attach to enemy ships in the same hex and deliver Marines to undertake a selection of missions (Sabotage, Capture Ship and Rescue).</li>
            <li>Pods first roll to attach on a d10, but success is automatic if they are moving faster than the target ship and the speed difference is not higher than pods thrust rating. 
                If the speed difference to target is greater than pod thrust rating it is unable to attach. 
                If the target is moving faster, each point of speed difference is -10% chance to attach.  
                Pods cannot attached to ships with Advanced Armor and certain factions like Llort have +1 to attach rolls.</li>
            <br>                 
            <li>Once attached, the Pod will roll a d10 again on the following table to try and deliver the Marines, with a base chance of 50% to successfully board the vessel.  
                Unsuccessful marines may be lost or return safely their pod.</li>                           
            <li><strong>DELIVERY TABLE (D10):</strong>
                <ul class="circle-list">
                    <li>1-5 - Marines successfully delivered.</li>
                    <li>6-8 - Marines fended off, but return safely to their pod.</li>
                    <li>9+ - Marines fended off, and were eliminated.</li>
                </ul>
            </li>            

            <li>A number of modifiers can also apply to the delivery roll, summarised below:
                <ul class="circle-list">
                    <li>+20% success - Yolu-specific bonus</li>
                    <li>+10% success - Elite marines / Llort / Target has Poor Crew / Directly boarding Primary section of target</li>
                    <li>-10% success - Narn or Gaim Defenders / Target has Elite Crew or Markab's Religious Fervor</li>
                </ul>
            </li>
            <li>Marines that successfully board a vessel will then attempt to carry out their intended missions from the end of the following turn.  
                These rolls also can attract several modifiers:
                <ul class="circle-list">
                    <li>+10% success - Elite Marines / Target vessel has Poor Crew</li>
                    <li>-10% success - Narn or Gaim Defenders / Target has Elite Crew or Markab's Religious Fervor</li>
                    <li>For Sabotage and Rescue missions only - Additional -10% for every two turns that Marines are active aboard the enemy vessel.</li>
                </ul>
            </li>
            <br>             
            <li>Details of each of the three types of marine mission are summarised below:
                <ul class="circle-list">
                    <li><strong>CAPTURE: </strong>Marines will fight the defending marine contingents directly (defenders are shown in CnC tooltip!).  
                    This has been simplified from Tabletop, and now only involves two dice rolls per attacker, one to see if marines eliminate a defender (50% base chance) and a second to see if marines are eliminated (25% base chance).  
                    If the attacking marines manage to defeat all defenders, the enemy ship is immediately be disabled for the remainder of the battle so long as there are still at least one attacking marine unit on board.
                    </li>
                    <br>                     
                    <li><strong>SABOTAGE: </strong>Marines can either attempt to damage a specific system on an enemy ships (by making a called shot against it using the usual rules) 
                    or Wreak Havoc on the ship more generally (e.g. minor damage to a Primary system, EW/Initiative/Thrust/Defence Profile penalties) by targeting it in the normal fashion.</li>  
                    <li>In both cases, Marines will roll on a d10 the following tables to see how successful their mission has been:</li>
                    <li><strong>SABOTAGE TABLE (D10):</strong>
                        <ul class="circle-list">
                            <li>1 - Deal 3d6 damage to target system.</li>
                            <li>2-3 - Deal 1d6 damage to target system, Marines not eliminated.</li>
                            <li>4-5 - Deal 1d6 damage to target system, Marines eliminated.</li>
                            <li>6-8 - No effect, Marines will try again next turn.</li>
                            <li>9+ - No effect, Marines were eliminated.</li> 
                        </ul>
                    </li>                           
                    <li><strong>WREAK HAVOC TABLE (D10):</strong>
                        <ul class="circle-list">
                            <li>1 - Deal 1d6 damage to a non-Structure system on Primary Hit Chart.</li>
                            <li>2 - Reduce ship's Initiative by 5-30 next turn.</li>
                            <li>3 - Reduce EW by 1d3 next turn.</li>
                            <li>4 - Reduce ship's Initiative by 5-30 next turn.</li>
                            <li>5 - Increase Defence Profiles of ship by 5 next turn.</li>
                            <li>6-8 - No effect, Marines will try again next turn.</li>
                            <li>9+ - No effect, Marines were eliminated.</li>                                                         
                        </ul>
                    </li>   
                    <li>NOTE - Marines which target a specific system and are successful in destroying it will then move to a Wreak Havoc mission providing they have not been eliminated.</li>
                    <br>                    
                    <li><strong>RESCUE: </strong>For scenarios only, Marines will attach their pod and attempt to board as normal.  
                    Then, from the following turn, the Combat Log will provide players with updates on the progress of their Rescue mission.</li>
                    <li><strong>RESCUE TABLE:</strong>
                        <ul class="circle-list">
                            <li>1-2 - Rescue is successful, Marines survive.</li>
                            <li>3-4 - Rescue is successful, but Marines eliminated.</li>
                            <li>5-6 - Rescue fails this turn, Marines will try again next turn.</li>
                            <li>7+ - Rescue fails, Marines were eliminated.</li> 
                        </ul>
                    </li>                      
                </ul>
            </li>
            <br>   
            <li>Note - Unlike Tabletop where Pods STAY attached to enemy units, this is not the case in FV and they are free to move the following turn.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3  id="called">Called Shots</h3>
        <ul>
            <li>Called Shots are possible in Fiery Void, providing the weapon selected is able to do s. For example ballistic weapons cannot do so, witht he exception of the Kor-Lyans Limpet Bore Torpedo.</li>  
            <li>To make a called shot, select the weapon you want to fire then bring up the enemies SCS by right-clicking on their ship. Find the system you wish to target and click on it.  
            Providing all the other conditions are met e.g. system can be targetd by called shots, is in arc of the firing ship etc.  
            This system will now be targeted and your weapon icon will highlight orange as usual to indicated it's locked in.</li>
            <li>Called Shots are usually made with a -40% chance to hit for most weapons, although some have bonuses towards this like the Dilgar's Point Pulsar.</li>            
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3  id="delayed">Delayed Deployment</h3>
        <ul>
            <li>You can select this option in the Create Game screen, by setting the <b>'Deploys on Turn'</b> field in a Player Slot to the Turn you wish that slot to deploy, or ‘jump in’.  
            Ships cannot jump into hexes occupied by terrain or Enormous units, so make sure you make the Deployment Zone large enough!</li>
            <li>Ships which would normally have to set systems on Turn 1 and choose to deploy later (e.g. Hyach Specialists, Vorlon Adaptive Armor) will set these systems on the turn they deploy instead.</li>
            <li>Terrain, Bases, and OSATS cannot deploy later in the game and will always deploy on Turn 1 even if the slot is set to deploy later.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="enormous" >Enormous Units</h3>
        <ul>
            <li>Some units in the game, such as Terrain, are classified as Enormous. They block line of sight if any part of a shot would pass through their hex.</li>
            <li>Ships that end movement on the same hex as an Enormous unit will automatically make a ramming attempt (fighters are exempt).</li>
            <li>Damage from Energy Mine, targeting from Mass Driver, and power from Improved Reactor also consider Enormous size.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="jump" >Jump Drives</h3>
        <ul>
            <li>The Jump Drive system usually cannot be turned off unless seriously damaged, but some scenarios allow it.</li>
            <li>The game warns the player when attempting to deactivate this system improperly (e.g., without Desperate rules or 50%+ damage).</li>
            <li>Ships equipped with Jump Drives can boost this system during Initial Orders to 'jump out' of the scenario at the end of the turn.  
                Doing will remove then from the rest of the scenario, and ships with damaged jump drives may be destroyed as they jump (the chance of this is the % of the Jump Drive's health lost).  
                The latter situation will be reflected in the Combat Log for that turn.</li>            
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="ladder" >Online Ladder</h3>
        <ul>
            <li>The purpose of the Online Ladder in Fiery Void is primarily to help players create well-balanced, interesting games, as well as provide some bragging rights along the way.  
                It works similar to a handicap in golf, whereby the difference in ratings between players is added as a % bonus to the lower rated player.  
                So if there was a difference of 5 rating, then the lower-rated player gets 5% extra points!</li>
            <li>To set-up a Ladder game, create a game as usual and tick the Ladder Game checkbox. You’ll see the ‘View Ladder’ button next to this option, 
                and it allows you to see current ratings and even calculate the points difference that should apply against a particular player / populate the team slots with these values.</li>
            <li>Alternatively you can set up an open Ladder game without using this feature and just set points values in team slots in the usual way.  
                When a player takes the other slot in the game their points will be adjusted automatically. 
                Alternatively you can set up an open Ladder game without using this feature and just set points values in team slots in the usual way.  
                When a player takes the other slot in the game their points will be adjusted automatically.  Either way, decide on the specifics for the game (Points, Map; Standard vs Simultaneous Movement, etc.) and then click Create Game.  
                Note, Ladder games are competitive matches so only two players can take part, and only one slot is allowed per team. </li>
            <li>When the game ends and one player surrendering (and at least one turn has been played), the winner will have their ranking increased by 1 on the Ladder, and the loser has their ranking reduced by 1.  
            You can review your own and other players' match history by clicking 'View Ladder' on the Fiery Void Home Page and then clicking on their name.</li>                                              
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


        <h3 id="ruler" >Ruler Tool</h3>
        <ul>
        <li>This tool helps players measure distance between any two hexes on the game map, and also indicates whether line of sight is blocked or not between the two hexes chosen.</li>
        <li>To use the tool, just activate it by pressing 'R' key, or clicking on the 'eye' button on the right-hand side of the screen. 
            ONce activated, left click on the hex from where you would like it to start measuring from, then move the mouse around the map to check distance and line of sight to other hexes.  
            Right-clicking with the mouse will reset the start hex.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="savedfleets" >Saved Fleets</h3>
        <ul>
            <li><b>Saving a Fleet:</b> While in Fleet Selection you can save any fleet that you are making for later.  Simply select your fleet as normal and when you're happy with your force click the 'Save Fleet' button and confirm your choice.
             Your saved fleet will then become available in this and future sessions (providing you have sufficient points avaialble) via the 'Load a Saved Fleet' dropdown button.</li>
            <li><b>Sharing Fleets and Loading with ID:</b> Each saved fleet in Fiery Void has a unique ID, providing the fleet is marked as 'Shared' (and you can set this when you save a fleet or toggle it with the padlock symbol) you can give this ID to another player. 
            They can then load the saved fleet by entering the fleet ID in the 'Load fleet by #ID' field and pressing Enter key.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


        <h3 id="skindancing" >Skin Dancing</h3>
        <ul>
            <li>Skin dancing refers to a maneuver wherein a unit flies only meters above the surface of a large unit or base. It is a very dangerous maneuver only performed by the most agile
                of ships.</li>
            <li>Any unit that is able to Skin Dance will automatically attempt to do so when it ends its movement on the same hex as an Enormous Unit 
                (not a Terrain unit though, where it will suffer the normal collision rules covered in the 'Terrain' section).  In order to be eligible to Skin Dance a unit must meet the following critieria:</li>
                <ul class="circle-list">
                    <li>Must be a Medium Ship or smaller, and must be classified as agile (fighters and shuttles are considered agile for this purpose).  There are some rare execptions to this rule e.g. Torvalus capital ships.</li>
                    <li>Skin Dancing unit cannot be stationary, and the Enormous Unit cannot be moving at more than Speed 5.  
                    If the Enormous unit is moving the dancing ship must be moving in the same direction or the exact opposite direction.</li>
                </ul>
            </li>
            <li>Eligible ships will then make a Skin Dancing roll on d20 just before the Firing Phase begins, and must roll 15 or less to succeed. The following modifiers are applied to this roll:
                <ul class="circle-list">
                    <li>If the skin dancer’s speed is greater than 5, +1 to the roll for each 2 points of speed (or any fraction) above this limit.</li>
                    <li>If either unit is rolling or pivoting, +5 is added to the roll. These are cumulative, so if the skin dancer is rolling while the target is pivoting, add +10. A rotating base is not considered to be pivoting. </li>
                    <li>If a skin dancing ship has lost any of its thrusters (regardless of their location), +1 is added for each point of thrust rating no longer available. 
                        For example, a ship that has lost two of its aft 4-rating thrusters would have +8 to the roll.</li>
                    <li>If a skin dancing fighter is jinking, +3 is added for each level.</li>
                    <li>If a flight of fighters are equipped with a navigator, -1 is subtracted from the roll.</li>
                </ul>
            </li>
            <li>If the roll fails by 5 or less (i.e., the modified roll is a 16, 17, 18, 19, or 20), the dance is aborted with no ill effects, and there is no chance of a ram.  
                If the roll result is 21 or higher, skin-dancing ships smashes into the hull of the Enormous unit. 
                For fighters, one fighter at random crashes into the hull as above while the others break away. 
                The survivors cannot fire (even defensively) or guide weapons on that turn as they are too busy pulling out of the maneuver.</li>
            <li>If skin dancing is successful, the unit cannot be fired upon by enemy units unless they also skin dance over the same target, 
                the exception being ballistic weapons that were launched at the skin dancing ship earlier in the turn.  
                The vessel you are skimming over also cannot fire at you, and cannot fire defensively against your weapons, because you’re inside its weapon’s tracking zones.</li>
            <li>Finally, any of your forward firing weapons (those that can legally fire into the row of hexes directly ahead of your ship) automatically roll the best result on their hit dice e.g. they will roll a 1 on a d100 meaning they automatically hit in almsot all cases.  
                In fact, your weapons may not fire at any other target (except other skin dancers, against whom they use the normal firing procedures) unless they can fire outside the 120° forward area ahead of the ship. 
                If this is the case, they can choose another eligible target if desired. That unit may use intercept fire or other defensive devices normally.</li>
            <li>Fighters cannot guide missiles or other ballistic weapons towards a target (even the unit being skin danced over) even if they have a navigator. 
                The pilot and any other crewmen are too busy controlling the fighter and its onboard weapons to perform another mission.</li>    
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>        

        <h3 id="stealth" >Hyach Stealth Ships</h3>
        <ul>
            <li>Stealth ships are invisible at long ranges until they reveal themselves or are detected.</li>
            <li>They will automatically reveal themselves by using any EW ability except Defensive EW (DEW) or by firing their weapons.</li>
            <li>They can also be detected once they get closer to an enemy vessel, providing there is line of sight available.  
                Detection occurs at the start of the Firing Phase and is based on both ship type and sensor ratings. 
                If an undetected stealth ship is within detection range at this point in the turn, it will become detected.
                 Detection ranges are:
                <ul class="circle-list">
                    <li>Base: 5x Sensor Rating</li>
                    <li>ELINT Ship: 3x Sensor Rating</li>
                    <li>Other Ship: 2x Sensor Rating</li>
                    <li>Fighter: Offensive Bonus</li>
                </ul>
            </li>
            <li>ELINT ships can spend EW points on 'Detect Stealth' to increase detection range by +2 per point invested in this way.</li>
            <li>After being detected, Stealth ships can become undetected by breaking line of sight with ALL enemy vessels at the end of a turn and not firing any weapons.</li>
            <li>If their scanner or computer system is destroyed, their defense increases by 15% for the battle.</li>
            <li>Stealth ships also receive the same benefits as Minbari Jammer-equipped ships from a certain distance:
                <ul class="circle-list">
                    <li>Ships: Double range penalty beyond 12 hexes (4 for fighters, 24 for bases).</li>
                    <li>Stealth fighters: Double range penalty beyond 5 hexes, and ballistic launches restricted beyond 5 hexes.</li>
                </ul>
            </li>
            <li>Stealth fighters cannot become fully invisible, they only benefit from jammer/no-lock effects.</li>
            <li>Note - These rules only cover the Stealth function for younger Bablyon 5 races, such as the Hyach.  
                For details of other factions' stealth mechanics, such as the Torvalus, see individual faction notes in <a href="https://fieryvoid.eu/factions-tiers.php" target="_blank" rel="noopener noreferrer">Fiery Void: Factions & Tiers</a></li>            
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="terrain" >Terrain</h3>
        <ul>
            <li><b>Asteroids (Single Hex Only):</b> Added in Create Game or manually from Terrain faction list. 
            They block line of sight and cause 1d10 * Speed raking damage to any unit that moves through them or ends on their movement on the same hex.  
            This damage is dealt immediately before Firing Phase.</li>           
            <li><b>Moons / Large Asteroids:</b> Describes anything larger than Asteroids above (e.g. occupy multiple hexes). 
            Units moving into their area automatically crash into it during the Pre-Firing Phase.</li>
            <li><b>Manual Placement:</b> If you want to have full control over where Terrain is placed, you can create a new player slot for yourself at game creation and 
            pick the terrain you want from the Terrain faction in Other.  
            Then, providing you have set an appropriately wide deployment zone, you can just place these like any other ship on Turn 1.</li>             
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="useful" >Useful Controls</h3>
        <ul>
            <li><b>W</b> - Show all Eelectronic Warfare (EW).</li>
            <li><b>X</b> - Show friendly EW.</li>
            <li><b>Y</b> - Show enemy EW.</li>
            <li><b>F</b> - Show friendly ballistic fire.</li>
            <li><b>E</b> - Show enemy ballistic fire.</li>
            <li><b>H</b> - Display hex numbers.</li>
            <li><b>R</b> - Toggle Ruler Tool.</li>            
            <li>Set a ship’s reactor to overload in Initial Orders to self-destruct at the end of the turn.</li>
            <li><b>Right-click actions:</b>
                <ul class="circle-list">
                    <li>Right-click Unit - select it (if yours) and open controls.</li>
                    <li>Right-click Weapon - select all similar weapons (e.g., all fighter guns).</li>
                    <li>Right-click Power Button - toggle all similar systems.</li>
                    <li>Right-click Firing Mode - change fire mode on all similar undeclared weapons.</li>
                    <li>Right-click Defensive Fire - enable defensive fire on all similar undeclared weapons.</li>
                    <li>Right-click Cancel Move - cancel all current moves for the unit.</li>
                    <li>Right-click Cancel Firing Order - cancel firing orders for all similar weapons.</li>
                    <li>Right-click Move Forward - move forward using all remaining movement.</li>
                </ul>
            </li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="disclaimer" >Disclaimer</h3>
        <ul>
            <p>This project is a non-commercial, fan-made adaptation inspired by Babylon 5 Wars, originally published by Agents of Gaming under license from Warner Bros. 
                This website, its content, and the associated game are not affiliated with, authorized by, endorsed by, or connected in any way to Warner Bros., Agents of Gaming, or any other rights holders. 
                ‘Babylon 5’ and all related names, logos, and material are trademarks and/or copyrighted properties of their respective owners..</p>
        </ul>
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