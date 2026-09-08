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
<body style="background: url('./img/webBackgrounds/aoe.jpg') no-repeat center center fixed; background-size: cover;">

<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <a href="games.php">Back to Game Lobby</a>
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<main class="container">
  <section class="faq-panel">
    <h2 id="top" style="margin-top: 5px">AMMO, OPTIONS & ENHANCEMENTS IN FIERY VOID</h2>

    <h3 id="creating-account" style="margin-top: 25px">TABLE OF CONTENTS</h3>

    <ul class="index-list">
        <li><a href="#missiles">Missiles</a>
           <ul class="sub-list">
                <li><a href="#shipbornemissiles">Shipborne Missiles</a></li>
                <li><a href="#fightermissiles">Fighter Missiles</a></li>
            </ul>
        </li>
        <li><a href="#shells">Grome Shells</a></li>                        
        <li><a href="#enhancements">Basic Enhancements</a>
           <ul class="sub-list">
                <li><a href="#shipenhancements">Ship Enhancements</a></li>
                <li><a href="#mineEnhancements">Mine Enhancements</a></li>                   
                <li><a href="#fighterenhancements">Fighter Enhancements</a></li>                
            </ul>
        </li>
        <li><a href="#factionenhancements">Faction Enhancements</a>
           <ul class="sub-list">
                <li><a href="#ancients">Ancients</a></li>
                <li><a href="#drazi">Drazi Freehold</a></li>                
                <li><a href="#ipsha">Ipsha Baronies</a></li>
                <li><a href="#kirishiaclords">Kirishiac Lords</a></li>                
                <li><a href="#markab">Markab Theocracy</a></li>
                <li><a href="#mindriders">The Mindriders</a></li>                  
                <li><a href="#shadows">Shadow Association</a></li>
                <li><a href="#thirdspace">Thirdspace</a></li>                    
                <li><a href="#vorlons">Vorlon Empire</a></li>
                <li><a href="#walkers">Walkers of Sigma-957</a></li>
            </ul>
      </li>
        <li><a href="#systemenhancements">System Enhancements</a>
           <ul class="sub-list">
                <li><a href="#usingsystemenhancements">Buying &amp; Using Them</a></li>
                <li><a href="#systemrefits">Available Refits</a></li>
                <li><a href="#systemenhancementnotes">Points, Damage &amp; Saved Fleets</a></li>
            </ul>
      </li>
        <!-- Add more sections here -->
    </ul>

    <!--<p>On this page, you'll find information about the various Ammo, Options and Enhancements that are available to the different factions in Fiery Void</p> -->

    <h3 id="missiles" style="margin-top: 20px;">Missiles</h3>
    <p>Many factions in Fiery Void use missiles, and when you purchase a missile-carrying ship in Fleet Selection you will have the chance to purchase any missile ammo that faction has available. 
    Ships will generally come preloaded with basic ammo for their respective launchers, so any missiles types you buy at Fleet Selection will be in addition to these. 
    Note -  while the amount of any given missile you can buy is limited by magazine size, total amount of missiles is not. 
    This means your ammo magazine may show more missiles available than it can actually hold but you cannot actually launch more missiles than magazine total. 
    Essentially, the extra missiles purchased provide you with extra variety, but not actual extra missiles in your magazine!</p>

    <p>Any missile available in magazine can be fired by any launcher (that is capable of firing it), missiles are not directly tied to particular mounts. 
    This is different for weapons that do store ammo directly on mount - but such weapons usually can only hold one kind of ammo. 
    This is particularly important for fighters (as ships' cavernous magazines are unlikely to run out during a battle). 
    Note also that fighters usually start with empty magazine (although some missile entry is present, for technical reasons) and will lose a missile they are carrying if they take 2 or more damage from a single shot. 
  In addition, fighters can dock on firendly carriers and reload the missiles they started the game with, providing the carrier has purchased the Extra Ordnance Resource enhancement (see below) and there are sufficient ordnance points to cover the cost of the replacement missiles.</p>

    <p>Missiles do not use firing ship\'s OEW and their built-in guidance package is usually combined into weapons' Fire control, rather than being kept separate.  Fighter missiles do benefit from the fighter's offensive bonus.</p>
    <p>Ammo listings usually mention ISD year for when that missile type becomes available (if two dates the first refers to Kor-Lyan only). This is not enforced by Fiery Void so players will need to do this themselves.</p>
    <p style="margin-bottom: 0px;">The table below summarises the base stats for different missile types.  Note - These stats can be modified by the type of launcher as well e.g. a L-Rack Missile Launcher would add +10 to the listed ranges below.</p>
    
    <h4 id="shipbornemissiles" style="margin-top: 20px;  margin-bottom: 15px;">Shipborne Missiles:</h4>       
    <ul>    
      <li><strong>Class B - Basic Missile:</strong> - Range 20 - Damage 20 - Fire Control: +3/+3/+3,</li> 
      <li><strong>Class A - Anti-fighter Missile (2231)</strong> - Range 15 - Damage 15 - Fire Control: +6/+3/+3,</li>      
      <li><strong>Class C - Chaff missile (2230)</strong> - Range 20 - Damage 0 - Fire Control: +3/+3/+3 - Applies -15% to non-ballistic shots from any target hit,</li>  
      <li><strong>Class D - Light Missile (2178)</strong> - Range 15 - Damage 12 - Fire Control: +3/+3/+3,</li>   
      <li><strong>Class F - Flash Missile (2225)</strong> - Range 20 - Damage 20 - Fire Control: +3/+3/+3 - Deals Flash damage,</li>  
      <li><strong>Class H - Heavy Missile (2225)</strong> - Range 10 - Damage 30 - Fire Control: +0/+3/+3,</li>         
      <li><strong>Class HM - Homing Missile (2252)</strong> - Range 20 - Damage 20 - Fire Control: +3/+3/+3 - Kor-Lyan only. If it misses without being shot down it stays in play and attacks again next turn, launching from its target's previous hex. Defensive fire that would have turned the miss into a hit destroys it instead. Lost once its cumulative travel exceeds its distance range. The enemy is not told a missile is Homing until it has missed once.</li>
      <li><strong>Class I - Interceptor Missile (2250/2263)</strong> - Range 0 - Damage 0 - Fire Control: -/-/-,  Fires defensively for -30 intercept rating against ballistic weapons,</li>      
      <li><strong>Class L - Long Range Missile (2225)</strong> - Range 30 - Damage 15 - Fire Control: +3/+3/+3,</li>  
      <li><strong> Class K - Starburst Missile (2260/2264)</strong> - Range 15 - Damage 10*[D3+3] - Fire Control: +3/+3/+3 - Deals damage in pulse mode,</li>   
      <li><strong>Class KK - Kinetic Missile (1976)</strong> - Range 60 [but -5% per hex range penalty after 15 hexes] - Damage 18 - Fire Control: +3/+3/+3 - Orieni only.  Deals matter damage,</li>  
      <li><strong>Class J - Jammer Missile (2239)</strong> - Range 15 - Damage 0 - Fire Control: -/-/-, Kor-Lyan only. All ships within 5 hexes receive two points of Blanket DEW (BDEW),</li>  
      <li><strong>Class M - Multiwarhead Missile (2256)</strong> - Range 20 - Damage 10*6 - Fire Control: +3/-/-, Can only target fighters, splits into 6 submunitions,</li>      
      <li><strong>Class P - Piercing Missile (2244)</strong> - Range 20 - Damage 30 - Fire Control: -/+3/+3 - Deals piercing damage,</li>  
      <li><strong>Class S - Stealth Missile (2252)</strong> - Range 20 - Damage 20- Fire Control: +3/+3/+3 - Kor-Lyan only, missile target not revealed to enemy player.</li>   
      <li><strong>Class X - HARM Missile (2248)</strong> - Range 20 - Damage 0 - Fire Control: -/+3/+3 - Hit chance increased by 5% per point OEW and CCEW target is using, causes -1d6 scanner output next turn.</li>       
      <li><strong>Class Z - Antimine Missile (2249)</strong> - Range 20 - Damage 15 - Fire Control: -/-/- - Hex targeted.  Attacks any mines within 3 hexes. Hit chance +15% if mine in same hex.</li>       
    </ul>    
    </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>
    
    <h4 id="fightermissiles" style="margin-top: 20px; margin-bottom: 15px;">Fighter Missiles:</h4>       
    <ul>  
      <li><strong>Class FB - Basic Fighter Missile (2165)</strong> - Range 10 - Damage 10 - Fire Control: +3/+3/+3,</li> 
      <li><strong>Class FD - Dropout Missile (2221/2245)</strong> - Range 10 - Damage 6 - Fire Control: +3/+1/+1 - Increase chance of dropout on fighters,</li>      
      <li><strong>Class FH - Heavy Fighter Missile(2226/2245)</strong> - Range 5 - Damage 15 - Fire Control: +1/+3/+3 - Limited to 1 per fighter (2 per Super Heavy Fighter),</li>  
      <li><strong>Class FL - Long Range Fighter Missile (2226/2245)</strong> - Range 15 - Damage 8 - Fire Control: +3/+3/+3,</li>   
      <li><strong>Class FY - Dogfight Missile (2165)</strong> - Range 8 - Damage 6 - Fire Control: +3/+3/+3 - Cannot snap-fire in Fiery Void.</li>
      <li><strong>Class DUM - Dummy Missile (2145)</strong> - Does not fire, but will always be the first missile lost to damage to preserve real munitions.</li>          
    </ul>

    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h3 id="shells" style="margin-top: 20px;">Grome Shells</h3>
    <p>The Grome are unique in that they have access to several types of shell for their railguns.  These can be purchased along with a ship in the same way as missiles in Fleet Selection.
      Whilst the amount of damage dealt will change depending on the size of railgun (Light, Medium or Heavy), the bonus effect provided by a shell will always be the same.
    </p>
    <ul>  
      <li><strong>Flash Shell</strong> - Deals Plasma damage in Flash mode,</li> 
      <li><strong>Scatter Shell</strong> - Gives Pulse properties to weapon with one shot by default, but with a grouping bonus of 25 i.e. for each 25% rolled under the % hit chance, an extra shot will hit target,</li>      
      <li><strong>Heavy Shell</strong> - Deals extra damage,</li>  
      <li><strong>Long Range Shell</strong> - Medium and Heavy Railguns only.  Improves range penalty, but lowers damage,</li>   
      <li><strong>Ultra Long Range Shell</strong> - Heavy Railguns only.  Further increases range but with even lower damage.</li>  
    </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h3 id="enhancements">Unit Enhancements</h3>
    <p>Unit enhancements are generally quite pricy, although not always.  For pickup battles - they're very much optional, and best to check with opponent that they are ok to use if it's not stated in game description. 
    Their main purpose is often to be an option in a scenario, a campaign tool where they can represent units accumulating experience, or a way of spending a few last points when making your fleet.</p>
    <p>Some enhancements represent regular unit options rather than actual enhancements to the ship. Fighter Navigators for example, or Markab Religious Fervor. 
    Additionally, not all enhancements improve the performance of units, some like Poor Crew actually make it worse but reduce the price of the unit in compensation for this.</p>    

    <h4 id="shipenhancements" style="margin-top: 20px; margin-bottom: 15px;">Ship Enhancements:</h4>
    <ul>  
      <li><strong>Elite Crew</strong>
        <ul class="circle-list">
            <li>Effect: +1 to hit with all weapons, +5 Initiative, +2 Engine, +1 Sensors, +2 Reactor power, -5% Defence Rating, -2 to critical results</li>
            <li>Points Cost: +40% of ship cost (second time: +60%)</li>
            <li>Limit: 2</li>
        </ul>      
      </li>
      <li><strong>Ordnance Reserve</strong>
        <ul class="circle-list">
            <li>A pool of points used to re-stock missile and torpedo allocations to docked fighters</li>
            <li>Points Cost: Up to 200pts can be bought, and the cost of missiles to restock docked fighters will be drawn from this reserve.</li>
            <li>Limit: 200pts</li>
        </ul>
      </li>
      <li><strong>Extra Marines</strong>
        <ul class="circle-list">
            <li>A pool of additional marine units used to re-stock the Marines weapon on Breaching Pods that dock back into the carrier (1 marine per pod per turn while docked).</li>
            <li>Points Cost: 10pts per contingent — each contingent is a single marine unit.</li>
            <li>Limit: 1% of the ship's base Combat Point value, rounded up (so a 600-PV ship can buy up to 6 contingents).</li>
        </ul>
      </li>
      <li><strong>Assault Shuttle / Fighter Hangar Conversions</strong>
        <ul class="circle-list">
            <li>Converts either a fighter slot into an assault shuttle slot, or vice versa</li>
            <li>Points Cost: 5pts per slot converted</li>
            <li>Limit: Equal to the number of assault shuttle/fighter slots</li>
        </ul>      
      </li>
      <li><strong>Shuttle to Breaching Pod Conversion</strong>
        <ul class="circle-list">
            <li>Converts one of a ship's default shuttle slots to a hangar slot that can accommodate a Breaching Pod unit (replacing the shuttle in process).</li>
            <li>Points Cost: 10pts per slot converted</li>
            <li>Limit: Equal to the number of default shuttle slots.</li>
        </ul>      
      </li>         
      <li><strong>Shuttle to Minesweeping Shuttle Conversion</strong>
        <ul class="circle-list">
            <li>Convert one of a ship's default shuttle units to a minesweeping shuttle.</li>
            <li>Points Cost: 10pts per shuttle converted</li>
            <li>Limit: Equal to the number of default shuttles.</li>
        </ul>      
      </li>              
    <li><strong>Identify Friend or Foe (IFF)</strong>
        <ul class="circle-list">
            <li>Effect: Prevent ballistic mines from attacking friendly units</li>
            <li>Points Cost: 4 pts per Ballistic Mine Launcher</li>
            <li>Limit: 1</li>
            <li>Notes: Only available to ships equipped with Ballistic Mine Launchers</li>              
        </ul>      
      </li>      
      <li><strong>Improved Engine</strong>
        <ul class="circle-list">
            <li>Effect: +1 Thrust</li>
            <li>Points Cost: 12 + (4 / turn cost), rounded up</li>
            <li>Limit: Engine thrust rating / 2</li>
        </ul>      
      </li>                    
      <li><strong>Improved Reactor</strong>
        <ul class="circle-list">
            <li>Effect: +1 to 4 Power (depends on unit size, from MCV to Enormous)</li>
            <li>Points Cost: 10 * amount of power added (doubled if ship has power deficit)</li>
            <li>Limit: 1</li>
        </ul>      
      </li> 
      <li><strong>Improved Sensors</strong>
        <ul class="circle-list">
            <li>Effect: +1 Scanner rating</li>
            <li>Points Cost: New Scanner rating * 5 (doubled for Elint Sensors / Advanced Sensors)</li>
            <li>Limit: 1</li>
        </ul>      
      </li>       
      <li><strong>Poor Crew</strong>
        <ul class="circle-list">
            <li>Effect: -1 to hit with all weapons, -5 Initiative, -1 Engine, -1 Sensors, -1 Reactor power, +5% Defence Ratings, +2 to critical rolls</li>
            <li>Points Cost: -15% of ship cost (-10% if selected a second time)</li>
            <li>Limit: 2</li>
        </ul>      
      </li> 
      <li><strong>Sluggish</strong>
        <ul class="circle-list">
            <li>Effect: -5 Initiative</li>
            <li>Points Cost: -6 per step</li>
            <li>Limit: 7</li>
        </ul>      
      </li> 
      <li><strong>Vulnerable to Criticals</strong>
        <ul class="circle-list">
            <li>Effect:  +1 to Critical rolls</li>
            <li>Points Cost: -4 per step</li>
            <li>Limit: 4</li>
        </ul>      
      </li>
    </ul>   
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="fighterenhancements" style="margin-top: 15px;">Fighter Enhancements:</h4>

    <p><strong>Note</strong> - All costs listed below are on per fighter basis e.g. you pay for each fighter in the flight.</p>
    <ul>
      <li><strong>Expert Motivator</strong>
        <ul class="circle-list">
            <li>Effect: -2 modifier to flight's dropout rolls</li>
            <li>Points Cost: 10% of craft price (rounded up)</li>
            <li>Limit: 1</li>
        </ul>      
      </li>
      <li><strong>Improved Targeting Computer</strong>
        <ul class="circle-list">
            <li>Effect: +1 Offensive Bonus (OB)</li>
            <li>Points Cost: New OB *3</li>
            <li>Limit: 1</li>
        </ul>      
      </li>  
      <li><strong>Improved Thrust</strong>
        <ul class="circle-list">
            <li>Effect: +1 thrust</li>
            <li>Points Cost: New thrust rating</li>
            <li>Limit: </li>
        </ul>      
      </li>  
      <li><strong>Minesweeper Conversion</strong>
        <ul class="circle-list">
            <li>Effect: Sets Offensives Bonus to 4, and gives shuttles the minesweeper property, allowing them to use full Offensive Bonus for mine detection.</li>
            <li>Points Cost: 10 or 20% of craft cost, whichever is higher</li>
            <li>Limit: 1</li>
            <li>Notes: Shuttles only.</li>
        </ul>      
      </li>        
      <li><strong>Navigator</strong>
        <ul class="circle-list">
            <li>Effect: Navigator provides 360 degree missile guidance, +5 Initiative</li>
            <li>Points Cost: 10</li>
            <li>Limit: 1</li>
        </ul>      
      </li>  
      <li><strong>Poor Training</strong>
        <ul class="circle-list">
            <li>Effect: -5 Initiative, -1 thrust, -1 OB, +1 Defence Rating, +2 to dropout rolls</li>
            <li>Points Cost: -10% of craft price (rounded up)</li>
            <li>Limit: 1</li>
        </ul>      
      </li>

      <li><strong>Elite Pilot</strong>
        <ul class="circle-list">
            <li>Effect: Pivot cost is reduced by one thrust. Initiative +5. Offensive Bonus +1. Profile -1</li>
            <li>Points Cost: -10% of craft price (rounded up)</li>
            <li>Limit: 1</li>
            <li>Notes: Custom Star Wars faction only.</li>            
        </ul>      
      </li>           
    </ul>      
    <a class="back-to-top" href="#top">↩ Back to Top</a>    


    <h4 id="mineEnhancements" style="margin-top: 15px;">Mine Enhancements:</h4>

    <p><strong>Note</strong> - All costs listed below are on per mine basis e.g. you pay for each mine individually.</p>
    <ul>
      
        <li><strong>Command Controller</strong>
        <ul class="circle-list">
            <li>Effect: Allows you to manually choose from valid targets, and overwrite mine's automatic targeting.  Includes the 'Identify Friend or Foe (IFF) System' enhancement' as well.</li>
            <li>Points Cost: 33%</li>
            <li>Limit: 1</li>
            <li>Notes: Proximity Mines will trigger a Pre-Firing phase where you can target any ships that passed into range during movement. 
              If you choose not to manually target a unit, the mine will still attack using it's default targeting logic.</li>
        </ul>      
      </li> 
        <li><strong>Flexible Targeting</strong>
        <ul class="circle-list">
            <li>Effect: Allows you to apply different range settings for different weapons during Deployment.</li>
            <li>Points Cost: 25%</li>
            <li>Limit: 1</li>
            <li>Notes: Only available to DEW mines which have more than one weapon.</li>
        </ul>      
      </li>       
      <li><strong>Identify Friend or Foe (IFF)</strong>
        <ul class="circle-list">
            <li>Effect: Prevent mines from attacking friendly units</li>
            <li>Points Cost: 10%</li>
            <li>Limit: 1</li>           
        </ul>      
      </li> 
      <li><strong>Improved Accuracy</strong>
        <ul class="circle-list">
            <li>Effect: +1 Accuracy</li>
            <li>Points Cost: 10% for Captor Mines, 20% for DEW Mines</li>
            <li>Limit: 5</li>
            <li>Notes: Captor & DEW only</li>
        </ul>      
      </li>  
      <li><strong>Improved Armour</strong>
        <ul class="circle-list">
            <li>Effect: +1 Armour</li>
            <li>Points Cost: New armour rating</li>
            <li>Limit: 5</li>
            <li>Notes: DEW only</li>            
        </ul>      
      </li>  
      <li><strong>Improved Range</strong>
        <ul class="circle-list">
            <li>Effect: +1 Range</li>
            <li>Points Cost: Current range value.</li>
            <li>Limit: 5</li>
            <li>Notes: Captor & DEW only</li>            
        </ul>      
      </li>  
      <li><strong>Improved Signature</strong>
        <ul class="circle-list">
            <li>Effect: +1 Signature</li>
            <li>Points Cost: New signature value + 1</li>
            <li>Limit: 5</li>
        </ul>      
      </li>          
    </ul>  
    <a class="back-to-top" href="#top">↩ Back to Top</a>       


    <h3 id="factionenhancements" style="margin-top: 15px;">Faction Enhancements:</h3>

    <h4 id="ancients" style="margin-top: 15px;">Ancients:</h4>
      <ul>
        <li><strong>Improved Self Repair</strong>
            <ul class="circle-list">
                <li>Effect: +1 rating for every Self Repair system on ship</li>
                <li>Points Cost: New total output * 100</li>
                <li>Limit: 50% of weakest Self Repair rating on ship (rounded down)</li>
            </ul>      
        </li> 
      </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>
          
   <h4 id="drazi" style="margin-top: 15px;">Drazi Freehold:</h4>
      <ul>
        <li><strong>Repeater Gunsights</strong>
            <ul class="circle-list">
                <li>Effect: Allows Particle Repeaters to split their shots within a 1 hex radius, and target different fighters in a flight.</li>
                <li>Points Cost: 12pts per Particle Repeater</li>
                <li>Limit: N/A</li>
            </ul>      
        </li> 
      </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>              

    <h4 id="ipsha" style="margin-top: 15px;">Ipsha Baronies:</h4>
      <ul>
        <li><strong>Spark Curtain</strong>
            <ul class="circle-list">
                <li>Effect: Ballistic hit chance reduced by 2 + any boost level for Spark Field</li>
                <li>Points Cost: 40 + 10 per Spark Field</li>
                <li>Limit: 1</li>
                <li>Notes: Unofficial.</li>             
            </ul>      
          </li>       

        <li><strong>Essan Barony Refit</strong>
            <ul class="circle-list">
                <li>Effect: Engine gains +1 thrust and +2 structure boxes, but Scanner lose 1 output and -2 structure boxes, Structure gains +1 armor on all sections (up to 5 max)</li>
                <li>Points Cost: 0</li>
                <li>Limit: 1</li>
                <li>Notes: Essan Barony-specific variant rather than enhancement.</li>              
            </ul>      
          </li> 
          
        <li><strong>Eethan Barony Refit</strong>
            <ul class="circle-list">
                <li>Effect: +2 thrust, +25% available power, +0.1 turn delay, -5 Initiative, +4 critical roll modifier for Reactor and Engine</li>
                <li>Points Cost: 10% of ship cost</li>
                <li>Limit: 1</li>
                <li>Notes: Eethan Barony-specific variant rather than enhancement. Represents Power Pod upgrade, remade as a ship upgrade rather than add-on system.</li>              
            </ul>      
        </li>       
      </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>      

   <h4 id="kirishiaclords" style="margin-top: 15px;">Kirishiac Lords:</h4>
      <ul>
        <li><strong>Improved Gravitic Converters</strong>
            <ul class="circle-list">
                <li>Effect: it only costs 4 thrust to boost the damage of the ships Hypergraviton Blaster.</li>
                <li>Points Cost: 150pts + 50pts per Hypergraviton Blaster</li>
                <li>Limit: 1</li>
            </ul>      
        </li> 
      </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>      


    <h4 id="markab" style="margin-top: 15px;">Markab Theocracy:</h4>
      <ul>
        <li><strong>Religious Fervor</strong>
            <ul class="circle-list">
                <li>Effect: +1 to hit on all weapons (or +5 OB for fighters), +10 initiative, fighters  gain -3 to Dropout; but defence ratings increased by 10</li>
                <li>Points Cost: 0</li>
                <li>Limit: 1</li>
                <li>Notes: Should be taken when 'Desperate Rules' should apply to Markab player.</li>             
            </ul>      
        </li>
      </ul>    
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="mindriders" style="margin-top: 15px;">The Mindriders:</h4>
      <ul>
        <li><strong>Improved Thought Shields</strong>
            <ul class="circle-list">
                <li>Effect: +1 to all Thought Shield ratings on vessel</li>
                <li>Points Cost: New total Thought Shield Rating * 2</li>
                <li>Limit: 5</li>           
            </ul>      
        </li>
      </ul>    
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="shadows" style="margin-top: 15px;">Shadow Association:</h4>
      <ul>
        <li><strong>Increased Diffusers</strong>
            <ul class="circle-list">
                <li>Effect: +1 rating, for every Diffuser on ship</li>
                <li>Points Cost: Total diffuser capacity * 2.5 (further steps are increased by 2.5 points for each Diffuser)</li>
                <li>Limit: 5</li>
            </ul>      
          </li>        

        <li><strong>Integrated Fighter</strong>
            <ul class="circle-list">
                <li>Effect: Purchases an integrated Shadow Medium Fighter for this ship. </li>
                <li>Points Cost: 150pts</li>
                <li>Limit: Equal to number of fighters the ship can carry.</li>
           </ul>      
          </li>  
      
      </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="thirdspace" style="margin-top: 15px;">Thirdspace:</h4>
      <ul>
        <li><strong>Improved Psychic Field</strong>
            <ul class="circle-list">
                <li>Effect: +1 range on Psychic Field</li>
                <li>Points Cost: 300pts</li>
                <li>Limit: 1</li>
            </ul>      
          </li>              
      </ul>   
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="vorlons" style="margin-top: 15px;">Vorlon Empire:</h4>
      <ul>
        <li><strong>Amethyst Skin</strong>
            <ul class="circle-list">
                <li>Effect: +1 Adaptive Armor (AA) point, AA allowance per weapon type and AA pre-assigned amount are increased on every even total.</li>
                <li>Points Cost: 20% of new AA total multiplied by total structure of ship</li>
                <li>Limit: 50% of base AA rating</li>
              <li>Notes: Ships only.</li>             
            </ul>      
          </li> 

        <li><strong>Azure Skin</strong>
            <ul class="circle-list">
                <li>Effect: 1 Shield rating, for all EM shields.</li>
                <li>Points Cost: Unit 'size factor' (see below) multiplied by new EM Shield rating multiplied by number of shield emitters</li>
                <li>Limit: 50% of base EM Shield rating</li>
                <li>Notes: 'Size factor' is 30 for Enormous units, 25 for Capitals and 20 for anything smaller.</li>             
            </ul>      
          </li>  

        <li><strong>Crimson Skin</strong>
            <ul class="circle-list">
                <li>Effect: Power Capacitor gains +2 storage points and +1 recharge point</li>
                <li>Points Cost: 20 * new Capacitor recharge rate</li>
                <li>Limit: 6</li>
            </ul>      
          </li>
      </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="walkers" style="margin-top: 15px;">Walkers of Sigma-957:</h4>
      <ul>
        <li><strong>Extended Draining Field</strong>
            <ul class="circle-list">
                <li>Effect: +1 hex of radius for every Energy Draining Field on the unit.</li>
                <li>Points Cost: 300 * new radius, for each field (that is 50 points per hex of field gained, and a hex of radius adds 6 x radius new hexes)</li>
                <li>Limit: 3</li>
                <li>Notes: On a <em>Variable</em> Energy Draining Field this raises the normal-power radius only - the double-power radius does not move, so each hex bought is one hex less that double power is worth. Once the two meet, boosting the field buys nothing further.</li>
            </ul>      
          </li>
      </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h3 id="systemenhancements" style="margin-top: 20px;">System Enhancements:</h3>

    <p>Everything above is bought for a whole unit. <strong>System enhancements</strong> (or "refits") are different - each one is bought for a
      <strong>single system</strong> on a single ship, so you can put Gunsights on one Twin Array and leave the other three alone, or harden the armour
      on just the reactor. They are bought from the ship window during Fleet Selection rather than from the ship purchase dialog, and like the other
      enhancements they are entirely optional - worth agreeing with your opponent before using them in a pickup battle.</p>

    <p>Refits represent field modifications made by a younger race with the tools it actually has, so they are only available to
      <strong>Young and Middleborn ships</strong>. Ancient and Primordial vessels cannot buy them, and neither can an Ancient-technology weapon that
      has been bolted onto a younger hull. They are also for <strong>ships only</strong> - fighter flights, mines and terrain cannot be refitted.</p>

    <h4 id="usingsystemenhancements" style="margin-top: 20px; margin-bottom: 15px;">Buying &amp; Using Them:</h4>
    <ul class="circle-list">
      <li>Buy the ship in Fleet Selection as normal, then open its ship window from your fleet (your own bought ships, not the store list on the right).</li>
      <li>Hover over (or long-press, on a touch screen) the system you want to refit. The same pop-up menu used for pre-battle damage appears, now titled
        <strong>"Add Enhancements &amp; Damage"</strong>.</li>
      <li>The gold <strong>Enhancements</strong> section sits at the top of that menu and lists every refit that system can take, each with
        <strong>[-] [ n ] [+]</strong> controls. The price shown on a row is the cost of the <strong>next</strong> level, so it goes up as you buy;
        the running total spent on that system appears underneath as "Refits: n pts", and your fleet points update as you click.</li>
      <li>If a system has no gold section at all, it simply has nothing to offer - that is the normal case for most systems on most ships.</li>
      <li>A refitted system gets a gold <strong>✦</strong> star on its icon, and the ship's Enhancements box gains a
        <strong>"System Enhancements (n)"</strong> line, where n is the number of systems refitted (a gun carrying two different refits still counts once).
        Both markers are shown to your own side only.</li>
      <li>The details of what a system is actually carrying appear in that system's own information tooltip, alongside its normal stats.</li>
      <li>Refits are a Fleet Selection purchase only. They cannot be bought, changed or removed once the battle has begun, and anything clicked after you
        press Ready is not submitted.</li>
    </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="systemrefits" style="margin-top: 20px; margin-bottom: 15px;">Available Refits:</h4>
    <ul>
      <li><strong>Advanced Defensive Targeting</strong>
        <ul class="circle-list">
            <li>Effect: +1 Interception rating on that weapon, for every level bought (each point of Interception applies -5% to the hit chance of
              incoming ballistic attacks). The bonus applies in every firing mode the weapon has.</li>
            <li>Points Cost: 8 per gun on the mount, multiplied by the rating you are upgrading from - so a single-gun weapon starting at rating 1 pays
              8, then 16, then 24. A Twin Array at rating 2 pays 32, then 48.</li>
            <li>Limit: The resulting rating cannot exceed 4, so a weapon already at 4 is not offered it at all.</li>
            <li>Notes: Only weapons that already have an intercept rating can be improved.</li>
        </ul>
      </li>
      <li><strong>Gunsights</strong>
        <ul class="circle-list">
            <li>Effect: +1 Fire Control against every target class the weapon can already shoot at, in every firing mode. A weapon that cannot target a
              size class at all (a Piercing missile against fighters, for instance) does not gain the ability to do so.</li>
            <li>Points Cost: 25% of the weapon's maximum damage (rounded up, minimum 4), multiplied by the number of guns on the mount.</li>
            <li>Limit: 1</li>
            <li>Notes: Weapons only. A handful of utility mounts that roll no meaningful attack - the Abbai Shield Projector, Aegis Sensor Pod, Combat
              Transporter, Grappling Claw, Gravitic Shifter, Grome Targeting Array - are excluded, as the refit would do nothing for them.</li>
        </ul>
      </li>
      <li><strong>Hardened Shields</strong>
        <ul class="circle-list">
            <li>Effect: +1 rating on that shield emitter. Both the damage it absorbs and the to-hit penalty it confers improve.</li>
            <li>Points Cost: 10 * the emitter's rating * the number of 60 degree arcs it covers (an all-round emitter counts as 6).</li>
            <li>Limit: 1 per emitter - but each emitter on the ship is bought separately.</li>
            <li>Notes: EM Shields and Gravitic Shields only. The Abbai Shield Projector is a support weapon rather than a shield and cannot take it.</li>
        </ul>
      </li>
      <li><strong>Hardened Armour</strong>
        <ul class="circle-list">
            <li>Effect: +1 Armour on that system.</li>
            <li>Points Cost: The system's total structure boxes multiplied by its current armour, halved and rounded up (armour counts as at least 2 for
              this calculation).</li>
            <li>Limit: 1</li>
            <li>Notes: Any system that already has armour, including Structure blocks.</li>
        </ul>
      </li>
      <li><strong>Improved Thrust Rating</strong>
        <ul class="circle-list">
            <li>Effect: +1 thrust rating on that thruster, for every level bought.</li>
            <li>Points Cost: Twice the combined thrust rating of every thruster on the ship facing the same direction, then +2 for each further level.</li>
            <li>Limit: Up to double the thruster's original rating.</li>
            <li>Notes: Thrusters only. Priced from the ship as designed, so buying Elite Crew for example does not change what a thruster refit costs.</li>
        </ul>
      </li>
    </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="systemenhancementnotes" style="margin-top: 20px; margin-bottom: 15px;">Points, Damage &amp; Saved Fleets:</h4>
    <ul class="circle-list">
      <li>Refits are charged against your fleet budget like anything else. If a refit would take you over, the purchase is refused and put back as it was.</li>
      <li>Pre-battle damage and refits share the same menu, so it is quite possible to wreck something you have just paid to improve. <strong>Damage
        wins</strong> - destroying a system removes its refits and refunds the points immediately, with a message naming what went. Destroying a
        Structure block takes every system in that location with it. This is one-way: un-ticking Destroy does not bring the refit back, you simply buy
        it again.</li>
      <li>Editing a ship keeps its refits; changing the ship to a different class clears them and refunds the points. Copying a ship copies its refits
        as well.</li>
      <li>Refits are saved with a fleet list. When that fleet is loaded again every refit is re-checked against the ship as it is designed
        <em>now</em> and re-priced accordingly, since ships are revised from time to time. If a refit has become more expensive, cheaper, or is no
        longer possible at all, you will be told what changed rather than it happening silently.</li>
      <li>Finally, be aware that the ✦ star and the "System Enhancements (n)" line are hidden from your opponent, but several of the improved values
        themselves are not - shield ratings, armour and thrust all have to reach the other player for their own damage and movement displays to be
        correct. A careful opponent comparing a ship against the published control sheet can still work out that something has been refitted.</li>
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