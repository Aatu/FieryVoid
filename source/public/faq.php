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
        <li><a href="#mechanics">INFO ON ADVANCED MECHANICS</a>
           <ul class="sub-list">
                <li><a href="#boarding">Boarding Actions & Marines</a></li>
                <li><a href="#called">Called Shots</a></li>
                <li><a href="#delayed">Delayed Deployment</a></li>                
                <li><a href="#enormous">Enormous Units</a></li>
                <li><a href="#jump">Jump Drives</a></li>
                <li><a href="#ruler">Ruler Tool</a></li>                    
                <li><a href="#stealth">Stealth Ships</a></li>
                <li><a href="#terrain">Terrain</a></li>  
                <li><a href="#useful">Useful Controls</a></li>                                                    
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
            <li>During the Firing Phase, Pods can attempt to attach to enemy ships in the same hex (and in arc) and deliver Marines to undertake a selection of missions (Sabotage, Capture Ship and Rescue).</li>
            <li>Pods first roll to attach, but this is automatic if they are moving faster than the target ship, so long as speed difference is not higher than pods thrust rating.  Llort have +1 to attach roll.</li>
            <li>Once attached, the Pod will roll again to deliver the Marines, with a base chance of 50% to successfully boarding the vessel.  Unsuccessful marines may be lost or return safely their pod.</li>                           
            
            <li>A number of modifiers can apply to the delivery roll, summarised below:
                <ul class="circle-list">
                    <li>+20% success - Yolu-specific bonus</li>
                    <li>+10% success - Elite marines / Llort / Target has Poor Crew / Directly boarding Primary section of target</li>
                    <li>-10% success - Narn or Gaim Defenders / Target has Elite Crew or Markab's Religious Fervor</li>
                    <li>Fighter: Offensive Bonus</li>
                </ul>
            </li>
            <li>Marines that successfully board a vessel will then attempt to carry out their intended missions from the end of the following turn.  These rolls also can attract several modifiers:
                <ul class="circle-list">
                    <li>+10% success - Elite Marines / Target vessel has Poor Crew</li>
                    <li>-10% success - Narn or Gaim Defenders / Target has Elite Crew or Markab's Religious Fervor</li>
                    <li>For Sabotage and Rescue missions only - Additional -10% for every two turns that Marines are active aboard the enemy vessel.</li>
                </ul>
            </li>
            <li>Details of each of the three types of marine mission are summarised below:
                <ul class="circle-list">
                    <li><strong>Capture: </strong>Marines will fight the defending marine contingents directly (defenders are shown in CnC tooltip!).  
                    This invovles two dice rolls, one to see if marines eliminate a defender (50% base chance) and a second to see if marines are eliminated (25% base chance).  
                    If the attacking marines manage to defeat all defenders, the enemy ship is immediately be disabled for the remainder of the battle so long as there are still at least one attacking marine unit on board.
                    </li>
                    <li><strong>Sabotage: </strong>Marines can either attempt to damage a specific system on an enemy ships (by making a called shot against it using the usual rules) or disrupt the ship more generally (e.g. minor damage to a Primary system, EW/Initiative/Thrust/Defence Profile penalties) by targeting it in the normal fashion.  
                    In both cases, Marines that are successfully delivered will roll on a d10 the following turn to see how successful their mission has been.  
                    Note - Marines which target a specific system and are successful in destroying it will then continue to Sabotage the ship generally providing they have not been eliminated.</li>
                    <li><strong>Rescue: </strong>For scenarios only, Marines will attach their pod and attempt to board as normal.  Then, from the following turn, the Combat Log will provide players with updates on the progress of their Rescue mission.</li>
                </ul>
            </li>

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
            <li>Ships equipped with Jump Drives can boost this system during Initial Orders to 'jump out' of the scenario at the end of the turn.  Doing will remove then from the rest of the scenario, and ships with damaged jump drives may be destroyed as they jump.  The latter situation will be reflected in the Combat Log for that turn.</li>            
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

        <h3 id="stealth" >Stealth Ships</h3>
        <ul>
            <li>Stealth ships are invisible at long ranges until they reveal themselves or are detected.</li>
            <li>They reveal themselves by using any EW ability except Defensive EW (DEW) or by firing weapons.</li>
            <li>Detection ranges:
                <ul class="circle-list">
                    <li>Base: 5x Sensor Rating</li>
                    <li>ELINT Ship: 3x Sensor Rating</li>
                    <li>Other Ship: 2x Sensor Rating</li>
                    <li>Fighter: Offensive Bonus</li>
                </ul>
            </li>
            <li>ELINT ships can spend EW points on 'Detect Stealth' to increase detection by +2 per point.</li>
            <li>Stealth ships can re-hide by breaking line of sight at the end of a turn and not firing weapons.</li>
            <li>If their scanner or computer system is destroyed, their defense increases by 15% for the battle.</li>
            <li>Jammer ability applies:
                <ul class="circle-list">
                    <li>Ships: double range penalty beyond 12 hexes (4 for fighters, 24 for bases).</li>
                    <li>Stealth fighters: no-lock bonus beyond 5 hexes, and ballistic launches restricted beyond 5 hexes.</li>
                </ul>
            </li>
            <li>Note: Stealth fighters cannot become fully invisible, only benefit from jammer/no-lock effects.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="terrain" >Terrain</h3>
        <ul>
            <li><b>Asteroids:</b> Added in Create Game or manually from Terrain faction list. They block line of sight and cause ramming damage to non-fighters moving through them (damage applies end of turn).</li>
            <li><b>Manual Placement:</b> If you want to have full control over where Terrain is placed, you can create a new player slot for yourself at game creation and pick the terrain you want from the Terrain faction in Other.  
            Then, providing you have set an appropriately wide deployment zone, you can just place these like any other ship on Turn 1.</li>            
            <li><b>Moons:</b> Larger than asteroids (multiple hexes). Units moving into a moon's area automatically crash into it during the Firing Phase.</li>
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

  </section>
</main>
</body>
</html>