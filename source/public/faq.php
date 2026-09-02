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
  <!-- Shared fv design tokens (roadmap item 6): MUST load before every other stylesheet. -->
  <link href="<?php echo AssetLoader::getAssetUrl('styles/tokens.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/base.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/lobby.css'); ?>" rel="stylesheet" type="text/css">
  <link href="<?php echo AssetLoader::getAssetUrl('styles/gamesNew.css'); ?>" rel="stylesheet" type="text/css">    
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
                <li><a href="#savedfleets">Battle Damage &amp; Saving Fleets</a></li>
                <li><a href="#boarding">Boarding Actions</a></li>
                <li><a href="#called">Called Shots</a></li>
                <li><a href="#delayed">Delayed Deployment Slot</a></li>
                <li><a href="#notifications">Discord Turn Notifications</a></li>
                <li><a href="#elint">ELINT &amp; Electronic Warfare (EW)</a></li>
                <li><a href="#enormous">Enormous Units</a></li>
                <li><a href="#escorts">Fighter Escorts</a></li>
                <li><a href="#hangar">Hangar Operations</a></li>
                <li><a href="#infopanel">Info Panel</a></li>
                <li><a href="#interception">Interception</a></li>
                <li><a href="#jump">Jump Drives</a></li>
                <li><a href="#ladder">Online Ladder</a></li> 
                <li><a href="#mines">Mines</a></li>                               
                <li><a href="#ruler">Ruler Tool</a></li>
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

        <h3>Electronic Warfare (EW) Summary</h3>
        <ul>
            <li>CCEW: Provides a lock on all enemy fighters within 10 hexes.</li>
            <li>Most activities requiring multiple EW points can now be done in fractions. For example, with Blanket DEW (5% per 4 points allocated), you may allocate 3 points and get around 4% Blanket DEW.</li>
            <li>Disruption EW: Enemy target locks are affected equally by the fraction of DEW used E.g. using 1 point of DIST against 3 separate OEW locks reduces each by 0.33 points.</li>
            <li>Disruption EW: CCEW is treated as one OEW target and affected appropriately.</li>
            <li>Disruption EW: Target locks weaker than 1 point but worth at least half a point provide half-lock (e.g., range penalties multiplied by 1.5 instead of being doubled).</li>
            <li>DEW Below 0: The B5 Wars rule about only a ship’s own DEW being able to bring its profile below 0 does not apply in Fiery Void.</li>
            <li>Electronic Warfare is covered in more details in the 'ELINT &amp; Electronic Warfare (EW)' section below</li>
        </ul>

        <h3>Movement</h3>
        <ul>
            <li>Snap turn: Implemented as consecutive but separate turns. Turn delay is taken from the first turn only (shorten the first segment to shorten the snap turn).</li>
            <li>Extended turn: Movement turns across multiple game turns are not implemented.</li>
        </ul>

        <h3>Firing and Weapons</h3>
        <ul>
            <li><strong>Firing Order of Weapons:</strong> Each weapon has a priority number determining firing order, except ramming attacks which always fire first. Players cannot influence this order.</li>
            <li><strong>Choosing Target Section:</strong> Automatically chosen based on target profiles and remaining structure. Fire from the same ship always hits same section, primary sections are avoided if alternatives exist.</li>
            <li><strong>Choosing System Hit:</strong> In-arc systems are prioritized before off-arc ones. Direction of damage is assumed to match the direction of the shot.</li>
            <li><strong>Targeting Fighters:</strong> The algorithm minimizes expected overall damage. You can manually target individual fighters, but it counts as a called shot with a -40% to-hit penalty.</li>
            <li><strong>Fighter Dropouts:</strong> These occur during critical hits resolution (after firing at ships).</li>
            <li><strong>Interception:</strong> Handled automatically, prioritizing the most powerful incoming shots. 1-turn recharge weapons intercept automatically if not fired; others require manual selection.</li>
            <li><strong>Ballistic Damage:</strong> Resolved within during normal fire resolution at the end of the turn, instead of a separate ballistic damage subphase.</li>
            <li><strong>Fighter Escorts:</strong> Fighters can intercept ballistics for ships they are escorting if they start and end their movement in the same hex.</li>
            <li><strong>Ballistics & Jammers:</strong> Power and missile launches are simultaneous. Disabling jammers affects missile launches from the next turn, not the current one.</li>
            <li><strong>Multi-mode Weapons:</strong> Simplified — may switch freely (Guardian Array) or can be boosted for free during Initial Orders (EA Interceptors).</li>
            <li><strong>Piercing Attacks:</strong> Damage is split into 3 parts (or 2 if entry and exit are through the same section). Piercing vs. MCVs is reduced by 10%. EW penalties are already included in fire control values.</li>
            <li><strong>Firing Modes:</strong> Always visible, meaning opponents can see missile types immediately.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h2 id="mechanics">INFO ON ADVANCED MECHANICS</h2>

        <h3 id="savedfleets" >Battle Damage &amp; Saving Fleets</h3>
        <p>A saved fleet is a reusable shopping list: the units you bought, the enhancements and ammunition you gave them, and &mdash; if you want it &mdash;
           the <b>battle damage and critical effects they are carrying</b>. That last part means you can save a fleet at the end of one battle and start the
           next one with exactly the ships that survived it, dents and all, which is what makes linked scenarios and campaigns possible.
           You can also apply damage by hand in Fleet Selection, if you want to set up a scenario without having fought the previous game.</p>

        <ul>
            <li><b>Saving a Fleet from Fleet Selection:</b> Build your fleet as normal, then click either of the <b>SAVE FLEET</b> buttons &mdash; one sits
                at the top of the screen next to the fleet loader, the other at the bottom of the ship-buying panel. Give the fleet a name and click Save.
                Your saved fleet is then available in this and every future game (as long as you have enough points to buy it) from the
                <b>LOAD A FLEET</b> dropdown.
                <ul class="circle-list">
                    <li>Terrain is never saved, even if you placed it yourself &mdash; a fleet list is ships, and the scenario provides the scenery.</li>
                    <li>Mines bought in bulk are saved as a bulk, so ten mines come back as ten mines rather than one.</li>
                </ul>
            </li>

            <li><b>Loading a Saved Fleet:</b> Pick a slot, open the <b>LOAD A FLEET</b> dropdown and click the fleet you want. The whole fleet is bought
                into your slot in one go. If the fleet costs more points than you have left, or contains units the scenario does not allow (mines in a
                no-mines game, for example), the load is refused outright rather than partly applied.</li>

            <li><b>Sharing Fleets and Loading with an ID:</b> Every saved fleet has a unique ID number. If the fleet is marked as shared &mdash; you can set
                this when you save it, or toggle it later with the padlock symbol in the dropdown &mdash; you can give that ID to another player. They load
                it by typing the number into the <b>Load Fleet by #ID</b> box and pressing Enter (or Go, on a phone). Fleets that have not been shared can
                only be loaded by the player who saved them.</li>

            <li><b>Saving a Fleet from a Game in Progress:</b> In any game, open the <b>SAVE FLEET</b> tab along the top of the log panel (next to LOG,
                DECLARATIONS and GAME CHAT). It tells you how many of your units will be saved, then the <b>Save Current Fleet</b> button writes them out
                as a normal saved fleet &mdash; but with all their accumulated damage and critical effects attached.
                <ul class="circle-list">
                    <li>The tab is available in <b>every phase</b>, and in finished games and replays too, so you can go back to a completed battle and
                        save the winning side's survivors.</li>
                    <li>Only <b>survivors</b> are saved. Destroyed ships, ships that docked into a hangar and left the battle, and mines you have already
                        laid are all left out; the summary line tells you how many were excluded.</li>
                    <li>Fighters are never saved as wrecks. If a flight of six lost two craft, it is saved as a flight of <b>four</b> damaged fighters
                        &mdash; and because you are buying fewer craft next time, it costs proportionally less.</li>
                    <li>By default only <b>lasting</b> wounds are saved. Tick <i>&quot;Also save temporary critical effects&quot;</i> if you want one-turn
                        effects carried over as well; they will be in effect during turn 1 of the next battle and then expire. Marine and boarding markers
                        are never saved &mdash; those belong to the battle you just fought.</li>
                    <li>The fleet is named after the game and turn by default (for example <i>Second Contact T7</i>), which is what tells two saves of the
                        same fleet apart in the dropdown. Type over it if you prefer something else.</li>
                </ul>
            </li>

            <li><b>Loading a Damaged Fleet:</b> A fleet carrying battle damage or critical effects is marked with a spanner icon in the fleet dropdown.
                When you load it, you are asked <b>two separate questions</b>: <i>Include saved battle damage</i> and <i>Include saved critical effects</i>.
                Both are ticked by default, and each is only offered if the fleet actually has that kind of state. So the same saved fleet can be reloaded
                pristine, damaged-but-uncritted, critted-but-repaired, or exactly as it ended the last battle &mdash; your choice, every time you load it.</li>

            <li><b>Applying Battle Damage by Hand:</b> In Fleet Selection, any unit <b>you have bought</b> can be damaged before the battle starts. There is
                no rule gating this &mdash; it is on the honour system, and it is meant for scenarios, campaigns and &quot;what if&quot; games. Open the
                ship's window on the right-hand side and:
                <ul class="circle-list">
                    <li><b>Damage a system:</b> click its icon. A small <i>Apply Damage &amp; Critical Effects</i> menu opens. The number shown is the
                        <b>remaining</b> structure boxes, not the damage &mdash; the same number you would read off the SCS. Use the &minus; and + buttons,
                        type a value straight in, or roll the mouse wheel over the box (up repairs, down damages). Tick <b>Destroy</b> to knock the system
                        out completely; untick it and the system comes back at whatever value you had dialled in before.</li>
                    <li><b>Damage a section's Structure:</b> click the coloured <b>section header bar</b> (the one showing the section name and its
                        structure total). Structure has no icon of its own &mdash; that bar is its icon. Destroying a section's Structure destroys the
                        systems in that section, exactly as it would in a real game. (Vree saucers are the exception: their outer structure blocks are a
                        ring around the disc rather than the compartments the systems sit in, so losing one does not take its systems with it.)</li>
                    <li><b>Add critical effects:</b> underneath the damage row is a <i>Critical Effects</i> list. Use <b>+ Add effect&hellip;</b> to choose
                        an effect that system can genuinely suffer, and the &minus; / + buttons to change how many of it it carries. Tick <b>All</b> to
                        widen the list with effects that apply to any system. Some effects only ever apply once, and the + button goes grey when you reach
                        that limit. Dropping an effect to zero leaves its row on screen so you can put it straight back.</li>
                    <li><b>Damage a fighter flight:</b> click the flight's <b>health bar</b>. You get one row per craft, so you can wound individual
                        fighters, plus <b>Apply Fighter 1 to all</b> to copy the first row across the flight. Critical effects are set <b>once for the whole
                        flight</b> rather than per craft. There is no Destroy option: a dead fighter is simply one fewer in the flight, so use <b>Edit</b>
                        on the flight to reduce its size instead.</li>
                    <li><b>Damage bulk-bought mines:</b> click the mine's structure bar. You get one row per mine in the purchase, plus
                        <b>Apply Mine 1 to all</b>. Mines take structure damage only, and cannot be destroyed &mdash; a mine you lost is one you did not
                        buy.</li>
                </ul>
            </li>

            <li><b>Things Worth Knowing:</b>
                <ul class="circle-list">
                    <li>Any unit carrying pre-battle damage or critical effects is marked with a <b>spanner</b> icon in your fleet list, so you can see at
                        a glance which ships are going in wounded.</li>
                    <li><b>Damage does not make a ship cheaper.</b> A crippled Omega costs exactly what a fresh one costs. (A shrunken fighter flight does
                        cost less, but only because it is fewer fighters.)</li>
                    <li>Clicking <b>READY</b> with damaged units in your fleet gives you an extra note in the confirmation, so nobody readies a wounded
                        fleet by accident.</li>
                    <li>If you <b>Edit</b> a flight and change its size &mdash; or change how many mines are in a bulk purchase &mdash; any damage you had
                        applied to it is cleared, and you are told so. The damage is stored per craft, and there is no honest way to re-map it onto a
                        different number of craft.</li>
                    <li>Everything you apply here goes into the game exactly as if it had been inflicted in a previous turn, so it is visible to your
                        opponents once the game starts, and the ships behave accordingly from Deployment onwards.</li>
                </ul>
            </li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>



        <h3 id="boarding" style="margin-top: 15px;">Boarding Actions</h3>
        <ul>
            <li>Many factions have access to Breaching Pods and Grappling Claws, which come equipped with marines that can undertake boarding actions.  
                During Fleet Selection, one pod can be purchased for every medium ship or HCV with hangar capacity for medium fighters, heavy fighter or assault shuttles, 
                two for every capital ship with these hangars, and four for enormous units or bases.  <br>Assault ships (i.e., those vessels containing ‘Assault’ in their name) can carry double these allowances
                providing they have the hangar space to permit it.</li>
            <br>              
            <li>In battle, Pods can attempt to attach to enemy ships in the same hex and deliver Marines to undertake a selection of missions (Capture Ship, Sabotage and Rescue) during the Firing Phase.</li>
            <li>Pods will initially roll to attach on a d10 in the same way that normal weapons roll to hit enemies, 
                but the calculation is very different and success is automatic if they are moving faster than the target ship and the speed difference between the two units is not higher than pod's thrust rating. 
                If the speed difference to target is greater than pod thrust rating it is simply unable to attach. 
                If the target is moving faster, each point of speed difference is -10% chance to attach.  
                Pods cannot attach to ships with Advanced Armor and certain factions like Llort have +1 to attach rolls.</li>
            <li>Attachment is limited <strong>per structure section</strong>: at most two Breaching Pods may attach to any one section.
                A hull's total therefore follows how many sections it actually has - a Capital Ship takes 2 per facing, an HCV 2 forward and 2 aft, a Medium Ship 2 in total,
                and a Drazi Stormfalcon (which has no aft structure) takes 6 rather than 8.
                <br>On a vessel with exterior structure blocks, the Primary section is reachable only once the exterior structure facing the pod has been destroyed, and it then holds
                <strong>two pods for every destroyed exterior section</strong> - so a Capital Ship that has lost both its sides can carry four on its Primary, two arriving through each breach.
                Medium Ships, LCVs and OSATs have no exterior structure blocks at all, so for them Primary is simply the hull and holds the usual two.
                <br>An overall ceiling by hull size applies on top of this, so a vessel with an unusual number of sections (such as a six-sided Vree saucer) can never exceed what its class allows:
                <strong>12</strong> for bases and Enormous units, <strong>8</strong> for Capital Ships, <strong>4</strong> for HCVs and <strong>2</strong> for Medium Ships and smaller.
                LCVs and OSATs are a special case and support only a <strong>single attached craft</strong>, delivering one marine contingent.
                <br>If more units try to attach and/or deliver marines than these limits allow, the extra attempts automatically fail.</li>
            <li>Grappling Claw ships are limited to <strong>one per structure section</strong>, and a section held by a Grappling Claw admits no Breaching Pods at all.
                Beyond that, only one vessel may grapple a Medium Ship or HCV, and a Capital Ship may be grappled by two vessels only, which must attach to opposite ends.
                Bases and Enormous units have no such overall limit - one claw per section is their only restriction.</li>
            <li>Breaching Pods will remain attached to a vessel's facing structure block until they choose to Detach in the Movement Phase, or the vessel is destroyed 
                (providing that the structure location the pod is attached to is NOT also destroyed).  If the structure block a pod is attached to IS destroyed before the Pod detaches, then the Pod is automatically destroyed.  
                While attached, Pods match speed and heading with their host ship, and suffer -10 Initiative penalty.  When they Detach Pods will automatically face away from the host ship to which they were attached.</li>
            <li>Units can shoot at attached pods, providing they are in arc of the structure location the pod is attached to, and will roll to hit them as normal. However any shot aimed at a pod will automatically hit the vessel it is attached to as well.
                <br>Weapons that can never damage a ship are the exception - hex-targeted weapons such as the Pak'ma'ra Plasma Web damage the pod only, and leave its host untouched.</li>
            <li>A unit that ends its movement on the same hex as an Enormous unit and fails to attach to it - or does not attempt an attachment at all - will <strong>ram</strong> it instead, resolved at the end of the Firing Phase.
                Units that attach successfully never ram their host.  Since bases do not move, an attachment attempt on one succeeds automatically, so only an attachment that is actively <em>blocked</em> (a full section, Advanced Armour, an Ancient hull) will lead to a ram.</li>
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

        <h3  id="delayed">Delayed Deployment Slot</h3>
        <ul>
            <li><b>This is not the same thing as <a style="font-size: 14px;" href="#reinforcements">Reinforcements</a>.</b> A Delayed Deployment Slot is a <i>scenario</i> setting: the game is
            created knowing that a whole player slot turns up on a fixed turn, whatever happens in the battle, and it arrives wherever its Deployment Zone allows.
            <b>Reinforcements</b> are bought by a player out of their own points, wait in hyperspace with no arrival turn at all, and only appear when one of their own jump-capable
            ships (or a jump gate) opens a jump point for them — which can be any turn, or never. Delayed Deployment is decided before the game starts; Reinforcements are played.
            See <a style="font-size: 14px;" href="#jump">Jump Drives</a> for how the latter work.</li>
            <li>You can select this option in the Create Game screen, by setting the <b>'Deploys on Turn'</b> field in a Player Slot to the Turn you wish that slot to deploy, or ‘jump in’.
            Ships cannot jump into hexes occupied by terrain or Enormous units, so make sure you make the Deployment Zone large enough!</li>
            <li><b>You choose your entry hexes a turn early.</b> A slot set to deploy on Turn 5 gets its Deployment Phase on <b>Turn 4</b>, where you position every arriving unit
            exactly as you would on Turn 1. Your units do not actually arrive until Turn 5 — through Turn 4 they cannot be seen, targeted, moved or fired, and they take no part in
            any other phase. A slot set to deploy on Turn 2 therefore picks its hexes during the normal Turn 1 Deployment Phase, alongside everyone else.</li>
            <li><b>Your opponents get one turn of warning.</b> From the moment you commit your Deployment Phase, every hex you chose is marked on the map for <i>all</i> players with a
            blue <b>Jump Point</b> hex. Reinforcements no longer materialise out of nowhere — the enemy can see where a jump point is about to open and has a turn to react to it.
            Only the hex is revealed, not what is coming through it (though the fleet list has always shown the composition of a delayed slot, marked <i>[Deploys on Turn N]</i>).
            A Jump Point appears even for stealthed units: the jump point itself is visible, whatever arrives through it may not be.</li>
            <li>Any unit that has not yet arrived shows a cyan <b>'Deploying on Turn N'</b> banner in its ship window, and is listed in the fleet list under a <i>[Deploys on Turn N]</i> header.</li>
            <li>Fighters and LCVs belonging to a delayed slot can be <b>deployed inside the hangars and rails</b> of a carrier from the same slot, arranged during that slot's
            Deployment Phase in the usual way.</li>
            <li>Ships which would normally have to set systems on Turn 1 and choose to deploy later (e.g. Hyach Specialists) will set these systems during their Deployment Phase — that is,
            on the turn they pick their entry hexes rather than the turn they arrive. Systems set in Initial Orders (e.g. Vorlon Adaptive Armor) are set as normal on the turn the unit arrives.</li>
            <li>Terrain, Bases, and OSATS cannot deploy later in the game and will always deploy on Turn 1 even if the slot is set to deploy later. If a slot contains any of these
            <i>and</i> delayed units, it gets a Turn 1 Deployment Phase for them and a second one later for its genuine reinforcements.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="notifications" >Discord Turn Notifications</h3>
        <ul>
            <li>Fiery Void games are often played out over days or weeks, so it is easy to miss the moment a game starts waiting on you.
                If you'd like, the <b>Fiery Void bot</b> can send you a <b>direct message on Discord</b> whenever one of your games needs your input.
                This is completely <b>opt-in</b> — nothing is ever sent unless you link your Discord account, and you can unlink again at any time.</li>
            <br>

            <li><b>Before you start</b> — two things must be true, or the bot cannot reach you:
                <ul class="circle-list">
                    <li>You must be a member of the <a href="https://discord.gg/4jXarWusp4" target="_blank" rel="noopener noreferrer">Fiery Void Discord server</a>.  Discord only lets a bot message people who share a server with it.</li>
                    <li>You must allow DMs from that server: right-click the Fiery Void server icon &rarr; <b>Privacy Settings</b> &rarr; turn on <b>Allow direct messages from server members</b>.</li>
                    <li>Don't worry that the bot always shows as <i>offline</i> in the member list — that is normal.  It only sends messages; it never logs in or reads your chat.</li>
                </ul>
            </li>
            <br>

            <li><b>Setting it up (a one-off, takes a minute):</b>
                <ul class="circle-list">
                    <li><b>1. Find your Discord user ID.</b> In Discord go to <b>User Settings &rarr; Developer</b> and turn on <b>Developer Mode</b>.  Then right-click your own name (in a chat or the member list) and choose <b>Copy User ID</b>.  This is a long number of 17&ndash;20 digits — it is <em>not</em> your username.</li>
                    <li><b>2. Paste it into Fiery Void.</b> From the FV Main Page click, <b>Set-Up Discord Notifications</b> in the top-right corner, paste your Discord User ID into the box, and click <b>Send verification code</b>.</li>
                    <li><b>3. Check your Discord DMs.</b> The Fiery Void bot will message you a <b>6-digit code</b>, valid for <b>10 minutes</b>.</li>
                    <li><b>4. Enter that code</b> back on the notifications page and click <b>Verify</b>.  You're linked.</li>
                    <li><b>5. Optionally you can then click 'Send test ping'</b> to prove a DM reaches you.</li>
                </ul>
            </li>
            <br>

            <li><b>Why is there a code?</b>  Your Discord user ID is not a secret — anyone who shares a server with you can copy it.
                Without a check, somebody could type <em>your</em> ID into <em>their</em> Fiery Void account and have the bot send you reminders for their games.
                Because the code is only ever DM'd to the true owner of the ID, only you can finish the link.  A Discord account can be linked to <b>one</b> Fiery Void account at a time.</li>
            <br>

            <li><b>How it works in practice:</b>
                <ul class="circle-list">
                    <li>You are messaged when a game <b>needs something from you</b> — exactly the same condition that highlights a game in your lobby list.  That covers every phase, including Fleet Selection, Deployment, Initial Orders, Movement and Firing.</li>
                    <li>The DM names the <b>game, the turn number and the phase</b>, and includes a link straight to the game.</li>
                    <li>You get <b>one DM each time a game becomes blocked on you</b> — not one per phase.  Once a game is waiting on you nothing further can happen until you act, so you will not be spammed.</li>
                    <li>You are <b>never pinged for your own moves</b>, and you are <b>not pinged while you are actively playing</b>: if the game has seen you in the last 5 minutes it assumes you are already there.  Two players trading moves in real time receive no DMs at all.</li>
                    <li>Nothing is sent for games that have finished or been surrendered.</li>
                </ul>
            </li>
            <br>

            <li><b>Turning it off, or moving to another Discord account:</b> go back to <b>Set-Up Turn Notifications</b> and click <b>Unlink</b> — pings stop immediately.
                To use a different Discord account, Unlink first and then verify the new ID.</li>
            <br>

            <li><b>Troubleshooting:</b>
                <ul class="circle-list">
                    <li><b>No code arrived.</b>  Nearly always your DM privacy setting, or you are not in the Fiery Void Discord server.  Fix those (see <i>Before you start</i>) and request another code.</li>
                    <li><b>"Incorrect code".</b>  Make sure you are using the newest code.  After a few wrong attempts the code is cancelled for safety — simply request a fresh one.</li>
                    <li><b>"That code has expired".</b>  Codes last 10 minutes; request another.</li>
                    <li><b>"Please wait a few seconds".</b>  There is a short cooldown between code requests, to stop the feature being abused to spam someone.</li>
                    <li>If your DMs are closed, a test ping may instead show up as a mention in the <b>#turn-pings</b> channel, where that fallback has been set up.  Opening your DMs is the better fix.</li>
                </ul>
            </li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="elint" >ELINT &amp; Electronic Warfare (EW)</h3>
        <ul>
            <li>Every ship with a working Scanner generates Electronic Warfare (EW) points each turn, allocated during the Initial Orders phase.  
                Dedicated <b>ELINT vessels</b> (those carrying an <b>ELINT Scanner</b>) can perform a number of additional, longer-ranged sensor operations on top of the standard EW functions available to any ship.</li>
            <li>To allocate EW, select your ship and click on an enemy or friendly unit to bring up the relevant EW buttons, then use the Add / Remove buttons (remember right-click sets a function to maximum or zero — see Hot Keys).  EW lines are drawn on the map and can be toggled with the W / X / Y keys.</li>
            <br>

            <li><b>Standard EW functions (any ship with a Scanner):</b>
                <ul class="circle-list">
                    <li><b>Offensive EW (OEW):</b> A target lock on a single enemy unit that improves your fire control against it (reducing range and tracking penalties).  Allocate more points for a stronger lock.</li>
                    <li><b>Defensive EW (DEW):</b> Lowers your own ship's defensive profile, making you harder to hit.  Any EW left unallocated at the end of Initial Orders is automatically applied as DEW.</li>
                    <li><b>Close Combat EW (CCEW):</b> Provides a lock on <em>all</em> enemy fighters within 10 hexes at once.  Counts as a single OEW target for the purposes of being disrupted.</li>
                    <li><b>Mine Detection:</b> Only when mines are present, see <a href="#mines">Mines</a> for full details.</li>                   
                </ul>
            <br>

            <li><b>ELINT-exclusive functions (require an ELINT Scanner):</b>  Most of these operate at a 30 hex range and this is checked when declared (Initial Orders) and at the moment of firing, 
            so a target that moves out of range, or a line of sight that becomes blocked, can cause the support to lapse.
                <ul class="circle-list">
                    <li><b>Supported OEW (SOEW):</b> Lends a friendly ship half of the ELINT's offensive lock against a chosen target.  The target must be within 30 hexes of the ELINT at the moment of firing, and the supported friendly ship within 30 hexes at both declaration and firing.  Requires line of sight from the ELINT to both the target and the supported ship.  Fighter flights receive only half the usual benefit.</li>
                    <li><b>Supported DEW (SDEW):</b> Boosts a friendly ship's defensive EW by 1 for every 2 points allocated.  Range 30 hexes, checked at both declaration and firing.</li>
                    <li><b>Blanket Protection (BDEW):</b> Grants <em>all</em> friendly units within 20 hexes (fighters included) +1 DEW for every 4 points allocated.  Blanket Protection cannot be combined with any other ELINT activity on the same ship that turn.</li>
                    <li><b>Disruption (DIST):</b> Degrades an enemy's sensors — reduces a target enemy ship's OEW and CCEW by 1 for every 3 points allocated.  The reduction is split evenly between that ship's offensive locks (CCEW counts as one lock), and it cannot push a lock below 0.  Range 30 hexes, checked at both declaration and firing.</li>
                    <li><b>Detect Stealth:</b> Increases this ship's stealth-detection range by +2 hexes per point allocated.  This option only appears when the enemy has stealth-capable ships in the game (see <a href="#stealth">Stealth Ships</a>).</li>
                </ul>
            </li>
            <br>

            <li><b>Jamming (JAM) &mdash; disrupting Hunter-Killers:</b>  ELINT vessels can also jam the command-link guidance of enemy <b>remotely-controlled fighter flights</b> &mdash; such as the Orieni <em>Shining Star</em> Hunter-Killers.
                <ul class="circle-list">
                    <li>During Initial Orders, select your ELINT ship and click on an enemy Hunter-Killer flight to bring up the <b>Add / Remove Jamming</b> buttons.  Jamming may only be applied to remote-controlled flights, and the flight must be within <b>30 hexes</b> of the ELINT.</li>
                    <li>Jamming costs <b>1 EW point</b> per point applied , and count as offensive EW for the purposes of <em>being</em> disrupted by an enemy ELINT's DIST.</li>
                    <li>The disruption is resolved during the Critical Hits phase.  Each jammed flight rolls a d20, with a <b>+1 modifier for every point of Jamming beyond the first</b>, on the following table:</li>
                    <li><strong>JAMMING TABLE (D20 + 1 per extra point):</strong>
                        <ul class="circle-list">
                            <li>1&ndash;14 &ndash; No effect.</li>
                            <li>15&ndash;16 &ndash; &minus;-10 Initiative next turn.</li>
                            <li>17&ndash;18 &ndash; &minus;-20 Initiative next turn.</li>
                            <li>19&ndash;20 &ndash; <b>Control Lost</b> for one turn (the flight becomes Uncontrolled), plus &minus;-20 Initiative.</li>
                            <li>21 &ndash; Control Lost plus &minus;-10 Initiative, and <b>1 Hunter-Killer drops out</b> of the flight.</li>
                            <li>22+ &ndash; Control Lost plus &minus;-10 Initiative, and <b>2 Hunter-Killers drop out</b>.</li>
                        </ul>
                    </li>
                    <li><b>Uncontrolled flights:</b> While Control is lost, the player can no longer steer the flight.  Instead, it operates semi-autonomously: it suffers an additional -15 Initiative penalty, jinks defensively for 2, and steers toward the nearest enemy ship, attempting to <b>ram</b> it if it reaches the same hex.  
                    An Uncontrolled flight is shown with a red <i>UNCONTROLLED</i> note in its tooltip.</li>
                    <li>Jamming cannot prevent a Hunter-Killer from completing a ram &mdash; but any surviving fighters in the flight are still disrupted as normal on following turn that the jamming is applied.  
                        A jamming roll is only skipped if the entire flight has already been destroyed.</li>
                </ul>
            </li>
            <br>

            <li><b>Advanced &amp; Improved Sensors:</b> Some factions field ships with upgraded sensors.  <em>Improved Sensors</em> halve the effectiveness of enemy Jammers.  <em>Advanced Sensors</em> ignore Jammers entirely, and also ignore enemy Blanket Protection, SDEW and Disruption, as well as defensive systems that lower a target's profile (shields, E-Web, etc.).</li>
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

            <li><b>LCV Rails:</b> Some carriers (such as the Deneth <em>Deliverer</em>) are fitted with LCV Rails — external docking collars that carry a whole
                Light Combat Vessel (LCV) rather than a fighter flight.  An LCV is a full ship in its own right, so an LCV Rail behaves differently to a fighter hangar.
                <ul class="circle-list">
                    <li><strong>Capacity:</strong> Each LCV Rail holds <strong>exactly one</strong> LCV.  Unlike Fighter Rails, an LCV Rail is an ordinary targetable
                        system with its own hit points — it has its own entry on the carrier's damage chart and can be destroyed by direct fire.</li>
                    <li><strong>Compatible craft:</strong> LCV Rails carry LCVs only.  Fighters, shuttles, and other small craft cannot dock to them, and an LCV cannot
                        dock into an ordinary hangar or fighter rail.</li>
                    <li><strong>Docking (recovery):</strong> To dock, the LCV must end its move in the <strong>same hex</strong> as the carrier, on a
                        <strong>matching heading</strong>, with the carrier <strong>stationary</strong>, and the LCV must have at least <strong>1 thrust unspent</strong>.
                        You can dock from the LCV's own "Enter Hangar" button or from the carrier's "Recover" button, and you may choose <strong>which specific rail</strong>
                        (e.g. Forward, Port, Starboard) the LCV docks onto.</li>
                    <li><strong>Launching:</strong> A launched LCV is placed back in the carrier's hex, inheriting the carrier's facing and speed.  The launched LCV
                        suffers a <strong>−50 Initiative</strong> penalty on the following turn (the LCV is briefly disoriented after release).</li>
                    <li><strong>Penalties while docked:</strong> Carrying LCVs makes a ship more sluggish.  For <em>each</em> LCV currently docked, the carrier suffers:
                        <ul class="circle-list">
                            <li><strong>−10 Initiative</strong>, and</li>
                            <li><strong>+1 thrust</strong> to the cost of a turn <em>and</em> to its turn delay (the ship's printed turn cost is unchanged — only the
                                thrust actually paid each turn increases, e.g. a carrier with four LCVs docked pays four extra thrust to turn).</li>
                        </ul>
                    </li>
                    <li><strong>No rearming while docked:</strong> Unlike fighters in a hangar, a docked LCV does <strong>not</strong> rearm or reload its weapons —
                        it simply sits on the rail until launched.</li>
                    <li><strong>Landing on a damaged rail:</strong> If the rail has taken damage when the LCV docks, the LCV takes Structure damage equal to the rail's
                        sustained damage.</li>
                    <li><strong>Rail destroyed while occupied:</strong> If an LCV Rail is destroyed while holding an LCV, the LCV is <strong>forced to launch</strong> and takes
                        Structure damage equal to the rail's lost hit points <strong>plus 2d10</strong> Matter damage from the resulting debris.</li>
                    <li><strong>Carrier destroyed:</strong> If the whole carrier is destroyed, every docked LCV escapes in the same way — each is forced to launch and takes the
                        rail's damage plus 2d10.</li>
                    <li><strong>Deployment phase docking:</strong> Because LCVs are the smallest vessels, they may share the carrier's hex during deployment.  You can
                        deploy an LCV directly onto a chosen rail (it starts the battle docked), place it in the carrier's hex, or un-dock a deploy-docked LCV from the
                        carrier's "Deploy Flights in Hangar" menu before committing.</li>
                </ul>
            </li>
            <br>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


        <h3 id="infopanel" >Info Panel</h3>
        <p>The <strong>Info Panel</strong> is the tabbed panel along the bottom-left of the battle screen.  Six tabs share a single body, so only one is ever on screen:
            <strong>COMBAT LOG</strong>, <strong>FLEET INFO</strong>, <strong>GAME CHAT</strong>, <strong>CHAT</strong>, <strong>DECLARATIONS</strong> and <strong>SAVE FLEET</strong>.
            Click a tab to switch to it.  A tab is only a view - switching between them never changes anything about your orders.</p>

        <ul>
            <li><b>Sizing the panel:</b>
                <ul class="circle-list">
                    <li><strong>Drag the panel's top edge</strong> up or down to set its height.  The edge lights up when you point at it (Note- drag it with your finger on touchscreen).</li>
                    <li><strong>The chevron (&#9650; / &#9660;) at the right-hand end of the tab strip</strong>, or a <strong>double-click on the top edge</strong>, switches between the compact and the tall panel.  Opening the tall panel also tucks the initiative drawer out of the way.</li>
                    <li>The compact and the tall heights are remembered <em>separately</em>, and are kept on your device for your next session - so you can set one working size for reading the log at a glance, and another for going through a whole turn.</li>
                    <li>Expanding or collapsing the panel leaves you on the tab you were reading.</li>
                    <li>On phones, and on a short landscape screen, the panel is shut down to its tab strip until you tap the chevron, and only the selected tab is drawn while it is closed - there is not room for six of them and a map.</li>
                </ul>
            </li>
            <br>
            <li><b>Right-clicking inside the panel</b> no longer opens your browser's own menu (Back / Reload / Save as...), so a right-click aimed at a unit that lands slightly low does not cover the game with it.
                The game's own right-click actions are unaffected - a right-click on a fleet-list row still opens that unit's ship window.  The browser menu is still there in a text box, so you can paste into chat, and when you have selected some text, so you can copy a log line out to Discord.</li>
            <br>

            <li><b>COMBAT LOG</b> - what happened, this turn and in every turn before it.
                <ul class="circle-list">
                    <li>During a replay the log fills in <em>live</em> as the turn plays out.  The rest of the time it shows the printed log of whichever turn you have selected.</li>
                    <li><strong>&#9664; TURN n &#9654;</strong> - step back and forth through the turns.  The arrows grey out at the ends of the range.  The <strong>Live</strong> button appears only while you are looking at an older turn, and takes you back to the current one.</li>
                    <li><strong>Sort</strong> - <em>Resolution</em> is the order the game actually resolved the fire in, which is what you want while following a replay; <em>Attacker</em> and <em>Target</em> regroup the same entries by who was shooting, or by who was being shot at, which is usually easier to read after the fact.  On a narrow screen the three buttons become a dropdown.</li>
                    <li><strong>All / Mine / Enemy</strong> - whose fire to show.</li>
                    <li><strong>Hits</strong> - show only fire that scored at least one hit, for when a big turn is mostly misses.</li>
                    <li><strong>Find</strong> - filter the turn down to a ship or a weapon by name.</li>
                    <li>The readout to the left of the Find box names the current game phase and, when a filter is on, how many fire groups it is hiding - so a filtered log never looks like a quiet turn.</li>
                    <li>Reading the colours: ship names are pale, and the <em>target</em> of each shot is drawn in its team colour, because who was shot at is what the eye is looking for.  Damage is red, criticals are amber, and shield absorption is blue.</li>
                </ul>
            </li>
            <br>

            <li><b>FLEET INFO</b> - every fleet in the battle, one block per player.
                <ul class="circle-list">
                    <li>The block header carries the <strong>team</strong> (coloured by allegiance), the <strong>player's name</strong>, the fleet's <strong>current / base points</strong>, and a chip saying where that player is up to: <em>Orders committed</em>, <em>Waiting for Movement orders</em>, <em>Surrendered T5</em>, or <em>Deploys T3</em> for a reinforcement slot that has not arrived yet.  <strong>Click the header to collapse or expand that fleet.</strong></li>
                    <li><strong>Click a column head</strong> (Ship Name, Class, Type, Ini, Value) to sort by it; click it again to reverse; a third click drops back to initiative order.  Your choice is remembered.</li>
                    <li><strong>Left-click a unit row</strong> - scroll the map to that unit.</li>
                    <li><strong>Right-click a unit row</strong> - open its <strong>ship window</strong>, without selecting the unit and without moving the map (Note- long press on touchscreen).  You can also click the <strong>&#9432;</strong> that appears at the right-hand end of a row when you point at it, which is the easier target on a phone.</li>
                    <li>Units that are off the board but still yours - <strong>docked flights</strong> and <strong>reinforcements waiting in hyperspace</strong> - open their ship window on <em>either</em> click, since there is nowhere to scroll to.  For a docked flight that window is the only way to see what is actually in the bay.</li>
                    <li><strong>Destroyed and jumped units are inert</strong>: no highlight and no window.  They are gone from the battle, and there is nothing left to inspect.</li>
                    <li><strong>On map only</strong> hides everything that is not currently on the board - destroyed, jumped, docked, and reinforcements still in hyperspace - and hides a whole fleet block if nothing in it is left to show.  Mines <em>are</em> on the map, so they stay.</li>
                    <li>The <strong>team dropdown</strong> appears in games of three or more teams, and narrows the list to one team.</li>
                    <li>The readout recounts fleets and units against whatever filters are on, so it always describes what is actually on screen.</li>
                    <li>The <strong>FAQ</strong>, <strong>Ammo &amp; Options</strong> and <strong>Factions</strong> buttons open those reference pages in a new tab.</li>
                </ul>
            </li>
            <br>

            <li><b>GAME CHAT</b> and <b>CHAT</b> - two separate channels.
                <ul class="circle-list">
                    <li><strong>GAME CHAT</strong> is this battle only, and everyone in the game can read it.  It is the place to agree a house rule, warn an opponent you will be slow, or ask what a system does.</li>
                    <li><strong>CHAT</strong> is the site-wide channel - the same one that appears in the game lobby.</li>
                    <li>A tab turns <strong>amber</strong> when a message has arrived on it that you have not read, so you can leave both closed and still not miss anything.</li>
                </ul>
            </li>
            <br>

            <li><b>DECLARATIONS</b> - a read-out of the orders currently on the table.  This is the tab to check <em>before</em> you commit: an unspent EW point or a weapon aimed at the wrong ship is far easier to spot here than in a dozen separate ship windows.
                <ul class="circle-list">
                    <li><strong>Side: Own / Enemy</strong> - whose orders to list.</li>
                    <li><strong>Show: EW / Fire</strong> - electronic warfare allocations, or firing orders.</li>
                    <li><strong>By: Source / Target</strong> - group under the unit <em>doing</em> it, or under the unit it is being done <em>to</em>.  So <em>Own + Fire + By Source</em> is your ships and what each of them is shooting at, while <em>Enemy + Fire + By Target</em> is your ships and what is shooting at them.  The same pairing works for EW: what you are emitting, versus what is being pointed at you.</li>
                    <li>In the <strong>EW</strong> view each unit is listed with every point it is emitting or receiving plus a <strong>per-unit total</strong>, so you can see at a glance whether a ship still has EW left to allocate.  The <strong>Fire</strong> view reads as "4x Heavy Laser &rarr; Vorchan  45-60%" - how many guns, at whom, and the to-hit range.</li>
                    <li><strong>Briefing</strong> (far right) replaces the panel with the game's name, the rules of engagement in play (Friendly fire, Mines, Reinforcements, Desperate) and the scenario text.  Press it again to return to the view you were on.  The three filter groups dim while it is up, because none of them means anything for a briefing.</li>
                    <li>This tab only ever shows what your own game data already contains, so it cannot reveal an opponent's orders to you any earlier than the rest of the interface does.</li>
                </ul>
            </li>
            <br>

            <li><b>SAVE FLEET</b> - saves your surviving ships, with their enhancements, remaining ammunition, current battle damage and critical effects, as a reusable fleet list.  Load it from the game lobby to carry a campaign on into the next battle.  See <a href="#savedfleets">Battle Damage &amp; Saving Fleets</a> for exactly what is and is not carried over.</li>
            <br>
        </ul>
        <a class="back-to-top" href="#top">&#8617; Back to Top</a>


        <h3 id="interception" >Interception</h3>
        <ul>
            <li>Interception is defensive fire that makes an incoming shot harder to hit.  Every weapon capable of it has an <strong>Intercept Rating</strong>.</li>
            <li>In general, when several weapons are put on the <em>same</em> shot, each one after the first is worth 5% less than its rating, cumulatively.  Three rating -20% weapons therefore give -20%, -15% and -10%, for -45% total intercept, rather than -60%.
                This degradation <strong>does not apply against ballistic weapons</strong> (missiles, captor mines) — every weapon assigned to a missile is worth its full rating.</li>
            <li>Automatic Interception:
                <ul class="circle-list">
                    <li>This is the default and needs no input from the player to happen.  When the Firing phase resolves, the game gathers every intercept-capable weapon that has <em>not</em> fired that turn and assigns it to the incoming shots itself.</li>
                    <li>Firing a weapon offensively takes it out of the defensive pool for that turn.  Interception is always paid for with weapons you chose not to shoot with.</li>
                    <li>Weapons that take <strong>more than one turn to load</strong> are only used automatically if you first place a self-intercept marker on them, using the green shield icon on the weapon in the ship window during Firing phase.
                        That marker is your consent to use the weapon's to intercept — without this consent the game will not spend a slow-charging weapon on defence.</li>
                    <li>Missile racks that reload in a single turn and carry Interceptor missiles are switched into their interceptor mode and used automatically, drawing rounds from the magazine as they go.
                        Racks with a longer loading time fall under the marker rule above.</li>
                    <li>Note - Automated interception is the only way to intercept non-Ballistic weapons, since you have no prior knowledge of these atacks until after Firing Phase.</li>    
                    <li>Mines do not intercept at all unless they carry the Command Controller enhancement.</li>
                </ul>
            </li>
            <li>Manual Interception — putting your own weapons on a specific shot.  This is declared in the <strong>Firing phase only</strong>:
                <ul class="circle-list">
                    <li>Click one of your own units to select it and open its ship tooltip.  The <strong>INCOMING</strong> list at the bottom of that tooltip is every ballistic shot currently aimed at it, grouped under the ship that fired,
                        and written as "2x Heavy Missile (Class-H)".</li>
                    <li>Select the weapon(s) you want to commit in the ship window, exactly as you would to fire them.</li>
                    <li>The hit chance at the end of the row <em>is</em> the button.  When the current selection can legally commit to that row the number is underlined in blue and the cursor becomes a pointer — click it to declare.
                        Hover it at any time to read the full to-hit breakdown and, when it is refusing, the reason (<em>No interceptor selected</em>, <em>Uninterceptable</em>, <em>No selected weapon can reach this shot</em>, and so on).</li>
                    <li>The hit chance drops as you commit, and the hover breakdown gains a <em>Declared interception</em> line.  That number is <strong>your own declared orders only</strong>: automatic interception is worked out after the
                        turn is committed and is deliberately not previewed, and your allies' uncommitted orders are not in your game data.  It is not floored at zero either — a shot reading -25% is one you have already spent more on than it was worth.</li>
                    <li>Clicking a grouped row spends the selection greedily, best interceptor first: it stacks weapons onto one shot of the group until that shot is fully suppressed, then moves on to the next one.
                        Use the ▶ caret to expand the group into one row per shot when you would rather place each weapon yourself.</li>
                    <li>A weapon that has been selected to intercept manually cannot also fire offensively, and neither will be in the automatic interceptor pool. For this reason you cannot manually declare an intercept with a weapon, and use Self Intercept shield to place it in the automated pool.</li>
                    <li>Weapons that can split their shots (Twin and Quad Arrays, Discharge Guns and so on) spend <strong>one gun</strong> per manual intercept, and the rest are still free to fire, to intercept something else, or to be
                        left to the automation.  A weapon that cannot split commits all of its guns to the one shot you point it at.</li>
                    <li>Missile racks and other weapons that declare in an earlier phase can be hand-assigned in the Firing phase too, so long as they have fired nothing at all that turn.  Ammunition is checked against the magazine
                        before the order is offered.</li>
                    <li>To withdraw, clear that weapon's fire orders from the ship window in the usual way.</li>
                </ul>
            </li>
            <li>What can be intercepted, either way:
                <ul class="circle-list">
                    <li>Fire aimed at the intercepting unit itself, provided the shot comes from within the weapon's firing arc.  Arcs are measured from where the shot is coming <em>from</em> — for a ballistic that is the hex it was
                        launched from, not wherever the shooter has since moved to.</li>
                    <li>Fire aimed at a <em>friendly</em> unit, but only by weapons carrying the free-intercept trait, and only when the unit being protected lies between them and the incoming shot.  Covering someone else is normally
                        left to the automation, since the INCOMING list is shown on your own units' tooltips.</li>
                    <li>Fighter flights may protect the ship they are escorting from ballistic fire, provided they end their movement on its hex now and were in the same hex as the ship at the start of the current turn.  A flight cannot cover another flight.</li>
                    <li>Some attacks cannot be intercepted at all: e.g. laser weapons tend to be uninterceptable, and proximity mines, rams and Molecular Slicers are other examples.  Those rows refuse with a reason rather than
                        disappearing from the list.</li>
                </ul>
            </li>
            <li>Note - The Molecular Slicer prices interception out of its damage pool rather than per gun and has its own declaration method — see the Shadow Association section of
                <a style="font-size: 14px;" href="./factions-tiers.php" target="_blank" rel="noopener noreferrer">Factions &amp; Tiers</a>.</li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


        <h3 id="jump" >Jump Drives</h3>
        <ul>
            <li><b>There are two kinds of jump point, and the colour tells you which.</b> They are named from hyperspace's point of view, not the battle's:
                <ul class="circle-list">
                    <li>A <span style="color:#e1b000;"><b>yellow Jump Point Entrance</b></span> is a doorway <i>into</i> hyperspace. Units fly into it to <b>leave</b> the battle.
                        This is the one a ship opens with its own Jump Drive, and everything in this section up to Reinforcements is about it.</li>
                    <li>A <span style="color:#00b8e6;"><b>blue Jump Point Exit</b></span> is a doorway <i>out of</i> hyperspace. Reinforcements <b>arrive</b> through it.
                        Nothing can go the other way &mdash; there is no Jump to Hyperspace button on a blue vortex, whoever opened it.</li>
                </ul>
            </li>
            <li>A ship equipped with a Jump Drive can use it to <b>open a jump point</b> &mdash; a vortex into hyperspace
                that appears in a nearby hex for up to four turns &mdash; allowing units to leave the battle by <b>flying into it</b>. Any unit may use any
                <i>open</i> jump point, including an enemy's, and including units with no Jump Drive of their own.</li>
            <li><b>Opening a jump point:</b>
                <ul class="circle-list">
                    <li>In <b>Initial Orders</b>, select the Jump Engine in the ship window (it behaves like any other hex-targeted weapon &mdash;
                        hovering it shows its reach as a <span style="color:#e1b000;">yellow</span> overlay on the map, and the overlay stays up while it is selected).</li>
                    <li>Right-click a target hex within <b>4 hexes</b> and choose 'Target selected weapons on hexagon'. You need line of sight to the hex,
                        and it must be clear of terrain, of another jump point and of Enormous units. Ships in the hex, friendly or enemy, do not block it.</li>
                    <li>A control appears on that hex with a <b>facing arrow</b> and two turn buttons. <b>The facing is the doorway</b> &mdash; it is the hex
                        side a unit has to come <i>through</i> to use the jump point. Step it round, then press OK. Clicking away, or deselecting the Jump
                        Engine, abandons the whole thing and no order is made. The facing cannot be changed afterwards; to re-aim, remove the fire order
                        and declare again.</li>
                    <li>The declaration is private until all player commit their Initial Orders. It then shows to everyone as a <span style="color:#e1b000;">yellow</span>
                        <b>'Jump Point Forming'</b> hex with the facing arrow over it, and the ship reads <b>'Jumping to Hyperspace'</b> in its own tooltip
                        and ship window.</li>
                    <li>A jump point <b>cannot be entered on the turn it is declared</b>. It forms at the end of that turn and is open from the next one.</li>
                    <li>Opening a jump point <b>reveals a stealthed or cloaked ship</b>, exactly as using non-DEW EW does &mdash; a Shading Field or Cloaking
                        Device drops for that turn.</li>
                </ul>
            </li>
            <li><b>Using a jump point:</b> during the <b>Movement</b> phase, plot the unit's path so it <i>enters</i> the jump point hex through the side the
                arrow points at, then press the <b>Jump to Hyperspace</b> button on the unit's tooltip. Movement ends there &mdash; any remaining thrust is
                forfeit &mdash; and the unit is removed at the end of the phase. It is judged on the actual step that carried it into the hex, so a
                <b>sideslip</b> through the correct side works, and a unit that has been sitting in the hex since an earlier turn is judged on the step that
                first put it there. Fighter flights may use a jump point too. Attached pods and docked craft are carried out with their host.</li>
            <li>A unit that leaves through a jump point <b>keeps its full combat value</b> &mdash; it escaped, it was not destroyed &mdash; and its fleet-list
                row reads <span style="color:#cc8500;">Jumped</span> rather than Destroyed. Craft docked aboard a carrier that jumps go with it and are not
                subject to the carrier-destruction escape roll.</li>
            <li><b>Maintaining a jump point:</b> a jump point closes at the end of <i>every</i> turn unless its holder declares Maintain. Use the blue
                <b>Jump Point</b> ON/OFF switch in the Jump Engine's own system menu during Initial Orders. Switching it ON makes the declaration <i>and</i>
                shuts the ship down for the turn &mdash; everything with a power cost except the Scanner and the Jump Engine itself &mdash; which is the price
                of holding it open. Those systems cannot be switched back on until you switch Maintain OFF again.</li>
            <li>A jump point also closes at the end of the turn if its holder <b>ends the turn more than 4 hexes away</b>, is <b>destroyed</b>, or
                <b>leaves the battle</b> (through this jump point or any other). It closes unconditionally after <b>four turns open</b>. Whatever the reason,
                it stays usable for the whole of the turn it closes on, and the reason is printed in that turn's Combat Log. The Initial Orders commit
                dialogue warns you before you lose one you could still have kept.</li>
            <li><b>Recharging:</b> opening a jump point spends the drive's entire charge. The Jump Engine's icon shows <b>N/4</b> &mdash; turns the jump point
                has been open, out of four &mdash; while one stands, and its ordinary charge counter the rest of the time. It starts a scenario fully charged,
                drops to zero when it opens a jump point, and recharges at 1 per turn from the turn <i>after</i> that jump point closes. It cannot open
                another until it is full again, and the time that takes varies by hull (a Centauri Primus, for example, needs 16 turns).</li>
            <li><b>Damaged Jump Drives are dangerous.</b> At the end of every turn a ship opens or maintains a jump point, it rolls d100 against the
                percentage of its Jump Engine boxes lost. Roll at or under, and the ship is destroyed outright &mdash; docked craft are lost with it, with no
                escape roll. The Combat Log records it. The risk is taken by the ship <i>opening</i> the jump point; there is no roll for units flying
                through one.</li>
            <li>The Jump Drive system usually cannot be turned off unless seriously damaged, but some scenarios allow it.
                The game warns the player when attempting to deactivate this system improperly (e.g. without Desperate rules or 50%+ damage).</li>

            <li id="reinforcements" style="margin-top:10px;"><b>REINFORCEMENTS &mdash; arriving through a jump point.</b> When the host ticks
                <b>'Allow Reinforcements'</b> in the Create Game screen, a player may buy part of their fleet as reinforcements: units that start the battle
                in <b>hyperspace</b> and come out of a <span style="color:#00b8e6;"><b>Jump Point Exit</b></span> one of their own ships opens during the game.
                They come out of the same points pool as the rest of the fleet &mdash; a reinforcement is not free, it is a ship you have chosen not to bring
                to the party yet. This is <i>not</i> the same as a
                <a style="font-size: 14px;" href="#delayed">Delayed Deployment Slot</a>, which arrives on a fixed turn set before the game began.
                <ul class="circle-list">
                    <li><b>Buying them.</b> In Fleet Selection the store has two groups, <b>MAIN FLEET</b> and <b>REINFORCEMENTS</b>; pick the group before you
                        buy, or use the <i>Reinforce</i> link on a bought row to move a unit between them. Saved fleets remember which group each unit was in.
                        Terrain, Bases and OSATs cannot be reinforcements &mdash; they always deploy on Turn 1 &mdash; and the lobby will not let you put them
                        in the group.</li>
                    <li><b>Bring a way in.</b> At least one reinforcement needs a <b>Jump Drive</b> of its own, or your side needs a <b>Jump Gate</b> on the map.
                        Without either, your reinforcements sit in hyperspace for the whole battle and their points are wasted &mdash; the lobby warns you
                        before you click Ready.</li>
                    <li><b>What the enemy sees.</b> Nothing but a line in your fleet list reading <i>Reinforcements &mdash; N units, X pts</i>. Not which hulls,
                        not what they carry, not who can open a jump point. You see your own in full.</li>
                    <li><b>Calling them in.</b> During <b>Initial Orders</b>, press <b>Manage Reinforcements</b>. Every jump-capable unit you have in hyperspace is
                        listed; choose one, press <b>Choose Hex</b>, click the hex you want the jump point to open in, set the <b>facing</b> with the arrow
                        control, and then tick the units that will ride through it &mdash; the <b>Jump Point Manifest</b>. The opening ship always rides its own
                        jump point. A unit already riding somebody else's is greyed out; you can withdraw a declaration from the same menu and start again.
                        To change your mind about <i>who rides</i> without giving up the jump point itself, select the row and press <b>Jump Manifest</b> &mdash;
                        it reopens the same tick list.</li>
                    <li><b>The declaration is public once orders are committed</b> &mdash; everyone sees a <span style="color:#00b8e6;"><b>blue hex</b></span> with a
                        facing arrow at the hex you named, for the rest of that turn. That warning is the price of arriving somewhere useful, and it is the same
                        deal a Delayed Deployment Slot gets.</li>
                    <li><b>You will probably miss.</b> At the end of the turn the jump point forms, and where it forms is a <b>deviation roll</b> against the
                        opening ship's <b>sensor rating</b>: d20, modified by <b>&minus;1</b> for the Minbari Federation, <b>&minus;5</b> for an Ancient race
                        (Vorlon, Shadow), <b>&minus;3</b> for a friendly base or OSAT on the map and <b>&minus;1</b> for a friendly ELINT vessel. A low roll
                        arrives exactly where you aimed; a high one scatters 1d3, 1d6, 1d10 or 2d10+2 hexes in a random direction, and the worst two bands turn
                        the facing as well. An Ancient fleet with a base on the map arrives precisely about 40% of the time; a young race with a sensor rating of
                        10 is precise only on a natural 1. The Combat Log names the band, the roll and the distance, so you can see which happened. The jump
                        point never forms inside terrain or an Enormous unit &mdash; if the dice put it there it is nudged to the nearest legal hex.</li>
                    <li><b>Arriving.</b> On the <i>next</i> turn the owner gets a <b>Deployment Phase</b>. The wave places itself in the jump point's hex on the
                        jump point's facing, stacked, and all you set is each unit's <b>speed</b>. Anything you leave unplaced goes back to hyperspace with
                        nothing spent, and can be called in again later. A ship's jump point is <b>one-shot</b>: it closes at the end of the arrival turn, and the
                        drive can then be used normally (including to open a way out).</li>
                    <li><b>Arriving is disorderly.</b> A wave that comes out of hyperspace off course spends the turn sorting itself out: on its <b>arrival turn
                        only</b>, every unit that rode that jump point takes an <b>initiative penalty of 1 per hex it scattered, plus 2 for every 60&deg; the
                        facing was turned</b>. A precise arrival costs nothing at all, which is one more reason the modifiers above are worth having.</li>
                    <li><b>Jump Gates.</b> A fixed Jump Gate can be signalled to open a jump point <i>inward</i> instead of outward: click the gate in Initial
                        Orders and choose <b>Signal Gate for Arrival</b>, which opens the same Manifest window. A gate's jump point does not deviate &mdash; it
                        opens in the gate's own mouth, on the gate's own facing, with no initiative penalty &mdash; and it stays open for its programmed hold, so
                        it can bring a fresh wave through on <i>every</i> turn it stands. Pick the gate up again from Manage Reinforcements on later turns. A gate
                        belongs to nobody: if two players signal the same gate in the same turn, the one whose nearest unit is closest wins it, and the loser's
                        manifest is refunded to hyperspace with nothing spent. While the contest is live only the nearest side's marker is shown on the gate, so
                        you can see whether you are going to get it.</li>
                    <li><b>Shadows and other phasing hulls.</b> A Shadow ship does not tear a vortex open &mdash; it fades out, and from now on it fades back
                        <b>in</b> the same way. It declares a hex exactly as anything else does and its arrival marker reads <b>REINFORCEMENTS</b>, but no jump
                        point terrain ever appears: the ships simply <i>are</i> there on the arrival turn. Everything else &mdash; the deviation roll, the manifest,
                        the Deployment Phase, the initiative penalty &mdash; is identical. Such a drive still cannot open a way <i>out</i>; it uses the old
                        one-click Jump to Hyperspace for that.</li>
                    <li><b>You cannot go back out the way you came.</b> A blue Jump Point Exit is one-way. To leave the battle you need a yellow Jump Point
                        Entrance, which means opening one with a drive that has recharged.</li>
                </ul>
            </li>
        </ul>
        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="ladder" >Online Ladder</h3>
        <ul>
            <li>The Online Ladder in Fiery Void is primarily aimed at helping players create well-balanced, interesting games, as well as provide some bragging rights along the way.  
                It works similar to a handicap in golf, whereby the difference in ratings between players is added as a % bonus to the lower rated player.  
                So if there was a difference of 5 rating, then the lower-rated player gets 5% extra points!</li>
            <li>To set-up a Ladder game, create a game as usual and tick the Ladder Game checkbox. You’ll see the ‘View Ladder’ button next to this option, 
                this allows you to see current ratings and even calculate the points difference that should apply against a particular player / populate the team slots with these values.</li>
            <li>Alternatively you can set up an open Ladder game without using this feature and just set points values in team slots in the usual way.  
                When a player takes the other slot in the game their points will be adjusted automatically.</li>
            <li>Either way, decide on the specifics for the game (Points, Map; Standard vs Simultaneous Movement, etc.) and then click Create Game.  
                Note, Ladder games are competitive matches so only two players can take part, and only one slot is allowed per team. </li>
            <li>When the game ends and one player surrenders (with at least one whole turn being played), the winner will have their ranking increased by one on the Ladder, and the loser has their ranking reduced by the same amount.  
            You can view players' match history form the last three months, including your own, by clicking 'View Ladder' on the Fiery Void Home Page and then clicking on their name.</li>                                              
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
                    <li>Right-click Electronic Warfare (EW) Add Button - Sets that EW type to the max available amount (Note- long press on touchscreen).</li>
                    <li>Right-click Electronic Warfare (EW) Remove Button - Sets that EW type to zero (Note- long press on touchscreen).</li>                      
                    <li>Right-click Firing Mode - Change fire mode on all similar undeclared weapons.</li>
                    <li>Right-click Defensive Fire - Enable defensive fire on all similar undeclared weapons.</li>
                    <li>Right-click Cancel Move - Cancel all current moves for the unit.</li>
                    <li>Right-click Cancel Firing Order - Cancel firing orders for all similar weapons.</li>
                    <li>Right-click Move Forward - Move forward using all remaining movement.</li>
                    <li>Richt-click + or - Jink Buttons - Sets jinking levels to maximum allowed or 0 respectively (Note- long press on touchscreen).                    
                </ul>
            </li>
            <br>
            <li><b>Ship Window (the Ship Control Sheer (SCS) that opens when you -rightclick a unit):</b>
                <ul class="circle-list">
                    <li>Drag the title bar - Move the window around the screen.  On a computer the next window you open on that side of the screen appears where you left the last one (until you reload the page).</li>
                    <li>Drag the bottom corner grip - Resize the window.  The grip is on the corner facing away from the edge the window is docked to (bottom-right for a left-hand window, bottom-left for a right-hand one), so you always drag outwards to make it bigger; a right-hand window stops growing at the left edge of the screen.  The size you settle on is used by every later window on that side of the screen - the two sides are remembered separately - and is kept on your device for your next session, so you can set it once to suit your screen (Note- drag it with your finger on touchscreen).</li>
                    <li>Double-click the corner grip, or the title bar - Reset the window to its normal size (Note- double-tap on touchscreen).  The title bar does it too, so you can always get back even if you have made a window so large that its corner sits off the edge of the screen.</li>
                    <li>Phones and tablets - Windows are scaled automatically to fit your screen before any resizing of your own, with extra size in portrait where space is tightest.  Anything that does not fit scrolls inside the window, and the title bar stays pinned to the top of it so it can always be dragged.</li>
                    <li>Hit Chart / Ship Art / Ship Stats / Notes buttons - Click one to open its panel, click anywhere outside to close it (Ship Stats and Notes also peek on mouse hover).  Ship Stats shows this turn's actual turn cost, turn delay, profile and initiative, with any figure the current situation has changed shown in yellow.</li>
                    <li>Ship Art - Shows the unit's artwork full colour in place of its sections; click the button again to go back.</li>
                    <li>Hover a system - Show its details in the info panel (Note- press and hold on touchscreen; a plain tap is the action, e.g. power, firing or repair).</li>
                    <li>Click the artwork behind the sections - Show the unit's own details rather than a system's.</li>
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
