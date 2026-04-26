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
                <li><a href="#escorts">Fighter Escorts</a></li>                
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
            <li>Many factions have access to Breaching Pods, which come equipped with marines that can undertake boarding actions.  
                During Fleet Selection, one pod can be purchased for every medium ship or HCV with hangar capacity for medium fighters, heavy fighter or assault shuttles, 
                two for every capital ship with these hangars, and four for enormous units or bases.  Assault ships (i.e., those vessels containing ‘Assault’ in their name) can carry double these allowances
                providing they have the hangar space to permit it.</li>
            <br>              
            <li>in battle, Pods can attempt to attach to enemy ships in the same hex and deliver Marines to undertake a selection of missions (Capture Ship, Sabotage and Rescue) during the Firing Phase.</li>
            <li>Pods will initially roll to attach on a d10 in the same way that normal weapons roll to hit enemies, 
                but the calculation is very different and success is automatic if they are moving faster than the target ship and the speed difference between the two units is not higher than pod's thrust rating. 
                If the speed difference to target is greater than pod thrust rating it is simply unable to attach. 
                If the target is moving faster, each point of speed difference is -10% chance to attach.  
                Pods cannot attach to ships with Advanced Armor and certain factions like Llort have +1 to attach rolls.</li>
            <li>There is a limit to how many pods can attached to enemy ships based on their size, 12 pods can attach to bases, 8 to Capital Ships, 4 to HCVs, 2 to Medium Ships and only 1 to LCVs and OSATS. 
                If more than this number try to attach and/or deliver marines then extra attacks over these limits will automatically fail.</li>    
            <li>Breaching Pods will remain attached to a vessels facing structure block until they choose to Detach in the Movement Phase, or the vessel is destroyed 
                (providing that the structure location the pod is attached to is NOT also destryed).  If the structure block a pod is attached to IS detroyed before the Pod detaches, then the Pod is automatically destroyed.  
                While attached, Pods matach speed and heading with their host ship, and suffer -10 Initiative penalty.  When the Detach they will automatically face away from the host ship to which they were attached.</li>
            <li>Units can shoot at attached pods, providing they are in arc of the structure location the pod is attached to, and will roll to hit them as normal. However any shot aimed at a pod will automatically hit the vessel it is attached to as well.</li>                 
            <br>               
            <li>After the attach roll, the Pod will attempt to deliver its marines by rolling on a d10 again on the following table, with a base chance of 50% to successfully board the vessel.  
                Depedning on the roll, unsuccessful marines may be lost in the attempt or return safely their pod.</li>                           
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
                    If the attacking marines manage to defeat all defenders, the enemy ship is immediately be disabled for the remainder of the battle so long as there is still at least one attacking marine unit on board.
                    After a successful capture, one marine unit will remain on the ship and the remainder will return to attached Breaching Pods if available
                    </li>
                    <br>                     
                    <li><strong>SABOTAGE: </strong>Using this firing mode, Marines can attempt to damage a specific system on an enemy ships (by making a called shot against it using the usual rules) 
                    or, if Desperate Rules are in effect, Wreak Havoc on the enemy ship (e.g. inflict minor damage to a Primary system or penatlies to ship's EW/Initiative/Thrust/Defence Profile) by targeting the ship itself, and not a specific system.   
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
                        otherwise they will swtich to a Wreak Havoc mission on the enemy vessel.</li>
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
            <li>Note - Ships euipped with Grappling Claws use the same rules to attach and deliver Marines as described above, however they do not currently attach to the enemy vessel in the same way as Breaching Pods.</li>
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


        <h3 id="escorts" >Fighter Escorts</h3>
        <ul>
            <li>Fighter units can escort friendly ships that are in the same hex in order to use their guns to help intercept ballistic weapons targeted at that ship(s).</li>
            <li>To escort a ship, the fighter unit must start AND end its movement in the same hex as the ship they are escorting.  Where this is the case,
                the fighters will use their weapons to intercept ballistics on behalf of the ship providing all other conditions of intercept are true e.g. 
                their weapons have an intercept rating, the incoming shot is in arc, the fighters are not jinking etc.        
            </li>
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
            <li>There are three types of mines in Fiery Void, Captor, Direct Enrgy Weapons (DEW) and Proximity.  These are described in more detail below.</li>
            <li>Buying & Deploying Mines:
                <ul class="circle-list">
                    <li>In game where the 'Mines Allowed' option has been enable in Create Game screen you can purchase mines in Fleet Selection from your Facion's list.</li>
                    <li>Unlike ships, mines can be bought in batches and when you click 'Add to Fleet' you will be able to choose the number of mines of that type you wish to buy, 
                        along with any Enhancements you wish to include (see in <a style="font-size: 14px;" href="./ammo-options-enhancements.php" target="_blank" rel="noopener noreferrer">Ammo, Options & Enhancements</a> for more details on Mine Enhancements).</li>
                    <li>There is a 100 point premium for taking any mines at all, plus an additional +10% to the unit cost of all mines per type of mine taken after the first (e.g. from the three type Captor, DEW and Proximity).</li>  
                    <li>Once the game starts you can choose to place mines individually or use the 'Deploy Minefield' button to place any number of mines randomly within a selected area.  
                        You can deploy mines anywhere within the map, so long as it's not withint 10 hexes of an enemy deployment zone.</li>                                                          
                </ul>
            </li>              
            <li>Once deployed, mines will initially be stealthed and won't become visible to enemies until they attack, or are detected.  
                On the turn that they are deployed or spawned you will have the opportunity to tailored their ranges for 
                Captials/HCVs, MCV/LCVs, Fighters from 0 up to their maximum range. 
                You do this by clicking on the mine weapons system icon during Deployment/Pre-Turn Orders phase.  
                Once set these ranges will apply for the rest of the game, and if you choose not to set the ranges they will default to their maximum range.  
                You can set the ranges of all mines of the same type using the propagate buttons in the Mine Settings menu.</li>
            <li>You can detect mines by applying EW points to 'Detect Mines' in the Initial Orders phase, an EW option that will only appear if the opponenet has mines in the game.  
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
                    <li>All mines have a basic profile of 60,</li>                    
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
                    <li>These mines come equipped with weapons and will fire these autoamtically at the first viable target it enoucnters during the movement phase, 
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
                    <li>Right-click Unit - Select it (if yours) and open controls.</li>
                    <li>Right-click Weapon - Select all similar weapons (e.g., all fighter guns).</li>
                    <li>Right-click Power Button - Toggle all similar systems.</li>
                    <li>Right-click Firing Mode - Change fire mode on all similar undeclared weapons.</li>
                    <li>Right-click Defensive Fire - Enable defensive fire on all similar undeclared weapons.</li>
                    <li>Right-click Cancel Move - Cancel all current moves for the unit.</li>
                    <li>Right-click Cancel Firing Order - Cancel firing orders for all similar weapons.</li>
                    <li>Right-click Move Forward - Move forward using all remaining movement.</li>
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