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
<body style="background: url('./img/maps/3.StarFormation.jpg') no-repeat center center fixed; background-size: cover;">

<header class="pageheader">
  <img src="img/logo.png" alt="Fiery Void Logo" class="logo">
  <div class="top-right-row">
    <a href="games.php">Back to Game Lobby</a>
    <a href="logout.php" class="btn btn-primary">Logout</a>
  </div>
</header>

<main class="container">
  <section class="faction-panel">
    <h2 id="top" style="margin-top: 5px">FIERY VOID: FACTIONS & TIERS</h2>

    <h3 id="creating-account" style="margin-top: 25px">TABLE OF CONTENTS</h3>

<div class="index-container">
    <ul class="faction-list">
        <li><a href="#general">GENERAL NOTES</a></li>  
        <li><a href="#tiers">TIER RATINGS</a></li>                           
        <li><a href="#majorfactions">MAJOR FACTIONS</a>
           <ul class="sub-list">
                <li><a href="#centauri">CENTAURI REPUBLIC</a></li>
                <li><a href="#dilgar">DILGAR IMPERIUM</a></li>                
                <li><a href="#ea">EARTH ALLIANCE</a></li>                  
                <li><a href="#minbari">MINBARI FEDERATION</a></li>
                <li><a href="#narn">NARN REGIME</a></li>
                <li><a href="#orieni">ORIENI IMPERIUM</a></li>                 
                <li><a href="#raiders">RAIDERS & PRIVATEERS</a></li>                               
            </ul>
        </li>
            <li><a href="#league">LEAGUE OF NON-ALIGNED WORLDS</a>
            <!--<ul class="sub-sub-list" style="padding-inline-start: 20px;">-->
           <ul class="sub-list">   
                <li><a href="#abbai">ABBAI MATRIARCHATE</a></li>
                <li><a href="#brakiri">BRAKIRI SYNDICRACY</a></li>
                <li><a href="#cascor">CASCOR COMMONWEALTH</a></li>
                <li><a href="#drazi">DRAZI FREEHOLD</a></li>   
                <li><a href="#gaim">GAIM INTELLIGENCE</a></li>
                <li><a href="#grome">GROME AUTOCRACY</a></li>
                <li><a href="#hurr">HURR REPUBLIC</a></li>
                <li><a href="#hyach">HYACH GERONTOCRACY</a></li> 
                <li><a href="#ipsha">IPSHA BARONIES</a></li>                                                   
                <li><a href="#korlyan">KOR-LYAN KINGDOMS</a></li>
                <li><a href="#pakmara">PAK'MA'RA CONFEDERACY</a></li>                
                <li><a href="#torata">TORATA REGENCY</a></li>  
                <li><a href="#vree">VREE CONGLOMERATE</a></li>                                                                     
            </ul>            
        </li>        
    </ul>    
    <ul class="faction-list">          
        <li><a href="#minorfactions">MINOR FACTIONS</a>
           <ul class="sub-list">
                <li><a href="#alacan">ALACAN REPUBLIC</a></li>
                <li><a href="#balosian">BALOSIAN UNDERDWELLERS</a></li>    
                <li><a href="#barada">BARADA IMPERIUM (Unofficial)</a></li>                				
                <li><a href="#beltalliance">BELT ALLIANCE</a></li>
                <li><a href="#chlonas">CH'LONAS COOPERATIVE (Unofficial)</a></li>
                <li><a href="#corillani">CORILLANI THEOCRACY</a></li>   
                <li><a href="#deneth">DENETH TRIBES</a></li>
                <li><a href="#descari">DESCARI COMMITTEES</a></li>
                <li><a href="#drakh">THE DRAKH (Unofficial)</a></li>                
                <li><a href="#llort">THE LLORT</a></li>
                <li><a href="#markab">MARKAB THEOCRACY</a></li>
                <li><a href="#minbariProt">MINBARI PROTECTORATE</a></li>                               
                <li><a href="#rogolon">ROGOLON DYNASTY</a></li>    
                <li><a href="#smallraces">SMALL RACES</a></li>  
                <li><a href="#usuuth">USUUTH COALITION</a></li>
                <li><a href="#yolu">YOLU THEOCRACY</a></li>                                                                       
            </ul>
        </li>                
        <li><a href="#ancientfactions">ANCIENT FACTIONS</a>
           <ul class="sub-list">
                <li><a href="#kirishiac">KIRISHIAC LORDS</a></li>             
                <li><a href="#mindriders">THE MINDRIDERS</a></li>             
                <li><a href="#shadows">SHADOW ASSOCIATION</a></li>
                <li><a href="#thirdspace">THIRDSPACE (Unofficial)</a></li> 
                <li><a href="#triad">THE TRIAD</a></li>                                   
                <li><a href="#torvalus">TORVALUS SPECULATORS</a></li>                                   
                <li><a href="#vorlons">VORLON EMPIRE</a></li> 
                <li><a href="#walkers">WALKERS OF SIGMA-957</a></li> 
            </ul>
        </li>
    </ul>       
    <ul class="faction-list">          
        <li><a href="#otherfactions">OTHER FACTIONS</a>
           <ul class="sub-list">
                <li><a href="#civilians">CIVILIANS</a></li>
                <li><a href="#terrain">STREIB</a></li>                
                <li><a href="#terrain">TERRAIN</a></li>
            </ul>
        </li>                               
        <li><a href="#customfactions">CUSTOM FACTIONS</a>
           <ul class="sub-list">
                <li><a href="#bsg">BSG</a></li>
                <li><a href="#custom">CUSTOM SHIPS</a></li>  
                <li><a href="#escalationwars">ESCALATION WARS</a></li>                                
                <li><a href="#nexus">NEXUS UNIVERSE</a></li>
                <li><a href="#greatcrusade">GREAT CRUSADE ORIENI</a></li> 
                <li><a href="#valheru">HOUSE VALHERU</a></li>                  
                <li><a href="#startrek">STAR TREK</a></li>   
                <li><a href="#starwars">STAR WARS</a></li>
                <li><a href="#system">THE SYSTEM</a></li>                                                          
            </ul>     
        </li>
 <!--<li><a href="#tiers" style="margin-right: 5px; margin-left: 5px; margin-top: 10px; font-size: 12px; color: #8bcaf2; font-size: 16px;"><span style="color:gold;">★</span> TIER RATINGS <span style="color:gold;">★</span></a></li>-->                                                                   
        <!-- Add more sections here -->
    </ul>
</div>    

<h3 id="general" style="margin-top: 30px;">GENERAL NOTES</h3>
    <p>Given the huge variety of factions in Fiery Void it made sense to try and provide a short overview of each one, to help players get a feeling for each one and how they play in the game.
    Sections have been written by different authors so while some will introduce the playstyle and systems of each faction, others will simply mention important things to be aware of when playing them in Fiery Void.    
    </p>

    <p>Later in the document the factions are listed in competitive ‘Tiers’, to help players pick fleets which will be reasonably well-matched for a pick-up game.  
    There is a degree of judgement being applied to this categorisation but, given the sheer number of factions in Fiery Void, giving players an idea of a relative strength of each faction is seen as being more helpful than not!</p>

    <p><strong>Custom Designs:</strong> Some factions (and units) are marked as ‘custom’. This means they're not official designs released by AoG. This means they may not be as well-balanced as official factions.
    Before using custom units it's good practice to make sure that your opponent is ok with using them, or you can mention it explicitly in game description if you are creating the game.
    </p>

    <p><strong>Semi-custom Units:</strong> Some units are marked as "semi-custom". 
    This means they're not official designs released by AoG, but are not considered fully custom units for a variety of reasons, e.g.</p>
        <ul>
<li>Official units that could not be fully ported into FV for technical reasons, but were nonetheless balanced with some changes (e.g. Brakiri Tashkat),</li>
        <li>Units that expand fleet build options of a faction while keeping close to official designs (e.g. Ipsha Jumpsphere),</li>
        <li>Units that were released by AoG after the company had officially ceased to produce B5Wars related materials (e.g. Showdowns-10, Variants-6).</li>     

    </ul>
<h5>Easy and Forgiving Factions to Get Started</h5>
    <p>If you're just looking to have your first few games of Fiery Void, we recommend these factions as a good place to start as you will find them relatively easier to fly than some of the more esoteric fleets!</p>
        <ul>
<li>Drazi - Very flexible and always strong, with a ship layout that makes them retain combat value even after sustaining heavy damage.</li>
        <li>Narn - Very tough with powerful weapons - they may not be fancy, but they get the job done and refuse to die even if put in unfavorable position.</li>
        <li>Earth Alliance - Extremely tough, with awesome defenses and good firepower. Their resilience makes them very forgiving to any beginner mistakes.</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>

<h3 id="majorfactions" >MAJOR FACTIONS</h3>

    <h4 id="centauri" >CENTAURI REPUBLIC</h4>
    <p>Once known as the Lion of the Galaxy, the Centauri Republic's glory days might now be over they are still more than capable of fielding a powerful fleet with potency at range and up close.
    Their ships tend to be very specialised, and often quite fragile so players will need to play around these elements in order to secure victory.</p>        
        <h5>Plasma Stream:</h5>
            <ul>
<li>A plasma weapon that permanently reduces armor of systems hit. Also, counts full armor for every rake, which is a difference from normal raking weapons.</li>   
        </ul>
<h5>Guardian Array:</h5>
            <ul>
<li>Primarily a defensive weapon that can intercept fire directed at a third party, provided the weapon is between the firing unit and its target.</li>   
            <li>In tabletop, it needs to be shut down to switch between offensive (anti-fighter) and defensive modes. There is no such requirement in Fiery Void, and Guardian Arrays can intercept or fire offensively at will.</li> 
            <li>Also, the definition of "between" was changed. In FV the weapon is "between" the firing unit and its target if the bearing difference on them is at least 120 degrees (2 hex sides).</li>

    </ul>
<h5>Chameleon Sensors:</h5>
            <ul>
<li>An ELINT array (with all the usual ELINT abilities) that can additionally disguise its ship as a different vessel. Carried by the Dargan Strike Cruiser.</li>
            <li>When you buy the ship, the enhancement dialog offers a free "Chameleon Suite" choice: pick any other Centauri hull, or leave it on "None". Your enemies then see that ship — its silhouette, name, damage sheet, weapons and defensive ratings all belong to the vessel you picked, not to the Dargan. Your own team always sees the truth.</li>
            <li>Shots at a disguised ship resolve against both sheets from a single die roll. Your opponent watches their shot land on the ship they think they are firing at, while the real damage goes on the real hull. <b>The disguise gives no defensive benefit</b> — a Dargan hiding inside a smaller hull is exactly as easy to hit as a Dargan.</li>
            <li>Weapon arming status is always masked from your enemies, whether you are disguised or not, and it stays masked even after the deception is broken. They can never tell which of your guns are loaded.</li>
            <li>The suite can be switched on and off in the Initial Orders phase, alongside the power controls. Switching it off is permanent — the disguise does not come back.</li>
        </ul>
<h5>Seeing through a Chameleon disguise:</h5>
            <ul>
<li>Detection is tracked <b>per team</b>, exactly as it is for stealth ships. In a multi-team game, a team that has seen through the deception faces the real ship while everyone else keeps facing the simulacrum.</li>
            <li><b>Proximity</b> — an enemy ship within 5 hexes with line of sight sees through it immediately (2 hexes for fighters and shuttles).</li>
            <li><b>Implausible manoeuvring</b> — changing speed faster than the ship you are imitating could manage.</li>
            <li><b>Implausible ELINT</b> — running an ELINT operation the imitated hull has no array for, or spending more EW than its sensors could produce.</li>
            <li><b>Firing the wrong weapon</b> — a shot only looks convincing if the imitated ship mounts a weapon of the same class whose arc covers that shot, and each gun you fire has to be matched by a different one on the simulacrum. Fire more Matter Cannons than it carries and the extra one gives you away. This reveal takes effect at the start of the <i>following</i> turn — your opponent works out the discrepancy after the fact.</li>
            <li><b>Losing the array</b> — if Chameleon sensros are destroyed, or switched off, the deception ends at once and permanently.</li>
            <li><b>Ancient sensors</b> — Ancient and Primordial races (Shadows, Vorlons, Kirishiac, Mindriders, Thirdspace) are never fooled at all. If any team fields such a unit, Chameleon ships are revealed to that team from the start of the game.</li>
            <li>Once revealed, the ship stays revealed to that team for the rest of the game — switching the suite back on will not restore the illusion for example.</li>
    </ul>
<h4 id="centauriwotc" >CENTAURI REPUBLIC (WoTCR)</h4>
    <p>The Centauri fleet from a few hundred years before the show era, representing the Wars of the Centauri Republic (WotCR) period.  
    A lot weaker than modern Centauri as you'd expect but intended to compete against other fleets from that period.</p>        
        <h5>Sentinel Point Defense</h5>
            <ul>
<li>A Guardian Array (see above) precursor which works the same way defensively. However, it doesn't have offensive mode available.</li>   
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="dilgar" >DILGAR IMPERIUM</h4>
    <p>A now-extinct race that once terrorised the League of Non-Aligned Worlds during the Dilgar War.  
    The Dilgar use a mix of ballistic, particle and plasma weapons on their ships to great effect, dealing large amounts of damage at any range.</p>        
        <h5>Pentacan Formation</h5>
            <ul>
<li>Dilgar ships can benefit from using Pentacan formation. In FV the rules for it have been simplified.  Dilgar gain an initiative bonus if that are with 10 hexes of a command ship (not cumulative). 		  
            For fighters, as long as flight leader (first craft in flight) is alive and uninjured (eg. received no damage), flight has +5 Initiative.  Dilgar fighters also have an inherent -2 dropout bonus.
            </li>   
        </ul>
<h5>Point Pulsar</h5>
            <ul>
<li>Dedicated weapon intended for making called shots - suffers only half of the regular penalty when doing so (e.g. -20% instead of =40%).</li> 
        </ul>
<h5>Mass Driver</h5>
            <ul>
<li>Many Dilgar ships have Mass Driver fitted. This weapon is functional in FV, but is intended as planetary bombardment weapon of mass destruction and has limited use in space combat (however, it can engage starbases and immobile Enormous ships). 
            It's a Matter weapon that automatically hits structure (e.g. not any systems) and can be intercepted without degradation (similar to ballistic weapons).  To use this weapon the firing ship must be at speed 0.</li>   
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="ea" >EARTH ALLIANCE</h4>
    <p>Whilst relatively new on the galactic scene the Earth Alliance has quickly become a major power, playing a pivotal role in the Dilgar War.  
    Since their near-extermination at the hands of the Minbari, Earthforce has built back bigger and stronger and their modern ships are notable for their firepower and survivability, albeit at the cost of manoeuvrability and limited firing arcs.</p>        
        <h5>Interceptor</h5>
            <ul>
<li>Dedicated defensive weapon that can be boosted during Initial Orders to allow it to target enemy fighters (note - it cannot intercept when boosted).
            Generates an Energy Web (E-Web) providing a shield-like effect on its arc that reduces the hit chance of all incoming fire by its intercept rating. 
            E-Webs cannot be flown under by fighters, and does NOT provide damage reduction.
            </li>   
        </ul>
<h5>Aegis Sensor Pod</h5>
            <ul>
<li>A specialised system, found only on the Hyperion Aegis Cruiser.  The system targets a fighter flight and automatically hits, giving the firing ship 3 CCEW against that unit.  This bonus EW cannot be combined with any other OEW, CCEW or EW from another Aegis Pod.  
            The Pod are relatively prominent on the ship's hull, and Called Shots have a +10% bonus against them (so -30% instead of the usual -40%).</li> 

    </ul>
<h4 id="eaearly" >EARTH ALLIANCE (EARLY)</h4>
    <p>The early Earth Alliance units are composed of semi-official custom designs that all were deployed before 2200. These units generally lack the better defenses of the later Earth designs and many use blast cannons. Some official units, like the Olympus Alpha are listed in 
    both the standard Earth Alliance fleet list as well as the early EA section. This is to represent their transitional nature from being high-end units in the early EA to very old units in the Dilgar War era. Although the more famous Starfuries (Tiger, Nova, Aurora) are unavailable 
    the early examples of the Starfury lineage (Flying Fox, Aries, and Atlas) formed the foundation of the fighter-heavy EA fleet in the future.
    </p>        
    <a class="back-to-top" href="#top">↩ Back to Top</a>


<h4 id="minbari" >MINBARI FEDERATION</h4>
    <p>The oldest of the Younger Races and the most technologically advanced.  This is demonstrated by their powerful Jammer system, and high damage weaponry.  
    Minbari fleets have few apparent weaknesses at first, but the cost of their ships means they will usually be out-numbered on the battlefield which can be a distinct disadvantage.</p>        
        <h5>Jammer</h5>
            <ul>
<li>A powerful defensive technology, it prevents enemies from gaining a ‘lock-on’ with their Electronic Warfare, meaning that range penalties against Jammer-protected units are always doubled.  
            It also halves ballistic weapons launch range. Minbari themselves ignore Jammer, as do races equipped with Advanced Scanners e.g. Ancients.  
            Note, on Minbari fighters, Jammer protection does not stack with jinking.</li> 
       </ul>
<h5>Gravitic Drives</h5>
            <ul>
<li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
            Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>
       </ul>
<h5>Antimatter Converter</h5>
            <ul>
<li>A weapon which deals damage based on how well it hits its target e.g. hit chance minus the dice result on its roll to hit.  
            Uses Flash mode, so 25% damage is caused to other units on the same hex as any target hit.</li>
       </ul>
<h5>Gravitic Net</h5>
            <ul>
<li>This weapon is used to move a target, and fires in the Pre-Firing phase, before regular weapon declaration.  
            First target a ship (friend or foe) and a green hexagonal sprite will appear showing the available hexes that the target unit can be moved.
            Next, target an available hex to confirm the shot.</li>
       </ul>
<h5>Electro-Pulse Gun</h5>
            <ul>
<li>Very short range, slow firing weapon that only affects fighters. However, it can make called shots at no penalty.</li>
       </ul>
<h5>White Stars</h5>
            <ul>
<li>The mainstay of Army of Light is available on Minbari fleet list. It's difficult to handle, but in skilled hands extremely potent unit, 
            equipped with Vorlon (see their list) technologies nominally beyond reach of Younger Races (e.g. Adaptive Armor, EM Shields, and in case of Command variant - even self repair).</li>
            <li>White Stars may be used as ISA/Army of Light/White Star Fleet as well. Such a fleet is NOT tournament legal, and for good reason - but may be an interesting scenario piece (or challenge). 
            It's also far too strong to be used in regular pickup battles without asking you opponenet first!
            When used in this way, only White Stars (and any Combat Flyers they can carry) can be deployed.  Be warned though - in skilled hands such a fleet is simply overwhelming.</li>                                                                         


    </ul>
<h4 id="narn" >NARN REGIME</h4>
    <p>A former Centauri colony, the Narn Regime is now a galactic power in it's own right.  What their vessels lack in outright technology they make up for powerful weaponry and sheer survivability.</p>        
        <h5>Energy Mines</h5>
            <ul>
<li>A hex-targeted weapon, which causes heavy damage to everything on hex hit and splash to everything in adjacent hexes, including allied units.  Devastating against fighters, and when used en masse (6+ Energy Mine launchers can easily saturate an area).</li>
            <li>Opponents will not be able to see where you have targeted your Energy Mines until after firing is resolved, just the fact that they've been launched. Launched mines also have a 25% chance to scatter or dissipate harmlessly.</li> 
        </ul>
<h5>Pulsar Mines</h5>
            <ul>
<li>An automated short-range weapon that fires at passing fighters.  The weapon will automatically track enemy fighters during the Movement Phase, and attack any that came within arc and range before the Firing Phase begins.  Each Pulsar Mine can fire up to 18 shots per turn.</li> 
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>            


    <h4 id="orieni" >ORIENI IMPERIUM</h4>
    <p>The arch-nemesis during the Wars of the Centauri Republic, Orieni fleets are typified by the large amount of matter weaponry they bring, their large range of medium ships and their Hunter-Killer drones.
     A peculiar faction in many ways as it can both feel oppressive to play against due to their damage potential, but also difficult to win with if you are not experienced with them.   
    </p>        
        <h5>Hunter-Killer (HK) Drones</h5>
        <ul>
            <li>Orieni long-range weapon of choice, and probably their most outstanding feature. They're essentially large missiles with a degree of control, using fighter rules and attacking by ramming enemies. Note - HKs must deploy in hangars at the start of the game.</li>
            <li> Hunter-Killers are controlled by Control Nodes on Orieni ships, these sytems can control a flight of HKs per point of output in system display. If there is insufficient control HKs can operate autonomously, but will suffer from the Uncontrolled critical effect.  
                This means they will move towards the closest enemy ship and automatially try to ram it if possible.  The will also jink for two levels automatically, and suffer from -15 Iniative whilst Uncontrolled.</li>
            <li>When attempting to ram enemies, Hunter-Killers receive a penalty to their hit chance based on their own speed (the faster they are moving, the worse this). 
            As ramming attacks happen before other firing, HKs that achieved ramming distance cannot be shot down before they attempt to ram.
            </li>                        
        </ul>
<h5>Light Gatling Railgun</h5>
            <ul>
<li>Orieni Templar's gun is Matter-based,making it a threat to even the biggest ships. The downside is it's limited to 6 shots before running out of ammunition.  More ammunition can be bought as an enhancements during Fleet Selection.</li> 
        </ul>
<h5>Strike Force</h5>
            <ul>
<li>The Orieni military is split into a few branches and a few ships have different limitations when deployed as part of Strike Force detachment instead of the regular navy, Hand of the Blessed. </li>
            <li>Assume any force led by Paragon to be Strike Force, while lesser command ships lead Hand of the Blessed. Note - The in-game fleet checker does not take this rule into account.</li>                                          
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="raiders" >RAIDERS & PRIVATEERS</h4>
    <p>The Raiders & Privateers faction is not a single faction with a standardized force list. Instead it represents the various kinds of pirates, privateers, and wanderers in the Babylon 5
    universe.</p>
    <p>Specific raider groups will be listed in the unit name, e.g. ‘Centauri Privateer’, meaning that  any unit without a faction prefix is considered a generic raider and can be fielded by any faction. 
        It is near-impossible to create a Fleet Checker for all of the various combinations within this faction, 
        so a bit of player responsibility is required in order to choose an appropriate fleet. Summaries of the Raider mini-Factions in Fiery Void are listed below:</p>
       <h5>Brakiri Shokhan</h5>
            <ul>
<li>The Shokhan grew out of various Brakiri groups that did not integrate into the modern Brakiri society. Many Shokhan operate purely as raiders, 
                but it is suspected that some receive outside support from Brakiri corporations or other entities and fulfill the role of privateers and sometimes mercenaries.</li>
            <li>The Shokhan have three Brakiri specific ships. They also operate a limited selection of generic raider units including the Aspar, Galleas, Hawk, Ma'Ri'e, Felucca, and Xebec. 
                The Delta-V and Folshot-A are the two fighter designs operated by the Shokhan.</li>   
       </ul>
<h5>Centauri Privateers</h5>
            <ul>
<li>Generally led by lesser nobles of the great houses or those from minor houses looking to advance, Centauri privateers have existed throughout the Centauri's time in space. 
                Originally, they operated against other Houses, but focused more on the edges of Centauri space as other powers were discovered. </li>
            <li>In addition to Centauri hulls, these privateers use a limited selection of the generic raider hulls, including the Ma'Ri'e, Ma'Ri'u, Pinnace, Uid'Ac'e, Felucca, and Xebec. 
                Centauri privateers use Delta-Vs almost exclusively, but have restricted access to Razik fighters or older Centauri fighters like the Glaive and Phalen.</li>
       </ul>
<h5>Drazi Hunters</h5>
            <ul>
<li>Derived from the Drazi penchant of hunting, it is typically assembled from Drazi disaffected with traditional Drazi society. 
                The Hunters operate more as a raider group than privateers, they are not officially endorsed by the Drazi. However, their members are welcome in Drazi space to a limited degree.</li>
            <li>They operate three units of Drazi design as well as a few generic raider hulls. These are the Aspar (limited), Galleon, Hawk, Ma'Ri'e, Pinnace, and Wolf Raider (limited). 
                Drazi Hunters only employ Cobra and Delta-V fighters.</li>                     
       </ul>
<h5>Independent Mercenaries League (IML)</h5>
            <ul>
<li>The IML formed during the Dilgar War, originally by Belt Alliance forces out of work. They have been supported since then by various League powers 
                and typically have better access to technology than traditional raider groups.</li>
            <li>The IML fields three unique units, the Attack Cruiser,  Armed Transport, and Missile Frigate along with variants of each.</li>
            <li>The IML use Armed Shuttles and Delta-Vs almost exclusively, but have restricted access to Star Snakes and Lellat fighters.</li>   
       </ul>
<h5>Imperial Star Legion</h5>
            <ul>
<li>This group operates primarily within Centauri, Earth, and Narn space and in addition to generic raider hulls.</li>
            <li>The Legion has three unique hulls, the Starjammer, Gladius, and Augustus and is characterized by using heavy weapons and fielding fusion cannons.</li>
            <li>The Legion operated all of the generic raider units at various times in their existence. The Legion uses Delta-Vs and Double-Vs fighters exclusively.</li>                                                                      
       </ul>
<h5>Junkyard Dogs (JYD)</h5>
            <ul>
<li>A raider group that supported the Army of Light in the Shadow War, but then had bigger plans for the future and wound up fighting the Minbari Protectorate.</li>
            <li>They operate several converted hulls from the main powers including a Lias, two Tethys, a Kutai, a Mograth, two Vorchans, two Sho'Kos, and a Thentus.</li>
            <li>The Junkyard Dogs primarily used ArmedShuttles, Delta-Vs, and Double-Vs but may also field a flight of Raziks or Goriths.</li>  
       </ul>
<h5>Narn Privateers</h5>
            <ul>
<li>Formed out of Narn elements considered too radical or unruly to integrate with the newly free Narn Regime, the Narn sent these privateers out to continue to strike at the Centauri. 
                The more organized groups received greater support from the Narn Regime and fulfilled many missions ranging from traditional raiding to intelligence gathering.</li>
            <li>Narn privateers operate three specific Narn units as well as a selection of generic raider designs. These include the Brigantine (limited), Hawk, Ma'Ri'u, Pinnace, and Xebec. 
            They primarily use Delta-V and Double-V fighters and a restricted number of Narn Goriths.
            </li>   
       </ul>
<h5>Tirrith Free State (TFS)</h5>
            <ul>
<li>Born out of an EA base built in the Tirrith system from the Dilgar War, the raiders that took over the base ultimately formed an independent star system at the confluence of multiple powers. 
                The TFS is now less of a raider group and more a tiny nation.</li>
            <li>The TFS has three unique hulls including the Blockade Runner, System Monitor, and Freedom Base. Beyond this, the TFS will utilize any generic raider unit.</li>
            <li>The majority of TFS fighters are Delta-Vs, but they can field Armed Shuttles, Double-Vs, Goriths, Star Snakes, and Koists. The TFS only use Drazi Dudroma defense satellites.</li>   
        </ul>
<h5>Torata Privateers (Custom)</h5>
            <ul>
<li>Based on the units created by Jason Stadnyk on The Great Machine. These Torata forces explored Markab and Vree space in search for resources and technologies.</li>
            <li>The Uala's low population makes the Torata Privateers relatively uncommon and with very few units. </li>
			<li>Torata privateers operate only two specific Torata units as well as a limited selection of generic raider designs. These include the Hybrid Saucer, Caravel, Freebooter, Galleon and Aspar. </li>   
            <li>The Torata privateers primarily use Armed Shuttles, Delta-Vs, and stripped down Tukas. They may also field a normal Tuka as a rare variant.</li>  

		</ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>



    <h4 id="league" >LEAGUE OF NON-ALIGNED WORLDS</h4>
    <p>The League of Non-Aligned Worlds is an group of 14 playable factions that were released through the Bablyon 5 Wars books, Militaries of the League 1 and 2. 
    The factions have a huge variety of special abilites and playstyles, and whilst their relative power level varies dramatically from faction to faction they can all be a lot of fun to play.   
    </p> 
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="abbai" >ABBAI MATRIARCHATE</h4>
       <h5>Gravitic Shield</h5>
            <ul>
<li>Most Abbai units are equipped with Gravitic Shield. Active shields reduce both the hit chance and damage of incoming fire on their respective arcs. At range 0 fighters are assumed to be flying underneath the shields and effectively ignore them.</li>
            <li>Gravitic Shields are powered by shield generator, that can power up a limited number of them (usually not allowing a ship all-around protection unless generator output is boosted).</li>
        </ul>
<h5>Quad Array</h5>
            <ul>
<li>Four guns in a single housing that's prone to overheating. Using more than 2 shots offensively results in one or two 'May Overheat' critical effects the following turn (depending on whether 3 or 4 shots are fired).</li>
            <li>When this critical is in place, firing the Quad Array again offensively will cause a critical roll at end of turn with the following modifiers; +2 modifier for every shot fired in the current turn, and -2 if there is only one May Overheat critical effect.</li>
        </ul>
<h5>Comms Disruptor</h5>
            <ul>
<li>Reduces target's EW and Initiative for next turn.</li>
        </ul>
<h5>Particle Impeder</h5>
            <ul>
<li>Purely defensive weapon, capable of intercepting weapons with "uninterceptable" trait.</li>
            <li>In addition, it can be boosted with EW (rather than the usual power). Such a boost, in addition to increasing intercept rating of the Impeder, counts as a special kind of shielding, reducing hit chance of all incoming weapons, including all fighter fire.</li>
            <li> Purely defensive weapon, capable of intercepting weapons with "uninterceptable" trait.</li>
        </ul>
<h5>Shield Projector</h5>
            <ul>
<li>System only seen on Abbai base and OSATs, can target allies within 10 hexes and boost their Gravitic Shield on that turn by the Projector's rating.</li>                        
            <li>Fires as a ballistic weapon effectively, with its effects being applied during the Firing Phase.  To receive the shield bonus the allied unit must be within 5 hexes of the projecting unit by the end of the Movement Phase.</li>
            
    </ul>
<h4 id="abbaiwotcr" >ABBAI MATRIARCHATE (WOTCR)</h4>
    <p>Abbai fleet a few hundred years before the show era, representing Wars of the Centauri Republic period. While one of the weaker factions overall, they have Gravitic Shielding much like modern Abbai (although much weaker), and weapons being precursors to modern Comm Disruptor (Comm Jammer and Sensor Spike).</p>            
        <h5>Mine Launcher</h5>
            <ul>
<li>Launches a mine at a target hex, with a 25% chance to scatter.  Once it lands on a hex it will look for ships up to its maximum range and will attack the closest one, including friendly ships (if multiple ships are equally distant it will choose one at random).</li>
            </ul>
<li>Unlike for missiles, the launcher does not come with any ammunition and you must purchase this at Fleet Selection from the available choices:
                <ul>
                    <li><strong>Basic:</strong> - 4 hex range, +10 to hit 12 Damage,</li>
                    <li><strong>Wide:</strong> - 7 hex range, +10 to hit 12 Damage.</li>
                </ul>
            </li>
            <ul>
<li>Mines that do not find a target on the turn they are fired, with persist as a Captor Mine until they are destroyed or find a unit to attack.</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="brakiri" >BRAKIRI SYNDICRACY</h4>
        <h5>Corporations</h5>
            <ul>
<li>Brakiri are split into competing corporations. On each unit it's noted which Corporation produces the design, if nothing is noted it's assumed that all Corporations have equal access to it.</li>
            <li>This is irrelevant when fielding a combined Brakiri fleet, but you have the option of fielding a single Corporation as well for flavour. 
                In this case, You have access only to designs available to that Corporation, but all restrictions (e.g. number deploy & variants) are eased by one level. 
                For Variant restrictions this means Rare->Uncommon->Common, for deployment Restricted(10%)->Limited->(33%)->Unlimited.</li>
                <li>Corporations have access to slightly different technologies and have their own areas of specialisation, so their individual fleets are usually lacking in some areas but have excellent access to something else.	
                    Note - The Fleet Checker does not take this rule into account.</li>
       </ul>
<h5>Gravitic Shield</h5>
            <ul>
<li>Most Abbai units are equipped with Gravitic Shield. Active shields reduce both the hit chance and damage of incoming fire on their respective arcs. 
                At range 0 fighters are assumed to be flying underneath the shields and effectively ignore them.</li>
            <li>Gravitic Shields are powered by shield generator, that can power up a limited number of them (usually not allowing a ship all-around protection unless generator output is boosted).</li>
       </ul>
<h5>Gravitic Drives</h5>
            <ul>
<li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
            Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>
        </ul>
<h5>Gravitic Bolt / Gravitic Pulsar</h5>
            <ul>
<li>These weapons can be boosted with power to increase damage and intercept rating. This does cause them to have a cooldown period equal to boost level used. 
                Additionally, if fired at maximum boost, the weapon can suffer critical damage.</li>
        </ul>
<h5>Gravitic Shifter</h5>
            <ul>
<li>This weapon fires in the Pre-Firing phase of the game.  You can target an ally or an enemy to try and change their facing by 60 degrees clockwise or anti-clockwise (using appropriate firing mode).
                If the weapon hits then the target will be rotated before the Firing Phase occurs on that turn and therefore Gravitic Shifters can be used tactically to escape enemy firing arcs, or bring enemies into allied ships firing arcs. /section>
                Note - Only ONE Gravitic Shifter can be used on a ship per turn, any other Shifter attempts will automatically miss.</li>
        </ul>
<h5>Gravitic Mine</h5>
            <ul>
<li>Ballistic weapon that targets a hex rather than a ship.  The target hex is hidden from the opponent and only revealed in the Pre-Firing phase, when the mine detonates. 
                The mine cannot be intercepted and causes no collateral damage.</li>
            <li>Any unit within 5 hexes of an active mine is pulled one hex toward the closest mine.  
                Where two or more mines overlap and catch a target between their fields, the unit is instead 'sheared' for damage scaled by its size class and proximity to the nearest mine - ignoring armour, with the impact side determined by the furthest mine in range.  
                Terrain, fixed Enormous bases, and other mines are unaffected.</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="cascor" >CASCOR COMMONWEALTH</h4>
       <h5>Poor Acceleration</h5>
            <ul>
<li>For ships this is reflected in very high acceleration costs. 
                For fighters, the normal acceleration cost is doubled but is compensated somewhat by medium and lighter fighters having unusually high thrust ratings.  
                In effect they can't accelerate very well, but are exceptionally manoeuvrable even at high speed.</li>
        </ul>
<h5>Ultralight Fighters</h5>
            <ul>
<li>The smallest Cascor fighters are Ultralight, which allows them to efficiently fit into hangar space intended for larger fighters by only taking up half a slot.</li>
        </ul>
<h5>Ioniser</h5>
            <ul>
<li>Cascor fighter weapons, unlike typical fighter weapons it cannot be intercepted, neither can it intercept.</li>
       </ul>
<h5>Ion Field Generator</h5>
            <ul>
<li>Area effect weapon, targeted on a hex rather than ship. 
                Units caught in area of effect have capabilities reduced next turn (reduced Initiative, Power, EW, Offensive Bonus, and has a chance to shutdown a system).</li>
        </ul>
<h5>Points Re-Evaluation Enhancement</h5>
            <ul>
<li>Consensus in Fiery Void was that the Cascor were a fun faction but too pricey ever to be competitive. 
                In order to correct this perceived drawback, an enhancement was added to their units that does nothing except modify its price.</li>
            <li>This has made the faction much more playable and better balanced against other Tier 2 opponents.  A custom variant of their attack fighter was made available too.</li>
            <li>Note this is player initiative and using these enhancements turns the fleet into Custom faction, so check with your opponent!</li>     
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="drazi" >DRAZI FREEHOLD</h4>
        <p>Drazi are a very solid, beginner-friendly faction which uses an assortment of fast recharging particle weapons to pummel their enemies.  
        Their distinctive HCV layout with outer hulls on port and starboard instead of the traditional front and aft layout, is very notable for producing surprisingly resilient ships.</p>
       <h5>Repeater Gunsights</h5>
            <ul>
<li>Ships equipped with Particle Repeaters can purchased this option in Fleet Selection.  Repeaters armed with gunsights are able to split their shots between different enemy units within a 1 hex radius of their original target,
                and different fighters within the same flights.  If the Particle Repeater takes any damage at all, it loses its gunsight ability.  All other rules relating to the Particle Repeater remain the same.</li>
                           
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="draziwotcr" >DRAZI FREEHOLD (WOTCR)</h4>
    <p>Drazi fleet a few hundred years before the show era, representing Wars of the Centauri Republic period.  No notable rules/technologies.</p>            
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="gaim" >GAIM INTELLIGENCE</h4>
    <p>Gaim ships, despite using repurposed hulls from other factions, are actually very good and their diversity is excellent - making them one of the stronger League factions.</p>
       <h5>Bulkheads</h5>
            <ul>
<li>Defensive system that can absorb in place of another system, which greatly increases resilience of Gaim systems. 
                Bulkheads can protect systems on the same structure that it's mounted upon. Note that primary systems are not protected by bulkheads.</li>
            <li>The decision whether to use a bulkhead is made automatically, and will aim to prevent a systems destruction, 
                or when the integrity of the structure block falls too low</li>    
        </ul>
<h5>Particle Concentrator</h5>
            <ul>
<li>Multiple Concentrators may combine into a single more accurate and more powerful shot. Each combining ship must be within 1 hex of every other combining ship, and all of them must be hitting the same side of the target.</li>
            <li>To combine, allocate the Concentrators in the desired combined firing mode at the same target (the game will pick the eligible partners automatically). If not enough eligible Concentrators are allocated, the shot resolves in the highest mode actually achievable.</li>
            <li>Hit chance is averaged across the combining weapons, but normalised to the range of the closest combining ship to the target. If any combining ship lacks lock-on against the target, the range penalty is doubled.</li>
            <li>Damage is rolled once for the combined shot and increases by +1d10 per additional weapon, up to a maximum of 5 additional weapons (+5d10).</li>
        </ul>
<h5>Packet Torpedo</h5>
            <ul>
<li>Ballistic weapon that has a range penalty, just like direct fire weapons - although only after the first 10 hexes of travel. 
                In addition, the target of this weapon is not known to your opponent until impact.</li>
       </ul>
<h5>Scattergun</h5>
            <ul>
<li>Light weapon that fires a random number of shots. When firing defensively these shots will be directed at separate threats, never the same shot more than once.</li>    
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="grome" >GROME AUTOCRACY</h4>
    <p>The Grome a low-tech faction that prides itself in only using what they can build themselves. Their armament is made entirely of matter class weapons and they can equip their railguns with various types of shells. 
        Despite being one of the weaker factions they have a number of interesting systems.
    </p>
       <h5>Antiquated Sensors</h5>
            <ul>
<li>These sensors cannot be boosted beyond their base output.</li>    
        </ul>
<h5>Connection Struts</h5>
            <ul>
<li>While Grome vessels have huge structure blocks, they are very fragile. If a connection strut is hit, the damage is doubled and applied to the relevant structure block.</li>
        </ul>
<h5>Flak Cannons</h5>
            <ul>
<li>These can engage fighters and deal matter damage in flash mode. While effective in this role, their main benefit is their impressive defensive fire.</li>
            <li>When a Flak Cannon intercepts a shot from an enemy it will also intercept all other fire from that ship using the normal interception degradation rules e.g. three Flak Cannons could generate -30% to hit from all fire from an enemy.</li>
            <li>In addition, the Flak Cannon can intercept friendly units as long as the friendly unit is within 5 hexes and both the friendly and firing unit are in arc.</li>
            <li>The Flak Cannon can also intercept uninterceptable weapons such as lasers to an extent (only 50% intercept is applied), but only when they are fired at the Flak Cannon-equipped ship e.g. it cannot intercept this type of weapon at all for friendly units</li>
        </ul>
<h5>Light and Heavy Railguns</h5>
            <ul>
<li>Smaller and larger versions, respectively, of the standard railgun.</li>
        </ul>
<h5>Targeting Array</h5>
            <ul>
<li>This weapon automatically hits, but scores no damage.  Instead it increases the hit chances for all other shots against the selected target from the same ship, by 5% for each point of rating on the Targeting Array e.g. A rating of 2 would equal +10% to hit chance.</li>
            <li>Multiple Targeting Arrays can be combined against the same target, but the effect will degrade by 5% for each subsequent array e.g. two Targeting Arrays with a rating of 2 would only increase overall hit chances by 15%, not 20%.</li> 
            <li>Ships with ‘Haphazard Targeting Systems’ in their notes have a lot of Targeting Arrays and this risks having them interfere with one other. After they have been ordered to fire, they have a chance to malfunction  This chance being reduced or removed if one or two Targeting Arrays are destroyed or deactivated.  
                With no Arrays disabled, there is 1 in 6 chance of malfunction, with one Array disabled this drops to a 1 in 8 chance.  With two or more arrays disabled none of the other arrays will malfunction.</li>
        </ul>
<h5>Escort Arrays</h5>
            <ul>
<li>These operate in exactly the same way as Targeting Arrays above, however they also provide their hit chance increase to friendly ships within 5 hexes.</li>
        </ul>
<h5>Special Shells </h5>
            <ul>
<li>The Grome Rail guns each have access to a number of different shells for their railguns, the effects for these are outlined in <a style="font-size: 14px;" href="./ammo-options-enhancements.php" target="_blank" rel="noopener noreferrer">Ammo, Options & Enhancements</a>.</li>                                      
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="hurr" >HURR REPUBLIC</h4>
    <p>Low-tech faction, one of the weaker League factions. No very specific rules/technologies relying on ballistic, plasma and particle weapons to reasonable effect.</p>            
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="hyach" >HYACH GERONTOCRACY</h4>
    <p>The Hyach are among the oldest of the League races, and have been in space longer than even the Abbai. Their ships are surprisingly advanced among League races, with formidable weapons and military training unequaled
    amongst the League.</p> 
    <p>As the Hyach are very advanced in the area of computers, robotics, and communications, their ships tend to reflect these advantages, with better fire controls, enhanced sensors, and efficient construction.</p>
       <h5>Hyach Computer</h5>
            <ul>
<li>The weapons on Hyach ships are tied into an advanced central computer system that tracks enemy units faster than any mind can. This produces one or Bonus Fire Control Points (BFCP) every turn.</li>
            <li>The Hyach player is free to distribute these points as desired among the three fire control categories (capital ships/heavy combat vessels, medium ships, and fighters/shuttles) each turn, but cannot allocate more than two to any given category.</li>    
        </ul>
<h5>Hyach Specialists</h5>
            <ul>
<li>Some Hyach crewmen receive special training as Specialists. In addition to their usual functions at their posts, they can provide their singular expertise at key moments in battle.</li>
            <li>Ships with at least one Specialist slot on-board must select these on the turn the ship is deployed.  
                To do so, in Deployment phase use the 'Specialists' technical system to select Specialists on each applicable ship, up to their maximum allowance. 
                Only one of each Specialist type can be picked per ship. You can then use your Specialists using the + button on the Specialist System in the game phase you want to use them. 
                Note, different Specialists can be used at different times e.g. Power/Sensor Specialists can only be used in Initial Orders phase, whereas Thruster/Engine Specialists could also be used in Movement Phase and Targeting/Weapon Specialists could be used in any phase up to Firing.</li>
            </ul>
<li>Specialists come in a range of different types,and these have been summarised below:
                <ul class="circle-list">
                    <li><strong>Computer:</strong> Generates two extra Bonus Fire Control Points (BFCP) this turn.</li>
                    <li><strong>Defence:</strong> Ship profiles lowered by 5%, intercept ratings increased by 10.</li>
                    <li><strong>Engine:</strong> +25% thrust for this turn (rounded down).  Removes an Engine critical if applicable.</li>                
                    <li><strong>Manoeuvring:</strong> Halves Turn Cost and Turn Delay for this turn.</li>                
                    <li><strong>Power:</strong> Extra power this turn, depending on ship size (Capitals +12, HCV +10, MCVs +8).  Also removes a Reactor critical if applicable.</li>                
                    <li><strong>Repair:</strong> Removes two critical effects from ship, automatically prioritised by most important systems.  Ignores temporary criticals.</li>                
                    <li><strong>Sensor:</strong> +1 EW this turn.  Removes a Scanner critical if applicable.</li>                
                    <li><strong>Targeting:</strong> All weapons have +3% to hit this turn.</li>                
                    <li><strong>Thruster:</strong> Remove limits on Thruster ratings and improves Engine efficiency by 1 point.</li>                
                    <li><strong>Weapon:</strong> +3 damage to all weapons this turn.</li>                      
                </ul>
            </li>              
        <h5>Interdictors </h5>
            <ul>
<li>The Interdictor is an excellent defensive weapon, though it lacks any sort of offensive firepower. Interdictors can also defend nearby friendly ships, in much the same way as the Centauri’s Guardian Array does, but with fewer restrictions.</li>
            </ul>
<li>In order to block a shot aimed at a different unit, the following must be true:
                <ul class="circle-list">
                    <li><strong>1.</strong> The enemy ship and the target ship must both be in the interdictor\'s firing arc.</li>
                    <li><strong>2.</strong> The target ship must be no farther than 5 hexes away from the interdicting ship.</li>
                    <li><strong>3.</strong> No other interdictor may be used against that same incoming shot.</li> 
                </ul>
            </li>
        <h5>Hyach Sensors</h5>
            <ul>
<li>Hyach sensors are resistant to damage, and shrug off critical hits more easily. Instead of adding +1 to the critical sensor roll for every point of damage, they add +1 only for every two boxes of damage (rounded down).</li>
        </ul>
<h5>Stealth Ships / Submarines</h5>
            <ul>
<li>The Hyach are the only race that operate stealth ships which are the spacegoing equivalent of submarines.  Full rules for Stealth can be found in the <a style="font-size: 14px;" href="./faq.php" target="_blank" rel="noopener noreferrer">Fiery Void FAQ</a></li>
        </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="ipsha" >IPSHA BARONIES</h4>
        <h5>Baronies </h5>
            <ul>
<li>There is no Ipsha "national fleet". The Ipsha species is split into feudal ‘Baronies’ , and any fielded fleet will belong to one of these. Players should only use designs specific to one chosen Barony plus any generic designs in use by all Baronies (indicated in unit notes). Similarly, some enhancements are Barony-specific.  
                In-game fleet checker does not take this rule into account. Some "baronization" of ships is available through unit enhancements (eg. You can apply typical Essan changes to basic Ipsha design).</li>    
        </ul>
<h5>Gravitic Drives</h5>
            <ul>
<li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
            Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>
        </ul>
<h5>Mag-Gravitic Reactor</h5>
            <ul>
<li> Ipsha power sources have fixed power outputs, rather than the usual default of having enough power to have all systems online. 
            This means that destruction of Ipsha systems does not cut into overall power produced by the ship - so even though power is usually negative for an undamaged ship e.g. it can't power everything, the number of systems the ship is able to power does not reduce with damage
            </li>
        </ul>
<h5>EM Hardened</h5>
            <ul>
<li>Ipsha units are especially resistant to Electromagnetic damage, and all critical/dropout rolls are made with -1 bonus. 
            In addition, EM weapons treat EM Hardened units as they would ones with Advanced Armor. This is cumulative with the dropout bonus their fighters already possess.</li>
        </ul>
<h5>Low Pivot Costs</h5>
            <ul>
<li>Ipsha ships have generally low pivot cost (even 0). Pivot-related penalties they suffer just like everyone else.</li>
        </ul>
<h5>Weapon Cooldowns</h5>
            <ul>
<li>All Ipsha weapons have a recharge rate of 1 turn. However, in practice they usually do require a cooldown period (e.g. forced shutdown) after firing (see Surge Cannon below).
            Coupled with limited power available, this means Ipsha usually power up and fire some of their weapons, and while they are cooling down - rotate to bring fresh ones to bear.
            </li>
        </ul>
<h5>Surge Cannon</h5>
            <ul>
<li>The primary Ipsha weapon is extremely versatile due to being able to fire more powerful beams by combination with other Surge Cannons. Basically two, three, four or even five weapons can combine into a single one shot with increased range, damage and fire control against ships.  Each rake causes additional +2 on critical/dropout roll of system hit, too.</li>
            <li>To exercise this ability, just switch weapons to appropriate mode (2,3,4,5-combined) and choose a target. All weapons from the same ship sharing mode and target will combine into appropriate shot(s) (e.g. 4 weapons in 2combined mode would produce 2 shots, but in 4Combined they would only produce one more powerful shot).</li>
            <li>If the indicated combination is not possible (eg. 3 weapons declared to fire in 4Combined mode) weapons will fire separately (in single fire mode) instead (usually missing, but not requiring cooldown).</li>   
        </ul>
<h5>Spark Fields</h5>
            <ul>
<li>This weapon creates a field that causes damage to all nearby units, friend or foe alike. This damage is low but still very threatening to fighters. The range of the Spark Field can be increased by boosting the system, but the greater the range the lower the damage.</li>
            <li>The Spark Field will fire automatically providing it is online, and the player does not need to manually declare it beyond ensuring it is online and they have set the boost level they desired during Initial Orders.</li>                                      
        </ul>
<h5>Jumpsphere</h5>
            <ul>
<li>The Ipsha have only one official jump-capable ship, Scout Wheel. This limits their legal fleet builds for tournament/pickup battles considerably. To allow greater flexibility in this area a custom jumpship design was added as a Warsphere variant, exchanging the fighter hangar for a Jump Drive. The ship has been marked ‘Semi-Custom’ accordingly.</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="korlyan" >KOR-LYAN KINGDOMS</h4>
    <p>The Kor-Lyan specialize in ballistics, using them as their primary offensive and defensive weaponry. This specialization leads to the Kor-Lyan having several fun and unique systems.</p>
       <h5>Class-D Launcher</h5>
            <ul>
<li>This is the main defensive system for the Kor-Lyan, it is essentially a light missile launcher that can only fire Interceptor, Anti-Fighter and Chaff missiles. 
                It comes loaded with a full amount of Interceptor missiles but the other two types need to be bought in the usual way.</li>      
        </ul>
<h5>Class-F Launcher</h5>
            <li>Where the ‘F’ stands for flexible and can fire in two separate modes.
                <ul class="circle-list">
                    <li><strong>Normal</strong>The launcher's normal range is 20 hexes, however it can fire up to an additional +15 hexes when it has been loaded for two turns. When this range extension is used the launcher suffers -2 fire control and cannot use Rapid mode the following turn.</li>
                    <li><strong>Rapid</strong>The F-Launcher can choose to fire every turn with a -5 hex and -2 Fire Control penalty. It cannot fire in this mode the turn after firing in long-range mode.</li>
                </ul>
            </li>                
        <h5>Proximity Laser</h5>
            <ul>
<li>A ballistic weapon that uses a separate launcher system to target the hex from which its main laser attack will then fire.</li>
            <li>To use this ability, first select the Proximity Laser system and target the hex from where you want the laser to fire from at an enemy target.  
                Then, without unselecting the same weapon, target the enemy ship with the Proximity Laser.</li>
            <li>The weapon will always automatically hit the intial hex targeted and then the laser attack will be made as normal as if originating from that hex, and not the firing ship.  
                It's highly recommended that you can use the Ballistic Lines button when targeting this weapon.</li>
                <li>The Proximity Laser does not use OEW but also does not suffer from having no lock on the enemy vessel it targets.  Range penalties are calculated from the hex targeted to the enemy unit you've target. You cannot target hexes for the initial shot that you do not have line of Sight to,
                    and if there is no line of sight between target hex and enemy vessel at the end of movement then the laser shot will automatically miss.</li>
        </ul>
<h5>Limpet Bore Torpedo</h5>
            <ul>
<li>A ballistic weapon that can only fire as Called Shot but can target any system on an enemy ship, including those that are not on facing sections.</li>
            <li>If the torpedo hits, it will attach to the target system and try to damage it by adding a critical effect. 
                If the system targeted is not on a facing section at the moment of impact, it will take an additional turn(s) to crawl around the enemy ship and attach to the target system.</li>
            <li>The target system will now have a critical effect noting the Limpets progress. 
                This critical effect will remain until the target system is destroyed, or after five failed attacks by the Limpet Bore. 
                It has no effect on OSATs or units equipped with Advanced Armor. </li>
        </ul>
<h5>Ballistic Mine Launcher</h5>
            <ul>
<li>Launches a mine at a target hex, with a 25% chance to scatter.  Once it lands on a hex it will look for ships up to its maximum radius then attack the closest, 
                including friendly ships (if multiple ships are equally distant it will select one at random). Mines that do not find a target on the turn they are fired, with persist as a Captor Mine until they are destroyed or find a unit to attack.</li>
            </ul>
<li>The launcher does not come with any ammunition and you must purchase this at Fleet Selection from the available choices:
                <ul class="circle-list">
                    <li><strong>Basic:</strong> - 3 hex range, +40 to hit 16-24 Damage,</li>
                    <li><strong>Heavy:</strong> - 2 hex range, +25 to hit 25-34 Damage,</li>
                    <li><strong>Wide:</strong> - 5 hex range, +40 to hit 13-22 Damage.</li>                    
                </ul>
            </li> 
        <h5>Armed Shuttles</h5>
            <ul>
<li>Like all Babylon 5 ships, Kor-Lyan ships receive a number of shuttles for no additional points. 
                However, the Kor-Lyan have the option to arm these shuttles with two basic fighter missiles.</li>
            <li>The only cost is the missiles. Additionally, the Kor-Lyan can replace their standard shuttles with armed shuttles. 
                The most common one has a single fighter gun and two missile hard points.</li>        
            <li>An uncommon shuttle variant removes the gun, but allows the armed shuttle to hold up to six basic fighter missiles. 
                The Kor-Lyan player is under no obligation to take any of these in a match, unlike fighters with the half filled hangars requirement.</li>    
        </ul>
<h5>‘Early’ Units</h5>
            <ul>
<li>A few key Kor-Lyan units are not available until the 2240s or 2250s. This can make fielding an accurate force difficult. 
                The semi-custom versions are units that follow the example of the Toloki Starbase and replace the F-Launchers with L-Launchers.</li>                                      
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="pakmara" >PAK'MA'RA CONFEDERACY</h4>
    <p>Pak'ma'ra ships are quick, with excellent thrust and engine power, but are not manoeuvrable.  They depend on a heavy armament of plasma-based weapons with wide firing arcs to win battles.  
        Their unique plasma battery systems allow them to store power for opportune moments whilst their plasma webs provide both defensive cover and close-range anti-fighter protection.</p>            
    <h5>Initiative Penalties</h5>
        <li>The Pak'ma'ra find it hard to organise effectively and this is reflected in a fleet wide initiative penalty of -5 for every three ships in their fleet (rounding down).  
            Fighter flights are not effected by this penalty and do not contribute to the number of ships for the purposes of calculating the penalty.</li>    
    
    <h5>Plasma Batteries</h5>
        <ul>
        <li>System which stores but does not generate any new power.  
            Starts the game fully charged and the power stored can be used in the normal way during Initial Orders, it will show as a surplus in the Reactor system.  
            Providing they have power stored, Batteries can also be used in Firing Phase to provide extra power to Plasma Webs.</li>
        <li>Once depleted, the Batteries can be re-filled by commiting your Initial Orders with a power surplus in the Reactor.</li>
    </ul>
<h5>Plasma Webs</h5>
        <li>Hex-targeted defensive weapon that can be used in one of two ways:  
            <ul class="circle-list">
                <li><strong>Defensive:</strong> - All fire from target hex suffers -10% hit chance against this vessel.  In addition, reduces damage from Antimatter, Laser and Particle attack by 2.</li>
                <li><strong>Anti-Fighter:</strong> Requires 1 extra power from boosting in Initial Orders or PLasma Batteries.  Creates a plasma cloud within 3 hexes of ship.  This cloud immediately damages any fighter in that hex and will persist to the end of the next Movement phase damaging any new fighters that pass through it,</li>               
            </ul>
        </li>
    <h5>Fighter Maneuverability </h5>
        <ul>
<li>Pak'ma'ra fighters have an inherent turn delay which cannot be shortened.  This is compensated by high thrust ratings and wider weapon arcs.</li>          
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="torata" >TORATA REGENCY</h4>
    <p>Torata extensively use Accelerator weapons, either allowing them to fire faster at the price of decreased damage output on most of their ship weapons, 
        or fully charge them to to deal massive damage.</p>
    <p>Note - Accelerator weapons will NOT be used for interception without explicit order to do so - even fighter guns.</p>                    
    <a class="back-to-top" href="#top">↩ Back to Top</a>    


    <h4 id="vree" >VREE CONGLOMERATE</h4>
    <p>The Vree are an advanced race within the League of Non-Aligned Worlds that use distinctive 'saucer' designs on all of their ships.  The Vree use antimatter weapons almost exclusively, which deal increased damage based on the difference between the base hit chance and actual dice roll to hit.  
        As a result their weapons are powerful at close and medium range but typically less effective at longer distances.</p>
        <h5>Vree Ship Layouts</h5>            
                <ul>
<li>Vree capital ships have six outer hull sections, similar to starbases.  On Vree capital ships each section has its own structure and 
                largely functions the same as on normal four-sided capital ships, 
                however destruction of an outer hull structure will not destroy any systems/weapons on that section.</li>
                <li>Incoming fire will roll to hit either a weapon, thruster or structure and then be randomly allocated to an appropriate system that is in arc of the firing ship.</li>
                <li>Different weapons from the same ship can hit different hull sections of a Vree ship to help maintain some of their durability in battle.  A single shot cannot be split between multiple sections though.  Meaning Vree can be vulnerable to raking (and to lesser degree pulse) weapons.</li>      
        </ul>
<h5>Antimatter Weapons</h5>
            <ul>
<li>Antimatter weapons calculate range penalty in non-standard way.  For first few hexes they receive no penalty, then they receive normal penalty, and at further ranges
                they receive double penalty. In the case of no lock-on, range itself is doubled (rather than range penalty) and their range-reduced critical effect doesn't affect range penalty itself. 
                Rather, it adds 3 hexes to effective distance to target.</li>
            <li>Antimatter weapons also calculate damage in non-standard way. Rather than being randomized, the difference between actual 
                and needed to hit number is calculated (refrred to as X) 
                and every 5 points of difference translates into 1 point of X. The damage equation incorporates this X factor rather than the normal dice roll. 
                The damage reduced critical on antimatter weapons, reduces the value of X by 2 (but can't reduce it below 0).</li>                       
        </ul>
<h5>Antimatter Shredder</h5>
            <ul>
<li>This is a hex targeted weapon, which affects both its target hex and all adjacent hexes. 
                The Antimatter Shredder rolls to hit on any enemy ships in the target area, shown as extra shots in the Combat Log due to how FV  handles firing for this weapon.</li>
            <li>Only one Shredder may engage a target on any given turn - if multiple ones are eligible, only one of them (chosen randomly) will actually fire. 
                Also, Shredder engages all units in the target area, without discriminating between friend or foe (but won't engage firing ship itself).</li>
            <li>Shredder attacks completely ignores any and all EW (and EW-affecting systems like Jammer or Rutarian Stealth), as well as Jinking</li>
        </ul>
<h5>Gravitic Drives</h5>
            <ul>
<li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
            Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>            
        </ul>
<h5>Turrets</h5>
            <ul>
<li>Some Vree weapons located on the primary section of Vree ships are grouped into turrets, 
                which have the limitation that all weapons in one turret must engage targets within 60 degrees of each other.  Additionally, whenever a turret weapon takes damage it rolls a d20 and on a roll of 17+ the whole turret becomes jammed to 60 degrees forward arc.
                </li>                                                 
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>



<h3 id="minorfactions" >MINOR FACTIONS</h3>
    
    <h4 id="alacan" >ALACAN REPUBLIC</h4>
    <p>No special rules or systems.</p>                
    <a class="back-to-top" href="#top">↩ Back to Top</a>    


    <h4 id="beltalliance" >BELT ALLIANCE</h4>
    <p>Belt Alliance is somewhat notable for extensive use of Matter weapons (including as fighter guns) and EA Interceptors (although with no integrated Energy Web).
    BA also makes use of LCVs. Belters can use (and are in fact the original designers and manufacturers) Delta-V fighters.</p>                    
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="balosians" >BALOSIAN UNDERDWELLERS</h4>
    <p>No special rules or systems.</p>                    
    <a class="back-to-top" href="#top">↩ Back to Top</a>    

    <h4 id="barada" >BARADA IMPERIUM</h4>
    <p>Based on design by Alex Fulton, published in The Great Machine. They lack heavy weapon mounts, but in exchange field large arrays of light, standard and heavy particle beams. Their ships also make use of LCV rails and large hangars.</p>                    
    <a class="back-to-top" href="#top">↩ Back to Top</a>    
	
    <h4 id="chlonas" >CH'LONAS COOPERATIVE</h4>
    <p>Based on design by Charles "Danesti" Haught, published in BabCom. They don't possess any special technologies per se - although they have a few interesting weapons that use existing mechanics in atypical ways e.g. Matter Stream.</p>                        
    <a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="corillani" >CORILLANI THEOCRACY</h4>
    <p>The Corillani are unusual in that they operate three independent navies, the Defenders of Corillan (DoC), the Corillani's People's Navy (CPN), and the Orillani Space Force (OSF).  
        Whilst these forces generally operate independently of one another they can, and will, fight alongside one another.  
        In practice though, there is no strict restriction on mixing and matching ships from the three different sub-navies during Fleet Selection.</p>
    <p>However, when choosing fighters you must take the version of the Tillini which corresponds with the type of faction where hangar space is available 
        i.e. DoC or OSF fighters could not be taken if the only hangar space available was on a CPN ship.</p> 
    <h5>Plasma Projector</h5>
        <ul>
<li>Used only by the OSF, this weapon focuses plasma into a tight beam, resulting in a longer range than most plasma weapons and with lower damage degradation.  
            It deals similar  damage to a heavy plasma cannon, however this is applied in raking (8) mode rather than standard mode.</li>                                  
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="deneth" >DENETH TRIBES</h4>
    <h5>LCV Carrier</h5>
        <ul>
<li>Deneth rely on LCVs more than other races and are able to purchase an LCV carrier to allow these small ships to be fielded whilst still conforming with the standard fleet composition rules.</li> 
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="descari" >DESCARI COMMITTEES</h4>
    <h5>Plasma Bolters</h5>
        <ul>
<li>These weapons combine features from both plasma and bolter weapon types.  
            They do a set amount of damage and do not suffer the damage reduction typical in plasma weapons up to a certain range.</li>                                  
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>     


    <h4 id="drakh" >THE DRAKH</h4>
    <p>Custom fleet created by Wolfgang Lackner and Marcin Sawicki, based heavily upon BabCom-published designs by Roman Perner. 
        Drakh are a Middle-born faction, with a civilization older than even Minbari or Yolu, but not as old as the Ancient factions. 
        This has no direct impact in the game itself, unless a particular system treats such a faction differently.</p>
    <h5>Absorption Shields</h5>
        <ul>
<li>Powerful shields that absorb incoming damage.  They can be boosted to be even stronger and are doubly effective against raking weapons.</li>
        <li>Unlike some other shields, they cannot be flown under by fighters and they don't decrease the unit’s defence rating, only reducing damage.</li>
    </ul>
<h5>Advanced Armor</h5>
        <ul>
<li>Provides a plethora of perks, generally reducing damage taken. It reduces or limits special effects of most weapons that have them, and counts as 2 points stronger against ballistics. 
            Ignored by weapons made by Ancients.</li>
    </ul>
<h5>Gravitic Drives</h5>
        <ul>
<li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>
    </ul>
<h5>Improved Sensors</h5>
        <ul>
<li>Halves the effectiveness of enemy Jammers.</li>                    
    </ul>
<h5>LCVs</h5>
        <ul>
<li>Drakh LCVs use regular Sensors (that is, they're not bound by the usual "all but 2 EW points need to be used offensively rule).</li>
    </ul>
<h5>Hangar Space</h5>
        <ul>
<li>Drakh LCVs do require hangar space (unlike regular LCVs). Instead they share it with Drakh fighters, although LCVs take twice as many slots.</li>                                                     
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>  


    <h4 id="llort" >THE LLORT</h4>
    <p>No very specific rules/technologies but it's worth noting that their ships are often unusually asymmetric with atypical firing arcs and eclectic weapons sets.</p>                                 
    <a class="back-to-top" href="#top">↩ Back to Top</a>    


    <h4 id="markab" >MARKAB THEOCRACY</h4>
       <h5>Scattergun</h5>
            <ul>
<li>Light weapon that fires a random number of shots. When firing defensively these shots will be directed at separate threats, never the same shot more than once.</li>    
       </ul>
<h5>Religious Fervor</h5>
            <ul>
<li>In certain scenarios Markab can fight whilst under the Religious Fervor effect.  This is selected as an Enhancement option in fleet selection at no cost, 
            and confers a +1 to hit with all weapons, +2 initiative and fighters gain a -3 bonus to dropout rolls. However, their recklessness means their defence ratings are increased by 10% in all directions.</li>
            <li>This a scenario-specific option, you should check with your opponent before selecting this enhancement, particularly as it signifies that the Markab are permitted to ram.  
            You should also select the Religious Fervor for all your Markab ships, or none of them since it’s intended as a fleet-wide effect.</li>                                        
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>   

    <h4 id="minbariProt" >MINBARI PROTECTORATE</h4>
    <p>This is a part of the Minbari Federation, but consists of several non-Minbari members that are overseen by the Federation. They have a degree of autonomy, 
    including providing local defense to their worlds.</p>
    <p>The Protectorate uses older Minbari hulls with the notable difference being that units do not come equipped with the Jammer system. 
    The Tinashi is their most modern hull along with a handful of Nials.</p>
    <p>The Protectorate generally uses the Tishat medium fighter. Their fleet options are rather limited based on the number of hulls and lacking ways to bring fighters outside a Morshin carrier. 
    Their one noted conflict is the Pseudo-War with the Imperial Star Legion raider group.</p>                                                                   
    <a class="back-to-top" href="#top">↩ Back to Top</a>
        

    <h4 id="rogolons" >ROGOLON DYNASTY</h4>
    <p>No special rules or systems.  Notable mainly for the complete absence of anti-fighter weaponry on their ships.</p>                                             
    <a class="back-to-top" href="#top">↩ Back to Top</a>  


    <h4 id="smallraces" >SMALL RACES</h4>
    <p>A couple of smaller factions that are only connected by the fact that they don't have many ship designs.</p>                                            
    <a class="back-to-top" href="#top">↩ Back to Top</a>   

    <h4 id="usuuth" >USUUTH COALITION</h4>
    <p>Their fleet relies on LCVs but they don't actually have a carrier for these since they only typically operate within their home system.  
        This presents challenges for Usuuth fleets when deployed under standard tournament/pickup battle limitations, unless players agree to change the rules 
        and allowances are made for the Usuuth player. </p>                                            
    <a class="back-to-top" href="#top">↩ Back to Top</a> 

    <h4 id="yolu" >YOLU THEOCRACY</h4>
    <p>A very strong faction, often too strong.</p>
       <h5>Light Molecular Disruptor</h5>
            <ul>
<li>Armor-stripping weapon equipped on Yolu heavy fighters, if removes 1 point of armour on enemy structure for every three shots that hit.</li> 
       </ul>
<h5>Fusion Agitator</h5>
            <ul>
<li>Raking weapon that does damage in smaller rakes (6).  However, it ignores a point of armor when damaging ships and its raw damage output is actually very good.</li>
            <li>Extra power can be used to boost damage further (by 1d10 per boost level) so its output can rise to be extremely high.</li>
       </ul>
<h5>Molecular Flayer</h5>
            <ul>
<li>Does not damage to the enemy ship, but instead reduces the armour of every system and structure on hull section it hits.</li>
        </ul>
<h5>Points Re-Evaluation Enhancement</h5>
            <ul>
<li>Due to the oppressive strength of the Yolu an optional enhancement has been added that that does nothing except modify the costs of some ships.</li>
            <li>This should make Yolu a much more reasonable faction to take against other Tier 1 opponents.</li>
            <li>Note this is player initiative and using these enhancements turns the fleet into Custom faction, so do check with your opponent!</li>                                                                                        
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a> 



<h3 id="ancientfactions" >ANCIENT FACTIONS</h3>
    <p>Ancient races typically have a much more complicated set of systems than the Younger Races. 
        They are also not very well balanced against the Younger Races as the tech difference and unit costs are too high for proper balance, nor are they tournament legal.</p>
    <p>But with these caveats - they can be a lot of fun!  Ancients should also be pretty well-balanced against each other, and if your opponent knows what to expect / or you're playing a scenario, 
        go ahead and rampage against the Younger Races as well. </p>
    <p>Note that the extremely high individual cost of Ancient units makes standard requirements based on total fleet value completely inadequate
         - so they have been modified for the Fleet Checker.</p>        

    <p>Most Ancient factions will have access to the standard systems for advanced races listed below:</p>
    <h5>Advanced Armor</h5>
        <ul>
<li>Provides a plethora of perks, generally reducing damage taken. It reduces or limits special effects of most weapons that have them, and counts as 2 points stronger against ballistics. 
            Ignored by weapons made by Ancients.</li>            
    </ul>
<h5>Self-Repair</h5>
        <ul>
<li>Automated, but players can modify the default priority of repairs. Damage and criticals suffered in the current turn cannot be repaired. 
            Cost of critical repair has been changed in places (in particular C&C criticals aren't that costly to fix), and there's no partial repair of crits.  
            If a Self-Repair system is destroyed or damaged, unused repair points are not lost.</li>
    </ul>
<h5>Ancient Jump Drives</h5>
        <ul>
<li>Each Ancient race has its own way into hyperspace, listed on the control sheet as a special jump drive. The <b>Shadows, Kirishiac, Mindriders, Torvalus, Triad, Thirdspace and Walkers</b>
            jump to hyperspace directly and <b>do not open jump points</b>: set <b>Jump to Hyperspace</b> on the drive in Initial Orders and the ship leaves the battle at the
            <b>end of that turn</b>. It can still be fired upon until it goes, only Walker ships may fire at enemies on a turn where they jump to Hyperspace.</li>
<li>Ancient factions can also spend power to boost a recharging Jump Engine, with each level of boost adding +1 to the recharge rate on that turn.</li>            
<li>A ship jumping out <b>may not fire</b> that turn, and that includes interception - setting the jump withdraws any fire orders it already has. The Walkers are the
            exception: their ships fire normally on the turn they jump.</li>
<li>If the drive is damaged, the chance of it destroying the ship as it jumps is <b>halved</b>. The Walkers' drives have no chance of failure at all.</li>
<li>Ancient jump drives <b>cannot be affected by a Vortex Disruptor</b>. The Vorlon Empire (and The System) use jump engines that open ordinary jump points, and remain vulnerable to it.</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>



    <h4 id="kirishiac" >KIRISHIAC LORDS</h4>
    <p>The Kirishiac Lords are the last of the hyper-evolved species and the youngest of the Ancients. They challenged the other Ancients in the past, spurring the Kirishiac War and, indirectly, initiating
    the cycle between the Shadows and Vorlon. At 7 meters tall and from a superterrestrial world, they are masters of gravitic technology. Their armor is just as much derived from their environment as their
    need to counter their Ancient cousins.</p>

    <h5>Hardened Advanced Armor</h5>
        <ul>
<li>Kirishiac armor is incredibly dense. It conveys all of the benefits of advanced armor, but also earns several other advantages ... even against Ancient-class weapons!
            <ul class="circle-list">
                <li>Against weapons that ignore advanced armor, hardened advance armor counts as half, rounding fractions down. This includes Ancient weapons and raking sub-vollies.</li>
                <li>Against flash weapons, hardened advanced armor is counted as double.</li>
                <li>Against plasma weapons, the armor is not halved, even against Ancient weapons.</li>
            </ul>
        </li>
    </ul>
<h5>Antigravity Beams</h5>
        <ul>
<li>The Kirishiac field two versions. Medium antigravity beams can fire one blast or be split into two shots. Standard antigravity beams are more powerful and can be split into three shots.</li>
    </ul>
<h5>Hypergraviton Beam</h5>
        <ul>
<li>The precursor to the Hypergraviton Blaster, this weapon scores +5 additional damage for every 4 points of thrust added to it.</li>
    </ul>
<h5>Hypergraviton Blaster</h5>
        <ul>
<li>This is an extremely powerful gravitic weapon that the Kirishiac have significant control over.
            <ul class="circle-list">
                <li>Can be boosted using engine thrust (instead of power) during Initial Orders and receives +10 damage for every 6 thrust added in this way.</li>
                <li>If the weapon misses, it subtracts 20 damage and re-rolls. This continues until it hits or runs out of damage.  If the weapon misses with its first shot, it can re-roll to hit but will not be able to tranfer to other targets.</li>
                <li>The Kirishiac player may choose to transfer the shot to a different target once the initial target has lost the facing structure block or has been destroyed. This is at the player's
                discretion. The new target must be in arc of the blaster and within 1 hex of the previous target. At the time of firing, a menu is provided. If the player chooses not to have any shot
                transfer, they may select fire normally. Otherwise, the unit they initially select will be listed. If the Kirishiac player wishes to transfer the beam when the facing structure is
                destroyed, they will check the 'Transfer on structure loss' box. Otherwise the blaster will not transfer until the first target is destroyed. The Kirishiac player may select the next target
                from the 'Available Next Targets' menu. The Kirishiac player is allowed to set the rules for a second transfer. As soon as the initial target's structure is destroyed or the unit itself is
                destroyed (depending on the option the Kirishiac player chose), all remaining damage is available for the next target.</li>
                <li>Note: Every transfer will require a new to-hit roll and 20 damage is lost whilst transferring. Transfer shots can also be re-rolled (with the usual 20 damage loss) again, until all remaining damage is exhuasted.</li>
                <li>If targeted against a fighter flight, the blaster will sweep through each fighter as long as damage remains.</li>
            </ul>
        </li>
    </ul>
<h5>Gravitic Augmenter</h5>
        <ul>
<li>The Kirishiac are masters of gravitic technology and the Gravitic Augmenter can be used for several offensive and defensive uses.
            <ul class="circle-list">
                <li>Matter and Ballistic weapon manipulation: This is selected during the Initial Orders phase. If selected, all friendly matter weapons within range and arc receive a +15% bonus to their fire control. Any
                enemy matter weapons receive a -15% penalty. This is cumulative with multiple augmenters. Additionally, friendly ballistics in arc receive a +30% bonus to their fire control while enemy ballistics receive
                a -30% penalty. This too is cumulative.</li>
                <li>Warrior enhancement: Selected during the Initial Orders phase, the augmenter targets a friendly Warrior Projectile flight. For the rest of the turn, and as long as the Warrior remains in
                arc the Warrior Projectile receives 3 levels of jinking, which counts against its total limit, +3 offensive bonus, +3 thrust, and -4 to dropout. Only one augmenter can be used on a Warrior
                flight at a time.</li>
                <li>Gravity shift: Used during the Pre-Firing phase, this allows the augmentor to turn a target up to 120 degrees (or 60 degrees for gravitic units). Only one augmenter can affect a ship like
                this per turn. There is no effect on enormouse units and mines.</li>
            </ul>
        </li>
    </ul>
<h5>Gravitic Drives</h5>
        <ul>
<li>Allows ships to undertake maneuvers even while pivoted/pivoting using thrusters appropriate for their current orientation. Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced
            critical they receive, increasing their resistance to damage.</li>
    </ul>
<h5>Phased Gravitic Torpedo</h5>
        <ul>
<li>Developed during their war with the other Ancients, the phased gravitic torpedo operates identically to the ballistic torpedo with the exceptions noted below.
            <ul class="circle-list">
                <li>The weapon starts fully charged with 9 torpedoes and can regenerate one per turn.</li>
                <li>It may fire at one or more targets per turn, limited only by available torpedoes.</li>
                <li>If more than one torpedo is fired at the same target, it is fired in saturation mode with a grouping bonus of +1 per 15%.</li>
                <li>The main advantage of the torpedo is its ability to phase throungh and disable enemy shields. Against, Younger Race shields, the torpedoes completely ignore the damage reduction. Against
                Ancient shields, each torpedo rolls a d10 and reduces the shield's value by that amount. Each torpedo treats the shield independently if they strike on the same turn. The overall reduction is
                permanent in subsequent turns. The shield will maintain any profile reduction abilities, but damage reduction can reduce the shield to zero.</li>
            </ul>
        </li>
    </ul>
<h5>Ultra Matter Cannon</h5>
        <ul>
<li>This is a long-ranged, rapid-firing matter weapon. It becomes particularly deadly when supported by gravitic augmenters.</li>
    </ul>
<h5>Warrior Projectiles</h5>
        <ul>
<li>These operate as fighters for the Kirishiac but are much better thought of as re-targetable railgun rounds. They attack using one of two special ramming methods that they are always free to use. These
            are resolved at the same time as other weapons fire. They can only attack at range zero. Additionally, they use the Warrior's offensive bonus to determine the chance to hit.
            <ul class="circle-list">
                <li>Glancing ram: The Warrior makes an attack that ensures no return damage to itself. This is the only mode that can be used on fighters.</li>
                <li>Warrior ram: In this mode, the Warrior attempts to punch straight through the target. This can do more damage, but risks damage in return. If the system hit is completely destroyed, the Warrior
                is unharmed. However, if the system hit is not destroyed, the Warrior takes 2 times the system's armor value and must make a drop out roll with a +4 penalty. The return damage is mitigated by the
                Warrior's own armor. NOTE: The Warrior Ram cannot be used against fighters and it cannot be used on the same turn as the Glancing Ram.</li>
            </ul>
        </li>
        <li>Warrior regeneration: As long as one Warrior survives, the flight can return to a Mastership and land. After 5 complete turns the ENTIRE flight will be regenerated to full strength. There is no limit on
            how many times this can occur in a scenario.</li>
    </ul>
<h5>Orbitals</h5>
        <ul>
<li>These are the most unique system used by the Kirishiac. These are small segments of the ship that float over the hull and mount various weapons and systems. There are three types described below.
            <ul class="circle-list">
                <li>Basic functionality (all orbitals): Orbitals can be in one of two states; docked or deployed. There is no penalty to the ship or orbital for docking or deploying. 
                    Orbitals can be docked or deployed at the start of a match, and afterwards their state can be toggled by clickng on the system icon during Firing Phase, with any change taking effect at the very end of the turn e.g. after firing is resolved. If the associated ship structure block is destroyed, all associated orbitals are lost.</li>
                <li>Docked:
                    <ul class="circle-list">
                        <li>The orbital's structure is merged with the structure block it is associated with.</li>
                        <li>Weapons can be deactivated for power and self repair can be used (note, heaavy orbitals have a special rule below).</li>
                    </ul>
                </li>
                <li>Deployed:
                    <ul class="circle-list">
                        <li>Weapons may be used freely.</li>
                        <li>The ship's self-repair cannot be used on them and their weapons cannot be deactivated for power.</li>
                        <li>When damaged overkill to the weapon transfers to the attached orbital's structure. If the orbital's structure is destroyed, any overkill is lost.</li>
                        <li>All orbitals can be directly targeted. This uses any offensive EW applied to the parent ship, but the orbital has a smaller profile. To do so, select the orbital you wish to attack
                        as if you were doing a called shot. Note, there is no called shot penalty.</li>
                    </ul>
                </li>
                <li>Each orbital class has its own specific special rules as detailed here.
                    <ul class="circle-list">
                        <li>Light Orbitals: These are smaller and cannot regenerate on their own. If destroyed, they will automatically dock where the ship's self repair must rebuild the orbital's structure
                        and weapon before it can be used again.</li>
                        <li>Medium Orbitals: These are the most common. In addition to the abilities above, they have the ability to regenerate. If the orbital's structure is fully destroyed, the orbital will automatically
                        dock to its ship. After 5 turns, the orbital and it's attached systems will fully regenerate allowing its weapon to be used for power or to be deployed once again as long as the associated ship
                        structure block has not been lost.</li>
                        <li>Heavy Orbitals: These are used as heavy weapon platforms and are much larger. Unlike medium orbitals, heavy orbitals CANNOT regenerate. However, they have their own self repair system that
                        they can use while they are deployed to remove criticals, fixe the attached weapon, or restore the structure. If they are docked, the self repair system's repair rate is doubled and can be used
                        on the ship or the heavy orbital can benefit from the ship's self repair system or other orbitals if they are also docked. Additionally, the weapon can still be fired when the heavy orbital is
                        docked, but the are is significantly reduced.</li>
                    </ul>
                </li>
            </ul>
        </li>
    </ul>


<a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="mindriders" >THE MINDRIDERS</h4>
    <p>Below is a list of systems The Mindriders use, with short description of effect and any notable design differences from their original tabletop versions.</p>
    <h5>Special Hull Arrangements</h5>
        <ul>
<li>Mindrider ships do not follow the usual layout, as a rule you can see what arcs their structure has by looking at the arcs of the Thought Shield (see below).  
            Mindrider vessels benefit from this arrangement by being allowed to pivot for free, and do not suffer the usual hit chance penalties for pivoting/rolling.  
            Note - The Wheel of Thought ship is unusual in that it MUST pivot every turn.</li>
    </ul>
<h5>Constrained EW</h5>
        <ul>
<li>All Mindrider ships are able to use ELINT abilities, however these are on a 'constrained' basis, and therefore each ability costs 1 extra EW compared to normal ELINT vessels.</li>
    </ul>
<h5>Gravitic Drives</h5>
        <ul>
<li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>                    
    </ul>
<h5>Though Shields</h5>
        <ul>
<li>Mindrider ships and fighters are protected by Thought Shields.  Each shield has a specific arc it covers, and will absorb enemy fire up to it's maximum hit points on that arc.</li>
        <li>Thoughts Shields will automatically regenerate to their full capacity at the start of each turn, and hit points can be transferred between arcs during Initial Orders phase to reinforce particular arcs.</li>
    </ul>
<h5>Contraction</h5>
        <ul>
<li>The Mind's Eye vessel can choose to Contract itself during the Movement Phase, gaining +1 bonus to its Thought Shields and -5 bonus to its Defence Profiles for every level of Contraction.  
            In addition, for every 3 levels of Contraction ALL systems on the Mind's Eye gain +1 Armour.</li>
        <li>To use Contraction ability simply click on the + and - buttons by its special icon, and then apply thrust as usual. Contraction lasts until the end of the turn and then resets to 0.</li>                                                         
    </ul>
<h5>Shield Reinforcement</h5>
        <ul>
<li>Mindrider ships equipped with this system can use its output to reinforce their own shields and the shields of ally ships with EM Shield properties 
            (e.g. reduced hit chance and damage reduction as well as usual Thought Shield damage absorbance).</li>
        <li>To do so, boost the Shield Reinforcement system to the level of reinforcement you'd like and then target ally ships in the Initial Orders phase.  
            All shields on a ship must be reinforced equally, at a cost of 1 output per level of reinforcement per shield (e.g. reinforcing a Thoughtforce vessel with 4 shields by 3 levels would cost 12 output, 3 * 4).  
            Any unspent output will instead be used to reinforce the vessel equipped with the Shield Reinforcement system.</li>      
    </ul>
<h5>Second Sight</h5>
        <ul>
<li>This weapon reduces the Initiative of all enemy ships on the following turn.  To fire the weapon select it during during the Firing phase and press 'Select' button.</li>      
    </ul>
<h5>Thought Wave</h5>
        <li>Ballistic area effect weapon that's fired in Initial Orders turn by selecting it and pressing the 'Select' button. 
            It will attempt to strike all non-Mindrider ships in the game and has a special calculation for both its Hit Chance and Damage:
                <ul class="circle-list">
                    <li><strong>Hit Chance:</strong> - 15 + OEW + d20 roll - Range Penalty - DEW - Target's Initiative,</li>
                    <li><strong>Damage:</strong> - (3d6/3) * (Target Defence Profile/5),</li>                   
                </ul>            
            </li>
        <ul>
<li>Note - For ships with Advanced Armour, the first half of this calculation is (3d6/5), vastly reducing the damage potential of the Thought Wave.</li>
        <li>Only successful attacks will be shown in the Combat Log.</li>      
    </ul>
<h5>Ultra Pulse Cannon</h5>
        <ul>
<li>The ultimate in pulse technology.  Can fire in three modes, Heavy (24 damage, D3 pulses), Medium (16 damage, D5 pulses), Light (12 damage, D6 pulses).</li>  
    </ul>
<h5>Telekinetic Cutter</h5>
        <ul>
<li>Fires two shots per turn, with high damage range.</li>  
    </ul>
<h5>Trioptic Pulsar</h5>
        <ul>
<li>Fires three shots, the main light weapon on Mindrider ships.</li> 
    </ul>
<h5>Thought Projections</h5>
        <ul>
<li>Mindriders use their psychic energy to create 'fighter' units called Thought Projections.</li>
        <li>These Projections must have a Mindrider ship with appropriate hangar capacity present in the game, or they will destabilise and drop out at the end of the turn where this is no longer the case.  
            E.g. two Thoughtforce vessels can sustain 24 Projections. 
            During a battle, if one of the Thoughtforce ships is destroyed then the number of Thought Projections that can be sustained would drop to 12, and any Projection over 12 would instantly drop out.</li>      
    </ul>
<h5>Minor Thought Pulsar</h5>
        <ul>
<li>Fighter weapon for Mindriders. Use the menu provided in Firing phase to boost weapons properties using any unused Thrust.  Each boost requires 3 unused Thrust and includes bonuses to number of shots, damage, and offensive bonus.</li>                                               
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>

    
    <h4 id="shadows" >SHADOW ASSOCIATION</h4>
    <p>Below is a list of systems used by the Shadows, with short description of effect and any notable design differences from their original tabletop versions.</p>
    <h5>Ship Layouts</h5>
        <ul>
<li>Even capital-sized Shadow ships use MCV ship layout and systems on Shadow ships cannot be targeted by called shots.</li>
    </ul>
<h5>Energy Diffusers</h5>
        <ul>
<li>Absorb damage as it's being dealt (and diffuses it over time). In FV the diffuser always kicks in if possible, there's no option to withhold its use 
            e.g. waiting for more threatening damage and conserving resources.</li>
        <li>For visual reasons tendrils are shown as separate, untargetable systems and damage absorbed by ship tendrils is not shown in the log.</li>
        <li>Diffusers will not try to stop no-overkill damage that would kill system hits despite Diffuser interference. This is important for very strong Matter or Piercing shots.
        Flash damage is treated in a special way - the portion of damage enough to destroy a system is treated as an entire shot which allows systems to be saved even if total damage of shot is very high.</li>    
    </ul>
<h5>Bio-Drive</h5>
        <ul>
<li>Shadow ships have omnidirectional thrusters that generate their own thrust. In FV, for technical reasons, these are represented by a series of untargetable thrusters with 
            unlimited rating alongside an untargetable engine with rating that’s the sum of all biothruster ratings. This set up allows any thrusters to provide thrust in any direction.</li>
        <li>Biothrusters have their own set of criticals (which reduce output) in place of regular thruster criticals.  The Bio-Drive cannot be boosted.</li>         
    </ul>
<h5>Half Phasing</h5>
        <ul>
<li>During the Movement Phase, Shadow ships can become semi-immaterial until the end of turn by Half-Phasing.</li>
        <li>While Half-Phased ships become much more difficult to hit (-20% penalty to hit from normal weapon fire, -40% from ballistics) but also becomes less accurate itself (-50% to hit on all shots). 
            In addition, they cannot ram or get rammed at all, unless both ships are half-phasing.  Half-phased ships will also not collide with terrain they move through so long as they don't end their movement on a terrain hex.</li>
        <li>Performing Half-Phasing costs full thrust from two undamaged BioThrusters, as well as the presence of an online, undamaged  Phasing Drive. 
            Any damage to Phasing Drive during half-phasing destroys the ship.</li>
    </ul>
<h5>Pilot</h5>
        <ul>
<li>Shadow ships have no crew, but they do have a pilot. The pilot is shown on ship windows as the C&C, with the main differences from regular C&C being:</li> 
                </ul>
<ul class="circle-list">
                    <li>Pilot cannot suffer criticals, but feels pain when ship is damaged (temporary) or when they’re wounded,</li>
                    <li>Pilots cannot be repaired by a self-repair system.  Telepathic attacks on Shadow ships are not implemented in Fiery Void.</li>                   
                </ul>            
            </li>            
    <h5>Molecular Slicer Beam</h5>
        <ul>
<li> Uninterceptable, armour-gnoring with a high FC and good range, it has very high damage output and can literally cut ships apart when fully charged.  
            It has a number of other traits too, some of which has been simplified for Fiery Void.</li>
        <li>The most powerful version of this weapon, the Heavy Slicer found only on Primordial ships uses Piercing damage as its default mode - which can even overkill at full power so that its damage potential isn’t wasted. 
            The Raking damage mode is capped at 2-turns charge level.</li>
        <li>All other Slicers use Sweeping Mode as their default, which allows them to target as many different enemies as it has dice/damage to allocate, assigning a number of d10 target dice and/or set-damage to each shot up to their maximum amount. Simply select the weapon and choose who you want to target in this mode, although each ship can only be targeted once (whilst fighter flights can be targeted multiple times). 
            When splitting shots, the weapon will attract a cumulative -5% penalty for every additional shot after the first, as well as any modifier for defensive shots (see below).</li>
        <li>Slicers may commit a 1d10 dice or 6 set-damage to gain -10 intercept by clicking the 'Self-Intercept' green shield icon and using the menu provided.  Each self-intercept dice committed in this way increases the number of different shots Slicer may intercept, as well as the total interception amount.
            If you choose to fire offensively, or select any amount self-intercept dice, but do not use all your available dice/damage, then any unspent dice will be added to intercept so long as you committed as least ONE self-intercept dice.  You can use ship tooltip to track defensive fire.
            NOTE - Each self intercept dice commited counts as a 'shot' for the purposes of the -5% penalty detailed above.  The Light Slicer has no intercept rating and cannot intercept at all.</li>
        <li>A Slicer can also be pointed at one <strong>specific</strong> incoming shot, rather than leaving the game to place its defensive fire for it.  In the Firing phase, select the Slicer, click the unit being shot at to bring up its ship tooltip,
            and click the hit chance of the shot you want to engage in the INCOMING list (manual interception is described in full in the
            <a style="font-size: 14px;" href="./faq.php#interception" target="_blank" rel="noopener noreferrer">FAQ</a>).</li>
        <li>A shot named that way costs exactly what a self-intercept costs - one damage die, or one whole 6-point block of set damage - out of the same pool, and counts as a 'shot' for the -5% penalty in the same way.
            Capacity you have already bought is re-used rather than paid for twice: if you hold self-intercept dice that are not yet pointed at anything, naming a shot spends one of those instead of charging the pool again.
            Withdrawing works from the ship window as usual, and hands the die or damage block back.</li>
        <li>When you look at a target's ship tooltip, each Slicer shot in its INCOMING list is written with the allocation you gave it - for example
            <em>1x Molecular Slicer (Sweeping) (3d + 12)</em> for three damage dice plus twelve points of set damage.</li>
            <li>Slicer can make Called Shots against fighters without any penalty.</li>                                                                 
    </ul>
<h5>Phasing Pulse Cannon</h5>
        <ul>
<li>Pulse weapons that completely ignore any kind of shield or shield-like defense used by Younger races. 
            Note that EM shields, like those used on the White Star, are treated as Ancient even when used on Younger Race ships.</li>      
    </ul>
<h5>Vortex Disruptor</h5>
        <ul>
<li>A weapon that destabilizes hyperspace vortexes, preventing enemy escape. 
            It is fired at hexes where vortex are formed or forming. If it hits, any ships which are traversing the vortexe on that turn will be automatically destroyed unless they are Ancients.  
            Ancients factions have a chance to survive the vortex collapse depending on their distance to the vortex and how well the vortex disruptor rolled to hit.  
            They roll 1d100 against the shot's to-hit margin plus the distance*5 ship travelled this turn, escaping on equal or higher.</li>
        <li>The game will resolve the shot (e.g. calculate hit chance and report result in firing log) and show the result in the Combat Log.  
            The Vortex Disruptor cannot be fired while a Shadow ship is half-phased.</li>          
    </ul>
<h5>Primordial Variants</h5>
        <ul>
<li>These are ships with Primordial and Additional Tendril enhancements added as standard, as neither of these options is available in FV.</li>      
    </ul>
<h5>Primordial Battle Cruiser</h5>
        <li>This ship differs from modern, quick-grown, Shadow vessels in a few ways:
            <ul class="circle-list">
                <li>Most important is primary weapon firing arc: The Heavy Slicer on the Shadow Battlecruiser has both forward and rear arcs, and you can split your sweeping fire between both forward and rear arcs.</li>
                <li>Six separate Energy Diffusers would have been difficult to set up on PRIMARY in a clear way - therefore in FV they've been moved to appropriate sides and hit charts adjusted.</li>                   
            </ul>            
        </li> 
    <h5>Destroyer</h5>
        <ul>
        <li>By original design should have acceleration cost of 1.5, which is not possible in FV so has been set to 2 instead.</li>  
        </ul>
<h5>Shadow Fighters & Fighter Bombs</h5>
        <ul>
        <li>Shadow fighters are semi-autonomous slivers made from the hull material of their carriers.</li>             
        <li>Some Shadow carriers form their fighters internally and keep them held within the hull rather than parked in space. These integrated fighters are bought as an enhancement on the carrier, and the carrier can carry no more than its listed hangar capacity.</li>
        <li>Such a carrier does not launch its fighters in the usual way. Instead it deploys them with a Fighter Bomb - a forward-arc weapon, selected and fired during the Firing Phase at a target hex within range. The held fighters deploy at that hex the following turn, sharing the carrier's heading and speed. Note - Fighters cannot be launched (or recovered) if the Shadow ship is Half-Phased.</li>
        <li>You choose how many fighters to commit per shot, up to the number currently held. If you launch more than a single flight can hold, the bomb forms several flights at the target hex. By default these are split for you (largest flights first), but you can instead set each flight's size by hand, and add extra flights so a large launch can be broken into smaller groups if you prefer.</li>
        <li>Unlike an ordinary fighter launch, firing the Fighter Bomb carries no initiative penalty - neither on the launched fighters nor on the carrier. (Note - Recovering fighters still incurs the usual hangar-operations penalty.)</li>
        <li>Integrated fighters return by landing on the carrier as normal, where they are reabsorbed back into the hull: their damage is repaired by the ship but this, plus any energy held in their diffusers, is drawn back into one of the carrier's own Energy Diffusers (with any excess penetrating to a random system), and they are ready to be lanched again from the following turn. An empty hangar - whether the fighters were spent or lost - leaves the Fighter Bomb with nothing to launch.</li>
        <li>Shadow fighters get a massive -100 dropout bonus  which means they'll pass all appropriate tests easily, 
            but can still be dropped out by weapons that explicitly cause dropouts without a test. Most weapons that do so are Electromagnetic in nature, and actually will be disregarded by Advanced Armor anyway. 
            But technically some Ancient weapons causing automatic dropouts might still affect a Shadow fighter. </li>
        <li>To take advantage of their resilience,through the combination of Energy Diffuser protection and dropout immunity, Shadow fighters have their own algorithm for hit allocation which will tend to spread damage among the flight more than usual.</li>
        <li>Shadow fighters are equipped with an accelerator weapon, for this reason it will not be used for interception without explicit order to do so!</li>        
        </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="thirdspace" >THIRDSPACE</h4>
    <p>The ‘Thirdspace aliens’ are a mysterious and terrifying race of malevolent telepaths from an alternate dimension, in possession of technology considered more advanced than even the Shadows or Vorlons.  
        While not indestructible, their ships are protected by a powerful energy shield that can absorb a significant amount of weapons fire before opponents can inflict damage on their ships themselves.</p>
    <p>In Fiery Void, they have been created as a Custom faction (with credit to Amras Arfeiniel for providing the original image for the Thirdspace Battleship) with the following key features:</p>    
    <h5>Thirdspace Shields</h5>
        <ul>
<li>Thirdspace shields absorb all incoming damage in the arc that they cover.  Once these shields have been reduced to zero rating damage will start to be inflicted on their ships as normal.</li>
        <li>During Initial Orders players can freely move shield power around via the Shield Generator. This system must be at 0 in order to commit your orders e.g. you can't 'save' shield energy in the generator for future turns. 
            The maximum amount you can allocate to any given shield is three times its base value.</li>
        <li>The Shield Generator also comes with a number of preset options you can click on to assist in moving shield energy around.  
            At the end of the turn, Shield Projectors will restore their respective shields by an amount based on their current rating.</li>
    </ul>
<h5>Psychic Field</h5>
        <ul>
<li>Thirdspace capital ships produce a passive Psychic Field which affects all enemies within range and debilitates opponents by applying debuffs on the following turn.  
            It reduces initiative for all types of unit and enemy ships will either suffer decreased hit chances if it impacts on the ship’s structure, or cause a possible critical effect when it hits any other system.  
            For fighters, it reduces their offensive bonus.</li>
        <li>Ancients are not immune to this effect but suffer only 50% of the effect compared to Younger races.</li>    
    </ul>
<h5>Gravitic Drives</h5>
        <ul>
<li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>                    
    </ul>
<h5>Psionic Weapons</h5>
        <ul>
<li>Thirdspace have a number of other Psionic weapons, which typically deal a large amount of electromagnetic damage.  
            Many also have additional effects on Younger races such as an increased critical hit/fighter dropout chance or a reduction in ship armour.</li>
        <li>Thirdspace heavy weapons, Psionic Lances, can be boosted with EW during Initial Orders phase to increase their damage.</li>
        <li>Their Psionic Torpedo deals a reasonable amount of damage, and in addition also causes temporary penalties to initiative, power and sensors (which are cumulative with the Psychic Field, but not with other Psionic Torpedoes).</li>
        <li>Thirdspace aliens make significant use of the flexible Psionic Concentrator weapon.  Which will fire four separate shots in in it's default firing mode.   
            This is very effective against fighters, but the weapon can also combine these four shots into two stronger double shots or even powerful, but short-ranged, single attack.</li>
    </ul>
<h5>Advanced Singularity Reactor</h5>
        <ul>
<li>Like the Ipsha, Thirdspace ships use a singularity reactor which produces a set amount of power, regardless of whether systems have been destroyed or not.  
            As Thirdspace ships utilise power management more heavily than other Ancients, to boost shields, engines or EW for example, it means they can continue to fight effectively even when taking damage.</li>                                                        
    </ul>
<h5>Psychic Focus</h5>
        <ul>
<li>In addition to boosting systems with power, the Thirdspace faction can channel their psychic energy (represented by EW points) into improving their weapon strength, self-repair systems and Psychic Field.</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>   
   
    <h4 id="torvalus" >TORVALUS SPECULATORS</h4>
    <p>The Torvalus are known for their highly advanced stealth technology as well as their love for making cosmic gambles. Below is a list of systems used by the Torvalus, with short description of effect and any notable design differences from their original tabletop versions.</p>    
    <h5>Laser Weaponry</h5>
    <ul>
    <li>Torvalus weaponry is entirely Laser-based, making it uninterceptable.  Whilst these weapons do not have an particularly unusual abilities per se, their high damage, flexibility and low cooldowns makes them very effective.</li>
        </ul>
    <h5>Hangar Requirements</h5>
        <li>The Torvalus ships don't have hangars and therefore do not need to meet the normal 50% hangar requirement when building their fleet. 
            Instead their Stiletto Drones simply operate independently but are immune to dropout whilst a ship with control capacity (and functioning CnC) is present.        
            In scenarios where a certain percentage of fighters ARE required, use the following numbers as control/hangar capacity:
            <ul class="circle-list">
                <li>Dark Knife - 24 drones</li>
                <li>Black Rapier - 6 drones</li>
                <li>Shrouded Saber - 12 drones (Optional)</li>                  
            </ul>
        </li>    
    <h5>Shading Field</h5>
        <ul>
            <li>The Shading Field operates the same as the Minbari's Jammer system (e.g. prevents weapons locks even when targeted with OEW), including against other Ancients as well as Younger Races.</li>
        
            <li>In addition the Shading Field has two states, Normal Mode and Shading Mode, which are described below:
            <ul class="circle-list">
                <li>Normal Mode- the Shading Field provides a 4-point EM Shield in all directions, which operates the same as Vorlon shields, but cannot be flown under by fighters.</li>
                <li>Shading Mode - During Deployment and Pre-Turn Order phases the Shading Field can be activated to make its vessel 'Shaded' that turn.  
                    Whilst Shaded the vessel retains the Jammer effect, doubles the EM shield rating to on its defence profile and, if it is more than 15 hexes away from all enemy units, it becomes invisible to opponents.
                    However, on a turn when a Torvalus ship is Shaded it will not be able to fire any of its weapons.</li> 
            </ul>
            </li>
        </ul>
        <ul>
        <li>Torvalus Stiletto Fighters have a smaller version of the Shading Field equipped.
            This can also be toggled on and off during Deployment and Pre-Turn Order phases like the ship version, but does not provide the Jammer effect in either mode.  
            In Shading Mode it reduces the profile of the fighter flight (by 15) and cannot be detected from more that 15 hexes away like ships.</li>         
    </ul>
<h5>Shade Modulator</h5>
        <li>The Shade Modulator is a versatile Support Weapons found on the Veiled Scimitar, it has four different firing modes which are described below. 
            <ul class="circle-list">
                <li>Blanket Shield Enhancement - The Modulator increases the shield rating of ever Shading Field within 3 hexes by 1 point at a cost of 4 capacity.</li>
                <li>Individual Shield Enhancement - The Modulator increases the shield rating of a single ally by 1 point at a cost of 2 capacity.</li> 
                <li>Blanket Shade Enhancement - The Modulator lowers the defensive profile of all Shaded allied ships within 15 hexes by 5% at a cost of 2 capacity.</li>
                <li>Individual Shade Enhancement - The Modulator lowers the defensive profile of a single Shaded ally by 5% point at a cost of 1 capacity.</li>                                     
            </ul>
        </li>    
        <ul>
<li>During the Firing Phase the Shade Modulator can be used on different allies in different modes, multiple times up to its maximum capacity.  For example, it could provide 1 point of Blanket Shield Enhancement, 
        as well as 1 point of Individual Shield Enhancement and 2 points of Individual Shade Enhancement on the same turn.</li>
        <li>Blanket firing modes are activated by clicking 'Select' when in that firing mode, whereas Individual modes require the targeting of a specific ally.</li>        
    </ul>
<h5>Transverse Drive</h5>
        <ul>
<li>This weapons fires in a special Pre-Firing phase, which will occur between Movement and Firing phases providing the Transverse Drive is fully charged.  
            During this phase simply select a hex up to 3 hexes on a straight line to the selected ship and then commit your orders.  The ship will attempt a Transverse Jump before the Firing phase.</li>
        
<li>When attempting a Transverse Jump a number of things will then happen depending a d20 roll: 
            <ul class="circle-list">
                <li>1-16 - The jump is successful and the Torvalus ship teleports to the new hex.</li>
                <li>17 - The jump is successful but off-target, ship travels the number of hexes selected but 60 degrees counter-clockwise from intended direction.</li> 
                <li>18 - The jump is successful but off-target, ship travels the number of hexes selected but 60 degrees clockwise from intended direction.</li> 
                <li>19 - The jump is unsuccessful, nothing happens.</li>
                <li>20 - The jump is unsuccessful, the ship does not move the Transverse Drive system has to roll for a critical hit.</li>                                                     
            </ul>
        </li>
        </ul>
        <ul>
<li>When a ship makes a transverse jump it puts a strain on the Jump Drive (a separate system), and so a ship can suffer a catastrophic failure (e.g be destroyed) if the Jump Engine has sustained damage in the battle.
            The chance of this failure is the % of the Jump Drive's health that has been lost.
        </li>
        <li>Additional effects of making a Transverse Jump is that all ballistic weapons will suffer a cumulative -20% chance to hit per hex travelled.  
            However, the light produced by a jump makes Shaded ships easier to detect and these will be revealed to enemy ships within 20 hexes instead of the usual 15 hexes at the start of the Firing Phase.</li>                  
        <li>Transverse Drive also has some interesting critical effects when damaged, including the effect whereby the ships Jump Drive takes d3 damage.</li>                      
    </ul>
<h5>Agile/Jinking Ships</h5>
        <ul>
<li>Torvalus ships are exceptionally maneuverable and this is reflected in even their largest ships having the Agile characteristic.  
            In addition, their Medium Ships have the ability to jink, an ability normally reserved only to Fighters.</li>                                                       
    </ul>
<h5>Gravitic Drives</h5>
        <ul>
<li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>                           
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>  


    <h4 id="triad" >THE TRIAD</h4>
    <p>The Triad represent the only Transcendental beings in Babylon 5 Wars. They are akin to Lorien, able to manipulate matter and energy at the subatomic level, travel through space and 
	dimensions on their own, and can influence locations at unfathomable distances. The ships presented do not carry any Tri members. For all intents and purposes, these are like windup toys. If a Tri
	needs to project its influence, they manipulate energy into forming the ships presented here. When the need is over, the Tri simply reabsorb the energy. Given their technology, for lack of a better
	term, is based on the Tri's will, their capabilities are very impressive</p>

    <h5>Tri Aspects</h5>
        <ul>
<li>For scenarios set in the Primordial era, the Triad player can only take one aspect in their force. This also means the Triumviron and Lesser Triumviron are unavailable to play. 
In the Ancient era the Triumvirons are available and multiple aspects can be flown if at least a Triumvrion or Lessser Triumviron are taken.
            <ul class="circle-list">
                <li>Chaos: This asepct was flamboyant and aggressive.</li>
                <li>Neutrality: This aspect favored ballistics and attempted to intimidate opponents with fearful formations.</li>
                <li>Order: This aspect was fascinated with crystals and was the least likely aspect to fight an opponent.</li>
            </ul>
        </li>
    </ul>
<h5>Gravitic Drives</h5>
        <ul>
<li>Allows ships to undertake maneuvers even while pivoted/pivoting using thrusters appropriate for their current orientation. Gravitic Thrusters also ignore the first "Efficiency Reduced" 
critical they receive, increasing their resistance to damage.</li>
    </ul>

    <h5>Structure Self Repair</h5>
        <ul>
<li>The Tri's ability to manipulate matter and energy meant that they had the ability to restore lost structure, even on the turn it was lost. As long as the primary section is not destroyed, the Tri
may restore structure on itself up to the rating on the icon. The Tri can even restore a lost structure block. If the entire block is restored, any systems attached to it are returned in the state they 
were in when the structure was lost.</li>
    </ul>
<h5>Triad Capital ships</h5>
        <ul>
<li>Triad capital ships were often created with enhanced abilities to support the other creations.
            <ul class="circle-list">
                <li>Command Node: Capital ships gain an extra +1 initiative. This is more basic than the table top ability.</li>
                <li>Jealous ELINT: This could not be fully implemented. The Triad use the Mindrider Constrained ELINT.</li>
                <li>Cooperative Structure Self Repair: Triad capital ships can use their structure self repair on friendly units (both ships and fighters). This is automatically handed. The 
				cooperative structure self repair will attempt to fix itself first. After that, it will prioritize friendly units without structure self repair and then other units with 
				structure self repair.</li>
            </ul>
</li>
    </ul>
<h5>Fighter Control</h5>
        <ul>
<li>Like their ships, the Triad create fighters as necessary. The Tri will instill some limited fighter controls in their ships to offload the burden from themselves. Ships can control a number of fighters as shown. 
The Triad are not required to take fighters.
            <ul class="circle-list">
                <li>The Triumviron: 24 fighters (Note, this is not the Lesser Triumviron)</li>
                <li>Capital Ships: 12 fighters</li>
                <li>Heavy Combat Vessels: 6 fighters</li>
                <li>Medium Ships: 0 fighters</li>
            </ul>
</li>
    </ul>

<h5>Triad Weapons</h5>
        <ul>
<li>Many Triad weapons have differnt abilities. These weapons will be listed below in alphabetical order.</li>
    </ul>
<h5>Asteroid Salvo</h5>
        <ul>
<li>The Tri creates an asteroid and other debris. It is fired like an energy mine and detonates like one too.
            <ul class="circle-list">
                <li>As long as the asteroid salvo does not dissipate, it will detonate as a matter+flash attack scoring 50, 20, and 10 damage at 0, 1, and 2 hexes, respectively.</li>
                <li>As long as the asteroid salvo does not deviate or dissipate, it will create an asteroid in the detonation hex, meteoroids at one hex, and dust at two hexes. Units without advanced 
				armor will take double damage from terrain.</li>
                <li>Asteroid - This operates exactly as a standard asteroid.</li>
                <li>Meteoroid - This could not implement the table top's interception ability. As such, the overall damage is reduced in Fiery Void. Each unit rolls to determine how many meteoroids 
				may hit, based on the unit's size. Each meteoroid hit does 1d6 + (unit's speed / 2) damage. </li>
                <li>Dust - A unit will take damage once from dust as long as it moves through any hex with dust. The side of the unit that is hit is based on the first dust hex the unit moves through. 
				The dust scores damage equal to the unit's speed / 2 (dropping all fractions).</li>
            </ul>
        </li>
    </ul>
<h5>Flare Generator</h5>
        <ul>
<li>This is a powerful electromatic shield that creates a "flare" around the ship.
            <ul class="circle-list">
                <li>Passive mode: Unless used in flare mode, the flare generator always produces a 4-point EM shield.</li>
                <li>Offensive mode: The generator can direct its output into a concentrated blast in raking (20).</li>
                <li>Flare mode: This is fired in the initial orders step as a ballistic weapon. It automatically targets
				the generating ship. When this is done, the shield is extended for the ship. At range 0 there is no shield. 
				It is a 4-point sheild at range 1, 6-points at range 2, and 7-points at range 3+. Furthermore, the flare 
				causes a burst of damage like an energy mine in the generating unit's hex. It will score 60 damage at range 
				0, 20 at range 1, and 10 at range 2.</li>
                <li>Immunity: All Triad Order units are immune to the damage generated by flare mode.</li>
            </ul>
        </li>
    </ul>
<h5>Hyperplasma Cutter</h5>
        <ul>
<li>This is the most versatile plasma weapon available. It is capable of engaging multiple targets as well as firing defensively. It is important to note that 
all Hyperplasma Cutters on a ship are treated as a single weapon. Each cutter has 10d10 available. This means a Triad Fiend has 20d10 whlie the Triumvirons 
have 30d10 available. In order to use the cutter follow the guide below.
            <ul class="circle-list">
                <li>In Normal mode, the player can select one or all cutters. The game engine will assemble all available dice. NOTE: The available 
				dice will also check to make sure they are all in arc.</li>
                <li>Players select a target and a menu will appear. The player may select the number of d10s to be fired at the target.</li>
                <li>Select a different target to fire at a new target. NOTE: If you return to a target you already fired at, it will create 
				a second shot.</li>
                <li>Fighters can be targeted individually. Simply select the flight and assign dice. Target the flight again and you will assign dice to another fighter.</li>
                <li>To fire defensively, select the shield icon and you may assign a number of d10s to defensive fire. Each provides a -1 (-5%) 
				to hit. This is cumulative. NOTE: At the moment, the hyperplasma cutter can only generate one defensive shot. This will be investigated 
				for improvement in a later update.</li>
                <li>Sustained Mode: In this mode ALL available cutters must be set to sustained mode. Every available die will be used on the same target. 
				The hyperplasma cutter is a sustained (3) weapon.</li>				
                <li>Criticals: The hyperplasma cutter only suffers one critical type. It can lose a number of d10s. Each lost d10 is a single 
				self repair point to remove. The engine will automatically modify the available dice for use.</li>
            </ul>
        </li>
    </ul>
<h5>Hyperplasma Matrix</h5>
        <ul>
<li>This is an unusual fighter weapon on the Chaos Imp. Each fighter is part of of a single weapon.</li>
           <ul class="circle-list">
                <li>To fire select any hyperplasma matrix in a flight. All fighters will automatically fire.</li>
                <li>The damage increases for each fighter surviving to fire. One fighter does 2d6+12 damage. Each additional fighter adds 
				2d6 damage to the total.</li>
                <li>Select a different target to fire at a new target. NOTE: If you return to a target you already fired at, it will create 
				a second shot.</li>
                <li>The hyperplasma matrix scores damage in flash mode. However, the flight is immune to its own shot. It is NOT immune to 
				hyperplasma matrixes from other flights.</li>
            </ul>
    </ul>
<h5>Matter Bolt</h5>
        <ul>
<li>This is another fighter weapon found on the Neutrality Phantom. It works by firing a piece of the fighter at a target.
            <ul class="circle-list">
                <li>Whenever fired offensively or defensively, the matter bolt scores one damage against the Phantom.</li>
                <li>If left unfired for a turn, the Phantom will restore two damage to itself. This is in addition to any 
				structure self repair that a Triad capital ship may apply in support.</li>
            </ul>
        </li>
    </ul>
<h5>Neutron Burst</h5>
        <ul>
<li>The Neutrality and Order aspects used this this to disable an opponent. Functionally, it is like a Shock Cannon, but with greater impacts.
            <ul class="circle-list">
                <li>Power using systems are deactivated if they take at least a point of damage. Unlike a shock cannon, the power from the 
				deactivated weapon is lost for the turn. For example, a twin array is struck and takes 2 damage. This is enough to trigger it 
				to be deactivated. In addition, the 2 power it normally uses is not available for the ship to use.</li>
                <li>Vorlon power using systems are deactivated and drain the minimum power to use the weapon from the capacitor.</li>
                <li>Damage scored on structure will reduce a unit's power by 2 for the next turn.</li>
                <li>Striking a Vorlon capacity will drain two power.</li>
                <li>Non-power systems, if damaged, roll for a critical with a +4 penalty.</li>
                <li>Any fighter damaged will automatically drop out. Fighters immune to drop outs, including Torvalus Stilettos, super-heavy 
				fighters, etc., must make a drop out roll as if they were not immune.</li>
                <li>The weapon is particularly painful to Shadow vessels. Even if the full power of the neutron burst is absorbed by a tendril 
				the system hit suffers the critical effects listed above.</li>
            </ul>
        </li>
    </ul>
<h5>Photonic Prism Beam</h5>
        <ul>
<li>This is a flexible array of lasers focused by Triad Order's crystal hulls. It has the unusual feature of scoring larger rakes of damage 
at short range versus long range.
            <ul class="circle-list">
                <li>Individual split beams: Each prism can fire three beams at -2 per hex but raking 20.</li>
                <li>Combined prism: All three beams from one prism are combined for a -1 per hex penalty and raking 20.</li>
                <li>Two prisms: All three beams from two prisms are combined for a -1 per 2 hex penalty and raking 15.</li>
                <li>Three prisms: All three beams from three prisms are combined for a -1 per 3 hex penalty and raking 15.</li>
                <li>Four prisms: All three beams from four prisms are combined for a -1 per 5 hex penalty and raking 10.</li>
            </ul>
        </li>
    </ul>
<h5>Light Prism Beam</h5>
        <ul>
<li>A fighter weapon found on the Order Cherub that operates as a much smaller photonic prism beam.
            <ul class="circle-list">
                <li>Individual split beams: Fires three individual shots at -2 per hex.</li>
                <li>Combined prism: All three beams combine for a -1 per hex shot.</li>
            </ul>
        </li>
    </ul>
<h5>Singularity Mine</h5>
        <ul>
<li>Nothing demonstrates the true power of the Triad more than their ability to manifest a black hole singularity on the battlefield.
            <ul class="circle-list">
                <li>The singularity mine is fired exactly like an energy mine, but adds a spin of clockwise or counter-clockwise. 
				As long as it does not dissipate, it will detonate and create a singularity in the targeted hex, which will create 
				gravitic-class shear damage to all enemy units within 10 hexes. The Triad's influence protects friendly units.</li>
                <li>The sudden gravitational shift affects all enemy units within 10 hexes. If there are multiple singularities, 
				each one will affect all enemy targets in range. Each singularity scores damage equal to the unit's ramming factor 
				divived by (5 times the range to the singularity). Units at range 0 are treated as being at range 1. </li>
                <li>Any unit in the detonation hex immidiately rolls for the effects of collision with the singularity (below).</li>
                <li>On the turn after firing, the singularity blocks line of sight.</li> 
                <li>On the turn after firing, the singularity will now affect EVERY unit with 50 hexes. They will be moved 
				one hex in the direction of the spin of the singularity and one hex closer to the singularity. If a unit is within 
				10 hexes, it will be pulled 2 hexes closer. (This is a change from table top as the variable maneuvering costs could 
				not be implemented at this time.)</li>
                <li>On the second turn after firing, the singularity's range is reduced to 25 hexes and the region of being pulled 
				in two hexes is reduced to 5 hexes. Afterwards, the singularity dissipates</li>
                <li>Any unit that moves into or is pulled into a singularity suffers one of the following effects, which are modified 
				by the unit's size. Additionally, units without advanced armor take DOUBLE the damage.
					<ul class="circle-list">
						<li>The crew manage to skim the edge of the singularity and suffer damage equal to their speed times a modifier. 
						0.5: Fighters/Shuttles, 2: MCVs, 4: HCVs, 6: Capital, 10: Enormous.</li>
						<li>The unit is flung wildly in a random direction 1d6+1 hexes and pivoted in a random facing. The unit takes 
						damage as skimming the edge, but also takes an equal amount of damage to the primary section.</li>
						<li>The singularity draws in the unit and expels it to hyperspace, removing it from the battle.</li> 
						<li>The singularity destroys the unit with no possibility of survival.</li> 
					</ul>
				</li>
                <li>Lastly, two singularities within range of each other with opposite spins will immediate dissipate both.</li>
            </ul>
        </li>
    </ul>
<h5>Spatial Cutter</h5>
        <ul>
<li>The Chaos aspect liked to flaunt its power, and the spatial cutter did just that by firing a hyperspace waveform at a target!
            <ul class="circle-list">
                <li>To fire the shooter MUST have a lock-on and be within 12 hexes of the target at firing.</li>
                <li>If the spatial cutter strikes, in addition to damaging the target, it creates a hyperspace waveform from the 
				shooter's hex to the target's hex.</li>
                <li>The waveform will persist for one turn. Units will take damage for every waveform hex traversed.</li>
                <li>The damage is equal to the unit's speed times a modifier. This occurs for EVERY hex the unit moves through. 
				Units without advanced armor take DOUBLE damage.</li>
                <li>The modifiers are as follows. 0.75: Fighters, 1: SHFs/Shuttles, 1.5: LCVs, 2: MCVs, 4: HCVs, 6: Capital, 10: Enormous</li>
                <li>Used defensively, the spatial cutter provides a -8 (-40%) chance to hit against ANY weapon.</li>
            </ul>
        </li>
    </ul>
<h5>Triad Missile Rack</h5>
        <ul>
<li>This was a favorite of the Neutrality aspect. The rack holds 5 missiles and the can be of any type the Tri desires.
            <ul class="circle-list">
                <li>The rack provides two times the missile's launch range.</li>
                <li>If the Tri chooses not to expend a missile in the magazine, the unit may expend 6 power to create a basic 
				missile.</li>
                <li>This missile must be used immidiately. It cannot be used to reload a rack, nor does it use any ammunition.</li>
                <li>Any missiles fired are treated as being fired by a First One.</li>
            </ul>
        </li>
    </ul>	


<a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="vorlons" >VORLON EMPIRE</h4>
    <p>Below is a list of systems used by the Vorlons, with short description of effect and any notable design differences from their original tabletop versions.</p>    
    <h5>Capital Ship Layout</h5>
        <ul>
<li>In B5 Wars, Vorlon capital ships, Lightning Cannons are usually tied to both the forward and relevant side sections, to prevent them from easily falling off. 
            In FV, they are fitted to virtual "Side-front" sections that are not directly hittable and do not have an actual structure system.</li>
        <li>‘Side-aft’ sections are, for all intents and purposes, the actual sides of the ship, and behave as such. Lightning Cannons are present on both Front and Side hit charts, as in tabletop - but Front has separate entries for Port and Starboard guns.</li>
        <li>Weapons on pseudo-sections can still be engaged by called shots from their own arc.</li>
    </ul>
<h5>EM Shield</h5>
        <ul>
<li>Works exactly as Gravitic Shield, but doesn't require a separate generator E.g. shields reduce both the hit chance and damage of incoming fire on their respective arcs by the value of their rating.  
            At range 0 fighters are assumed to be flying underneath the shields and effectively ignore them.</li>
    </ul>
<h5>Adaptive Armor</h5>
        <ul>
<li>Additional protection that effectively increases all armor ratings on a unit against specific types of damage.</li>
        <li>Pre-Assigned AA points can be set at the start of battle on Turn 1 only (if ship design allows - White Star is not advanced enough) or in the Initial Orders phase of later turns after receiving damage from a weapon type.</li>
        <li>Protection is limited both in the total amount of AA points available, and in the maximum value that can be assigned to a particular kind of damage.</li>
        <li>The AA system propagation button allows you to share AA settings amongst your entire fleet. Assigned points cannot be unassigned during battle.</li>
        <li>For fighters, every fighter can use different AA settings and AA points are unlocked for the entire flight (unless Super Heavy Fighter flight).</li>  
    </ul>
<h5>Power Capacitor</h5>
        <li>Produces power for Vorlon ships and can hold Power between turns up to its maximum capacity.  Some key points to note about it are:
                <ul class="circle-list">
                    <li>New power is produced in the Initial Orders phase, and is shown and usable from the <b>start</b> of that phase: the Reactor display already includes this turn's recharge (up to the Capacitor's maximum), so power drawn at the end of the previous turn - by weapon fire, or by a Jump Drive - is topped back up before you allocate anything.  The power produced can be doubled (along with Self Repair at end of turn) at the cost of deactivating all weapons and shields on that turn.</li>
                    <li>Separately, you can also open petals on some Vorlon ships but this will reduce the armor on all of the ship’s primary systems by 2 and increase Defence Profiles by 5% for that turn, but adds 50% to power generation.</li> 
                    <li>Capacitor destruction would leave the ship powerless, but doesn't cause a catastrophic explosion like Reactor destruction.  In FV it will leave ship powerless (as the Capacitor is the main power source on Vorlon ships), 
                        add Power reduction critical to Reactor (so ship goes out of control) and Self Repair system so that the damage isn't just repaired in a few turns.</li> 
                    <li>Vorlon Fighters also have petals that they can toggle open in Initial Orders. Doing so provides 2 extra thrust that turn, but increases their Defence Profiles by 5% and reduces side armour by 2.</li>                                         
                </ul>            
            </li>             
    <h5>Mag-Gravitic Reactor </h5>
        <ul>
<li>For technical reasons, Vorlons in FV are using Mag-Gravitic Reactor. This means destroying power-using systems will free appropriate power. 
            Note that the <b>Jump Drive is the only Vorlon system with a power requirement at all</b> - everything else on the hull is nominally 0.</li>
            <li>Scanner uses no power: its operating costs are base hull features and do not affect power usage. 
                The <b>Jump Drive is the exception</b> - it is the one Vorlon system with a real power requirement, though it only draws it on a turn it is actually used. See <b>Jump Drive</b> below.</li>
            <li>All Vorlon weapons have nominally 0 Power usage - they still technically need Power to actually shoot, but that is only decided and taken from Reactor at the moment of firing.</li>                                            
    </ul>
<h5>Jump Drive</h5>
        <ul>
<li>Vorlons open <b>ordinary jump points</b> rather than jumping out directly the way the other Ancients do (see <b>Ancient Jump Drives</b> in the general rules),
            and they can throw one a very long way: a Vorlon jump point may be projected up to <b>12 hexes</b> from the ship, against the standard 4.
            Because it is an ordinary jump point, it <b>can be shot out by a Vortex Disruptor</b> - Vorlons and The System are the only Ancient-era fleets that remain vulnerable to one.</li>
<li>Unlike every other Vorlon system, the Jump Drive <b>does use power</b>, drawn from the Power Capacitor:
            <b>8</b> on the Planet Killer, <b>6</b> on the Strike Cruiser, Heavy Cruiser and Heavy Carrier, and <b>5</b> on the Battle Destroyer, Destroyer Escort, Dreadnought, Heavy Destroyer,
            Light Carrier, Light Cruiser and Scout. The Transport carries no drive at all.</li>
<li><b>It is a per-use cost, not a standing one.</b> The drive draws that power <b>only on a turn it is actually used</b> - the turn it <b>opens</b> a jump point, and every turn it
            <b>maintains</b> one. On any other turn it costs the ship nothing at all, so a Vorlon carrying an idle jump drive has its full power available.</li>
<li>When it is used, the cost is reserved out of your power allocation in Initial Orders and drawn from stored power at the <b>end of that turn</b>. Opening and holding cost the same,
            so a jump point opened on one turn and held for three more costs the drive's power requirement four times. The Capacitor's recharge tops the store back up at the start of
            the next Initial Orders, so a ship whose recharge covers the cost starts every turn full.</li>
<li>Once you set <b>Maintain Vortex</b>, it <b>carries over automatically</b> to each following turn until you turn it OFF - you do not have to re-declare it every turn.
            If the Capacitor cannot cover it, the Initial Orders power check will say so before you commit.</li>
<li>In exchange, a Vorlon is <b>exempt from the all-systems-dark rule</b>: where any other race must shut down every powered system for the whole turn to hold a jump point open,
            a Vorlon simply pays the upkeep and fights on normally.</li>
<li>And a paid jump point has <b>no four-turn limit</b>. It stands for <b>as long as the upkeep is paid</b>, so the system icon counts the turns open without a denominator
            ("5" rather than "5/4"), and the Maintain toggle stays available indefinitely.</li>
<li>If the Capacitor <b>cannot cover the upkeep</b> at the end of the turn - drained by weapon fire, emptied or halved by a critical, or shot out altogether - the jump point
            <b>closes</b>, and the combat log says so. Everything else still applies as normal: the jump point also closes if the holder is destroyed, drifts more than 12 hexes away,
            or simply stops declaring Maintain.</li>
<li><b>Arriving as reinforcements</b> works the same way. Opening a jump point <b>exit</b> from hyperspace uses the drive, so it costs the power requirement on the turn the
            exit forms (reserved in that turn's Initial Orders, as for any other use); if the Capacitor cannot pay, the exit collapses at the end of that turn before anything
            comes through it, and the wave waits in hyperspace with nothing spent. Once the
            ship is on the board it may <b>maintain</b> its exit like any jump point - paying the upkeep, with no four-turn limit - and bring a fresh wave through on every turn it does.</li>
    </ul>
<h5>Gravitic Drives</h5>
        <ul>
<li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>                    
    </ul>
<h5>Lightning Cannon</h5>
        <ul>
<li>These have the accelerator property just so that the weapon requires explicit permission to intercept (and draw power). 
            It does not affect the weapon in any other way e.g. leaving it to recharge for two turns will not increase power of shot.</li>
        <li>Can combine Lightning Cannons for higher power shots. To do so, the appropriate number of cannons must have the same firing mode and target assigned.
            If player mis-declares and not enough weapons are assigned for declared mode, each of those shots <b>fires as 1 Prong instead</b>, at the same target, drawing 1 power; the combat log notes it.
            The commit window warns you about any such shot before you commit your fire orders.</li>
    </ul>
<h5>Lightning Gun</h5>
        <ul>
<li>Each Lightning Gun is a <b>single system that fires up to twice per turn</b>. (Strike Cruisers in games started before this change still show the older version: a Lightning Gun plus a separate "Mirror Lightning Gun".)</li>
        <li>Every click on a target declares <b>one shot</b> in the gun's current firing mode. The firing mode can be changed between clicks, so the two shots may go at different targets, in different modes.
            Each declared shot, and its mode, is listed in the target's incoming fire list.</li>
        <li>A single gun fires in <b>1 Prong</b> (Light) mode. Heavier modes <b>combine one shot from each of several different Lightning Guns</b> on the same ship:
            <ul class="circle-list">
                <li><b>2 Prongs</b> (Medium, Raking) - 2 guns.</li>
                <li><b>3 Prongs</b> (Heavy, Raking 15) or <b>P3Piercing</b> (Heavy, Piercing) - 3 guns.</li>
                <li><b>4 Prongs</b> (Mega, Raking 20) or <b>Q4Piercing</b> (Mega, Piercing) - 4 guns.</li>
            </ul>
            A gun cannot combine its own two shots together.</li>
        <li><b>How to fire a combined shot:</b> select the Lightning Guns you want to combine, set them all to the same firing mode, and click the target. Each selected gun declares one shot and they are fused into a single shot when fire is resolved.
            Click the target again to fire a second combined shot with the same guns. All shots being combined must have the same target, the same firing mode, and the same called system (if any).</li>
        <li>If a combined shot is declared without enough Lightning Guns, each of its shots <b>fires as 1 Prong instead</b>, at the same target, drawing 1 power; the combat log notes it.
            The commit window warns you about any such shot before you commit your fire orders. A combined shot that does fire appears in the log as a single shot.</li>
        <li>Power is drawn from the Power Capacitor for <b>each shot separately</b>: 1 for 1 Prong, 3 for 2 Prongs, 9 for 3 Prongs, and 18 for 4 Prongs. Each gun shows its own share of the power for the shots it takes part in.</li>
        <li>All modes are uninterceptable. As with the Lightning Cannon, the accelerator property only means the gun must be explicitly ordered to intercept; each unused shot may then intercept, drawing 1 power per interception.</li>
    </ul>
<h5>Discharge Gun & Discharge Cannon</h5>
        <ul>
<li>LCan split shots amongst multiple targets up to four shots.  Can also split amongst offensive and defensive fire, with any manually selected intercept shots using minimal power if required 
            e.g. they actually try to intercept an incoming shot.</li>
        <li>Increased power levels are implemented as firing modes, so select this before targeting any enemy ships. Each offensive shot will be fired at the same power level.</li>                                                            
    </ul>
<h5>Planet-Cracker Beam</h5>
        <ul>
<li>The signature weapon of the Vorlon Planet Killer, an Ancient superweapon meant for very specific scenarios rather than normal balanced play. Rather than being aimed at a target, it is activated during the Firing Phase and, once fired, automatically sweeps the four hexes directly ahead of the ship along its current facing.</li>
        <li>Anything standing in those hexes - friend or foe alike, the beam does not discriminate - is automatically destroyed. This includes every hex of a multi-hex Terrain/Huge unit caught by the sweep, and every fighter of a Flight caught in it, not just one.</li>
        <li>It cannot be intercepted, ignores jinking and Line of Sight, and always hits.</li>
        <li>It has an extremely long recharge time, effectively making it usable only once per battle.</li>
    </ul>
<h5>Vorlon Primordial Fighters</h5>
        <ul>
<li>Super Heavy Fighters that use regular hangars, with one Primordial fighter counting as two heavy fighters for fleet design/hangar purposes.
            They are treated as rare variant of Heavy Fighters in FV except in Primordial times, where Assault Fighters should be treated as a common availability.</li>
    </ul>
<h5>Skin Enhancements</h5>
        <li>Vorlon units can pick only one Skin Color enhancement (this will not be enforced by FV though, it's up to the player to comply with this rule). Summaries of these options are given below:
            <ul class="circle-list">
                <li>Amethyst Skin Coloring: Improves Adaptive Armor for players desiring more flexibility,</li>
                <li>Azure Skin Coloring: EM Shield improvements, for greater overall protection,</li> 
                <li>Crimson Skin Coloring: Power Capacitor improvement, for greater…power.</li>                  
            </ul>  
        </li>            
    <a class="back-to-top" href="#top">↩ Back to Top</a>        
 

    <h4 id="walkers" >WALKERS OF SIGMA-957</h4>
    <p>The Walkers are the enigmatic inhabitants of Sigma-957, an Ancient race that surveys and maps the galaxy rather than conquering it.
        Below is a list of the systems used by the Walkers, with a short description of effect and any notable design differences from their original tabletop versions.</p>

    <h5>Electromagnetic Weaponry</h5>
        <ul>
            <li>Every Walker weapon is of the Electromagnetic weapon class, and counts as Ancient (First One) technology for any rule that asks.</li>
            <li>Some of them are <b>accelerators</b>: they gain more damage when fully charged, and they do <b>not</b> begin the battle fully charged.
                A freshly deployed Medium Lightning Array or Chromatic Pulse Driver opens turn 1 with one turn of charge (1/2) rather than two.
                The one exception is <i>The Wanderer</i>, whose weapons do start the battle fully charged.</li>
        </ul>

    <h5>Lightning Array / Medium Lightning Array</h5>
        <ul>
            <li>The Array fires <b>discharges</b>. Each click on a target declares one discharge; a further click on the <b>same</b> target fuses another discharge into the shot
                already standing there rather than declaring a second one. Four clicks on one ship therefore produce one four-discharge bolt, while four clicks on four ships
                produce four single ones.</li>
            <li>Fusing several shots changes weapon's stats: the <b>damage</b>, the <b>fire control</b> and the <b>range penalty</b>. A fused shot is
                not simply a bigger one - it is markedly better against capital ships and markedly worse against fighters.</li>
            <li>Two firing modes, and both are usable in the same turn:
            <ul class="circle-list">
                <li><b>Combined Fire</b> (default) - repeat clicks on one target fuse into a single shot.</li>
                <li><b>Single Shots</b> - every click is a separate one-discharge order, which is what you want against a fighter flight where four small shots usually are better than one larger shot.</li>
            </ul>
            </li>
            <li>'Remove a firing order' <b>peels one discharge</b> off a combined shot rather than deleting the whole thing, and the shot's hit chance is re-priced at its new
                size. A single-discharge order simply goes.</li>
            <li>The <b>Medium</b> Array is also an accelerator: its pool is one discharge per turn charged, up to two. That is also why it cannot combine its shots until it has
                charged for at least two turns - at one turn of charge there is only one discharge, and nothing to combine.</li>
            <li>Both weapons carry an intercept rating and can be used defensively, and both spend from available <b>discharges</b> when they do.</li>
        </ul>

    <h5>Wide-Beam Lightning Array Enhancement</h5>
        <ul>
            <li>A per-mount refit available to Ancient hulls only, bought in the lobby as a System Enhancement by clicking on Lightning Array system: <b>300 points</b> for a full Lightning Array, <b>200 points</b> for a Medium one, one level
                each. Two Arrays on the same hull are refitted separately.</li>
            <li>The purchase buys the <i>capability</i>; the wide beam itself is a <b>per-turn toggle</b> in the Array's activation box, declared in the Firing phase, and it
                applies in whichever firing mode is used.</li>
            <li>A wide beam trades power for spread: <b>-2 on every damage die, to a minimum of 1 per die</b>. Flat damage bonuses are untouched.</li>
            <li>It doubles the weapon's collateral damage to <b>50%</b> of the damage scored - and it is the one flash weapon that still scores collateral <b>inside</b> an
                Energy Draining Field, where it falls back to the ordinary 25%.</li>
            <li>Firing a wide beam costs the Array its <b>next</b> turn: the weapon does not recharge on the turn advance following a wide-beam shot.</li>
        </ul>

    <h5>Chromatic Pulse Driver</h5>
        <ul>
            <li>An accelerator pulse weapon with two firing modes:
            <ul class="circle-list">
                <li><b>Pulse</b> - charged one turn: D3 pulses, up to 4, at 14 damage. Charged two turns: D5 pulses, up to 8, at 18 damage.</li>
                <li><b>Scanning</b> - a single shot that does no damage at all, but which analyses the target's shielding when it hits.</li>
            </ul>
            </li>
            <li>A scanning hit on any unit carrying a shield-type system banks <b>one point of shield adaptation against that race</b>, and the adaptation is <b>fleet-wide</b>:
                from the next turn on, every Walker in the fleet treats that race's shields as one point weaker, in both hit chance and damage reduction. Further scans stack.</li>
            <li>'That race' is the target's faction exactly as written, so (for example) the Brakiri and Abbai adapt separately even though both use Gravitic Shields.</li>
            <li>Overlapping shields are not cumulative, and neither is the adaptation: it is applied once, against the strongest shield source protecting the target - including
                the shield types that hold a capacity pool rather than a flat rating.</li>
            <li>Adaptation is public knowledge, so a defender can see why how many times their shields have been scanned.</li>
        </ul>

    <h5>Energy Draining Field</h5>
        <ul>
            <li>A permanent, omnidirectional field centred on the ship, covering a radius in hexes (e.g 5 on a Traveler field). It can be deactivated like any other system, and a
                <b>Variable</b> field can be run at <b>double power</b> in the Ship Power segment of Initial Orders for a larger radius.</li>
            <li>The field is <b>public</b> - it is drawn on the map for everybody, because an energy drain is a visible phenomenon. Overlapping field hexes are counted once, and
                <b>the rest of the fleet that deployed the field is immune to it</b>.</li>
            <li><b>Shooting through a field is more dififcult.</b> Every field hex a shot crosses (your own fleet's fields excepted) is a -1 penalty on the d20 to-hit table. For
                <b>Plasma</b> and <b>Antimatter</b> weapons belonging to a younger race the penalty is <b>doubled</b>, and Plasma additionally treats the crossed hexes as extra
                range for its damage falloff.</li>
            <li><b>Flash and proximity weapons lose their collateral damage and their blast radius inside a field</b>, friend and foe alike. The only exception is a Wide-Beam
                Lightning Array.</li>
            <li><b>The drain.</b> Any unit that ends its turn in somebody else's field has the following drained <b>for the next turn</b>, resolved at the Critical Hit step:
            <ul class="circle-list">
                <li><b>1d10 thrust</b> - Engine output, or free thrust on a fighter flight,</li>
                <li><b>1d10 power</b> - Reactor output; a reactor drained to zero blacks out every powered system on the ship,</li>
                <li><b>1d10 initiative</b> - x5 in FV units, and the total penalty cannot exceed -100,</li>
                <li><b>1d6 total EW.</b></li>
            </ul>
            </li>
            <li>Every further <b>consecutive</b> turn ended in a field adds one more die to the total already in force, so the drain only ever grows while a unit stays put.
                Nothing but initiative can be driven below zero. <b>Enormous units are limited to the first die</b> and never escalate.</li>
            <li>A <b>fighter flight</b> in a field must additionally roll for drop-out on <b>2d10</b> instead of the usual 1d10, plus a further 1d10 per successive turn, and a
                grounded flight cannot shoot on the following turn.</li>
            <li>Additional fields do <b>not</b> stack: standing in three overlapping fields drains exactly as standing in one.</li>
            <li>Criticals shrink the field - a fixed field loses a hex of radius per critical result (floor 1), while a variable one loses its double-power bonus first and then a
                hex at a time (floor 0).</li>
        </ul>

    <h5>Extended Draining Field Enhancement</h5>
        <ul>
            <li>Buys <b>+1 hex of Energy Draining Field radius per level</b>, up to 3 levels, applying to every field on the hull. The cost is the rules' own formula - 50 points
                per hex added, i.e. 300 x (radius + 1) for the first level - summed over each field the ship mounts.</li>
            <li>On a <b>Variable</b> field the refit raises the normal-power radius only: the double-power radius does not move, so every level bought is one level of boost made
                redundant. Once the two meet, boosting the field buys nothing.</li>
        </ul>

    <h5>Energy Draining Mine</h5>
        <ul>
            <li>A ballistic launcher that fires <b>sensor probes at a hex</b> rather than at a unit.</li>
            <li>The launcher stores <b>3</b> probes and <b>begins the battle with 1</b>, gaining one per turn - a critical result slows that to one every two turns, then every
                three, and so on. Any number of stored probes may be launched, at any number of hexes, in one turn.</li>
            <li>Scatter, as the rules' table: <b>75%</b> the probe lands on the chosen hex, <b>15%</b> it scatters up to 5 hexes, <b>10%</b> it fails to arrive.</li>
            <li>A probe that lands deals no damage but instead projects an Energy Draining Field over <b>its hex and the six around it</b> for one turn, with every field rule above - the targeting
                penalty and its own fleet's immunity included. It drains on the turn it lands <b>and</b> on the turn after, so a unit caught by both escalates.</li>
            <li>The orb shows on the map as a purple seven-hex disc. <b>It cannot be targeted</b>: its structure is indestructible and destroying it would achieve nothing anyway.
                A blast that happens to cover its hex still resolves normally against everything else standing there.</li>
        </ul>

    <h5>Energy Draining Net</h5>
        <ul>
            <li><b>Links up with other Nets in the same fleet</b> to turn empty space into an Energy Draining Field.</li>
            <li>Any two active Nets of the same fleet within <b>3 hexes</b> of one another link, and the hexes <b>between</b> them join the Energy Draining Field - two hexes at
                range 3, one at range 2, none at range 1 (adjacent Nets still count as linked).</li>
            <li>Where linked Nets form a <b>closed ring</b>, the area enclosed is filled with field as well. A chain of Nets is not a ring and fills nothing; a ring with a
                trailing Net keeps its ring. The fill is capped by the number of Nets involved, and an over-large enclosure fills nothing at all.</li>
            <li>Where several corridors of equal length are possible the game picks one for you, preferring the corridor that catches the most enemy units.</li>
        </ul>

    <h5>Electronic Warfare Detector</h5>
        <ul>
            <li>Reads the configuration of enemy EW suites and reports it to the whole fleet, giving every friendly unit within <b>20 hexes</b> the Expert Scanner benefit: a
                point of EW may be held back and spent <b>late</b>, once movement has been resolved.</li>
            <li>In FV that late window is usually at the start of the <b>Firing</b> phase, or the <b>Pre-Firing</b> phase if one occurs, and it uses the same EW buttons the
                Initial Orders menu does.</li>
            <li>Multiple Detectors are cumulative, but their efficiency degrades: detectors <b>1-4</b> allow 1 saved point each, <b>5-8</b> allow half a point each, and
                <b>9 or more</b> allow a quarter each. Fractions of a quarter and a half round down; three quarters rounds up.</li>
            <li>The saved point comes out of <b>unspent DEW only</b>. A ship that has already committed all of its EW to OEW, CCEW or other EW functions has nothing
                left to save, and the figure in the ship window falls as you allocate during Initial Orders.</li>
            <li>Range is measured at the unit's <b>post-movement</b> hex, so a ship that drifts out of range of every Detector finds its saved EW allowance is zero (and just treated as DEW as per usual). ELINT ships may save
                points too, on the same terms.</li>
        </ul>

    <h5>Sensor Charge Transceiver</h5>
        <ul>
            <li>Only found on the Scribe, and unlike other weapons it does not shoot at anything. Instead, the player plots a <b>course</b> for a sensor charge, and the
                charge damages every enemy unit in the hexes it passes through (Standard 6d10, no overkill, three turns to recharge).</li>
            <li>Plotting: click hexes to lay down waypoints. A leg must run along one of the six hex axes, and the <b>first</b> leg is the launch, which must leave along an axis
                the mount's own arc covers. After that the charge steers freely.</li>
            <li>The charge has <b>16 hexes of range and 4 manoeuvres</b>. Changing axis at a waypoint costs 1 manoeuvre for 60 degrees, 2 for 120 and 3 for a reversal; the launch
                itself is free. Boosting the transceiver in Initial Orders buys further hexes <b>or</b> manoeuvres out of one shared pool - which axis a level goes to is worked
                out from the course you actually plot.</li>
            <li>While the weapon is selected the map shows the course with direction chevrons, the hexes reachable from the head of the course in arc blue, and a spent-of-total
                read-out for hexes and manoeuvres (plus the shared boost pool, when the weapon is boosted). Green hexes mark the places where something happened - where the
                charge manoeuvred, the head of the course, and any hex where you named a unit to hit.</li>
            <li>Where several units share a hex, the choice of which to hit is yours, through the 'Target Ship' button on that hex's tooltip.</li>
            <li>The course ends when it reaches a friendly transceiver - the charge is home, and that transceiver recharges faster for having caught it. You can always re-select
                the weapon and fly on past a receiver if you would rather.</li>
        </ul>

    <h5>Gravitic Drives and Advanced Armour</h5>
        <ul>
            <li>Walker hulls are gravitic: they can undertake manoeuvres even while pivoted or pivoting, using the thrusters appropriate to their current orientation, and their
                thrusters ignore the first 'Efficiency Reduced' critical they receive.</li>
            <li>Every Walker unit carries Advanced Armour, and every Walker ship mounts Self Repair.</li>
        </ul>

    <h5>Walker Jump Drives</h5>
        <ul>
            <li>Every Walker ship and every Mapmaker probe carries a <b>Walker jump drive</b> - an Ancient jump drive (see Ancient Factions above), so it opens no jump points
                and jumps to hyperspace at the end of the turn it is set to - with two differences: a Walker unit <b>may fire normally</b> on the turn it jumps, and its drive
                has <b>no chance of failure</b>, however badly damaged.</li>
            <li>A Mapmaker flight jumps as one: set Jump to Hyperspace on any probe's drive and the whole flight leaves at the end of the turn.</li>
        </ul>

    <h5>Extra-Dimensional Jump Drive</h5>
        <ul>
            <li>The <b>Wanderer, Traveler, Waymarker and Guideship</b> carry an Extra-Dimensional Jump Drive, which can drag an <b>enemy</b> unit into hyperspace over
                several turns. In Initial Orders, select the jump drive and click the enemy unit. The declaration is visible to everyone from the Movement phase,
                as a purple "Abduction" marker on the target. <b>No line of sight is needed.</b></li>
            <li><b>Terrain can be abducted too</b> - asteroids, moons, fixed jump gates and shipyards, whoever bought them, and including the large multi-hex
                terrain no weapon can shoot at. Jump points cannot be abducted, and neither can fighter flights. A terrain unit carries no EW of its own, so
                one point of OEW on it satisfies the EW condition below - but a unit occupying <b>more than one hex</b> needs <b>every hex it stands in</b>
                inside your connected Energy Draining Field, not just its centre.</li>
            <li>The <b>first turn only takes hold</b> of the target: it costs no extra power and delivers no power-turns. If the conditions below are met, the
                abduction has begun, and <b>from the next turn</b> the drive keeps targeting it automatically and power can be applied in the drive's menu.</li>
            <li>Each power level costs the drive's power requirement again and delivers <b>1 power-turn</b>: normal power is 1, double power is 2, up to 4. The power
                comes off the reactor like a boost, so Walker ships must shut systems down to pay for it.</li>
            <li>The abduction only <b>takes hold</b> if, at the end of that first turn's Firing phase, <b>both</b> hold for the ship carrying the drive:
                <ul class="circle-list">
                    <li>the target <b>ended its movement in an Energy Draining Field connected to that ship's own field</b> - extended through Energy Draining Mines,
                        Nets or other Walker ships, but not through an enemy's field;</li>
                    <li>that ship's <b>OEW on the target is higher than the target's DEW</b>, counting defensive ELINT support (supporting and blanket DEW) but not
                        offensive ELINT support.</li>
                </ul>
            </li>
            <li>Once the abduction has begun, <b>the field and EW conditions are not checked again</b>. Power-turns must be <b>consecutive</b>: the abduction
                continues while at least one Extra-Dimensional Jump Drive that took hold of the target keeps applying power every turn. If none does, it ends and
                everything built up so far is lost.</li>
            <li>The cost is the target's <b>ramming factor / 50</b> power-turns, or <b>/ 10</b> if it has advanced armour, rounded up, with the ramming factors of
                any units <b>attached</b> to it added. What it carries in its hangars or docking bays does <b>not</b> count. The cost is shown in the jump drive's
                menu, is <b>fixed on the turn the abduction takes hold</b>, and does not fall as the target is damaged.</li>
            <li>When the total is reached the target is <b>removed to hyperspace</b>, as if it had jumped out itself, taking anything attached or docked with it.
                Progress is shown on the target's tooltip and ship window ("Being abducted") and in the combat log every turn, including the reason a turn did not count.</li>
            <li><b>Deactivating the jump drive cancels its abduction</b>, and a drive destroyed during an attempt delivers nothing that turn - the combat log says the
                abduction was cancelled.</li>
            <li>The <b>Pathfinder and Scribe</b> cannot begin an abduction, but once a friendly Extra-Dimensional Jump Drive has taken hold of a target they may target
                it too, adding <b>half a power-turn</b> each turn by applying double power to their drive. Mapmaker probes cannot take part.</li>
            <li>After taking part in an abduction, however it ends, a jump drive <b>recharges for its own jump delay</b> before it can begin another.</li>
            <li>A drive cannot abduct and jump to hyperspace on the same turn - setting Jump to Hyperspace withdraws the abduction.</li>
            <li>Unlike the Walkers' ordinary jump, an <b>Extra-Dimensional Jump Drive that is damaged may be destroyed</b>: on every turn it is abducting it rolls
                for detonation at <b>half</b> the percentage of its boxes lost, as other Ancient jump drives do. Its own jump to hyperspace keeps no chance of failure.</li>
            <li>Not implemented: abducting friendly units. Asteroids and moons use the same ramming-factor cost as anything else rather than the rulebook's
                "10 x radius cubed", so a moon is a very long job indeed.</li>
        </ul>

    <h5>Hangar Requirements</h5>
        <ul>
            <li>Walker fighters are <b>Mapmaker Sensor Probes</b>, and they occupy their own 'Mapmaker Probes' capacity category. They may be bought with <b>no hangar
                space at all</b> - a Walker fleet needs no carrier to field them.</li>
            <li>When susing standard Fleet Composition rules, <b>the 50% full-hangar rule still applies to the capacity a Walker hull declares.</b> Walker ships are not exempt from it: a hull carrying Mapmaker Probe
                capacity must have at least half of it filled with probes, exactly like any other hangar in the game. A Traveler with no probes in the fleet fails the
                Fleet Checker.</li>
            <li>Mapmaker Probe capacity per hull, which is both the control/hangar figure and the basis of the half-full requirement:
            <ul class="circle-list">
                <li>Traveler - 36 probes (at least 18 required)</li>
                <li>Waymarker - 18 probes (at least 9 required)</li>
                <li>Pathfinder - 6 probes (at least 3 required)</li>
                <li>Guideship - 6 probes (at least 3 required)</li>
            </ul>
            </li>
            <li><b>Ships count toward a Traveler's capacity too.</b> 24 of the Traveler's 36 boxes are its <b>Docking Bay</b> (see below), so every Waymarker (24 boxes),
                Pathfinder or Guideship (12) and Scribe (4) in the fleet counts its boxes toward the Mapmaker Probe requirement - up to 24 boxes for each Traveler in the
                fleet, because a ship only counts for a bay it could actually sit in. A Pathfinder or Waymarker cannot meet its own probe capacity that way. Example: a
                Traveler and three Scribes (12 boxes) need only 6 more probes to reach the 18 required.</li>
        </ul>

    <h5>The Traveler's Docking Bay</h5>
        <ul>
            <li>The Traveler's aft hangar is a <b>Docking Bay</b>: 24 boxes that take Mapmaker Probes exactly like an ordinary hangar, and also <b>whole ships</b> -
                <b>Scribes</b> (4 boxes each), <b>Pathfinders</b> and <b>Guideships</b> (12 each) and a <b>Waymarker</b> (24). Probes and ships share the one pool,
                so the bay holds 24 probes, or 6 Scribes, or 2 Pathfinders, or one Waymarker, or any mix that adds up to 24.</li>
            <li><b>Only one type of craft may launch or be recovered through it per turn</b> - on a turn the Scribes use it the probes cannot, and the other way round.
                Its launch rate is <b>12 Mapmakers, or 2 Scribes, or 1 Pathfinder, Guideship or Waymarker</b> per turn, launches and recoveries together. Mapmakers auto-fill
                the Traveler's two side hangars before the Docking Bay, which is the only one that can hold a ship.</li>
            <li>To dock, a ship must end its movement in the Traveler's hex, on the Traveler's heading, with at least 1 thrust unspent, while the Traveler is at speed 0 -
                the same conditions as an LCV docking on a rail. A docked ship is off the board. It launches at the Traveler's position, heading and speed, takes the
                usual launch initiative penalty that turn, and cannot launch on the turn it docked.</li>
            <li>Ships may also <b>start the battle aboard</b>: during Deployment, select the ship and click the Traveler to dock it straight into the bay. A Traveler
                arriving as a reinforcement can bring Scribes, Pathfinders, Guideships and a Waymarker in its Docking Bay as well as its probes, and they arrive docked.</li>
            <li>Any unit ordered into or out of <i>any</i> hangar during the Firing phase now says so on itself: <b>"Docking with &lt;ship&gt;"</b> or <b>"Launching
                from &lt;ship&gt;"</b>, on the map tooltip and as a cyan banner on its ship window. (A flight being launched from scratch has no unit to label until
                the order resolves, so it carries no banner.)</li>
            <li>A <b>stowed unit's fleet-list row</b> - a docked ship, a docked fighter flight or a rail-parked LCV alike - scrolls the map to the <b>carrier holding
                it</b> on left-click, which is where it actually is. Right-click, or the &#9432; affordance, opens its own ship window.</li>
            <li>A docked ship's weapons keep recharging while it is aboard, exactly as on the board; an Energy Draining Mine launcher restocks up to its usual 3.</li>
            <li><b>Docked craft keep projecting what they project.</b> An <b>Energy Draining Field</b> or <b>Net</b> on a ship inside the bay stays operational, and a
                docked <b>EW Detector</b> still lets the fleet save EW points within its 20 hexes - both measured from <b>the Traveler's hex</b>, so the bubble travels
                with the carrier. A docked Guideship's radius-4 field is drawn on the Traveler's own icon.</li>
            <li><b>The Traveler's Self Repair also repairs the ships it carries.</b> Damaged Structure, C&amp;C and Self Repair on a docked ship, and any repairable
                critical on any of its systems, appear in the Traveler's own repair list, marked with the owning ship's name. They are ordered by priority alongside
                the Traveler's own entries and nothing is pinned below anything else, so a docked hull may be repaired first if you want it repaired first. A docked
                ship's <i>own</i> Self Repair keeps working as well, and spends its own points before the Traveler spends any - so a docked Scribe can have its
                Thruster mended out of its own pool while the Traveler mends its Structure.</li>
            <li><b>Docked ships share power with the Traveler.</b> Every 4 points of spare reactor power across all the ships aboard gives the Traveler <b>1 extra
                point</b>: the surpluses are added together first and the total is then divided by four and rounded down, so 4 shared gives 1, 7 gives 1 and 8 gives 2.
                Docked <i>probes</i> share nothing. The donor is not charged for it - what it costs is having the spare power in the first place, which is why you can
                now <b>manage a docked ship's power from its own ship window</b> during Initial Orders: open it from its fleet-list row (right-click, or the &#9432;
                affordance) and switch systems off, boost, overcharge and so on exactly as you would on the board. Power a docked Scribe's thrusters down and the
                Traveler's available power goes up on the same click. The Traveler's Reactor tooltip shows how much is being shared and how much surplus is being
                pooled to get it, and the <b>opposing player sees the same figure</b> on the Traveler's reactor once Initial Orders are committed - sharing power is
                public, even though what is in the bay otherwise is not.</li>
            <li>Damage to the bay never forces a ship out - probes are evicted first as boxes are lost. If the Docking Bay or the Traveler is destroyed, every docked
                ship is forced out and takes the bay's damage plus 2d10 to its Structure.</li>
            <li><b>A Waymarker takes two turns to dock, and two to launch.</b> It is 24 boxes - the whole bay - and it does not go straight in. Order the dock as
                usual and, when the turn resolves, the Waymarker <b>clamps to the Traveler's aft</b> instead: it is still on the map, in the Traveler's hex, and it
                stays there for the whole of the following turn before going inside at the end of it. Launching runs the same two steps backwards - it comes out onto
                the aft, rides there for a turn, and is a free ship again the turn after that. Its 24 boxes are held from the moment the order resolves, so nothing
                else can be loaded into the bay while it is on its way in or out.</li>
            <li><b>While it is riding the Traveler's aft</b> a Waymarker:
                <ul>
                    <li><b>moves with the Traveler</b> and cannot steer itself - it has no movement of its own to plot;</li>
                    <li><b>may not fire</b>, and may not intercept. Its weapon menu and firing-mode selector are withdrawn for the turn;</li>
                    <li><b>may still be shot at</b> normally, and <b>hits on the Traveler's own aft section are rolled on the Waymarker instead</b> - on whichever of
                        its Front or Aft sections has the <b>most Structure remaining</b>. The Traveler is hit on the ordinary hit chart first; only a hit that lands
                        aft transfers;</li>
                    <li><b>uses no EW</b> - it makes no allocations of any kind - and <b>no ship may target it with EW</b>, friendly or hostile. It keeps its own
                        defensive EW, so it is no easier to hit than usual.</li>
                </ul>
                The map tooltip and its ship window both show <b>"Docking with &lt;ship&gt;"</b> (or "Launching from") for the whole of that turn.</li>
            <li>If the Docking Bay or the Traveler is destroyed while a Waymarker is riding it, the Waymarker simply <b>lets go</b> - it was clamped to the outside of
                the hull rather than stowed inside it, so it takes none of the fragment damage a docked ship takes.</li>
        </ul>

    <h5>Mapmaker Sensor Probes</h5>
        <ul>
            <li>The Walkers' only small craft, and the only fighter flight in the game with an <b>EW allowance of its own</b>: <b>3 points per flight per turn</b>, split freely
                between OEW and DEW exactly as a ship spends its scanner output, with anything unspent becoming DEW when Initial Orders are committed. It is 3 per <i>flight</i>,
                not 3 per craft. Their own mine-detection allowance is a separate pool, bought as usual with the flight's Offensive Bonus.</li>
            <li>They need <b>no hangar space at all</b> in the Fleet Checker, so a Walker fleet may take them whether or not it brings a carrier - but any Walker hull that
                does declare Mapmaker Probe capacity must still have at least half of it filled. See Hangar Requirements above.</li>
            <li>Every probe also carries a <b>Medium Lightning Array</b>, and it is the only weapon in the game that <b>several craft fire as one gun</b>. A lone array
                cannot fire at all: three or six probes must declare together, at the same target and in the same firing mode, and the group resolves as a single shot.
            <ul class="circle-list">
                <li><b>3-Probes</b> - 4d10+12, fire control +2/+4/+6, -1 to hit per 3 hexes.</li>
                <li><b>6-Probes</b> - 8d10+12, fire control +5/+5/+4, -1 to hit per 4 hexes. Note it is <i>better</i> against fighters and <i>worse</i> against
                    capital ships than the 3-probe group: the two modes are a real choice, not an upgrade.</li>
            </ul>
            </li>
            <li>A flight of six may therefore fire <b>two 3-probe groups</b> (at the same target or at two different ones) <b>or one 6-probe group</b>. Four or five
                declaring in 3-probe mode fire one group and waste the rest, and the client warns before the click lands.</li>
            <li><b>A probe that has taken ANY damage cannot contribute</b> to a group - not merely a destroyed one. A battered flight loses the weapon before it loses
                the craft.</li>
            <li>The array recharges over <b>4 turns</b>, but it does begin the battle fully charged, so a flight may fire a combined shot on turn 1. Unlike the ship-mounted
                accelerators, it is not an accelerator: waiting longer buys nothing.</li>
            <li><b>The flight cannot fire its Medium Lightning Arrays and its Light Chromatic Pulsars in the same turn</b> - the restriction is flight-wide, not per craft,
                so one probe firing its pulsar locks the arrays out for every other probe as well.</li>
            <li>The array locks on with the <b>flight's own EW, exactly as a ship would</b>: it does not use the Offensive Bonus, it uses its own fire control, the
                flight's OEW on the target is added to the roll, and the target's defensive EW (DEW, plus any blanket or supported DEW) counts against it just as it would
                against a ship's shot. With no OEW allocated to the target it has <b>no lock-on</b>, and its range penalty is doubled as usual.</li>
            <li>The <b>Light Chromatic Pulsar</b> keeps the flight's Offensive Bonus and adds the flight's OEW on the target <b>less the target's defensive EW</b>, never
                below +0 - so enemy DEW can cancel the OEW but never eats into the bonus, and the pulsar never suffers the no-lock penalty. Against a target with 5 DEW,
                3 OEW adds nothing; against one with 2 DEW, it adds +1.</li>
            <li>The array deals <b>Flash</b> damage, so it scores no collateral damage at all against a target standing inside <i>any</i> Energy Draining Field - the
                Walkers' own included.</li>
            <li>Every probe carries a <b>Walker jump drive</b> - see Walker Jump Drives above. The flight opens no jump points; it jumps to hyperspace as one, at the end of any
                turn Jump to Hyperspace is set on one probe's drive, and may fire that turn.</li>
        </ul>

    <h5>The Hulls</h5>
        <ul>
            <li><b>Traveler</b> (capital, 5400pts) - the core of the fleet: a full Lightning Array, three Chromatic Pulse Drivers, an Energy Draining Field, a Docking Bay and two hangars.</li>
            <li><b>The Wanderer</b> (capital, 8750pts, Unique) - an Elite Crew hull carrying a Lightning Array, three Chromatic Pulse Drivers, two Energy Draining Mine launchers, an
                Energy Draining Field and an EW Detector. Alone among Walker ships, <b>its weapons begin the battle fully charged</b>.</li>
            <li><b>Waymarker</b> (Heavy Combat Vessel, 2575pts) - the net-layer: an Energy Draining Net, four Energy Draining Mine launchers, two Medium Lightning Arrays, three
                Chromatic Pulse Drivers and an EW Detector.</li>
            <li><b>Pathfinder</b> (Medium Ship, 3150pts, <b>Limited 50%</b>) - an ELINT hull with an Energy Draining Field, a Medium Lightning Array, a Chromatic Pulse Driver and a mine
                launcher.</li>
            <li><b>Guideship</b> (Medium Ship, 2350pts) - an Energy Draining Field, a Medium Lightning Array, two Chromatic Pulse Drivers and a mine launcher.</li>
            <li><b>Scribe</b> (Medium Ship, 750pts) - agile.  Carries the Sensor Charge Transceiver, with an Energy Draining Net and two Chromatic Pulse Drivers.</li>
            <li><b>Mapmaker Sensor Probes</b> (Heavy Fighters) - very fast gravitic probes with advanced armour and advanced sensors, carrying a Light Chromatic
                Pulsar, a Medium Lightning Array that three or six of them fire as one gun, an EW allowance and a Walker jump drive, and needing no hangar space of their own.</li>
        </ul>

    <a class="back-to-top" href="#top">↩ Back to Top</a>

<h3 id="otherfactions" >OTHER FACTIONS</h3>

    <h4 id="civilians" >CIVILIANS</h4>
    <p>Commonly available/generic civilian ships - or, if they're faction-specific civilians, they're noted as such in name. 
        Not a real faction, but rather a convenient place to store all the various non-combatant scenario units.</p>                                                                                     
    <a class="back-to-top" href="#top">↩ Back to Top</a> 

    <h4 id="streib" >STREIB</h4>
    <p>Very strangely balanced faction which relies on disruption more than straight-up damage.  Their strange tactics and incredibly high armour makes them unsuitable for pick-up games.</p>                                                                                     
    <a class="back-to-top" href="#top">↩ Back to Top</a> 

    <h4 id="terrain" >TERRAIN</h4>
    <p>Not a real faction, just a convenient place to store all Terrain units for scenarios where you want to manually place these.</p>                                                                                     
    <a class="back-to-top" href="#top">↩ Back to Top</a> 


<h3 id="customfactions" >CUSTOM FACTIONS</h3>
    <p>These factions have been created by the community using the Fiery Void framework.  Some of them should be considered balanced against each other (e.g. Nexus units will be fairly balanced against other Nexus units), 
        but will usually be unbalanced one way or another against the official B5 Wars factions.</p>         

    <h4 id="bsg" >Battlestar Galactica</h4>
    <p>The Battlestar Galactica ‘Colonials’ (Tier 2) and the ‘12 Colonies of Kobol’ (Tier 1) factions are parallel creations by Fred and Kirill respectively. 
        Still works in progress but fun to try out if you like the setting.</p>
    <p>These factions are characterized by good armor and bulkheads, providing significant durability. Weaponry is a mix of missiles and matter weapons. 
        The primary heavy weapons are nearly identical to the Belt Alliance's blast cannons. The Colonials also use rapid gatling railguns for close-in work.</p>    
    <h5>Flak Battery</h5>
        <ul>
<li>This is very similar to the Grome flak cannon and has similar features including friendly intercept and flash mode damage. The primary difference is a maximum range of 5 hexes.</li>
    </ul>
<h5>Fighters</h5>
        <ul>
<li>The Colonial fighters use a pulse style weapon that has an alternate mode with a bonus to hit fighters in a very narrow forward arc.</li>
    </ul>
<h5>Raptor</h5>
        <ul>
<li>The Raptor super-heavy fighter is a support unit that provides +5 initiative to friendly Colonial fighters.</li>                            
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="custom" >CUSTOM SHIPS</h4>
    <p>A place to store customs ships from the Babylon 5 Wars setting, either because they are unofficial designs created by players or one-off campaign/scenario ships. Not a real faction.</p>                                                                                     
    <a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="escalationwars" >ESCALATION WARS</h4>
    <p>The Escalation Wars setting was developed by Tyrel Lohr. The units available in Fiery Void represent a small segment of the setting that focused on the early rise of the Circasian Empire. Given this focus, the available Escalation Wars units are generally lower tech compared to the standard Babylon 5 forces. 
       The earliest forces are Tier 3 in strength, but the later designs are in the Tier 2 bracket. Details for Escalation Wars factions can be accessed using these links:</p>
        <ul style="font-size: 16px">
            <li><a href="#bloodsword">BLOOD SWORD RAIDERS</a></li>
            <li><a href="#choukaraiders">CHOUKA RAIDERS</a></li>                
            <li><a href="#chouka">CHOUKA THEOCRACY</a></li>
            <li><a href="#circasian">CIRCASIAN EMPIRE</a></li>
            <li><a href="#kastan">KASTAN IMPERIAN MONARCHY</a></li>   
            <li><a href="#sshelath">SSHEL'ATH ALLIANCE</a></li>                                                                      
        </ul>  
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="bloodsword" >BLOOD SWORD RAIDERS</h4>
        <p>The Blood Swords were a pirate group active during the Circasian "Raider Wars" time period. They were a serious threat to the trade routes near the Thaline node of the Outward sector trade routes due to the size and capabilities of their warships. 
            The raiders were eventually defeated by the Circasians, and their defeat marked the unofficial end of the Raider Wars.</p>
        <p>The Blood Swords have two specialty units, the Lady of Dark Souls and Raining Thunder, each of which come with an elite crew.</p>
    <a class="back-to-top" href="#escalationwars">↩ Back to Escalation Wars</a>

    <h4 id="choukaraiders" >CHOUKA RAIDERS</h4>
        <p>This group of raiders operated in and around Chouka controlled territories and were directly financed by the Chouka Theocracy. 
            They were an early threat to the Circasian Empire as it expanded its own sphere of influence. These raiders operate cast-off Chouka military and civilian ships and fighters. 
            Their weapons are sourced from the Chouka, along with limited supplies from other black market sources. 
            Because of their ties to the Chouka government, these raiders are fairly well armed compared to the Circasian and Chouka fleets and are comparable to other low tech raiders in the B5 universe.</p>
    <a class="back-to-top" href="#escalationwars">↩ Back to Escalation Wars</a>

    <h4 id="chouka" >CHOUKA THEOCRACY</h4>
        <p>The Chouka Theocracy was a minor regional power in the Escalation Wars universe, and the Circasian's opponents in their first major interstellar war. 
            The Chouka were more technologically advanced than the Circasians, but lacked the tactical experience and fleet assets to combat the more numerous and maneuverable Circasian fleet.</p>
        <p>The ships included here are the Chouka spacecraft that served during the Chouka War period. They are armed with a mix of plasma and laser weapons, with older Chouka ships relying on plasma weapons while their newer ships rely on lasers in the anti-shipping role. 
            Plasma weapons continued to be used for anti-fighter work into the Chouka War period, however.</p>    
        <h5>Point Plasma Guns</h5>
            <ul>
<li>A plasma-based defensive weapon.</li>
        </ul>
<h5>Gravitic Tracting Rod</h5>
            <ul>
<li>This rare device was only found on the Apostle Holy Cruiser. It was used to tractor on to smaller craft and draw them closer to dock with the ship for boarding.</li>
        </ul>
<h5>Light Energy Mine</h5>
            <ul>
<li>This is a smaller version of the Energy Mine used by the Narn. It has a shorter range (25 hexes) and does less damage (10/5) than the conventional Energy Mine.</li>                
    </ul>
<a class="back-to-top" href="#escalationwars">↩ Back to Escalation Wars</a>

    <h4 id="circasian" >CIRCASIAN EMPIRE</h4>
        <p>The Circasians are one of the two central powers in the Escalation Wars milieu. These Dilgar-like humanoids started as a small minor power surrounded by other, more established empires but through political maneuvering and conquest they were able to eventually carve out a major imperium of their own. 
            The ships included here are low tech units the Circasians used during the Chouka War period. 
            These vessels are principally armed with light particle, plasma, and laser weapons. Anything more powerful is beyond their level of technology at the start of the war. </p>    
        <h5>Rockets</h5>
            <ul>
<li>The Circasians made use of these primitive torpedo launchers on their early ships. These weapons are treated like Torpedoes and benefit from OEW.</li>
        </ul>
<h5>Particle Lance</h5>
            <ul>
<li>An avenue of advanced R&D before the war, this weapon was an attempt to develop a heavier version of the Light Particle Cannon. 
                Like the Gravitic Lance, this weapon is capable of firing as one more powerful weapon or as two weaker Light Particle Cannons.</li>              
    </ul>
<a class="back-to-top" href="#escalationwars">↩ Back to Escalation Wars</a>

    <h4 id="kastan" >KASTAN IMPERIAN MONARCHY</h4>
        <p>The Kastan inhabit a remote location of hyperspace deep within the Rapids of Rodirra, a treacherous section of hyperspace that makes travel to or from the Kastan territories difficult at best (and impossible at worst). 
            Navigating the rapids has forced the Kastan to be superb pilots.</p>
        <p>The Kastan have had limited interactions with their neighbours, although they do engage in trade and have been known to sell out their services as mercenaries. 
            Most of their active political ambitions have focused on the coreward sectors, having fought back against an invasion by the Ingalli in recent history.</p>    
        <h5>Laser Bolt</h5>
            <ul>
<li>The Kastan developed an effective short range anti-fighter laser weapon for their ships.</li>
        </ul>
<h5>Pulse Torpedo</h5>
            <ul>
<li>This weapon fires a number of small munitions at a target, designed to strip targets of weapons and external systems prior to closing to close range.</li>               
    </ul>
<a class="back-to-top" href="#escalationwars">↩ Back to Escalation Wars</a>
    
    <h4 id="sshelath" >SSHEL'ATH ALLIANCE</h4>
        <p>The Sshel'ath Alliance was formed out of the civil war between the two largest Sshel'ath factions. The early Alliance focused on growth of the homeworld, as they were blocked by the more established Chouka Theocracy and Novon Trade Lords. Two conflicts with the Novon showed that action against this faction  was ill-advised. 
            It was not until the later Circasian defeat of the Chouka that the Sshel'ath managed to expand into and capture several Chouka worlds.</p>
        <p>The Sshel'ath themselves are made of intertwined mass of rope-like elastic fibers, which allow for limited rearrangement of their body's form. 
            Only their faces are inelastic and they are highly resistant to radiation.</p>    
        <h5>Physiology</h5>
            <ul>
<li>The Sshel'ath gain a 1 point advantage to their role when attempting to board or when defending against boarding.</li>
        </ul>
        <h5>Gatling Laser</h5>
            <ul>
<li>Evolved from the basic light laser cannon, the gatling laser fires several short, discreet volleys in pulse mode.</li>
        </ul>
<h5>Electromagnetic Torpedo</h5>
            <ul>
<li>The EM Torpedo was developed to counter Novon shields. This ballistic weapon releases a large electromagnetic pulse and can destroy or short out enemy systems.</li>
        </ul>
<h5>Electron Polarizer</h5>
            <ul>
<li>A flash mode electromagnetic weapon, this can destroy or short out numerous systems on a target.</li>                
    </ul>
<a class="back-to-top" href="#escalationwars">↩ Back to Escalation Wars</a>    



    <h4 id="greatcrusade" >GREAT CRUSADE ORIENI</h4>
    <p>This is a fan-based vision of the modern Orieni. This comes from the fan-based Great Crusade supplement by Steven Cross and Renaud Gagne.</p> 
    <p>These are currently considered in Playtest until their units can be tested.</p>
        <h5>Flak Array</h5>
            <ul>
<li>Most new Orieni weapons are improvements to their original armament. The exception is the flak array. This is treated like a dual flak cannon, 
			but with an offensive mode that can attack any units.</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="valheru" >HOUSE VALHERU</h4>
    <p>House Valheru, led by Drem Valheru, had little clout or power. However, Drem had mysterious backers that provided financial backing and shipyard
space. Valheru's concepts used existing Centauri hulls, but adapted them to use recent technologies and capabilities, regardless of the 
developer of said technologies were politically well connected or not.</p> 
    <p>House Valheru designs are considered rare variants if used in a traditional Centauri force. If only House Valheru units are used, these
	are considered common hulls.</p>
    <p>House Valheru was created by Fred Colman</p>
        <h5>Snipper Cannon</h5>
            <ul>
<li>A long-range, piercing matter weapon unique to the Valheru forces.</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="nexus" >NEXUS UNIVERSE</h4>
    <p>The Nexus setting is designed by Jeremy and Geoffrey Stano. 
        The current factions in Fiery Void (and described below) are the Brixadii Clans, Union of Craytan States, Dalithorn Commonwealth, 
        Federation of Makar, Polaren Confederacy, Sal-bez Coalition, and the Velrax Republic.  </p>
    <p>By design, Nexus is a lower tech setting. It is characterized by more basic weapons and lower sensor values. 
        Over time the factions evolve better units and these are available to play as well. 
        The Nexus factions can be sub-divided into four "tech levels" or "tech eras", of which the first three are available in Fiery Void. 
        These tech levels correspond to various conflicts each faction faced and were major drivers in the evolution of the Nexus fleets. 
        The specific years in question are listed in the faction descriptions. 
        In general, the first two tech levels fit into Tier 3. The third tech level brings the Nexus factions into the low end of Tier 2.</p>
    <p>One feature unique to Nexus compared to the standard B5 factions is that Nexus LCVs are more functional. 
        Nexus LCVs can always be deployed independently in a force. LCV tenders do exist, but are primarily geared towards transporting and repairing LCVs. 
        However, some afford bonuses to the LCVs if the tender is in combat with its LCVs.</p>    
    <p>Details for Nexus Universe factions can be accessed using the links below:</p>
        <ul style="font-size: 16px">
            <li><a href="#brixadii">BRIXADII CLANS</a></li>
            <li><a href="#crayton">UNION OF CRAYTON STATES</a></li>                
            <li><a href="#dalithorn">DALITHORN COMMONWEALTH</a></li>
            <li><a href="#makar">FEDERATION OF MAKAR</a></li>
            <li><a href="#polaren">POLAREN CONFEDERACY</a></li>
            <li><a href="#salbez">SAL-BEZ COALITION</a></li>   
            <li><a href="#velrax">VELRAX REPUBLIC</a></li>                                                                      
        </ul>     
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="brixadii" >BRIXADII CLANS</h4>
        <p>The Brixadii Clans are a spinward power within the Nexus sector, having fought with the Dalithorn Commonwealth early on and having hostile relations with the Velrax Republic.</p>
        <p>Brixadii's technology is primarily focused around particle weapons. These are rugged, easy to repair, and low mass. 
            The Clans have begun to specialize in pulsar technology as this affords superior mass to damage ratios. This last point is important as the key feature that defines the Clans are their extremely maneuverable ships. 
            They have few longer-ranged weapons, but prefer high-speed firing passes or getting in close and using maneuverability to get advantageous shots. 
            They do not field fighters and therefore rely on their rapid firing weapons, wide arcs, and light combat vessels to deal with fighters.</p>

        <h5>Chaff Launcher</h5>
            <ul>
<li>Technically fired at an enemy ship, it will always hit (causing no damage or adverse effects) and apply interception to ALL fire from target hex at chaff-protected unit (including nominally uninterceptable weapons).</li>
        </ul>
<h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Jump War (up through the 2050s).</li>
                <li>Tech Level 2: Jump War era through the War of Ascendancy (2050s through the 2110s).</li> 
                <li>Tech Level 3: War of Ascendancy through the First War of Control (2110s through 2150s).</li>                  
            </ul>  
      <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="crayton" >UNION OF CRAYTON STATES</h4>
        <p>The Craytan are an early opponent of the Sal-bez Coalition due in part to a botched first contact by the Sal-bez. The Craytan share a lot in common with the Dalithorn Commonwealth from a technological standpoint. Many of their systems are air-breathing weapons converted for use in space. While basic, but the Craytan have found these to be effective for their needs. 
            Plasma would become a specialization as an improvement over the older, ammunition-based systems.</p>
        <p>The Craytan initially focused on using armed shuttles that are more akin to an assault shuttle. 
            The Craytan's reasoning was that the shuttle will be used in non-combat situations far more than combat situations. 
            As such, most Craytan ships are allowed to swap Polten Armed Shuttles in place of standard shuttles. 
            These will be fielded in traditional flights when deployed in Fiery Void. 
            The initial conflict with the Sal-bez would show the limitations of the Polten, but the Craytan would choose to create a more advanced design in support of a fighter class. 
            The Craytan also utilize the “projectile” weapon class. 
            Functionally they are not different from particle weapons, but are treated differently for various shields and advanced armor.</p>

        <h5>Assault Cannons</h5>
            <ul>
<li>The larger versions are designed to fire a small, dense projectile with enough velocity to generate a piercing effect. 
                It suffers from a slow rate of fire and limited arcs, but it is the longest ranged weapon available to the Craytan. It has remained in service due to its simplicity. 
                The light version fires faster and has wider arcs, but loses the piercing ability due to a slower projectile.</li>

        </ul>
<h5>Close-In Defense System (CIDS)</h5>
            <ul>
<li>This, along with an advanced version (ACIDS), operates much like a Scattergun. Each does a random number of shots (4 or 6) and only one shot can intercept an incoming shot. This represents the CIDS firing a tremendous volume of small projectiles. 
                It relies on volume of fire over specifically aiming at a target.</li>
                
        </ul>
<h5>Enhanced Plasma</h5>
            <ul>
<li>As the Craytan advanced they developed multiple improvements to the standard plasma cannon. These enhanced plasma cannons have superior range, rate of fire, and damage loss compared to standard plasma cannons. 
                They are an intermediary step between plasma cannons and plasma bolters. </li>                 

        </ul>
<h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Craytan War (up through 2105).</li>
                <li>Tech Level 2: Craytan War era through the Daybreak War era (2090s through 2130s).</li> 
                <li>Tech Level 3: Daybreak War era through the First War of Control (2130s through 2150s).</li>                  
            </ul>  
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="dalithorn" >DALITHORN COMMONWEALTH</h4>
        <p>The Dalithorn Commonwealth are likely the lowest tech of the main Nexus powers. 
            They managed to leverage their position across a major trade route to keep their sovereignty up to the later years of the First War of Control in the 2150s.</p>
        <p>They primarily use terrestrial weapons converted for use in space combat. While individually weak, many of the weapons are treated as matter weapons, allowing the Dalithorn to cause damage against most targets. 
            When desired, many of the smaller weapons have a concentrated mode that produces a few, harder hitting shots, at the expense of some fire control. 
            This is often useful against shielded targets. While simple, a number of their weapons are still effective, such as the gas gun. 
            The Commonwealth does not use fighters in the traditional sense. They only field super-heavy fighters, but these are typically slow. 
            However, their size and armament allow them to serve as area of denial platforms against enemy fighters or as serviceable anti-ship units.</p>    
        <h5>Protector</h5>
            <ul>
<li>A specialized, flak-style system to defend their ships. This can intercept for friendly units and is capable of intercepting lasers.</li>

        </ul>
<h5>Laser Missile</h5>
            <ul>
<li>A ballistic weapon that triggers a bomb-pumped laser near the target. It is intercepted as a normal, non-ballistic as the target attempts to shoot down the missile before it triggers.</li>
 
        </ul>
<h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Jump War (up through the 2050s).</li>
                <li>Tech Level 2: Jump War era through the War of Ascendancy era (2050s through the 2110s).</li> 
                <li>Tech Level 3: War of Ascendancy through the First War of Control (2110s through th 2150s).</li>                  
            </ul>              
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="makar" >FEDERATION OF MAKAR</h4>
        <p>The Makar Federation is made up of two species from the homeworld of Makar; a blend of 'home' from both languages. The Federation consists of the land dwelling Hitat and the squid-like Qom Yomin. 
            The Hitat make up the majority of the Federation's combat forces, but the Qom Yomin contribute a variety of support units. Additionally, being aquatic, Qom Yomin ships have larger ramming values if they are rammed. 
            The Makar have been shaped by the Reshmiyar Invasion and their subsequent rebellion that took place early in their space faring history.</p> 

        <h5>Water Caster</h5>
            <ul>
<li>Qom Yomin vessels have the ability to spray some of their onboard water to intercept incoming shots. This functions much like a plasma web, but with no offensive ability.</li>

        </ul>
<h5>Plasma Charge</h5>
            <ul>
<li>Another special Qom Yomin weapon. Qom Yomin ships have low thrust, but extra power. This can be used to charge this weapon or to increase thrust.</li>
        
        </ul>
<h5>Drones</h5>
            <ul>
<li>Except for a couple exceptions, the Makar use armed drones as fighters and hunter-killers. These are almost always on Qom Yomin vessels.</li> 

        </ul>
<h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Jump War (up through the 2050s).</li>
                <li>Tech Level 2: Jump War era through the War of Ascendancy era (2050s through the 2110s).</li> 
                <li>Tech Level 3: War of Ascendancy through the First War of Control (2110s through th 2150s).</li>                  
            </ul> 
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="polaren" >POLAREN CONFEDERACY</h4>
        <p>The Polaren are a difficult faction to quantify. Outwardly, they look like plants, but they are highly mobile and have many animal-like characteristics.
			They can derive energy from photosynthesis, but also chemosynthesis by "eating" the atmosphere. 
			This ability has led to the Polaren not being fully trusted by the other powers after they captured two Sal-bez worlds and heavily terraformed them to be toxic to other factions. 
			Polaren politics are just as hard to quantify with a dizzying array of ever changing alliances. The Polaren were one of the earliest space-faring factions and have fought several civil wars due to their political differences. 
			The Polaren developed numerous "non-violent" weapons to be able to disable opposing ships. The allowed them to be captured, but more importantly, return the ship to service relatively quickly. 
			The Polaren had to adapt when they encountered the Sal-bez in order to help their ships cause more damage to their targets. 
			Due to Polaren physiology, they use light combat vessels extensively, even among the Nexus factions. The Polaren also have an low-level telepathic ability that intensifies in larger numbers.</p> 

<h5>Initiative Bonus</h5>

    <ul>
		<li>For every three Polaren light combat vessels or larger, the Polaren gain +5 initiative up to a total of +50.</li>
		<li>This represents the improved information processing power of the Polaren as more individuals come together.</li>
    </ul>

<h5>Masers</h5>

    <ul>
		<li>Masers are popular among the Polaren due to their ability to disable opponents. While any system hit is forced to roll a critical, the system's armor counts as double for resisting damage.</li>
		<li>The Polaren operate the standard maser, but also introduce a longer-ranged, heavier-hitting heavy maser that was developed to counter the Sal-bez Coalition.</li>
    </ul>

<h5>Radiation Cannons</h5>

    <ul>
		<li>The Polaren are exceptionally hardy to radiation (needed due to conditions on their homeworld) and use rad cannons to quickly disable and board opposing vessels.</li>
    </ul>

<h5>Sand Caster</h5>

    <ul>
		<li>This operates in one of two modes. In defensive mode, it can intercept all incoming fire from an enemy ship. Normally uninterceptable weapons, like lasers, are intercepted at 50% the sand caster's 
		intercept rating. Offensively, the sand caster operates as a matter and flash mode weapon.</li> 
    </ul>

<h5>Light Combat Vessel Controller</h5>
    <ul>
		<li>Concentrations of Polaren are able to function as a single organism for periods of time, even if separated by great distances. The Polaren take advantage of this by aiding their light combat 
		vessels with an LCV Controller that can increase the initiative of all friendly LCVs. This increase can be boosted with extra power, representing the Polaren integrating more information and data 
		processing into the <i>polair</i>.</li> 
    </ul>


<h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Garentithean Revolt and Tyr Discovery (up through the 2050s).</li>
                <li>Tech Level 2: Tyr Discovery era through the Polarn Conflict era (2050s through the 2120s).</li> 
                <li>Tech Level 3: Polaren Conflict era through the First War of Control (2120s through th 2150s).</li>                  
            </ul> 
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

<h4 id="salbez" >SAL-BEZ COALITION</h4>
        <p>The Sal-bez Coalition is an insectoid race that started as a typical queen / worker societ, but during the Enlightenment of Fire, the Sal-bez won their freedom. They are now a fiercely individualistic society. 
            The Coalition did not have a traditional military upon reaching for the stars and their early units are exclusively civilian designs using industrial systems as makeshift weapons.</p>
        <p>As the Sal-bez grew (and discovered several technology caches from a previous power), they began to field purpose-built combat vessels that would ultimately lead to the Coalition becoming one of the most powerful of the Nexus factions. 
            If you are used to using the traditional B5 powers, the Sal-bez are the most similar without a lot of different systems or rules.</p>     

        <h5>Swarm Torpedo</h5>
            <ul>
<li>One of the most iconic systems developed by the Sal-bez for their later generation of ships. It fires multiple, small ballistics at a target, which has led to its common name of swarm torpedo. 
                This system provides both long-range firepower and can strip systems off a target to enable more effective damage from follow-up laser hits.</li>

        </ul>
<h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Craytan War (up through 2105).</li>
                <li>Tech Level 2: Craytan War era through the Polaren Conflict (2090s through the 2120s).</li> 
                <li>Tech Level 3: Polaren Conflict era through the First War of Control (2120s through the 2150s).</li>                  
            </ul> 
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="velrax" >VELRAX REPUBLIC</h4>
        <p>The Velrax Republic is potentially the most aggressive faction within the Nexus setting. 
            They engaged the Brixadii Clans, Dalithorn Commonwealth, and Makar Federation by the early 2100s. 
            The Velrax are the most prolific users of fighters, which are supported with a rigid fleet structure.</p>
        <p>The Velrax field well armed "strike" carriers supported by anti-ship focused "gunships". Everything revolves around supporting their fighter operations. 
            The carriers utilize plasma waves to break up enemy forces at range, and gunships with plasma and lasers to single out anti-fighter units. 
            Also, some Velrax carry plasma arcs used to strip armor off their targets to make them more vulnerable to laser and fighter fire. 
            Their heavy fighter mounts a potent anti-ship gun that was retrofitted to some of the interceptor fighters when they realized that their opponents did not use fighters extensively.</p>     

        <h5>Plasma Arc</h5>
            <ul>
<li>Small, raking plasma weapon that is designed to strip armor from the target.</li>
        
        </ul>
<h5>Dart/Streak Interceptors</h5>
            <ul>
<li>These are small torpedoes that can accept OEW and are used to engage enemy fighters.</li>

        </ul>
<h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through expansion era (up through 2050s).</li>
                <li>Tech Level 2: Expansion era through the War of Ascendancy (2050s through 2110s).</li> 
                <li>Tech Level 3: War of Ascendancy era through the First War of Control (2110s through 2150s).</li>                  
            </ul> 
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>



    <h4 id="startrek" >STAR TREK</h4>
    <p>Star Trek units, as designed by Wolfgang Lackner-Warton and Marcin Sawicki, based on original conversion by Tyrel Lohr.</p>
    <p>Due to vast timeline (and technology) differences between the series, every faction is (or at least might be) split into 3 sub-factions, 
        representing distinct arbitrary "eras": early (eg. Enterprise series), TOS (eg. original series) and TNG (eg. The Next Generation, Voyager, Deep Space Nine...).</p>  
    <p>StarTrek units utilize a common set of concepts and technologies, which make them behave differently from their B5 universe counterparts:</p>
    <h5>Impulse Drives</h5>
        <ul>
<li>StarTrek units utilize a distinct method of generating and using thrust for performing maneuvers. 
            While they do possess an engine (which may get damaged or destroyed as usual, and which allows for overthrusting), its base output is very low (even 0 in some cases). 
            However, each Warp Nacelle also generates thrust - with their output being summed up and displayed as Engine output for the player. Application of said thrust is free (eg. no thrusters are required) 
            - FV ships do possess thrusters (which have unlimited capacity and cannot be damaged in any way) for technical reasons only.</li>
        <li>Besides generating thrust, Warp Nacelles are also jump drive equivalents (so essentially every ST ship - and some small craft as well - can perform FTL travel on their own). 
            Nacelle criticals cannot reduce their output below 0, but Engine criticals can 
            - also, if Engine is destroyed, the entire maneuvering system ceases to function (even if Nacelles are still operational).</li>              
    </ul>
<h5>Star Trek Shileds</h5>    
        <ul>
<li>StarTrek units utilize energy fields (represented by Shield Projection system) that absorb damage from incoming fire. 
            These fields reduce incoming damage by a bit "for free" (call it armor), then some more at the cost of their own health. </li>
        <li>Absorption is applied to every impact separately (with every rake - and in general every non-overkill damage allocation - being treated as a separate impact). 
            If shield projection is brought to 0 points of health, it stops functioning (does not weaken incoming shots any more, in any way). 
            The system doesn't get destroyed however, and can start functioning again if replenished. Only destruction of the associated structure block causes destruction of shield projection.</li>
        <li>Replenishment of shield happens at end of turn (after actual firing), by (surviving) Shield Projectors. Shield Projector will replenish only its associated projection, regardless of arc (which is used for things like called shots). 
            Projectors are NOT necessary for Projection itself to function - they may be switched off (and power used elsewhere) and Projection will stay at current power (will not regenerate in case of damage though).</li>
        <li>The oldest ships use Polarized Hull Plating instead of Shield Projection - which technically behaves exactly the same, but doesn't have associated Projectors (so can't be regenerated during battle).</li>
        <li>Fighter shields do not use separate Projectors either - they do regenerate on their own (at double rate if the fighter in question is not using its direct fire weapons).</li>
        <li>Note that while they are called "shields" (and are so by the lore), technically they're a different kind of system than B5 universe shields - any shield-related special property (like Phasing Pulse Cannon family ignoring shield) will not be taken into account when hitting a Star Trek unit.</li>
        <li>Shield mechanics mean that (besides necessary adaptation of tactics) in very small battles/duels Star Trek ships may perform slightly above their price tag.</li>        
    </ul>
<h5>Independent Fighters</h5>
        <ul>
<li>While most StarTrek fighters/shuttles require hangar slots to be deployed (just like their B5 counterparts do), some of them possess Warp Drive of their own, as well as high endurance. Such craft do not require hangars, despite being fighter-sized. 
            They are affected by "number of ships on a given hull limit" (with each flight counted as one "ship"), and usually are marked as Restricted Deployment.</li>
   </ul>
<h5>Self-reliable LCVs</h5>
        <ul>
<li>Most StarTrek LCV-sized ships are fully capable of operating on their own, do not require hangars and have regular sensors (rather than restricted B5 "LCV Sensors").</li>    
   </ul>
<h5>Militarized Shuttles</h5>   
        <ul>
<li>Most StarTrek factions don't use dedicated fighters. However, their shuttles are usually armed and combat capable if necessary. Ships of these factions do have shuttle capacity listed (which is usually omitted in FV) and have combat shuttle designs available. 
            Combat shuttles are not fighters, and as such are not necessary if the player doesn't wish to deploy them.</li>

    </ul>
<h4 id="startrekfederation" >STAR TREK: FEDERATION</h4>            
    <p>The United Federation of Planets is a conglomerate of multiple separate species. They remain independent enough to operate their own warships, built to different specifications - which is marked in fleet list. This is NOT, however, intended as a fleet composition restriction
         - Federation fleet on a field of battle may use any mix of units desired (although small craft should be based on carriers of the same species).</p>  
    <a class="back-to-top" href="#top">↩ Back to Top</a>

     

    <h4 id="starwars" >STAR WARS</h4>
    <p>Star Wars units, as designed by Wolfgang Lackner-Warton and Marcin Sawicki.</p>
    <p>Due to nature of StarWars universe, essentially any faction can use almost any unit. Primary users are indicated in notes though.</p>
    <p>It's assumed that - for fleet design purposes - fighters do require hangars (even if they're hyperdrive equipped). It's also assumed that any fighter can use any hangar, taking more or less space - therefore small craft are grouped into "Fighter squadrons" and "Assault squadrons", 
        and appropriately limited (with superheavy fighter squadron and light fighter squadron requiring the same hangar space, but the latter being composed of far more craft). Fleet checker does properly account for SW small craft.</p>
    <p>Weapons are portrayed to allow long range of fire, but for the fire to be wildly inaccurate. Hence Pulse mode with very low base number of hits. 
        This allowed to keep very high weapon count of SW units manageable (they're grouped into batteries that are firing as single weapons in FV)</p>
    <p>Based on visuals and effects in movies - we call SW Laser/Turbolaser weapons to be Particle class, with all the associated consequences.</p>
    <p>SW units are portrayed as quite durable for their size, and that durability is boosted by shields. Shields are difficult to take out, too (projectors have high HP count and low hit table profile). 
        Shields have a number of exceptions to their protection based on how we see them to work (notably increased effectiveness vs Raking weapons, while SWIon, Ballistic and Matter weapons just bypass them).</p>
    <p>StarWars Sensors are portrayed to be generally of lower tech than B5 ones. This is shown by generally low output and limited boostability (notably ElInt boostability is not reduced though). However, LCV do have full-blown sensors, without B5 LCV Sensors limitations.</p>
    
    <h4 id="starwarsclone" >STAR WARS: CLONE WARS ERA</h4>
    <p>This is a separate take on implementing Star Wars units to play against B5 fleets. This is deliberately focusing on the Clone Wars era so as to not cause confusion with the existing Star Wars work.</p>
    <p>The approach here is that Star Wars ships are typically seen firing rapidly (and technical manuals show they have a lot of weapons). The approach here is to use faster firing, but overall lower damage weapons.
    Weapons typically come in single, twin, and quad configurations. As turbolasers, these fire every other turn, once per turn, and twice per turn, respectively. Shields use the B5 Wars EM shield rules. Ion weapons use Burst Beam effects.</p>                                                                                           
    <a class="back-to-top" href="#top">↩ Back to Top</a>          


<h4 id="system" >THE SYSTEM</h4>
    <p>The System is the name of a computer network that was the controlling authority of a highly advanced, Ancient-level alien civilisation. The System has mastered a number of extremely advanced technologies, able to construct advanced artificial intelligences 
	and extremely powerful starships. System vessels are characterized by their speed, resiliency, and shielding.</p>
    <p>In Fiery Void, they have been created as a Custom faction (with credit to pauluk(Reman) to initiate the project and creating the graphics) with the following key features:</p>    

<h5>Shielding</h5>
    <ul>
		<li>The System shields use the same mechanics as the Thirdspace shields. They absorb all incoming damage in the arc that they cover. Once these shields have been reduced to zero rating damage will start to be inflicted on their ships as normal.</li>
        <li>During Initial Orders players can freely move shield power around via the Shield Generator. This system must be at 0 in order to commit your orders (e.g. you can't 'save' shield energy in the generator for future turns). 
            The maximum amount you can allocate to any given shield is two times its base value.</li>
        <li>The Shield Generator also comes with a number of preset options you can click on to assist in moving shield energy around.  
            At the end of the turn, Shield Projectors will restore their respective shields by an amount based on their current rating.</li>
    </ul>

<h5>Gravitic Drives</h5>
    <ul>
		<li>Allows ships to undertake maneuvers even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>                    
    </ul>

<h5>Artificial Intelligence</h5>
	<li>System vessels are operated by an advanced artificial intelligence (AI). This conveys several advantages.
        <ul class="circle-list">
            <li>System vessels have an exceptional initiative bonus for their size.</li>
            <li>System vessels are resilient to criticals and have a -2 bonus to all critical rolls.</li>                   
            <li>The AI is manages highly efficient energy usage allowing for exceptional maneuvering and power-hungry weapons.</li>                   
        </ul>            
    </li> 

<h5>Plasma Driver</h5>
    <ul>
		<li>Extremely potent pulse weapons scoring damage as plasma. They are highly effective against all targets.</li>                    
    </ul>

<h5>Neutron Blaster</h5>
	<li>A versatile electromagnetic weapon with multiple modes of fire and may not be intercepted.
        <ul class="circle-list">
            <li>If a single blaster is used, it may fire twice per turn scoring 1d10+10 damage with a -2.5 per hex range penalty.</li>
            <li>Two blasters can be combined to do 4d10+20 damage in raking (15) with a -1.65 per hex range penalty.</li>                   
            <li>Three blasters can be combined to do 5d10+30 damage in raking (20) with a -1.25 per hex range penalty.</li>       
			<li>If player mis-declares and not enough weapons are assigned for declared mode, the shot automatically misses.</li>
        </ul>            
    </li> 

<a class="back-to-top" href="#top">↩ Back to Top</a>





<h3 id="tiers" >TIER RATINGS</h3>  
    <p>The factions of Fiery Void have placed into Tiers by the community.  These are subjective and not set in stone, but provided simply to manage expectations on the relative power of one faction against another.</p>

    <h4 id="tier1" >TIER 1 - HIGH COMPETITIVENESS</h4>
    <p>This is the default power level of show era major factions ('Big 4') - more or less the 'baseline' of competitive play.</p>
       <ul>
<li>Centauri Republic</li>
       <li>Dilgar Imperium</li>
       <li>Drakh (Unofficial)</li>
       <li>Drazi Freehold</li>
       <li>Earth Alliance (without Warlock)</li>
       <li>Gaim Intelligence</li>
       <li>Hyach Gerontacracy</li>
       <li>Kor-Lyan Kingdoms</li>
       <li>Llort</li>
       <li>Minbari Federation</li>
       <li>Minbari Protectorate</li>
       <li>Narn Regime (with 6 or less Energy Mines)</li>
       <li>Orieni Imperium</li>
       <li>Torata Regency</li>
       <li>Vree Conglomerate</li>
       <li>Yolu Confederation (with 'Points Re-Evaluation' enhancement)</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>           
       
    <h4 id="tier2" >TIER 2 - MEDIUM COMPETITIVENESS</h4>
    <p>Factions that point for point will have a difficult time competing against Tier 1 opponents, yet otherwise are balanced and competitive. 
        Many of them have weaknesses more pronounced than Tier 1 factions but may offer a very distinct playstyle instead. 
        Should be interesting to play against each other, and if given some extra PV allowance - possibly against Tier 1 as well.</p>           
        <ul>
<li>Abbai Matriarchate</li>
        <li>Balosian Underdwellers</li>
		<li>Barada Imperium</li>
        <li>Belt Alliance</li>
        <li>Brakiri Syndicracy</li>
        <li>Cascor Commonwealth (with PV reevaluation)</li>
        <li>Ch'Lonas Cooperative</li>
        <li>Corillani Theocracy</li>
        <li>Deneth Tribes</li>
        <li>Descari Committees</li>
        <li>Drazi Freehold (WotCR)</li>
        <li>Pak'ma'ra Confederacy</li>
        <li>Raiders & Privateers</li>
        <li>Escalation Wars Chouka Theocracy</li>
        <li>Escalation Wars Circasian Empire</li>
        <li>Escalation Wars Kastan Monarchy</li>
        <li>Escalation Wars Sshel'ath Alliance</li>
        <li>Nexus Brixadii Clans</li>
        <li>Nexus Craytan Union</li>
        <li>Nexus Dalithorn Commonwealth</li>
        <li>Nexus Makar Federation</li>
        <li>Nexus Sal-bez Coalition</li>
        <li>Nexus Velrax Republic</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>    

    <h4 id="tier3" >TIER 3 - LOW COMPETITIVENESS </h4>
    <p>Factions that point for point will have uphill battle against Tier 2, and quite often have glaring holes in their capabilities that can easily be exploited by a more advanced opponent. 
        Yet, they can be competitive against each other, and can offer an interesting change of pace compared to more capable factions.</p>    
        <ul>
<li>Abbai Matriarchate (WotCR)</li>
        <li>Cascor Commonwealth (without PV reevaluation)</li>
        <li>Centauri Republic (WotCR)</li>
        <li>Earth Alliance (Early)</li>
        <li>Grome Autocracy</li>
        <li>Hurr Republic</li>
        <li>Ipsha Baronies</li>
        <li>Markab Theocracy</li>
        <li>Rogolon Dynasty</li>
        <li>Usuuth Coalition</li>
        <li>Escalation Wars Blood Sword Raiders</li>
        <li>Escalation Wars Chouka Raiders</li>
        <li>Nexus Brixadii Clans (early)</li>
        <li>Nexus Craytan Union (early)</li>
        <li>Nexus Dalithorn Commonwealth (early)</li>
        <li>Nexus Makar Federation (early)</li>
        <li>Nexus Sal-bez Coalition (early)</li>
        <li>Nexus Velrax Republic (early)</li>
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>    

    <h4 id="unbalanced" >UNBALANCED FLEETS</h4>
    <p>Of course, in a game as complex as Fiery Void, there was always going to be certain factions, or unit combinations, that are just too unbalanced to provide for a enjoyable pick-up game.  
        Below is a list that is considered by the community to fall into that category:</p>
       <ul>
<li>Ancients (see 'Ancients' section),</li>        
       <li>Alacan (with Rotia swarms),</li>
       <li>Earth Alliance when using Warlock Advanced Destroyer or Shadow Omega Destroyer,</li>
       <li>Geoff,</li>         
       <li>Interstellar Alliance's all-White Star fleet,</li>
       <li>Narn Regime, where over 6 Energy Mines are taken,</li>
       <li>Nexus Polaren Confederacy (playtest),</li>         
       <li>Sorithian, as they are simply too weak,</li>
       <li>Streib, as they are not really designed for normal competitive play,</li>
       <li>Yolu without points reevaluation</li>  
    </ul>
<a class="back-to-top" href="#top">↩ Back to Top</a>    

  </section>
</main>
</body>
</html>