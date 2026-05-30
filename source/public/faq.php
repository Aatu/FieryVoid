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

    <ul class="index-list">
        <li><a href="#general">GENERAL NOTES</a> </li>
        <li><a href="#differences">DIFFERENCES FROM BABYLON 5 WARS</a></li>                        
        <li><a href="#mechanics">ADVANCED MECHANICS</a>
           <ul class="sub-list">
                <li><a href="#boarding">Boarding Actions</a></li>
                <li><a href="#called">Called Shots</a></li>
                <li><a href="#delayed">Delayed Deployment</a></li>                
                <li><a href="#enormous">Enormous Units</a></li>
                <li><a href="#escorts">Fighter Escorts</a></li>
                <li><a href="#hangar">Hangar Operations</a></li>
                <li><a href="#jump">Jump Drives</a></li>
                <li><a href="#ladder">Online Ladder</a></li> 
                <li><a href="#mines">Mines</a></li>                               
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
            <li>A lot has been automated in Fiery Void compared to B5 Wars, such as dice rolling, and players have a little less control over certain minutiae, but overall the game is more streamlined.</li>
            <li>By default the game is played in a fixed, rectangular map. It does not enforce anything about the boundaries — it’s up to the players to ensure ships leaving the map behave as disengaged.</li>
            <li>Fiery Void does not enforce standard fleet design rules. Fleet requirement rules can however still be checked using the 'Check Fleet' button during Fleet Selection.</li>
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

        <h3 id="boarding" style="margin-top: 15px;">Boarding Actions</h3>
        <ul>
            <li>Many factions have access to Breaching Pods and Grappling Claws, which come equipped with marines that can undertake boarding actions.  
                During Fleet Selection, one pod can be purchased for every medium ship or HCV with hangar capacity for medium fighters, heavy fighter or assault shuttles, 
                two for every capital ship with these hangars, and four for enormous units or bases.  Assault ships (i.e., those vessels containing ‘Assault’ in their name) can carry double these allowances
                providing they have the hangar space to permit it.</li>
            <br>              
            <li>In battle, Pods can attempt to attach to enemy ships in the same hex and deliver Marines to undertake a selection of missions (Capture Ship, Sabotage and Rescue) during the Firing Phase.</li>
            <li>Pods will initially roll to attach on a d10 in the same way that normal weapons roll to hit enemies, 
                but the calculation is very different and success is automatic if they are moving faster than the target ship and the speed difference between the two units is not higher than pod's thrust rating. 
                If the speed difference to target is greater than pod thrust rating it is simply unable to attach. 
                If the target is moving faster, each point of speed difference is -10% chance to attach.  
                Pods cannot attach to ships with Advanced Armor and certain factions like Llort have +1 to attach rolls.</li>
            <li>There is a limit to how many pods can attach to enemy ships based on their size, 12 pods can attach to bases, 8 to Capital Ships, 4 to HCVs, 2 to Medium Ships and only 1 to LCVs and OSATS.   
                If more than this number try to attach and/or deliver marines then extra attacks over these limits will automatically fail. 
                In addition, if a Grappling Claw ship is already attached to a structure facing this will prevent any Breaching Pods from attaching to that location.</li>    
            <li>Breaching Pods will remain attached to a vessel's facing structure block until they choose to Detach in the Movement Phase, or the vessel is destroyed 
                (providing that the structure location the pod is attached to is NOT also destroyed).  If the structure block a pod is attached to IS destroyed before the Pod detaches, then the Pod is automatically destroyed.  
                While attached, Pods match speed and heading with their host ship, and suffer -10 Initiative penalty.  When they Detach Pods will automatically face away from the host ship to which they were attached.</li>
            <li>Units can shoot at attached pods, providing they are in arc of the structure location the pod is attached to, and will roll to hit them as normal. However any shot aimed at a pod will automatically hit the vessel it is attached to as well.</li>                 
            <br>               
            <li>After the attach roll, the Pod will attempt to deliver its marines by rolling on a d10 again on the following table, with a base chance of 50% to successfully board the vessel.  
                Depending on the roll, unsuccessful marines may be lost in the attempt or return safely to their pod.</li>                           
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
                    If the attacking marines manage to defeat all defenders, the enemy ship is immediately disabled for the remainder of the battle so long as there is still at least one attacking marine unit on board.
                    After a successful capture, one marine unit will remain on the ship and the remainder will return to attached Breaching Pods if available
                    </li>
                    <br>                     
                    <li><strong>SABOTAGE: </strong>Using this firing mode, Marines can attempt to damage a specific system on an enemy ships (by making a called shot against it using the usual rules) 
                    or, if Desperate Rules are in effect, Wreak Havoc on the enemy ship (e.g. inflict minor damage to a Primary system or penalties to ship's EW/Initiative/Thrust/Defence Profile) by targeting the ship itself, and not a specific system.   
                    In both cases, Marines will roll on a d10 the following tables to see how successful their mission has been:</li>
                    <li>Note - Marines which target a specific system and are successful in destroying it will then move to a Wreak Havoc mission providing they have not been eliminated.</li>                    

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
                    <li>NOTE - Marines which are successful in destroying their target system will return to an attached Breaching Pod if one is available, 
                        otherwise they will switch to a Wreak Havoc mission on the enemy vessel.</li>
                    <br>                    
                    <li><strong>RESCUE: </strong>For scenarios only, Marines will attach their pod and attempt to board as normal.  
                    Then, from the following turn, the Combat Log will update players on the progress of their Rescue mission each turn.</li>

                    <li><strong>RESCUE TABLE:</strong>
                        <ul class="circle-list">
                            <li>1-2 - Rescue is successful, Marines survive and return to a Breaching Pod if one is available.</li>
                            <li>3-4 - Rescue is successful, but Marines eliminated.</li>
                            <li>5-6 - Rescue fails this turn, Marines will try again next turn.</li>
                            <li>7+ - Rescue fails, Marines were eliminated.</li> 
                        </ul>
                    </li>                      
                </ul>
            </li>
            <br> 
            <li><strong>GRAPPLING CLAWS:</strong></li>              
            <li>Some ships are equipped with Grappling Claws, which largely use the same rules to attach and deliver Marines as described above, however there are a few notable differences outlined below:
                    <ul class="circle-list">
                        <li>The attaching unit cannot perform any maneuvers, but the host ship may do so, if it is of the same size or larger than the attached ship. 
                            The host ship's turn costs and turn delays are increased to the sum of both units’ values (e.g., a ship with a 1/3 turn cost attached to one with a 2/3 turn cost would produce a total turn cost of around 1). 
                            The target unit moves the conglomerate group when its turn arrives in the initiative sequence, and it may maneuver normally.</li>
                        <li>No more than one ships can use grappling claws to attach to a medium ship or heavy combat vessel (shipSizesClasses 1 and 2). 
                            Two grapple units can attach to a capital ship, but both must be on opposite ends. One unit can attach to each section of an enormous base. </li>
                        <li>If the structure the attached ship has grappled is destroyed during the battle (or if the host ship itself is destroyed), the claw‐equipped vessel is broken free and any claws that are still attached are destroyed. 
                            The destruction of either ship will not affect the other.</li>
                        <li>Once the attacking ship has attached itself to the target, all fire by weapons through the claw’s firing arcs is blocked by the target’s hull (they cannot fire at the target unit for safety reasons). 
                            If the attached unit’s weapons are capable of firing into other arcs or into a wider range, they can shoot at targets in those positions. 
                            The unit to which the ship is attached is not prevented from firing any weapons (except at the attached unit)</li> 
                        <li>If a Grappling Claw is destroyed, and it still had Marine units available, then these will be transferred to any surviving Grappling Claws on the ship.
                            Marine Units held in Grappling Claw systems will count towards the total marines available for defence if an opponent tries to capture the ship.</li>
                        <li>The automatic extra hit on the host ship does not apply to shots fired at attached Grappling Claw ships.</li>      
                        <li>Grappling Claw ships do not automatically face away from their host ship when they detach.</li>                                                  
                    </ul>                                               
                </li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3  id="called">Called Shots</h3>
        <ul>
            <li>Called Shots are possible in Fiery Void, providing the weapon selected is able to do so. For example ballistic weapons cannot do so, with the exception of the Kor-Lyans Limpet Bore Torpedo.</li>  
            <li>To make a called shot, select the weapon you want to fire then bring up the enemies SCS by right-clicking on their ship. Find the system you wish to target and click on it.  
            Providing all the other conditions are met e.g. system can be targeted by called shots, is in arc of the firing ship etc.  
            This system will now be targeted and your weapon icon will highlight orange as usual to indicate it's locked in.</li>
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


        <h3 id="escorts" >Fighter Escorts</h3>
        <ul>
            <li>Fighter units can escort friendly ships that are in the same hex in order to use their guns to help intercept ballistic weapons targeted at that ship(s).</li>
            <li>To escort a ship, the fighter unit must start AND end its movement in the same hex as the ship they are escorting.  Where this is the case,
                the fighters will use their weapons to intercept ballistics on behalf of the ship providing all other conditions of intercept are true e.g. 
                their weapons have an intercept rating, the incoming shot is in arc, the fighters are not jinking etc.        
            </li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


        <h3 id="hangar" >Hangar Operations</h3>
        <ul>
            <li>Carriers can launch and recover their fighters, shuttles and other small craft <em>during</em> a battle, as well as start them docked inside the carrier
                rather than placing them directly on the map. The options to do so appear automatically on any ship with hangar capacity.</li>
            <br>

            <li><b>Default shuttles:</b>
                <ul class="circle-list">
                    <li>A ship's hangar capacity that is not already allocated to fighters/assault shuttles or breaching pods capacity is automatically filled with <b>Shuttles</b> (or <b>Minesweeping Shuttles</b> on ships with a minesweeper bonus).</li>              
                    <li>Hover over a <b>Hangar</b> system in the ship's SCS to see its starting contents, shown as e.g. <i>"Carrying: 2 / 14 slots"</i> along with a list of the stored craft.</li>
                </ul>
            </li>
            <br>

            <li><b>Deployment Phase:</b> During the Deployment Phase you can choose to deploy fighters in a ship's hangar by clicking on the ships hex and selecting the blue 'Dock...' option.  
                        Or, if the fighters are already deployed to the carrier ship's hex you can click on the 'Deploy Flights in Hangar' tooltip button on the ship, or click on the Hangar system icon in the ship's SCS window.  
                        Reinforcement flights arriving on later turns can dock into an already-deployed carrier the same way.
                        Note, some fighters such as Orieni Hunter-Killers MUST deploy in hangars at the start of a game.</li>   

            <li><b>Launching craft (Firing Phase):</b>
                <ul class="circle-list">
                    <li>Select one of your hangar ships and click the <b>Launch</b> tooltip button in its tooltip menu.  A dialog lets you pick which stored craft to launch, and how many.</li>
                    <li>Shuttles are stored individually but can be launched as a single flight of 1–6.  Fighters launch as their stored flight (subject to the usual partial-flight rules).</li>
                    <li>The order resolves at the <b>end of the turn</b>: the new flight appears in the carrier's hex, matching its heading and speed, facing the carrier's facing (plus an offset for side hangars).</li>
                    <li>You cannot launch from a carrier that is pivoting or rolling that turn.</li>
                </ul>
            </li>
            <br>

            <li><b>Recovering / Docking craft (Firing Phase):</b>
                <ul class="circle-list">
                    <li>Select one of your fighter flights and click the <b>Dock</b> button, or use the <b>Recover</b> tooltip button on the carrier to pull craft in from the carrier's side.</li>
                    <li>A flight can only dock into a carrier that is in the <b>same hex</b>, on the <b>same heading</b>, and at a compatible speed (the carrier must be at least as fast as the flight,
                        and the speed difference must be within the flight's thrust rating).</li>
                    <li>The carrier must have a free hangar slot of a compatible type, must not be pivoting or rolling, and the hangar must not be destroyed.</li>
                    <li>If more than one eligible carrier is in the hex, you'll be asked to pick one.  If a hangar can't hold the whole flight, you'll be offered a split — dock some now and leave the rest in space.</li>
                    <li>Like launching, docking resolves at the end of the turn.</li>
                </ul>
            </li>
            <br>

            <li><b>Launch / land budget:</b> Each hangar has a per-turn capacity (its <i>output</i>) that is <b>shared</b> between launching and landing.
                A hangar with an output of 6 can launch 6 craft, recover 6 craft, or any combination (e.g. launch 4 and recover 2) in a single turn.</li>
            <br>

            <li><b>Initiative penalties:</b> Operating hangars is disruptive.  A carrier that launches and/or recovers craft suffers <b>−20 Initiative</b> on the following turn (just once, no matter how many craft it moved).
                A freshly launched flight suffers <b>−50 Initiative</b> on the turn after it launches.  These penalties clear automatically.</li>
            <br>

            <li><b>Compatible hangar types:</b> Craft can only be stored in slots that fit them.  Universal fighter bays accept any size of combat fighter (and shuttles),
                but Assault Shuttles and Breaching Pods need their own dedicated slots, and custom-named fighters (e.g. Thunderbolts) are limited by each carrier's individual capacity for that type.</li>
            <br>

            <li><b>Hangar damage:</b> If a hangar takes damage, stored craft are destroyed along with it (empty slots and shuttles are lost first, then the cheaper craft).</li>
            <br>

            <li><b>Carrier destruction &mdash; hangar craft may escape:</b> When a carrier is destroyed (other than by successfully jumping to hyperspace),
                some of its docked fighters and shuttles may scramble out before the wreck goes up.  The game rolls a d20:
                <ul class="circle-list">
                    <li>1&ndash;5: no craft escape.</li>
                    <li>6&ndash;10: one quarter of docked craft escape (round down).</li>
                    <li>11&ndash;18: one half escape (round down).</li>
                    <li>19&ndash;20: all docked craft escape.</li>
                </ul>
                Only combat fighters, armed shuttle variants, Assault Shuttles, and Breaching Pods are eligible for possible escape. Escapees are auto-selected by combat value (most expensive craft escape first).
                They appear in the carrier's final hex with its heading and speed, facing the carrier's final facing plus the originating hangar's launch direction, and suffer
                the standard &minus;50 Initiative penalty on their first acting turn (as if freshly launched).  Craft on a carrier that successfully jumped to hyperspace are
                NOT subject to this roll &mdash; they ride along with the jump and retain their full combat value.</li>
            <br>

            <li><b>Rearming docked craft:</b> A flight that remains docked for a full turn will automatically rearm its limited-ammo weapons before relaunching.
                <ul class="circle-list">
                    <li><strong>Matter weapons</strong> (SlugCannon, Gatling Gun, etc.): restore 1 round per weapon per turn while docked, up to the weapon's starting load.  This is free and automatic — no carrier cost.</li>
                    <li><strong>Missiles</strong>: require a pre-purchased <strong>Ballistic Ordnance Reserve</strong> enhancement on the carrier (see below).  
                    One missile per fighter per turn is restocked automatically, most expensive missile type first, drawing from the shared pool.</li>
                    <li><strong>Marines (Breaching Pods)</strong>: require a pre-purchased <strong>Extra Marine Contingents</strong> enhancement on the carrier (see below).  One marine unit per pod per turn is restocked automatically while docked, drawing from the shared marine pool.</li>
                    <li>The turn the flight docks does not count — rearming begins on the first full turn spent inside the hangar.</li>
                    <li>When a flight is split on relaunch, the fighters with the most missiles (i.e., those that were restocked) are extracted first into the launched flight.</li>
                </ul>
            </li>
            <br>

            <li><b>Ballistic Ordnance Reserve:</b> Carriers in missile-capable factions can purchase an Ordnance Reserve enhancement in the Fleet Lobby, up to 200 points.
                <ul class="circle-list">
                    <li>The pool is <strong>shared across the whole carrier</strong> and is shown in the Hangar system tooltip as <i>"Ordnance Reserve: X / Y pts"</i>.</li>
                    <li>Each turn a missile or torpedo flight is docked (and has been in for a full turn), the carrier spends points from the reserve equal to the <strong>PV of the missile type</strong> being restocked.  The most expensive type is always refilled first.</li>
                    <li>The pool is <strong>one-way</strong>: spent points does not regenerate during the battle.</li>
                    <li>Only carriers with combat fighter hangar slots (i.e. not small hangars with only shuttles) have access to the Ordnance Reserve option in the Lobby.</li>
                </ul>
            </li>
            <br>

            <li><b>Extra Marine Contingents:</b> Any ship can purchase Extra Marine Contingents in the Fleet Lobby — a pool of additional marine units used to restock docked Breaching Pods.
                <ul class="circle-list">
                    <li>Each contingent costs <strong>10 points</strong> and represents a single marine unit.</li>
                    <li>Limit per ship: <strong>1% of the ship's base Combat Point value</strong>, rounded up (e.g. a 600-PV ship can buy up to 6 contingents).</li>
                    <li>The pool is <strong>shared across the whole carrier</strong> and is shown in the Hangar system tooltip as <i>"Marine Contingents: X / Y"</i>.</li>
                    <li>Each turn a Breaching Pod flight is docked for a full turn it is able to restock one marine unit per pod from the Marine Contingent pool, up to each pod's starting load (including any Extra Marine Units bought as an enhancement).</li>
                    <li>The pool is <strong>one-way</strong>: spent points do not regenerate during the battle.</li>
                </ul>
            </li>
            <br>

            <li><b>Catapults:</b> Some carriers are equipped with a Catapult instead of (or in addition to) a standard hangar.  A catapult is a fixed forward-firing launch rail designed to hold and deploy a single superheavy fighter.
                <ul class="circle-list">
                    <li>A catapult holds <strong>exactly one</strong> superheavy fighter — no other craft may launch from or dock into it.</li>
                    <li>The catapult's box count represents structural hit points only, but not additional capacity, so extra boxes do not hold shuttle as i the case with normal Hangars.</li>
                    <li><strong>Launching:</strong> A catapult always launches its fighter directly forward (at the carrier's current facing).  Launching from a catapult applies <strong>no</strong> initiative penalty 
                    — neither the −50 that a freshly launched flight would normally receive, nor the −20 applied to the carrier.  Launching works even if the catapult is damaged or destroyed.</li>
                    <li><strong>Landing / recovery:</strong> The fighter may only dock back into the catapult if it approaches the carrier's hex from the <strong>rear</strong> — the flight's heading must match the carrier's facing (the fighter overtakes the carrier from behind).  
                    Like launching, recovery works regardless of catapult damage, but the carrier still receives the standard −20 initiative penalty on the following turn.</li>
                    <li><strong>Landing on a damaged catapult:</strong> If any catapult boxes are destroyed at the time of landing, the recovering fighter takes damage equal to the number of destroyed boxes.
                        <ul class="circle-list">
                            <li>If the fighter survives, it is stored with its damage intact and can be relaunched normally on a later turn.</li>
                            <li>If the fighter is destroyed by the landing damage, it is still counted as recovered and stored — but it can <strong>never be relaunched</strong>.  
                            The wreck permanently occupies the catapult bay for the rest of the battle; no replacement fighter can be loaded.</li>
                        </ul>
                    </li>
                </ul>
            </li>

            <li><b>Fighter Rails:</b> Some carriers are equipped with external Fighter Rails instead of, or in addition to, standard hangar bays.
                <ul class="circle-list">
                    <li><strong>Capacity:</strong> The rail's structure is also its capacity — a 6 structure rail carries up to 6 fighters.
                        However, rail boxes are part of the associated structure block on the carrier, not a separate HP pool, so they do not add extra hit points to the ship.</li>
                    <li><strong>Compatible craft:</strong> Fighter Rails hold combat fighters only (the type declared in the ship file, e.g. light fighters).
                        They do not hold shuttles, assault shuttles, or breaching pods.</li>
                    <li><strong>Launching:</strong> Each fighter on a rail launches independently, like a normal hangar.
                        The carrier suffers the standard <strong>−20 Initiative</strong> penalty on the following turn, but the launched flight receives
                        <strong>no −50 Initiative penalty</strong> — fighters launch directly from the rail with no disorientation.</li>
                    <li><strong>Landing / recovery:</strong> Fighters dock back onto the rail exactly as they would into a normal hangar — same hex, matching heading and speed.
                        The carrier still receives the −20 Initiative penalty on the following turn.</li>
                    <li><strong>Reload cadence:</strong> Because the airlocks connecting rails to the carrier's interior are narrow, rearming a docked fighter takes
                        <strong>twice as long</strong> as a standard hangar — a docked flight begins rearming on the second full turn inside the rail.</li>
                    <li><strong>Structure-coupled destruction — damage crit (1d20):</strong> Whenever the structure block a rail is attached to takes damage in a turn,
                        an unmodified d20 is rolled at the end of that turn.  On a natural <strong>16–20</strong> one entire rail on that structure is destroyed
                        (the smallest remaining rail is chosen automatically).  Any fighters on the destroyed rail immediately attempt to escape using the standard
                        carrier-destruction escape table (see <em>Carrier destruction</em> above), but escapees <strong>do</strong> suffer the −50 Initiative penalty
                        on the following turn — a forced evacuation is not a clean launch.</li>
                    <li><strong>Structure-coupled destruction — structure block destroyed:</strong> If the structure block itself is destroyed entirely, all rails attached to it
                        are simultaneously destroyed.  Each rail's fighters independently attempt escape using the same d20 table.</li>
                    <li><strong>Deployment phase docking:</strong> Flights too large for any single rail are automatically distributed across multiple rails
                        (e.g. a 9-fighter flight onto a carrier with 6-box and 3-box rails will split 6 + 3 automatically).</li>
                </ul>
            </li>
            <br>
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

        <h3 id="mines" >Mines & Minesweeping</h3>
        <ul>
            <li>There are three types of mines in Fiery Void, Captor, Direct Energy Weapons (DEW) and Proximity.  These are described in more detail below.</li>
            <li>Buying & Deploying Mines:
                <ul class="circle-list">
                    <li>In game where the 'Mines Allowed' option has been enabled in Create Game screen you can purchase mines in Fleet Selection from your Facion's list.</li>
                    <li>Unlike ships, mines can be bought in batches and when you click 'Add to Fleet' you will be able to choose the number of mines of that type you wish to buy, 
                        along with any Enhancements you wish to include (see in <a style="font-size: 14px;" href="./ammo-options-enhancements.php" target="_blank" rel="noopener noreferrer">Ammo, Options & Enhancements</a> for more details on Mine Enhancements).</li>
                    <li>There is a 100 point premium for taking any mines at all, plus an additional +10% to the unit cost of all mines per type of mine taken after the first (e.g. from the three type Captor, DEW and Proximity).</li>  
                    <li>Once the game starts you can choose to place mines individually or use the 'Deploy Minefield' button to place any number of mines randomly within a selected area.  
                        You can deploy mines anywhere within the map, so long as it's not within 10 hexes of an enemy deployment zone.</li>                                                          
                </ul>
            </li>              
            <li>Once deployed, mines will initially be stealthed and won't become visible to enemies until they attack, or are detected.  
                On the turn that they are deployed or spawned you will have the opportunity to tailor their ranges for 
                Captials/HCVs, MCV/LCVs, Fighters from 0 up to their maximum range. 
                You do this by clicking on the mine weapons system icon during Deployment/Pre-Turn Orders phase.  
                Once set these ranges will apply for the rest of the game, and if you choose not to set the ranges they will default to their maximum range.  
                You can set the ranges of all mines of the same type using the propagate buttons in the Mine Settings menu.</li>
            <li>You can detect mines by applying EW points to 'Detect Mines' in the Initial Orders phase, an EW option that will only appear if the opponent has mines in the game.  
                Fighters and Shuttles can do this also, converting their Offensive Bonus (OB) in to 'Detect Mines' EW points, at a cost of 10 OB per point of 'Detect Mines' EW 
                (Note - any OB used in this way will not be available for firing later in the turn).
                The detection calculation depends on a number of factors e.g.
                <ul class="circle-list">
                    <li>Number of 'Detect Mines' EW,</li>
                    <li>Minesweeper Bonus,</li>                    
                    <li>Distance to currently hidden enemy mine,</li>
                    <li>The mine's signature'.</li>
                </ul>
            </li>
            <li>Detection is done at the END of each committed Movement Phase segment (i.e. not DURING a unit's movement, to prevent players detecting a mine and then cancelling back their moves etc) 
                and mines will be revealed if your 'Detect Mines' EW + Minesweeper Bonus is GREATER THAN the Distance to the mine + the mine's signature.</li>
            <li>Once detected you will not automatically know what type of mine has been discovered, to get this information you'll need to scan it with at least 1 OEW after initial detection.</li>

            <li>To shoot at mines, in addition to the usual modifiers there are some unique modifiers.                
                <ul class="circle-list">
                    <li>All mines have a basic profile of 60 (minus their signature value * 5).  So a mine with Signature 3 would have a basic profile of 45,</li>                    
                    <li>The firing ship's 'Detect Mines' EW for that turn is added to any inherent Minesweeper Bonus it has and acts as a 'General Lock On'.</li>
                    <li>If your 'General Lock On' score is greater than the Distance and Signature of the mine added together, this is added to your hit chance.  
                        E.g. If you have 10 EW allocated to mine detection and you spot a mine with a Signature of 1 at range 3, the minimum detection needed is 4 EW. Therefore, the
                        detecting ship has a +6 (multiplied by 5) to hit the mine.  So you'd gain +30% to hit, but would still have a double range penalty if there wasn't a OEW lock on the mine as per usual.</li>
                    <li>Unless a mine is equipped with the Command Controller enhancement, it will not use its weapons to intercept.</li>    
                </ul>
            </li>                        
            
            <li>All mines will automatically attack the first unit that comes into their range, even allies unless you have purchased the Identify Friend or Foe enhancement for the mine or mine launcher.</li>
            <li>Captor Mines:
                <ul class="circle-list">
                    <li>These mines a range within which they will launch at the first viable target during movement, 
                        and then resolve the attack as ballistic weapon during Firing Phase.  
                        As such, these type of mines can be intercepted in the same way as other ballistic weapons.</li>
                    <li>Units equipped with Jammers halve the range at which a captor mine will attack them.</li>
                    <li>A captor mine is destroyed whether it hits or not.</li>  
                    <li>Both the Kor-Lyan and WotCR Abbai operate ships with Ballistic Mine Launchers, these weapons can leave lingering Captor Mines if they do not immediately find a target.</li>                                                          
                </ul>
            </li>  
            <li>Proximity Mines:
                <ul class="circle-list">
                    <li>These mines a range within which they explode and damage the first viable target during movement. They resolve this attack just before the Firing Phase, similar to Terrain collisions.</li>
                    <li>Proximity mines automatically hit their target and are destroyed after they attack.</li>                    
                </ul>
            </li> 
            <li>Direct Energy Weapon (DEW) Mines:
                <ul class="circle-list">
                    <li>These mines come equipped with weapons and will fire these automatically at the first viable target it encounters during the movement phase, 
                        these attacks are then resolved normally during the Firing Phase.</li>
                    <li>DEW mines become detected once they fire, and they must normally fire all their weapons at the same target.  
                        AS they have no EW of their own, they don't not benefit from having a weapon lock, but do get an accuracy bonus to their shot.</li>
                    <li>Once they have been detected their signature reduces to a lower value.</li>                        
                </ul>
            </li>                              
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


        <h3 id="ruler" >Ruler Tool</h3>
        <ul>
        <li>This tool helps players measure distance between any two hexes on the game map, and also indicates whether line of sight is blocked or not between the two hexes chosen.</li>
        <li>To use the tool, just activate it by pressing 'R' key, or clicking on the 'eye' button on the right-hand side of the screen. 
            Once activated, left click on the hex from where you would like it to start measuring from, then move the mouse around the map to check distance and line of sight to other hexes.  
            Right-clicking with the mouse will reset the start hex.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="savedfleets" >Saved Fleets</h3>
        <ul>
            <li><b>Saving a Fleet:</b> While in Fleet Selection you can save any fleet that you are making for later.  Simply select your fleet as normal and when you're happy with your force click the 'Save Fleet' button and confirm your choice.
             Your saved fleet will then become available in this and future sessions (providing you have sufficient points available) via the 'Load a Saved Fleet' dropdown button.</li>
            <li><b>Sharing Fleets and Loading with ID:</b> Each saved fleet in Fiery Void has a unique ID, providing the fleet is marked as 'Shared' (and you can set this when you save a fleet or toggle it with the padlock symbol) you can give this ID to another player. 
            They can then load the saved fleet by entering the fleet ID in the 'Load fleet by #ID' field and pressing Enter key.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


        <h3 id="skindancing" >Skin Dancing</h3>
        <ul>
            <li>Skin dancing refers to a maneuver wherein a unit flies only meters above the surface of a large unit or base. It is a very dangerous maneuver only performed by the most agile
                of ships.</li>
            <li>Any unit that is able to Skin Dance will automatically attempt to do so when it ends its movement on the same hex as an Enormous Unit 
                (not a Terrain unit though, where it will suffer the normal collision rules covered in the 'Terrain' section).  In order to be eligible to Skin Dance a unit must meet the following criteria:</li>
                <ul class="circle-list">
                    <li>Must be a Medium Ship or smaller, and must be classified as agile (fighters and shuttles are considered agile for this purpose).  There are some rare execptions to this rule e.g. Torvalus capital ships.</li>
                    <li>Skin Dancing unit cannot be stationary, and the Enormous Unit cannot be moving at more than Speed 5.  
                    If the Enormous unit is moving the dancing ship must be moving in the same direction or the exact opposite direction.</li>
                    <li>Must not be a unit designed to ram e.g. Orieni Hunter-Killer drones.</li>
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
                If the roll result is 21 or higher, skin-dancing ship smashes into the hull of the Enormous unit. 
                For fighters, one fighter at random crashes into the hull as above while the others break away. 
                The survivors cannot fire (even defensively) or guide weapons on that turn as they are too busy pulling out of the maneuver.</li>
            <li>If skin dancing is successful, the unit cannot be fired upon by enemy units unless they also skin dance over the same target, 
                the exception being ballistic weapons that were launched at the skin dancing ship earlier in the turn.  
                The vessel you are skimming over also cannot fire at you, and cannot fire defensively against your weapons, because you’re inside its weapon’s tracking zones.</li>
            <li>Finally, any of your forward firing weapons (those that can legally fire into the row of hexes directly ahead of your ship) automatically roll the best result on their hit dice e.g. they will roll a 1 on a d100 meaning they automatically hit in almost all cases.  
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
            <li>Where an opponent has stealth ships present, ELINT ships can spend EW points on 'Detect Stealth' to increase detection range by +2 per point invested in this way.</li>
            <li>After being detected, Stealth ships can become undetected by breaking line of sight with ALL enemy vessels at the end of a turn and not firing any weapons.</li>
            <li>If their scanner or computer system is destroyed, their defense increases by 15% for the battle.</li>
            <li>Stealth ships also receive the same benefits as Minbari Jammer-equipped ships from a certain distance:
                <ul class="circle-list">
                    <li>Ships: Double range penalty beyond 12 hexes (4 for fighters, 24 for bases).</li>
                    <li>Stealth fighters: Double range penalty beyond 5 hexes, and ballistic launches restricted beyond 5 hexes.</li>
                </ul>
            </li>
            <li>Stealth fighters cannot become fully invisible, they only benefit from jammer/no-lock effects.</li>
            <li>Note - These rules only cover the Stealth function for younger Babylon 5 races, such as the Hyach.  
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

        <h3 id="useful" >Hot Keys & Useful Controls</h3>
        <p>Below are some useful controls to know to help get the best out of Fiery Void.  
            Note - On touchscreens Right-Click functions are using duplicated by a long depress on your screen.</li>
        <ul>
            <li><b>W</b> - Show all Electronic Warfare (EW).</li>
            <li><b>X</b> - Show friendly EW.</li>
            <li><b>Y</b> - Show enemy EW.</li>
            <li><b>F</b> - Show friendly ballistic fire.</li>
            <li><b>E</b> - Show enemy ballistic fire.</li>
            <li><b>H</b> - Display hex numbers.</li>
            <li><b>R</b> - Toggle Ruler Tool.</li>            
            <li><b>Right-click actions:</b>
                <ul class="circle-list">
                    <li>Right-click Unit - Select it (if yours) and open controls.</li>
                    <li>Right-click Weapon - Select all identical weapons (e.g., all fighter guns).</li>
                    <li>Right-click Power Buttons - Toggle On/Off all identical systems.</li>
                    <li>Right-click Firing Mode - Change fire mode on all similar undeclared weapons.</li>
                    <li>Right-click Defensive Fire - Enable defensive fire on all similar undeclared weapons.</li>
                    <li>Right-click Cancel Move - Cancel all current moves for the unit.</li>
                    <li>Right-click Cancel Firing Order - Cancel firing orders for all similar weapons.</li>
                    <li>Right-click Move Forward - Move forward using all remaining movement.</li>
                </ul>
            </li>
            <br>
            <li><b>During Deployment Phase:</b>
                <ul class="circle-list">
                    <li>Shift & Left-click - Instantly deploy a ship to a hex already occupied by other units (Note- long press on touchscreen).  
                        Must still be a valid deployment e.g. Fighters and Mines can stack with ships, but ships cannot be deployed with other ships.</li>
                    <li>Double Left-click - Instantly select a single unit in a hex (if there are multiple units in the hex you'll still need to select from list), when you already have a deployable unit as your selected ship.  
                        Makes it slightly quicker to select units when you have fighters or mines as you selected ship.</li>
                    <li>Right-click on + and - Movement Icons -  Will set the speed of the ship to maximum (Speed 10) or the minimum (Speed 0) in a single click.</li>                        
                </ul>
            </li>
            <br>            
            <li><b>During Initial Orders Phase:</b>
                <ul class="circle-list">
                    <li>Right-clicking Electronic Warfare (EW) Add Button - Sets that EW type to the max available amount (Note- long press on touchscreen).</li>
                    <li>Right-clicking Electronic Warfare (EW) Remove Button - Sets that EW type to zero (Note- long press on touchscreen).</li>                    
                </ul>
            </li>                                      
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="disclaimer" >Disclaimer</h3>
        <ul>
            <p>This project is a non-commercial, fan-made adaptation inspired by Babylon 5 Wars, originally published by Agents of Gaming under license from Warner Bros. 
                This website, its content, and the associated game are not affiliated with, authorized by, endorsed by, or connected in any way to Warner Bros., Agents of Gaming, or any other rights holders. 
                ‘Babylon 5’ and all related names, logos, and material are trademarks and/or copyrighted properties of their respective owners.</p>
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