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
    <h2 id="top" style="margin-top: 5px">FLEET CHECKER RULES</h2>

    <h2 id="contents" style="margin-top: 25px">TABLE OF CONTENTS</h2>

    <ul class = index-list>
        <li><a href="#general">GENERAL NOTES</a> </li>
        <li><a href="#b5wrsComp">FLEET COMPOSITION RULES IN BABYLON 5 WARS</a></li>                        
        <li><a href="#FVcomp">FIERY VOID FLEET COMPOSITION RULES</a>
           <ul class="sub-list">
                <li><a href="#FVchecks">What the Fleet Checker Actually Checks</a></li>
                <li><a href="#variants">Variant Restrictions</a></li>
                <li><a href="#deployment">Deployment Restrictions</a></li>                                                                                 
            </ul>     
      </li>                                                                 
        <!-- Add more sections here -->
    </ul>

<h2 id="general" style="margin-top: 30px;">GENERAL NOTES</h2>
    <p>Fleet Checker will tell you whether your fleet conforms to standard battle fleet composition rules, and also higlight any elements that might need manual check or opponent's consent.  
        If you don't wish to use the rules applied by the Fleet Checker, or for some reason need to deploy something different (perhaps scenario-related), the game will not block you in any way, 
        providing that yor total points value does not exceed your points allowance. Just make sure you're on the same page as your opponent and have fun!
        However, if you want standard and reasonably balanced match - it's generally a good idea to conform to Fleet Checker's rules. </p>
    
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h2 id="b5wrsComp">FLEET COMPOSITION RULES IN BABYLON 5 WARS</h2>
        <p>In order to allow players to compete in fair conditions a set of tournament rules was created by AoG. 
            Fleets created using these rules tend to be well-rounded, and create fair battles, so it's worthwhile to use them even outside of tournament conditions.  
            The original B5 Wars rules made a set of assumptions:</p>
        <ul style="margin-top: 10px;">
            <li>3500 points game size.</li>
            <li>42x30 fixed map.</li>
            <li>The player is only using one faction.</li>
            <li>Ancient and ISA fleets are not allowed.</li>
        </ul>

    <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3>Original B5 Wars Fleet Composition Rules:</h3>
        <ul>
            <li>At least one Jump Drive is required.</li>
            <li>At least one Capital Ship is required.</li>
            <li>All units of LCV size or smaller need to have hangars present (eg. cannot bring fighters based elsewhere).</li>
            <li>At least half of fighter hangars need to be filled (eg. cannot bring (too many) empty carriers) (this rule is only relevant for actual fighters, not for other craft like assault shuttles or LCVs).</li>
            <li>Deployment restrictions observed (see end of file).</li>
            <li>Variant restrictions observed (see end of file).</li>
            <li>At most two Uncommon and one Rare unit in fleet (on top of regular variant restrictions).</li>
            <li>At most three ships based on the same hull (fighters and other small craft are exempt).</li>
            <li>Static objects (bases, OSATs, mines...) are not allowed.</li>
            <li>Only actual combat units are allowed (so eg. no transports (even military transports) or diplomatic ships).</li>
            <li>Some factions (notably ISA (all-White Star fleet) and Ancients) were deemed not tournament-legal.</li>                                                            
        </ul>

    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h2 id="FVcomp">FIERY VOID FLEET COMPOSITION RULES</h2>
        <p>For Fiery Void the rules above were modified to allow balancing different fleet sizes and Ancient fleets. 
            This tailoring was made by FV team and is not B5Wars-official in any way (except it was inspired by tournament rules above). 
            If You're looking for a balanced game with these rules, it's advisable to use:</p> 

        <ul>
            <li>42x30 fixed map (or similar).</li>  
            <li>The player is only using one faction.</li>
            <li>Both players use fleets of similar power level (see Faction-Tiers file - it contains subjective grouping of available fleets into "power levels" suitable for balanced pickup battles).</li>            
        </ul>

        <h3  id="FVchecks">What the FV Fleet Checker Actually Checks</h3>
        <ul>
            <li>At least one Jump Drive is required.</li>
            <li>Capital Ships required (minimum number, You can take more):                
                <ul class="circle-list">
                    <li>(Young) 1 per 5000 points (no capital ship is required below 3000 points).</li>
                    <li>(Ancient) 1 per 15000 points (no capital ship is required below 5000 points).</li>
                </ul>
            </li>
            <li>Most units of LCV size or smaller need to have hangars present (there are exceptions, especially for custom factions from other universes).</li>
            <li>At least half of fighter hangars need to be filled (this rule is only relevant for regular fighters, not for other craft like assault shuttles, LCVs or even SHFs).</li>
            <li>At most 1 flight smaller than 6 craft (checked only for craft with maximum flight size of 6 or more).</li>
            <li>Variant restrictions observed (see below).</li>
            <li>Deployment restrictions observed (see below).</li>
            <li>Uncommon/Rare units are limited at fleet level (on top of Variant Restrictions above) to at most:                
                <ul class="circle-list">
                    <li>(Young) Uncommon: 2 up to 3500 points, and additional one per every further full 1000 points (so 3rd Uncommon slot is unlocked at 4500 points).</li>
                    <li>(Ancient) Uncommon: 2 up to 8000 points and additional one per every further full 4000 points (so 3rd Uncommon slot is unlocked at 12000 points).</li>
                    <li>Rare: half of Uncommon allowance (rounded DOWN).</li>                    
                </ul>
            </li>
            <li>Maximum number of ships being variants of same hull (units that require hangar space are exempt)                
                <ul class="circle-list">
                    <li>(Young) One per full 1100 points (with minimum of 2).</li>
                    <li>(Ancient) One per full 3000 points (with minimum of 2).</li>              
                </ul>
            </li>            
            <li>Static objects (bases, OSATs) are not allowed.</li>
            <li>Non-combat units (freighters, exploration ships, VIP transports...) are not allowed.</li>
            <li>Presence of non-standard elements (enhancements, custom units, Special variants) is called for player attention (but doesn't mark the fleet as incorrect).</li>                                                            
        </ul>

        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="variants" >Variant Restrictions</h3>
        <p>Every unit in game has variant rating, in one of categories:</p> 
        <ul>
            <li>Common (C) - Unit is a common example of what is built on a given hull. No limit.</li>  
            <li>Uncommon (U) - Not as common, they're found in numbers only in presence of their more mundane cousins. For every three units of any given hull, or fraction thereof, one unit may be Uncommon.</li>
            <li>Rare (R) - Very rare units, almost never deployed in numbers (often command variants). One of every three Uncommon slots may instead be taken by Rare unit.</li>
            <li>Unique (Q) - Only very few (possibly even one) exist. For fleet selection rules treat them as Rare, but with added limitation than no more than 1 may be present.</li>  
            <li>Special (X) - Non-standard restrictions that require special player attention. Actual restriction is present in unit description. Fleet Checker will NOT check them correctly, but will bring them to player's attention with appropriate note.</li>                        
        </ul>

        <p>What the above means for number of variously restricted units:</p> 
        <ul>
            <li>1 ship total (on a given hull): may be of any restriction,</li>
            <li>2 ships: 1 any, 1 has to be Common,</li>    
            <li>3 ships: 1 any, 2 Common,</li>    
            <li>4 ships: 1 any, 1 U or C, 2 Common,</li>    
            <li>5 ships: 1 any, 1 U or C, 3 Common,</li>    
            <li>6 ships: 1 any, 1 U or C, 4 Common,</li>    
            <li>7 ships: 1 any, 2 U or C, 4 Common,</li>    
            <li>8 ships: 1 any, 2 U or C, 5 Common,</li>    
            <li>9 ships: 1 any, 2 U or C, 6 Common,</li>    
            <li>...and beginning with 10th ship you start again.</li>                                                                                                                                       
        </ul> 
        <p>For fighters rule is exactly the same, but you count entire flights instead of individual craft.</p>  
        <p>Please do not abuse variable flight size - keep Your restricted flights the same size as regular ones! 
            Automatic checker will let You take 2 full-sized Rare flights with 8 single-craft Common "flights", but it's really not good sportsmanship!</p> 

        <a class="back-to-top" href="#top">↩ Back to Top</a>

        <h3 id="deployment" >Deployment Restrictions</h3>
        <p>Every unit in game has availability rating. It's one of three categories:</p> 
        <ul>
            <li>Unlimited (100%) - unit is common in a given fleet</li>  
            <li>Limited (33%) - mostly specialty support units, like dedicated missile ships and ELINT ships.</li>
            <li>Restricted (10%) - rarest of units, like newest/experimental designs and largest battleships.</li>
            <li>Unique (Q) - only very few (possibly even one) exist. For fleet selection rules treat them as Rare, but with added limitation than no more than 1 may be present.</li>  
            <li>Special (X) - non-standard restrictions that require special player attention. Actual restriction is present in unit description. Fleet Checker will NOT check them correctly, but will bring them to player's attention with appropriate note.</li>                        
        </ul>

        <p>To put it simply, total value of units in a given category cannot exceed appropriate percentage (so you can spend up to 33% of total fleet allowance on Limited units, for example). There are two additional rules though:</p> 
        <ul>
            <li>If you have only one unit in a given category, it can exceed the limit (to allow taking such units in small battles, basically).</li>  
            <li>If you have Restricted unit - it needs another unit as escort (such rare vessels aren't usually allowed to wander all alone).</li>
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