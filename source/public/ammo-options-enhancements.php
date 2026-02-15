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

    <ul class = index-list>
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
                <li><a href="#fighterenhancements">Fighter Enhancements</a></li>                
            </ul>
        </li>
        <li><a href="#factionenhancements">Faction Enhancements</a>
           <ul class="sub-list">
                <li><a href="#ancients">Ancients</a></li>
                <li><a href="#drazi">Drazi Freehold</a></li>                
                <li><a href="#ipsha">Ipsha Baronies</a></li>
                <li><a href="#markab">Markab Theocracy</a></li>
                <li><a href="#mindriders">The Mindriders</a></li>                  
                <li><a href="#shadows">Shadow Association</a></li>
                <li><a href="#thirdspace">Thirdspace</a></li>                    
                <li><a href="#vorlons">Vorlon Empire</a></li>                  
            </ul>     
      </li>                                                                 
        <!-- Add more sections here -->
    </ul>

    <!--<p>On this page, you'll find information about the various Ammo, Options and Enhancements that are available to the different factions in Fiery Void</p> -->

    <h3 id="missiles" style="margin-top: 20px;">Missiles</h3>
    <p>Many factions in Fiery Void use missiles, and when you purchase a missile-carrying ship in Fleet Selection you will have the chance to purchase any missile ammo that faction has available. 
    Ships will generally come preloaded with basic ammo for their respective launchers, so any missiles types you buy at Fleet Selection will be in additiona to these. 
    Note -  while amount of any given missile You can buy is limited by magazine size, total amount of missiles is not. 
    This means your ammo magazine may show more missiles available than it can actually hold but you cannot actually launch more missiles than magazine total. 
    Essentially, the extra missiles purchased provide you with extra variety, but not actual extra missiles in your magazine!.</p>

    <p>Any missile available in magazine can be fired by any launcher (that is capable of firing it), missiles are not directly tied to particular mounts. This is different for weapons that do store ammo directly on mount - but such weapons usually can only hold one kind of ammo. 
    This is particularly important for fighters (as ships' cavernous magazines are unlikely to run out during a battle). 
    You are therefore encouraged to observe the total number of missiles a fighter can have, but if you do not (whether by omission or intentionally) - you will get an extra missile variety, but no extra missiles. 
    Note also that fighters usually start with empty magazine (although some missile entry is present, for technical reasons).</p>

    <p>Missiles do not use firing ships OEW and their built-in guidance package is usually combined into weapons' Fire control, rather than being kept separate.  Fighter missiles do benefit from the fighter's offensive bonus.</p>
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
      <li><strong>Class I - Interceptor Missile (2250/2263)</strong> - Range 0 - Damage 0 - Fire Control: -/-/-,  Fires defensively for -30 intercept rating against ballistic weapons,</li>      
      <li><strong>Class L - Long Range Missile (2225)</strong> - Range 30 - Damage 15 - Fire Control: +3/+3/+3,</li>  
      <li><strong> Class K - Starburst Missile (2260/2264)</strong> - Range 15 - Damage 10*[D3+3] - Fire Control: +3/+3/+3 - Deals damage in pulse mode,</li>   
      <li><strong>Class KK - Kinetic Missile (1976)</strong> - Range 60 [but -5% per hex range penalty after 15 hexes] - Damage 18 - Fire Control: +3/+3/+3 - Orieni only.  Deals matter damage,</li>  
      <li><strong>Class J - Jammer Missile (2239)</strong> - Range 15 - Damage 0 - Fire Control: -/-/-, Kor-Lyan only. All ships within 5 hexes receive two points of Blanket DEW (BDEW),</li>  
      <li><strong>Class M - Multiwarhead Missile (2256)</strong> - Range 20 - Damage 10*6 - Fire Control: +3/-/-, Can only target fighters, splits into 6 submunitions,</li>      
      <li><strong>Class P - Piercing Missile (2244)</strong> - Range 20 - Damage 30 - Fire Control: -/+3/+3 - Deals piercing damage,</li>  
      <li><strong>Class S - Stealth Missile (2252)</strong> - Range 20 - Damage 20- Fire Control: +3/+3/+3 - Kor-Lyan only, missile target not revealed to enemy player.</li>   
      <li><strong>Class X - HARM Missile (2248)</strong> - Range 20 - Damage 0 - Fire Control: -/+3/+3 - Hit chance increased by 5% per point OEW and CCEW target is using, causes -1d6 scanner output next turn.</li>       
    </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>
    
    <h4 id="fightermissiles" style="margin-top: 20px; margin-bottom: 15px;">Fighter Missiles:</h4>       
    <ul>  
      <li><strong>Class FB - Basic Fighter Missile (2165)</strong> - Range 10 - Damage 10 - Fire Control: +3/+3/+3,</li> 
      <li><strong>Class FD - Dropout Missile (2221/2245)</strong> - Range 10 - Damage 6 - Fire Control: +3/+1/+1 - Increase chance of dropout on fighters,</li>      
      <li><strong>Class FH - Heavy Fighter Missile(2226/2245)</strong> - Range 5 - Damage 15 - Fire Control: +1/+3/+3 - Limited to 1 per fighter (2 per Super Heavy Fighter),</li>  
      <li><strong>Class FL - Long Range Fighter Missile (2226/2245)</strong> - Range 15 - Damage 8 - Fire Control: +3/+3/+3,</li>   
      <li><strong>Class FY - Dogfight Missile (2165)</strong> - Range 8 - Damage 6 - Fire Control: +3/+3/+3 - Cannot snap-fire in Fiery Void.</li>  
    </ul>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h3 id="shells" style="margin-top: 20px;">Grome Shells</h3>
    <p>The Grome are unique in that they have access to a several types of shell for their railguns.  These can be purchased along with a ship in the same way as missiles in Fleet Selection.
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
    <p>Unit enhancements are general quite pricy, although not always.  For pickup battles - they're very much optional, and best to check with opponent that they are ok to use if it's not stated in game description. 
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
      <li><strong>Hangar Conversions</strong>
        <ul class="circle-list">
            <li>Convert either a fighter slot into an assault shuttle slot, or vice versa</li>
            <li>Points Cost: 5pts per slot converted</li>
            <li>Limit: Equal to the number of assault shuttle/fighter slots</li>
        </ul>      
      </li>      
    <li><strong>Identify Friend or Foe (IFF) System</strong>
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
      <li><strong>Improved Sensor Array</strong>
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

    <ul>
      <p><strong>Note</strong> - All costs listed below are on per fighter basis e.g. you pay for each fighters in the flight.</p>
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
            <li>Effect: Pivot cost is reduced by one thrust. Initiative +5. Offensive Bonuns +1. Profile -1</li>
            <li>Points Cost: -10% of craft price (rounded up)</li>
            <li>Limit: 1</li>
            <li>Notes: Custom Star Wars faction only.</li>            
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
        <li><strong>Increased Diffuser Capability</strong>
            <ul class="circle-list">
                <li>Effect: +1 rating, for every Diffuser on ship</li>
                <li>Points Cost: Total diffuser capacity * 2.5 (further steps are increased by 2.5 points for each Diffuser)</li>
                <li>Limit: 5</li>
            </ul>      
          </li>        

        <li><strong>Launched Fighter</strong>
            <ul class="circle-list">
                <li>Effect: N/A </li>
                <li>Points Cost: 0pts</li>
                <li>Limit: Equal to number of fighters ship can launch</li>
                <li>Notes: Not an actual enhancement - used to denote ship has launched a fighter (and reduces ship's Structure by 1).</li>            
            </ul>      
          </li>  

        <li><strong>Uncontrolled Fighter</strong>
            <ul class="circle-list">
                <li>Effect: -2 Offensive Bonuns, -15 Initiative</li>
                <li>Points Cost: 0</li>
                <li>Limit: 1</li>
                <li>Notes: Not an actual enhancement - used to denote fighter does not have corresponding 'Launched Fighter' enhancement on a Shadow ship.</li>            
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
        <li><strong>Amethyst Skin Coloring</strong>
            <ul class="circle-list">
                <li>Effect: +1 Adaptive Armor (AA) point, AA allowance per weapon type and AA pre-assigned amount are increased on every even total.</li>
                <li>Points Cost: 20% of new AA total multiplied by total structure of ship</li>
                <li>Limit: 50% of base AA rating</li>
              <li>Notes: Ships only.</li>             
            </ul>      
          </li> 

        <li><strong>Azure Skin Coloring</strong>
            <ul class="circle-list">
                <li>Effect: 1 Shield rating, for all EM shields.</li>
                <li>Points Cost: Unit 'size factor' (see below) multiplied by new EM Shield rating multiplied by number of shield emitters</li>
                <li>Limit: 50% of base EM Shield rating</li>
                <li>Notes: 'Size factor' is 30 for Enormous units, 25 for Capitals and 20 for anything smaller.</li>             
            </ul>      
          </li>  

        <li><strong>Crimson Skin Coloring</strong>
            <ul class="circle-list">
                <li>Effect: Power Capacitor gains +2 storage points and +1 recharge point</li>
                <li>Points Cost: 20 * new Capacitor recharge rate</li>
                <li>Limit: 6</li>
            </ul>      
          </li>
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