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
                <li><a href="#mindriders">THE MINDRIDERS</a></li>             
                <li><a href="#shadows">SHADOW ASSOCIATION</a></li>
                <li><a href="#thirdspace">THIRDSPACE (Unofficial)</a></li> 
                <li><a href="#torvalus">TORVALUS SPECULATORS</a></li>                                   
                <li><a href="#vorlons">VORLON EMPIRE</a></li> 
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
                <li><a href="#nexus">NEXUS UNIVERSE</a></li>
                <li><a href="#escalationwars">ESCALATION WARS</a></li>
                <li><a href="#starwars">STAR WARS</a></li>
                <li><a href="#startrek">STAR TREK</a></li>                                           
            </ul>     
      </li>
      <a href="#tiers" style="margin-right: 5px; margin-left: 5px; margin-top: 10px; font-size: 12px; color: #8bcaf2; font-size: 16px;"><span style="color:gold;">★</span> TIER RATINGS <span style="color:gold;">★</span></a>                                                                      
        <!-- Add more sections here -->
    </ul>
</div>    

<h3 id="general" style="margin-top: 30px;">GENERAL NOTES</h3>
    <p>Given the huge variety of factions in Fiery Void it made sense to try and provide a short overview of each one, to help players get a feeling for each one and how they play in the game.
    Sections have been written by different authors so while some will introduce the playstyle and systems of each faction, others will simply mention important things to be aware fo when playing them in Fiery Void.    
    </p>

    <p>Later in the document the factions are listed in competitive ‘Tiers’, to help players pick fleets which will be reasonably well-matched for a pick-up game.  
    There is a degree of judgement being applied to this categorisation but, given the sheer number of factions in Fiery Void, giving players an idea of a relative strength of each faction is seen as being more helpful than not!</p>

    <p><strong>Custom Designs:</strong> Some factions (and units) are marked as ‘custom’. This means they're not official designs released by AoG. This means they may not be as well-balanced as official factions.
    Before using custom units it's good practice to make sure that your opponent is ok with using them, or you can mention it explicitly in game description if you are creating the game.
    </p>

    <p><strong>Semi-custom Units:</strong> Some units are marked as "semi-custom". 
    This means they're not official designs released by AoG, but are not considered fully custom units for a variety of reasons, e.g.</p>
        <li>Official units that could not be fully ported into FV for technical reasons, but were nonetheless balanced with some changes (e.g. Brakiri Tashkat),</li>
        <li>Units that expand fleet build options of a faction while keeping close to official designs (e.g. Ipsha Jumpsphere),</li>
        <li>Units that were released by AoG after the company had officially ceased to produce B5Wars related materials (e.g. Showdowns-10, Variants-6).</li>     

    <h5>Easy and Forgiving Factions to Get Started</h5>
    <p>If you're just looking to have your first few games of Fiery Void, we recommend these factions as a good place to start as you will them relatively easier to fly than some of the more esoteric fleets!</p>
        <li>Drazi - Very flexible and always strong, with a ship layout makes them retain combat value even after sustaining heavy damage.</li>
        <li>Narn - Very tough with powerful weapons - they may not be fancy, but they get the job done and refuse to die even if put in unfavorable position.</li>
        <li>Earth Alliance - Extremely tough, with awesome defenses and good firepower. Their resilience makes them very forgiving to any beginner mistakes.</li>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

<h3 id="majorfactions" style="">MAJOR FACTIONS</h3>

    <h4 id="centauri" style="">CENTAURI REPUBLIC</h4>
    <p>Once known as the Lion of the Galaxy, the Centauri Republic's glory days might now be over they are still more than capable of fielding a powerful fleet with potency at range and up close.
    Their ships tend to be very specialised, and often quite fragile so players will need to play around these elements in order to secure victory.</p>        
        <h5>Plasma Stream:</h5>
            <li>A plasma weapon that permanently reduces armor of systems hit. Also, counts full armor for every rake, which is a difference from normal raking weapons.</li>   
        <h5>Guardian Array:</h5>
            <li>Primarily a defensive weapon that can intercept fire directed at a third party, provided the weapon is between the firing unit and its target.</li>   
            <li>In tabletop, it needs to be shut down to switch between offensive (anti-fighter) and defensive modes. There is no such requirement in Fiery Void, and Guardian Arrays can intercept or fire offensively at will.</li> 
            <li>Also, the definition of "between" was changed. In FV the weapon is "between" the firing unit and its target if the bearing difference on them is at least 120 degrees (2 hex sides).</li> 

    <h4 id="centauriwotc" style="">CENTAURI REPUBLIC (WoTCR)</h4>
    <p>The Centauri fleet from a few hundred years before the show era, representing the Wars of the Centauri Republic (WotCR) period.  
    Alot weaker than modern Centauri as you'd expect but intended to compete against other fleets from that period.</p>        
        <h5>Sentinel Point Defense</h5>
            <li>A Guardian Array (see above) precursor which works the same way defensively. However, it doesn't have offensive mode available.</li>   
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="dilgar" style="">DILGAR IMPERIUM</h4>
    <p>Now extinct race that once terrorised the League of Non-Aligned Worlds during the Dilgar War.  
    The Dilgar use a mix of ballistic, particle and plasma weapons on their ships to great effect, dealing large amounts of damage at any range.</p>        
        <h5>Pentacan Formation</h5>
            <li>Dilgar ships can benefit from using Pentacan formation. In FV the rules for it have been simplified.  Dilgar gain an initiative bonus if that are with 10 hexes of a command ship (not cumulative). 		  
            For fighters, as long as flight leader (first craft in flight) is alive and uninjured (eg. received no damage), flight has +5 Initiative.  Dilgar fighters also have an inherent -2 dropout bonus.
            </li>   
        <h5>Point Pulsar</h5>
            <li>Dedicated weapon intended for making called shots - suffers only half of the regular penalty when doing so (e.g. -20% instead of =40%).</li> 
        <h5>Mass Driver</h5>
            <li>Many Dilgar ships have Mass Driver fitted. This weapon is functional in FV, but is intended as planetary bombardment weapon of mass destruction and has limited use in space combat (however, it can engage starbases and immobile Enormous ships). 
            It's a Matter weapon that automatically hits structure (e.g. not any systems) and can be intercepted without degradation (similar to ballistic weapons).  To us ethis weapon the firing ship must be at speed 0.</li>   
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="ea" style="">EARTH ALLIANCE</h4>
    <p>Whilst relatively new on the galactic scene the Earth Alliance has quickly become a major power, playing a pivotal role in the Diglar War.  
    Since their near-extermination at the hands of the Minbari, Earthforce has built back bigger and stronger and their modern ships are notable for their firepower and survivability, albeit at the cost of manoeuvrability and limited firing arcs.</p>        
        <h5>Interceptor</h5>
            <li>Dedicated defensive weapon that can be boosted during Initial Orders to allow it to target enemy fighters (note - it cannot intercept when boosted).
            Generates an Energy Web (E-Web) providing a shield-like effect on its arc that reduces the hit chance of all incoming fire by its intercept rating. 
            E-Webs cannot be flown under by fighters, and does NOT provide damage reduction.
            </li>   
        <h5>Aegis Sensor Pod</h5>
            <li>A specialised system, found only on the Hyperion Aegis Cruiser.  The system targets a fighter flight and automatically hits, giving the firing ship 3 CCEW against that unit.  This bonus EW cannot be combined with any other OEW, CCEW or EW from another Aegis Pod.  
            The Pod are relatively prominent on the ship's hull, and Called Shots have a +10% bonus against them (so -30% instead of the usual -40%).</li> 

    <h4 id="eaearly" style="">EARTH ALLIANCE (EARLY)</h4>
    <p>The early Earth Alliance units are composed of semi-official custom designs that all were deployed before 2200. These units generally lack the better defenses of the later Earth designs and many use blast cannons. Some official units, like the Olympus Alpha are listed in 
    both the standard Earth Alliance fleet list as well as the early EA section. This is to represent their transitional nature from being high-end units in the early EA to very old units in the Dilgar War era. Although the more famous Starfuries (Tiger, Nova, Aurora) are unavailable 
    the early examples of the Starfury lineage (Flying Fox, Aries, and Atlas) formed the foundation of the fighter-heavy EA fleet in the future.
    </p>        
    <a class="back-to-top" href="#top">↩ Back to Top</a>


<h4 id="minbari" style="">MINBARI FEDERATION</h4>
    <p>The oldest of the Younger Races and the most technoloigcally advanced.  This is demonstrated by their powerful Jammer system, and high damage weaponry.  
    Minbari fleets have few apparent weaknesses at first, but the cost of their ships means they will usually be out-numbered on the battlefield which can be a distinct disadvantage.</p>        
        <h5>Jammer</h5>
            <li>A powerful defensive technology, it prevents enemies from gaining a ‘lock-on’ with their Electronic Warfare, meaning that range penalties against Jammer-protected units are always doubled.  
            It also halves ballistic weapons launch range. Minbari themselves ignore Jammer, as do races equipped with Advanced Scanners e.g. Ancients.  
            Note, on Minbari fighters, Jammer protection does not stack with jinking.</li> 
       <h5>Gravitic Drives</h5>
            <li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
            Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>
       <h5>Antimatter Converter</h5>
            <li>A weapon which deals damage based on how well it hits its target e.g. hit chance minus the dice result on its roll to hit.  
            Uses Flash mode, so 25% damage is caused to other units on the same hex as any target hit.</li>
       <h5>Gravitic Net</h5>
            <li>This weapon is used to move a target, and fires in the Pre-Firing phase, before regular weapon declaration.  
            First target a ship (friend or foe) and a green hexagonal sprite will appear showing the available hexes that the target unit can be moved.
            Next, target an available hex to confirm the shot.</li>
       <h5>Electro-Pulse Gun</h5>
            <li>Very short range, slow firing weapon that only affects fighters. However, it can make called shots at no penalty.</li>
       <h5>White Stars</h5>
            <li>The mainstay of Army of Light is available on Minbari fleet list. It's difficult to handle, but in skilled hands extremely potent unit, 
            equipped with Vorlon (see their list) technologies nominally beyond reach of Younger Races (e.g. Adaptive Armor, EM Shields, and in case of Command variant - even self repair).</li>
            <li>White Stars may be used as ISA/Army of Light/White Star Fleet as well. Such a fleet is NOT tournament legal, and for good reason - but may be an interesting scenario piece (or challenge). 
            It's also far too strong to be used in regular pickup battles without asking you opponenet first!
            When used in this way, only White Stars (and any Combat Flyers they can carry) can be deployed.  Be warned though - in skilled hands such a fleet is simply overwhelming.</li>                                                                         


    <h4 id="narn" style="">NARN REGIME</h4>
    <p>A former Centauri colony, the Narn Regime is now a galactic power in it's own right.  What their vessels lack in ourright technology they makes up for powerful weaponry and sheer survivability.</p>        
        <h5>Energy Mines</h5>
            <li>A hex-targeted weapon, which causes heavy damage to everything on hex hit and splash to everything in adjacent hexes, including allied units.  Devastating against fighters, and when used en masse (6+ Energy Mine launchers can easily saturate an area).</li>
            <li>Opponents will not be able to see where you have targeted your Energy Mines until after firing is resolved, just the fact that they've been launched. Launched mines also have a 25% chance to scatter or dissipate harmlessly.</li> 
        <h5>Pulsar Mines</h5>
            <li>An automated short-range weapon that fires at passing fighters.  The weapon will automatically track enemy fighters during the Movement Phase, and attack any that came within arc and range before the Firing Phase begins.  Each Pulsar Mine can fire up to 18 shots per turn.</li> 
    <a class="back-to-top" href="#top">↩ Back to Top</a>            


    <h4 id="orieni" style="">ORIENI IMPERIUM</h4>
    <p>The arch-nemesis during the Wars of the Centauri Republic, Orieni fleets are typified by the large amount of matter weaponry they bring, their large range of medium ships and their Hunter-Killer drones.
     A peculiar faction in many ways as it can both feel oppresive to play against due to their damage potential, but also difficult to win with if you are not experienced with them.   
    </p>        
        <h5>Hunter-Killer (HK) Drones</h5>
            <li>Orieni long-range weapon of choice, and probably their most outstanding feature. They're essentially large missiles with a degree of control, using fighter rules and attacking by ramming enemies.</li>
            <li> Hunter-Killers are controlled by Control Nodes on Orieni ships. If there is insufficient control HKs can operate autonomously, which this limits their performance by applying a significant Initiative penalty.
            This loss of control is exercised proportionally - e.g. if there are 12 HKs on map and only 6 can be fully controlled, then all 12 receive half the penalties., but no other penalties. 
            In addition, HKs suffer Initiative penalties during the first 2 turns of the game (but start deployed like any other fighter).    
            </li>
            <li>When attempting to ram enemies, Hunter-Killers receive a penalty to their hit chance based on their own speed (the faster they are moving, the worse this). 
            Note - As ramming attacks happen before other firing, HKs that achieved ramming distance cannot be shot down before they attempt to ram.
            </li>                        
        <h5>Light Gatling Railgun</h5>
            <li>Orieni Templar's gun is Matter-based,making it a threat to even the biggest ships. The downside is it's limited to 6 shots before running out of ammunition.  More ammunition can be bought as an enhancements during Fleet Selection.</li> 
        <h5>Strike Force</h5>
            <li>The Orieni military is split into a few branches and a few ships have different limitations when deployed as part of Strike Force detachment instead of the regular navy, Hand of the Blessed. </li>
            <li>Assume any force led by Paragon to be Strike Force, while lesser command ships lead Hand of the Blessed. Note - The in-game fleet checker does not take this rule into account.</li>                                          
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="raiders" style="">RAIDERS & PRIVATEERS</h4>
    <p>The Raiders & Privateers faction is not a single faction with a standardized force list. Instead it represents the various kinds of pirates, privateers, and wanderers in the Babylon 5
    universe.</p>
    <p>Specific raider groups will be listed in the unit name, e.g. ‘Centauri Privateer’, meaning that  any unit without a faction prefix is considered a generic raider and can be fielded by any faction. 
        It is near-impossible to create a Fleet Checker for all of the various combinations within this faction, 
        so a bit of player responsibility is required in order to choose an appropriate fleet. Summaries of the Raider mini-Factions in Fiery Void are listed below:</p>
       <h5>Brakiri Shokhan</h5>
            <li>The Shokhan grew out of various Brakiri groups that did not integrate into the modern Brakiri society. Many Shokhan operate purely as raiders, 
                but it is suspected that some receive outside support from Brakiri corporations or other entities and fulfill the role of privateers and sometimes mercenaries.</li>
            <li>The Shokhan have three Brakiri specific ships. They also operate a limited selection of generic raider units including the Aspar, Galleas, Hawk, Ma'Ri'e, Felucca, and Xebec. 
                The Delta-V and Folshot-A are the two fighter designs operated by the Shokhan.</li>   
       <h5>Centauri Privateers</h5>
            <li>Generally led by lesser nobles of the great houses or those from minor houses looking to advance, Centauri privateers have existed throughout the Centauri's time in space. 
                Originally, they operated against other Houses, but focused more on the edges of Centauri space as other powers were discovered. </li>
            <li>In addition to Centauri hulls, these privateers use a limited selection of the generic raider hulls, including the Ma'Ri'e, Ma'Ri'u, Pinnace, Uid'Ac'e, Felucca, and Xebec. 
                Centauri privateers use Delta-Vs almost exclusively, but have restricted access to Razik fighters or older Centauri fighters like the Glaive and Phalen.</li>
       <h5>Drazi Hunters</h5>
            <li>Derived from the Drazi penchant of hunting, it is typically assembled from Drazi disaffected with traditional Drazi society. 
                The Hunters operate more as a raider group than privateers, they are not officially endorsed by the Drazi. However, their members are welcome in Drazi space to a limited degree.</li>
            <li>They operate three units of Drazi design as well as a few generic raider hulls. These are the Aspar (limited), Galleon, Hawk, Ma'Ri'e, Pinnace, and Wolf Raider (limited). 
                Drazi Hunters only employ Cobra and Delta-V fighters.</li>                     
       <h5>Independent Mercenaries League (IML)</h5>
            <li>The IML formed during the Dilgar War, originally by Belt Alliance forces out of work. They have been supported since then by various League powers 
                and typically have better access to technology than traditional raider groups.</li>
            <li>The IML fields three unique units, the Attack Cruiser,  Armed Transport, and Missile Frigate along with variants of each.</li>
            <li>The IML use Armed Shuttles and Delta-Vs almost exclusively, but have restricted access to Star Snakes and Lellat fighters.</li>   
       <h5>Imperial Star Legion</h5>
            <li>This group operates primarily within Centauri, Earth, and Narn space and in addition to generic raider hulls.</li>
            <li>The Legion has three unique hulls, the Starjammer, Gladius, and Augustus and is characterized by using heavy weapons and fielding fusion cannons.</li>
            <li>The Legion operated all of the generic raider units at various times in their existence. The Legion uses Delta-Vs and Double-Vs fighters exclusively.</li>                                                                      
       <h5>Junkyard Dogs (JYD)</h5>
            <li>A raider group that supported the Army of Light in the Shadow War, but then had bigger plans for the future and wound up fighting the Minbari Protectorate.</li>
            <li>They operate several converted hulls from the main powers including a Lias, two Tethys, a Kutai, a Mograth, two Vorchans, two Sho'Kos, and a Thentus.</li>
            <li>The Junkyard Dogs primarily used ArmedShuttles, Delta-Vs, and Double-Vs but may also field a flight of Raziks or Goriths.</li>  
       <h5>Narn Privateers</h5>
            <li>Formed out of Narn elements considered too radical or unruly to integrate with the newly free Narn Regime, the Narn sent these privateers out to continue to strike at the Centauri. 
                The more organized groups received greater support from the Narn Regime and fulfilled many missions ranging from traditional raiding to intelligence gathering.</li>
            <li>Narn privateers operate three specific Narn units as well as a selection of generic raider designs. These include the Brigantine (limited), Hawk, Ma'Ri'u, Pinnace, and Xebec. 
            They primarily use Delta-V and Double-V fighters and a restricted number of Narn Goriths.
            </li>   
       <h5>Tirrith Free State (TFS)</h5>
            <li>Born out of an EA base built in the Tirrith system from the Dilgar War, the raiders that took over the base ultimately formed an independent star system at the confluence of multiple powers. 
                The TFS is now less of a raider group and more a tiny nation.</li>
            <li>The TFS has three unique hulls including the Blockade Runner, System Monitor, and Freedom Base. Beyond this, the TFS will utilize any generic raider unit.</li>
            <li>The majority of TFS fighters are Delta-Vs, but they can field Armed Shuttles, Double-Vs, Goriths, Star Snakes, and Koists. The TFS only use Drazi Dudroma defense satellites.</li>   
        <a class="back-to-top" href="#top">↩ Back to Top</a>



    <h4 id="league" style="">LEAGUE OF NON-ALIGNED WORLDS</h4>
    <p>The League of Non-Aligned Worlds is an group of 14 playable factions that were released through the Bablyon 5 Wars books, Militaries of the League 1 and 2. 
    The factions have a huge variety of special abilites and playstyles, and whilst their relative power level varies dramatically from faction to faction they can all be alot of fun to play.   
    </p> 
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="abbai" style="">ABBAI MATRIARCHATE</h4>
       <h5>Gravitic Shield</h5>
            <li>Most Abbai units are equipped with Gravitic Shield. Active shields reduce both the hit chance and damage of incoming fire on their respective arcs. At range 0 fighters are assumed to be flying underneath the shields and effectively ignore them.</li>
            <li>Gravitic Shields are powered by shield generator, that can power up a limited number of them (usually not allowing a ship all-around protection unless generator output is boosted).</li>
        <h5>Quad Array</h5>
            <li>Four guns in a single housing that's prone to overheating. Using more than 2 shots offensively results in one or two 'May Overheat' critical effects the following turn (depending on whether 3 or 4 shots are fired).</li>
            <li>When this critical is in place, firing the Quad Array again offensively will cause a critical roll at end of turn with the following modifiers; +2 modifier for every shot fired in the current turn, and -2 if there is only one May Overheat critical effect.</li>
        <h5>Comms Disruptor</h5>
            <li>Reduces target's EW and Initiative for next turn.</li>
        <h5>Particle Impeder</h5>
            <li>Purely defensive weapon, capable of intercepting weapons with "uninterceptable" trait.</li>
            <li>In addition, it can be boosted with EW (rather than the usual power). Such a boost, in addition to increasing intercept rating of the Impeder, counts as a special kind of shielding, reducing hit chance of all incoming weapons, including all fighter fire.</li>
            <li> Purely defensive weapon, capable of intercepting weapons with "uninterceptable" trait.</li>
        <h5>Shield Projector</h5>
            <li>System only seen on Abbai base and OSATs, can target allies within 10 hexes and boost their Gravitic Shield on that turn by the Projector's rating.</li>                        
            <li>Fires as a ballistic weapon effectively, with its effects being applied during the Firing Phase.  To receive the shield bonus the allied unit must be within 5 hexes of the projecting unit by the end of the Movement Phase.</li>
            
    <h4 id="abbaiwotcr" style="">ABBAI MATRIARCHATE (WOTCR)</h4>
    <p>Abbai fleet a few hundred years before the show era, representing Wars of the Centauri Republic period. While one of the weaker factions overall, they have Gravitic Shielding much like modern Abbai (although much weaker), and weapons being precursors to modern Comm Disruptor (Comm Jammer and Sensor Spike).</p>            
        <h5>Mine Launcher</h5>
            <li>Launches a mine at a target hex, with a 25% chance to scatter.  Once it lands on a hex it will look for ships up to its maximum range and will attack the closest one, including friendly ships (if multiple ships are equally distant it will choose one at random).</li>
            <li>Unlike for missiles, the launcher does not come with any ammunition and you must purchase this at Fleet Selection from the available choices:
                <ul>
                    <li><strong>Basic:</strong> - 4 hex range, +10 to hit 12 Damage,</li>
                    <li><strong>Wide:</strong> - 7 hex range, +10 to hit 12 Damage.</li>
                </ul>
            </li>
            <li>Unexploded mines do not remain in play the following turn.</li>
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="brakiri" style="">BRAKIRI SYNDICRACY</h4>
        <h5>Corporations</h5>
            <li>Brakiri are split into competing corporations. On each unit it's noted which Corporation produces the design, if nothing is noted it's assumed that all Corporations have equal access to it.</li>
            <li>This is irrelevant when fielding a combined Brakiri fleet, but you have the option of fielding a single Corporation as well for flavour. 
                In this case, You have access only to designs available to that Corporation, but all restrictions (e.g. number deploy & variants) are eased by one level. 
                For Variant restrictions this means Rare->Uncommon->Common, for deployment Restricted(10%)->Limited->(33%)->Unlimited.</li>
                <li>Corporations have access to slightly different technologies and have their own areas of specialisation, so their individual fleets are usually lacking in some areas but have excellent access to something else.	
                    Note - The Fleet Checker does not take this rule into account.</li>
       <h5>Gravitic Shield</h5>
            <li>Most Abbai units are equipped with Gravitic Shield. Active shields reduce both the hit chance and damage of incoming fire on their respective arcs. 
                At range 0 fighters are assumed to be flying underneath the shields and effectively ignore them.</li>
            <li>Gravitic Shields are powered by shield generator, that can power up a limited number of them (usually not allowing a ship all-around protection unless generator output is boosted).</li>
       <h5>Gravitic Drives</h5>
            <li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
            Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>
        <h5>Gravitic Bolt / Gravitic Pulsar</h5>
            <li>These weapons can be boosted with power to increase damage and intercept rating. This does cause them to have a cooldown period equal to boost level used. 
                Additionally, if fired at maximum boost, the weapon can suffer critical damage.</li>
        <h5>Gravitic Shifter</h5>
            <li>This weapon fires in the Pre-Firing phase of the game.  You can target an ally or an enemy to try and change their facing by 60 degrees clockwise or anti-clockwise (using appropriate firing mode). 
                If the weapon hits then the target will be rotated before the Firing Phase occurs on that turn and therefore Gravitic Shifters can be used tactically to escape enemy firing arcs, or bring enemies into allied ships firing arcs. /section>
                Note - Only ONE Gravitic Shifter can be used on a ship per turn, any other Shifter attempts will automatically miss.</li>                        
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="cascor" style="">CASCOR COMMONWEALTH</h4>
       <h5>Poor Acceleration</h5>
            <li>For ships this is reflected in very high acceleration costs. 
                For fighters, the normal acceleration cost is doubled but is compensated somewhat by medium and lighter fighters having unusually high thrust ratings.  
                In effect they can't accelerate very well, but are exceptionally manoeuvrable even at high speed.</li>
        <h5>Ultralight Fighters</h5>
            <li>The smallest Cascor fighters are Ultralight, which allows them to efficiently fit into hangar space intended for larger fighters by only taking up half a slot.</li>
        <h5>Ioniser</h5>
            <li>Cascor fighter weapons, unlike typical fighter weapons it cannot be intercepted, neither can it intercept.</li>
       <h5>Ion Field Generator</h5>
            <li>Area effect weapon, targeted on a hex rather than ship. 
                Units caught in area of effect have capabilities reduced next turn (reduced Initiative, Power, EW, Offensive Bonus, and has a chance to shutdown a system).</li>
        <h5>Points Re-Evaluation Enhancement</h5>
            <li>Consensus in Fiery Void was that the Cascor were a fun faction but too pricey ever to be competitive. 
                In order to correct this perceived drawback, an enhancement was added to their units that does nothing except modify its price.</li>
            <li>This has made the faction much more playable and better balanced against other Tier 2 opponents.  A custom variant of their attack fighter was made available too.</li>
            <li>Note this is player initiative and using these enhancements turns the fleet into Custom faction, so check with your opponent!</li>     
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="drazi" style="">DRAZI FREEHOLD</h4>
        <p>Drazi are a very solid, beginner-friendly faction which uses an assortment of fast recharging particle weapons to pummel their enemies.  
        Their distinctive HCV layout with outer hulls on port and starboard instead of the traditional front and aft layout, is very notable for producing surprisingly resilient ships.</p>
       <h5>Repeater Gunsights</h5>
            <li>Ships equipped with Particle Repeaters can purchased this option in Fleet Selection.  Repeaters armed with gunsights are able to split their shots between different enemy units within a 1 hex radius of their original target,
                and different fighters within the same flights.  If the Particle Repeater takes any damage at all, it loses its gunsight ability.  All other rules relating to the Particle Repeater remain the same.</li>
                           
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="draziwotcr" style="">DRAZI FREEHOLD (WOTCR)</h4>
    <p>Drazi fleet a few hundred years before the show era, representing Wars of the Centauri Republic period.  No notable rules/technologies.</p>            
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="gaim" style="">GAIM INTELLIGENCE</h4>
    <p>Gaim ships, despite using repurposed hulls from other factions, are actually very good and their diversity is excellent - making them one of the stronger League factions.</p>
       <h5>Bulkheads</h5>
            <li>Defensive system that can absorb in place of another system, which greatly increases resilience of Gaim systems. 
                Bulkheads can protect systems on the same structure that it's mounted upon. Note that primary systems are not protected by bulkheads.</li>
            <li>The decision whether to use a bulkhead is made automatically, and will aim to prevent a systems destruction, 
                or when the integrity of the structure block falls too low</li>    
        <h5>Particle Concentrator</h5>
            <li>Multiple Concentrators firing from the same hex may combine into a more accurate and more powerful single shot. 
                The hit chance for this shot will be an average of all the weapons concentrating (as individually their chances may differ).</li>
        <h5>Packet Torpedo</h5>
            <li>Ballistic weapon that has a range penalty, just like direct fire weapons - although only after the first 10 hexes of travel. 
                In addition, the target of this weapon is not known to your opponent until impact.</li>
       <h5>Scattergun</h5>
            <li>Light weapon that fires a random number of shots. When firing defensively these shots will be directed at separate threats, never the same shot more than once.</li>    
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="grome" style="">GROME AUTOCRACY</h4>
    <p>The Grome a low-tech faction that prides itself in only using what they can build themselves. Their armament is made entirely of matter class weapons and they can equip their railguns with various types of shells. 
        Despite being one of the weaker factions they have a number of interesting systems.
    </p>
       <h5>Antiquated Sensors</h5>
            <li>These sensors cannot be boosted beyond their base state.</li>    
        <h5>Connection Struts</h5>
            <li>While Grome vessels have huge structure blocks, they are very fragile. If a connection strut is hit, the damage to the structure is doubled.</li>
        <h5>Flak Cannons</h5>
            <li>These can also engage fighters and deal matter damage in flash mode. While effective in this role, the main ability is its defensive fire.</li>
            <li>It is capable of intercepting all fire from a single ship using the normal interception rules. Therefore three Flak Cannons can generate a -6 to hit from all fire from an enemy.</li>
            <li>Unlike most weapons, the Flak Cannons intercept fire can also be targeted manually using its alternative Firing Mode in the Firing phase, and will apply its intercept to all non-Ballistic fire from the target enemy ship.  
            This ability can be useful if the player is expecting large amounts of low damage fire from an unit (e.g. fighters), which automatic intercept algorithms may not prioritise.</li>
            <li>In addition, the Flak Cannon can intercept friendly units as long as the friendly unit is within 5 hexes and both the friendly and firing unit are in arc, it is able to engage.</li>
            <li>the Flak Cannon can also intercept uninterceptable weapons like lasers when fired at the Flak Cannon-equipped ship e.g. it cannot intercept this type of weapon at all for friendly units</li>
        <h5>Light and Heavy Railguns</h5>
            <li>Smaller and larger versions, respectively, of the standard railgun.</li>
        <h5>Targeting Array</h5>
            <li>This weapon automatically hits, but scores no damage.  Instead it increases the hit chances for all other shots against the selected target from the same ship, by 5% for each point of rating on the Targeting Array e.g. A rating of 2 would equal +10% to hit chance.</li>
            <li>Multiple Targeting Arrays can be combined against the same target, but the effect will degrade by 5% for each subsequent array e.g. two Targeting Arrays with a rating of 2 would only increase overall hit chances by 15%, not 20%.</li> 
            <li>Ships with ‘Haphazard Targeting Systems’ in their notes have a lot of Targeting Arrays and this risks having them interfere with one other. After they have been ordered to fire, they have a chance to malfunction  This chance being reduced or removed if one or two Targeting Arrays are destroyed or deactivated.  
                With no Arrays disabled, there is 1 in 6 chance of malfunction, with one Array disabled this drops to a 1 in 8 chance.  With two or more arrays disabled none of the other arrays will malfunction.</li>
        <h5>Escort Arrays</h5>
            <li>These operate in exactly the same way as Targeting Arrays above, however they also provide the hit chance increases to friendly ships within 5 hexes.</li>
        <h5>Special Shells </h5>
            <li>The Grome Rail guns each have access to a number of different shells for their railguns, the effects for these are outlined in <a style="font-size: 14px;" href="./ammo-options-enhancements.php" target="_blank" rel="noopener noreferrer">Ammo, Options & Enhancements</a>.</li>                                      
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="hurr" style="">HURR REPUBLIC</h4>
    <p>Low-tech faction, one of the weaker League factions. No very specific rules/technologies relying on ballistic, plasma and particle weapons to reasonable effect.</p>            
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="hyach" style="">HYACH GERONTOCRACY</h4>
    <p>The Hyach are among the oldest of the League races, and have been in space longer than even the Abbai. Their ships are surprisingly advanced among League races, with formidable weapons and military training unequaled
    amongst the League.</p> 
    <p>As the Hyach are very advanced in the area of computers, robotics, and communications, their ships tend to reflect these advantages, with better fire controls, enhanced sensors, and efficient construction.</p>
       <h5>Hyach Computer</h5>
            <li>The weapons on Hyach ships are tied into an advanced central computer system that tracks enemy units faster than any mind can. This produces one or Bonus Fire Control Points (BFCP) every turn.</li>
            <li>The Hyach player is free to distribute these points as desired among the three fire control categories (capital ships/heavy combat vessels, medium ships, and fighters/shuttles) each turn, but cannot allocate more than two to any given category.</li>    
        <h5>Hyach Specialists</h5>
            <li>Some Hyach crewmen receive special training as Specialists. In addition to their usual functions at their posts, they can provide their singular expertise at key moments in battle.</li>
            <li>Ships with at least one Specialist slot on-board must select these on the turn the ship is deployed.  
                To do so, in Deployment phase use the 'Specialists' technical system to select Specialists on each applicable ship, up to their maximum allowance. 
                Only one of each Specialist type can be picked per ship. You can then use your Specialists using the + button on the Specialist System in the game phase you want to use them. 
                Note, different Specialists can be used at different times e.g. Power/Sensor Specialists can only be used in Initail Orders phase, whereas Thruster/Engine Specialists could also be used in Movement Phase and Targeting/Weapon Specilists could be used in any phase up to Firing.</li>
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
                    <li><strong>Weapon:</strong> Generates two extra Bonus Fire Control Points (BFCP) this turn.</li>
                    <li><strong>Computer:</strong> +3 damage to all weapons this turn.</li>                      
                </ul>
            </li>              
        <h5>Interdictors </h5>
            <li>The Interdictor is an excellent defensive weapon, though it lacks any sort of offensive firepower. Interdictors can also defend nearby friendly ships, in much the same way as the Centauri’s Guardian Array does, but with fewer restrictions.</li>
            <li>In order to block a shot aimed at a different unit, the following must be true:
                <ul class="circle-list">
                    <li><strong>1.</strong> The enemy ship and the target ship must both be in the interdictorâ€™s firing arc.</li>
                    <li><strong>2.</strong> The target ship must be no farther than 5 hexes away from the interdicting ship.</li>
                    <li><strong>3.</strong> No other interdictor may be used against that same incoming shot.</li> 
                </ul>
            </li>
        <h5>Hyach Sensors</h5>
            <li>Hyach sensors are resistant to damage, and shrug off critical hits more easily. Instead of adding +1 to the critical sensor roll for every point of damage, they add +1 only for every two boxes of damage (rounded down).</li>
        <h5>Stealth Ships / Submarines</h5>
            <li>The Hyach are the only race that operate stealth ships which are the spacegoing equivalent of submarines.  Full rules for Stealth can be found in the <a style="font-size: 14px;" href="./faq.php" target="_blank" rel="noopener noreferrer">Fiery Void FAQ</a></li>
        <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="ipsha" style="">IPSHA BARONIES</h4>
        <h5>Baronies </h5>
            <li>There is no Ipsha "national fleet". The Ipsha species is split into feudal ‘Baronies’ , and any fielded fleet will belong to one of these. Players should only use designs specific to one chosen Barony plus any generic designs in use by all Baronies (indicated in unit notes). Similarly, some enhancements are Barony-specific.  
                In-game fleet checker does not take this rule into account. Some "baronization" of ships is available through unit enhancements (eg. You can apply typical Essan changes to basic Ipsha design).</li>    
        <h5>Gravitic Drives</h5>
            <li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
            Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>
        <h5>Mag-Gravitic Reactor</h5>
            <li> Ipsha power sources have fixed power outputs, rather than the usual default of having enough power to have all systems online. 
            This means that destruction of Ipsha systems does not cut into overall power produced by the ship - so even though power is usually negative for an undamaged ship e.g. it can't power everything, the number of systems the ship is able to power does not reduce with damage
            </li>
        <h5>EM Hardened</h5>
            <li>Ipsha units are especially resistant to Electromagnetic damage, and all critical/dropout rolls are made with -1 bonus. 
            In addition, EM weapons treat EM Hardened units as they would ones with Advanced Armor. This is cumulative with the dropout bonus their fighters already possess.</li>
        <h5>Low Pivot Costs</h5>
            <li>Ipsha ships have generally low pivot cost (even 0). Pivot-related penalties they suffer just like everyone else.</li>
        <h5>Weapon Cooldowns</h5>
            <li>All Ipsha weapons have a recharge rate of 1 turn. However, in practice they usually do require a cooldown period (e.g. forced shutdown) after firing (see Surge Cannon below).
            Coupled with limited power available, this means Ipsha usually power up and fire some of their weapons, and while they are cooling down - rotate to bring fresh ones to bear.
            </li>
        <h5>Surge Cannon</h5>
            <li>The primary Ipsha weapon is extremely versatile due to being able to fire more powerful beams by combination with other Surge Cannons. Basically two, three, four or even five weapons can combine into a single one shot with increased range, damage and fire control against ships.  Each rake causes additional +2 on critical/dropout roll of system hit, too.</li>
            <li>To exercise this ability, just switch weapons to appropriate mode (2,3,4,5-combined) and choose a target. All weapons from the same ship sharing mode and target will combine into appropriate shot(s) (e.g. 4 weapons in 2combined mode would produce 2 shots, but in 4Combined they would only produce one more powerful shot).</li>
            <li>If the indicated combination is not possible (eg. 3 weapons declared to fire in 4Combined mode) weapons will fire separately (in single fire mode) instead (usually missing, but not requiring cooldown).</li>   
        <h5>Spark Fields</h5>
            <li>This weapon creates a field that causes damage to all nearby units, friend or foe alike. This damage is low but still very threatening to fighters. The range of the Spark Field can be increased by boosting the system, but the greater the range the lower the damage.</li>
            <li>The Spark Field will fire automatically providing it is online, and the player does not need to manually declare it beyond ensuring it is online and they have set the boost level they desired during Initial Orders.</li>                                      
        <h5>Jumpsphere</h5>
            <li>The Ipsha have only one official jump-capable ship, Scout Wheel. This limits their legal fleet builds for tournament/pickup battles considerably. To allow greater flexibility in this area a custom jumpship design was added as a Warsphere variant, exchanging the fighter hangar for a Jump Drive. The ship has been marked ‘Semi-Custom’ accordingly.</li>
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="korlyan" style="">KOR-LYAN KINGDOMS</h4>
    <p>The Kor-Lyan specialize in ballistics, using them as their primary offensive and defensive weaponry. This specialization leads to the Kor-Lyan having several fun and unique systems.</p>
       <h5>Class-D Launcher</h5>
            <li>This is the main defensive system for the Kor-Lyan, it is essentially a light missile launcher that can only fire Interceptor, Anti-Fighter and Chaff missiles. 
                It comes loaded with a full amount of Interceptor missiles but the other two types need to be bought in the usual way.</li>      
        <h5>Class-F Launcher</h5>
            <li>Where the ‘F’ stands for flexible and can fire in two separate modes.
                <ul class="circle-list">
                    <li><strong>Normal</strong>The launcher's normal range is 20 hexes, however it can fire up to an additional +15 hexes when it has been loaded for two turns. When this range extension is used the launcher suffers -2 fire control and cannot use Rapid mode the following turn.</li>
                    <li><strong>Rapid</strong>The F-Launcher can choose to fire every turn with a -5 hex and -2 Fire Control penalty. It cannot fire in this mode the turn after firing in long-range mode.</li>
                </ul>
            </li>                
        <h5>Proximity Laser</h5>
            <li>A ballistic weapon that uses a separate launcher system to target the hex from which its main laser attack will then fire.</li>
            <li>To use this ability, first select the Proximity Laser system and target the hex from where you want the laser to fire from at an enemy target.  
                Then, without unselecting the same weapon, target the enemy ship with the Proximity Laser.</li>
            <li>The weapon will always automatically hit the intial hex targeted and then the laser attack will be made as normal as if originating from that hex, and not the firing ship.  
                It's highly recommended that you can use the Ballistic Lines button when targeting this weapon.</li>
                <li>The Proximity Laser does not use OEW but also does not suffer from having no lock on the enemy vessel it targets.  Range penalties are calculated from the hex targeted to the enemy unit you've target. You cannot target hexes for the initial shot that you do not have line of Sight to,
                    and if there is no line of sight between target hex and enemy vessel at the end of movement then the laser shot will automatically miss.</li>
        <h5>Limpet Bore Torpedo</h5>
            <li>A ballistic weapon that can only fire as Called Shot but can target any system on an enemy ship, including those that are not on facing sections.</li>
            <li>If the torpedo hits, it will attach to the target system and try to damage it by adding a critical effect. 
                If the system targeted is not on a facing section at the moment of impact, it will take an additional turn(s) to crawl around the enemy ship and attach to the target system.</li>
            <li>The target system will now have a critical effect noting the Limpets progress. 
                This critical effect will remain until the target system is destroyed, or after five failed attacks by the Limpet Bore. 
                It has no effect on OSATs or units equipped with Advanced Armor. </li>
        <h5>Ballistic Mine Launcher</h5>
            <li>Launches a mine at a target hex, with a 25% chance to scatter.  Once it lands on a hex it will look for ships up to its maximum radius then attack the closest, 
                including friendly ships (if multiple ships are equally distant it will select one at random). Unexploded mines do not remain in play the following turn.</li>
            <li>The launcher does not come with any ammunition and you must purchase this at Fleet Selection from the available choices:
                <ul class="circle-list">
                    <li><strong>Basic:</strong> - 3 hex range, +40 to hit 16-24 Damage,</li>
                    <li><strong>Heavy:</strong> - 2 hex range, +25 to hit 25-34 Damage,</li>
                    <li><strong>Wide:</strong> - 5 hex range, +40 to hit 13-22 Damage.</li>                    
                </ul>
            </li> 
        <h5>Armed Shuttles</h5>
            <li>Like all Babylon 5 ships, Kor-Lyan ships receive a number of shuttles for no additional points. 
                However, the Kor-Lyan have the option to arm these shuttles with two basic fighter missiles.</li>
            <li>The only cost is the missiles. Additionally, the Kor-Lyan can replace their standard shuttles with armed shuttles. 
                The most common one has a single fighter gun and two missile hard points.</li>        
            <li>An uncommon shuttle variant removes the gun, but allows the armed shuttle to hold up to six basic fighter missiles. 
                The Kor-Lyan player is under no obligation to take any of these in a match, unlike fighters with the half filled hangars requirement.</li>    
        <h5>‘Early’ Units</h5>
            <li>A few key Kor-Lyan units are not available until the 2240s or 2250s. This can make fielding an accurate force difficult. 
                The semi-custom versions are units that follow the example of the Toloki Starbase and replace the F-Launchers with L-Launchers.</li>                                      
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="pakmara" style="">PAK'MA'RA CONFEDERACY</h4>
    <p>Pak'ma'ra ships are quick, with excellent thrust and engine power, but are not manoeuvrable.  They depend on a heavy armament of plasma-based weapons with wide firing arcs to win battles.  
        Their unique plasma battery systems allow them to store power for opportune moments whilst their plasma webs provide both defensive cover and close-range anti-fighter protection.</p>            
    <h5>Plasma Batteries</h5>
        <li>System which stores but does not generate any new power.  
            Starts the game fully charged and the power stored can be used in the normal way during Initial Orders, it will show as a surplus in the Reactor system.  
            Providing they have power stored, Batteries can also be used in Firing Phase to provide extra power to Plasma Webs.</li>
        <li>Once depleted, the Batteries can be re-filled by commiting your Initial Orders with a power surplus in the Reactor.</li>
    <h5>Plasma Webs</h5>
        <li>Hex-targeted defensive weapon that can be used in one of two ways:  
            <ul class="circle-list">
                <li><strong>Defensive:</strong> - All fire from target hex suffers -10% hit chance against this vessel.  In addition, reduces damage from Antimatter, Laser and Particle attack by 2.</li>
                <li><strong>Anti-Fighter:</strong> Requires 1 extra power from boosting in Initial Orders or PLasma Batteries.  Creates a plasma cloud within 3 hexes of ship.  This cloud immediately damages any fighter in that hex and will persist to the end of the next Movement phase damaging any new fighters that pass through it,</li>               
            </ul>
        </li>
    <h5>Fighter Maneuvrability </h5>
        <li>Pak'ma'ra fighters have an inherent turn delay which cannot be shortened.  This is compensated by high thrust ratings and wider weapon arcs.</li>          
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="torata" style="">TORATA REGENCY</h4>
    <p>Torata extensively use Accelerator weapons, either allowing them to fire faster at the price of decreased damage output on most of their ship weapons, 
        or fully charge them to to deal massive damage.</p>
    <p>Note - Accelerator weapons will NOT be used for interception without explicit order to do so - even fighter guns.</p>                    
    <a class="back-to-top" href="#top">↩ Back to Top</a>    


    <h4 id="vree" style="">VREE CONGLOMERATE</h4>
    <p>The Vree are an advanced race within the League of Non-Aligned Worlds that use distinctive 'saucer' designs on all of their ships.  The Vree use antimatter weapons almost exclusively, which deal increased damage based on the difference between the base hit chance and actual dice roll to hit.  
        As a result their weapons are powerful at close and medium range but typically less effective at longer distances.</p>
        <h5>Vree Ship Layouts</h5>            
                <li>Vree capital ships have six outer hull sections, similar to starbases.  On Vree capital ships each section has its own structure and 
                largely functions the same as on normal four-sided capital ships, 
                however destruction of an outer hull structure will not destroy any systems/weapons on that section.</li>
                <li>Incoming fire will roll to hit either a weapon, thruster or structure and then be randomly allocated to an appropriate system that is in arc of the firing ship.</li>
                <li>Different weapons from the same ship can hit different hull sections of a Vree ship to help maintain some of their durability in battle.  A single shot cannot be split between multiple sections though.  Meaning Vree can be vulnerable to raking (and to lesser degree pulse) weapons.</li>      
        <h5>Antimatter Weapons</h5>
            <li>Antimatter weapons calculate range penalty in non-standard way.  For first few hexes they receive no penalty, then they receive normal penalty, and at further ranges
                they receive double penalty. In the case of no lock-on, range itself is doubled (rather than range penalty) and their range-reduced critical effect doesn't affect range penalty itself. 
                Rather, it adds 3 hexes to effective distance to target.</li>
            <li>Antimatter weapons also calculate damage in non-standard way. Rather than being randomized, the difference between actual 
                and needed to hit number is calculated (refrred to as X) 
                and every 5 points of difference translates into 1 point of X. The damage equation incorporates this X factor rather than the normal dice roll. 
                The damage reduced critical on antimatter weapons, reduces the value of X by 2 (but can't reduce it below 0).</li>                       
        <h5>Antimatter Shredder</h5>
            <li>This is a hex targeted weapon, which affects both its target hex and all adjacent hexes. 
                The Antimatter Shredder rolls to hit on any enemy ships in the target area, shown as extra shots in the Combat Log due to how FV  handles firing for this weapon.</li>
            <li>Only one Shredder may engage a target on any given turn - if multiple ones are eligible, only one of them (chosen randomly) will actually fire. 
                Also, Shredder engages all units in the target area, without discriminating between friend or foe (but won't engage firing ship itself).</li>
            <li>Shredder attacks completely ignores any and all EW (and EW-affecting systems like Jammer or Rutarian Stealth), as well as Jinking</li>
        <h5>Gravitic Drives</h5>
            <li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
            Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>            
        <h5>Turrets</h5>
            <li>In tabletop, Vree weapons located on the primary section of Vree ships are grouped into turrets, 
                which had the limitation that all weapons in one turret had to engage targets within 60 degrees of each other - and upon being hit, a turret may become locked. 
                These limitations are not present in Fiery Void.</li>                                                 
    <a class="back-to-top" href="#top">↩ Back to Top</a>



<h3 id="minorfactions" style="">MINOR FACTIONS</h3>
    
    <h4 id="alacan" style="">ALACAN REPUBLIC</h4>
    <p>No special rules or systems.</p>                
    <a class="back-to-top" href="#top">↩ Back to Top</a>    


    <h4 id="beltalliance" style="">BELT ALLIANCE</h4>
    <p>Belt Alliance is somewhat notable for extensive use of Matter weapons (including as fighter guns) and EA Interceptors (although with no integrated Energy Web).
    BA also makes use of LCVs. Belters can use (and are in fact the original designers and manufacturers) Delta-V fighters.</p>                    
    <a class="back-to-top" href="#top">↩ Back to Top</a>


    <h4 id="balosians" style="">BALOSIAN UNDERDWELLERS</h4>
    <p>No special rules or systems.</p>                    
    <a class="back-to-top" href="#top">↩ Back to Top</a>    


    <h4 id="chlonas" style="">CH'LONAS COOPERATIVE</h4>
    <p>Based on design by Charles "Danesti" Haught, published in BabCom. They don't possess any special technologies per se - although they have a few interesting weapons that use existing mechanics in atypical ways e.g. Matter Stream.</p>                        
    <a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="corillani" style="">CORILLANI THEOCRACY</h4>
    <p>The Corillani are unusual in that they operate three independent navies, the Defenders of Corillan (DoC), the Corillani's People's Navy (CPN), and the Orillani Space Force (OSF).  
        Whilst these forces generally operate independently of one another they can, and will, fight alongside one another.  
        In practice though, there is no strict restriction on mixing and matching ships from the three different sub-navies during Fleet Selection.</p>
    <p>However, when choosing fighters you must take the version of the Tillini which corresponds with the type of faction where hangar space is available 
        i.e. DoC or OSF fighters could not be taken if the only hangar space available was on a CPN ship.</p> 
    <h5>Plasma Projector</h5>
        <li>Used only by the OSF, this weapon focuses plasma into a tight beam, resulting in a longer range than most plasma weapons and with lower damage degradation.  
            It deals similar  damage to a heavy plasma cannon, however this is applied in raking (8) mode rather than standard mode.</li>                                  
    <a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="deneth" style="">DENETH TRIBES</h4>
    <h5>LCV Carrier</h5>
        <li>Deneth rely on LCVs more than other races and are able to purchase an LCV carrier to allow these small ships to be fielded whilst still conforming with the standard fleet composition rules.</li> 
    <a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="descari" style="">DESCARI COMMITTEES</h4>
    <h5>Plasma Bolters</h5>
        <li>These weapons combine features from both plasma and bolter weapon types.  
            They do a set amount of damage and do not suffer the damage reduction typical in plasma weapons up to a certain range.</li>                                  
    <a class="back-to-top" href="#top">↩ Back to Top</a>     


    <h4 id="drakh" style="">THE DRAKH</h4>
    <p>Custom fleet created by Wolfgang Lackner and Marcin Sawicki, based heavily upon BabCom-published designs by Roman Perner. 
        Drakh are a Middle-born faction, with a civilization older than even Minbari or Yolu, but not as old as the Ancient factions. 
        This has no direct impact in the game itself, unless a particular system treats such a faction differently.</p>
    <h5>Absorption Shields</h5>
        <li>Powerful shields that absorb incoming damage.  They can be boosted to be even stronger and are doubly effective against raking weapons.</li>
        <li>Unlike some other shields, they cannot be flown under by fighters and they don't decrease the unit’s defence rating, only reducing damage.</li>
    <h5>Advanced Armor</h5>
        <li>Provides a plethora of perks, generally reducing damage taken. It reduces or limits special effects of most weapons that have them, and counts as 2 points stronger against ballistics. 
            Ignored by weapons made by Ancients.</li>
    <h5>Gravitic Drives</h5>
        <li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>
    <h5>Improved Sensors</h5>
        <li>Halves the effectiveness of enemy Jammers.</li>                    
    <h5>LCVs</h5>
        <li>Drakh LCVs use regular Sensors (that is, they're not bound by the usual "all but 2 EW points need to be used offensively rule).</li>
    <h5>Hangar Space</h5>
        <li>Drakh LCVs do require hangar space (unlike regular LCVs). Instead they share it with Drakh fighters, although LCVs take twice as many slots.</li>                                                     
    <a class="back-to-top" href="#top">↩ Back to Top</a>  


    <h4 id="llort" style="">THE LLORT</h4>
    <p>No very specific rules/technologies but it's worth noting that their ships are often unusually asymmetric with atypical firing arcs and eclectic weapons sets.</p>                                 
    <a class="back-to-top" href="#top">↩ Back to Top</a>    


    <h4 id="markab" style="">MARKAB THEOCRACY</h4>
       <h5>Scattergun</h5>
            <li>Light weapon that fires a random number of shots. When firing defensively these shots will be directed at separate threats, never the same shot more than once.</li>    
       <h5>Religious Fervor</h5>
            <li>In certain scenarios Markab can fight whilst under the Religious Fervor effect.  This is selected as an Enhancement option in fleet selection at no cost, 
            and confers a +1 to hit with all weapons, +2 initiative and fighters gain a -3 bonus to dropout rolls. However, their recklessness means their defence ratings are increased by 10% in all directions.</li>
            <li>This a scenario-specific option, you should check with your opponent before selecting this enhancement, particularly as it signifies that the Markab are permitted to ram.  
            You should also select the Religious Fervor for all your Markab ships, or none of them since it’s intended as a fleet-wide effect.</li>                                        
    <a class="back-to-top" href="#top">↩ Back to Top</a>   

    <h4 id="minbariProt" style="">MINBARI PROTECTORATE</h4>
    <p>This is a part of the Minbari Federation, but consists of several non-Minbari members that are overseen by the Federation. They have a degree of autonomy, 
    including providing local defense to their worlds.</p>
    <p>The Protectorate uses older Minbari hulls with the notable difference being that units do not come equipped with the Jammer system. 
    The Tinashi is their most modern hull along with a handful of Nials.</p>
    <p>The Protectorate generally uses the Tishat medium fighter. Their fleet options are rather limited based on the number of hulls and lacking ways to bring fighters outside a Morshin carrier. 
    Their one noted conflict is the Pseudo-War with the Imperial Star Legion raider group.</p>                                                                   
    <a class="back-to-top" href="#top">↩ Back to Top</a>
        

    <h4 id="rogolons" style="">ROGOLON DYNASTY</h4>
    <p>No special rules or systems.  Notable mainly for the complete absence of anti-fighter weaponry on their ships.</p>                                             
    <a class="back-to-top" href="#top">↩ Back to Top</a>  


    <h4 id="smallraces" style="">SMALL RACES</h4>
    <p>A couple of smaller factions that are only connected by the fact that they don't have many ship designs.</p>                                            
    <a class="back-to-top" href="#top">↩ Back to Top</a>   

    <h4 id="usuuth" style="">USUUTH COALITION</h4>
    <p>Their fleet relies on LCVs but they don't actually have a carrier for these since they only typically operate within their home system.  
        This presents challenges for Usuuth fleets when deployed under standard tournament/pickup battle limitations, unless players agree to change the rules 
        and allowances are made for the Usuuth player. </p>                                            
    <a class="back-to-top" href="#top">↩ Back to Top</a> 

    <h4 id="yolu" style="">YOLU THEOCRACY</h4>
    <p>A very strong faction, often too strong.</p>
       <h5>Light Molecular Disruptor</h5>
            <li>Armor-stripping weapon equipped on Yolu heavy fighters, if removes 1 point of armour on enemy structure for every three shots that hit.</li> 
       <h5>Fusion Agitator</h5>
            <li>Raking weapon that does damage in smaller rakes (6).  However, it ignores a point of armor when damaging ships and its raw damage output is actually very good.</li>
            <li>Extra power can be used to boost damage further (by 1d10 per boost level) so its output can rise to be extremely high.</li>
       <h5>Molecular Flayer</h5>
            <li>Does not damage to the enemy ship, but instead reduces the armour of every system and structure on hull section it hits.</li>
        <h5>Points Re-Evaluation Enhancement</h5>
            <li>Due to the oppressive strength of the Yolu an optional enhancement has been added that that does nothing except modify the costs of some ships.</li>
            <li>This should make Yolu a much more reasonable faction to take against other Tier 1 opponents.</li>
            <li>Note this is player initiative and using these enhancements turns the fleet into Custom faction, so do check with your opponent!</li>                                                                                        
    <a class="back-to-top" href="#top">↩ Back to Top</a> 



<h3 id="ancientfactions" style="">ANCIENT FACTIONS</h3>
    <p>Ancient races typically have a much more complicated set of systems than the Younger Races. 
        They are also not very well balanced against the Younger Races as the tech difference and unit costs are too high for proper balance, nor are they tournament legal.</p>
    <p>But with these caveats - they can be a lot of fun!  Ancients should also be pretty well-balanced against each other, and if your opponent knows what to expect / or you're playing a scenario, 
        go ahead and rampage against the Younger Races as well. </p>
    <p>Note that the extremely high individual cost of Ancient units makes standard requirements based on total fleet value completely inadequate
         - so they have been modified for the Fleet Checker.</p>        

    <p>Most Ancient factions will have access to the standard systems for advanced races listed below:</p>
    <h5>Advanced Armor</h5>
        <li>Provides a plethora of perks, generally reducing damage taken. It reduces or limits special effects of most weapons that have them, and counts as 2 points stronger against ballistics. 
            Ignored by weapons made by Ancients.</li>            
    <h5>Self-Repair</h5>
        <li>Automated, but players can modify the default priority of repairs. Damage and criticals suffered in the current turn cannot be repaired. 
            Cost of critical repair has been changed in places (in particular C&C criticals aren't that costly to fix), and there's no partial repair of crits.  
            If a Self-Repair system is destroyed or damaged, unused repair points are not lost.</li>
    <a class="back-to-top" href="#top">↩ Back to Top</a> 

    <h4 id="mindriders" style="">THE MINDRIDERS</h4>
    <p>Below is a list of systems The Mindriders use, with short description of effect and any notable design differences from their original tabletop versions.</p>
    <h5>Special Hull Arrangements</h5>
        <li>Mindrider ships do not follow the usual layout, as a rule you can see what arcs their structure has by looking at the arcs of the Thought Shield (see below).  
            Mindrider vessels benefit from this arrangement by being allowed to pivot for free, and do not suffer the usual hit chance penalties for pivoting/rolling.  
            Note - The Wheel of Thought ship is unusual in that it MUST pivot every turn.</li>
    <h5>Constrained EW</h5>
        <li>All Mindrider ships are able to use ELINT abilities, however these are on a 'constrained' basis, and therefore each ability costs 1 extra EW compared to normal ELINT vessels.</li>
    <h5>Gravitic Drives</h5>
        <li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>                    
    <h5>Though Shields</h5>
        <li>Mindrider ships and fighters are protected by Thought Shields.  Each shield has a specific arc it covers, and will absorb enemy fire up to it's maximum hit points on that arc.</li>
        <li>Thoughts Shields will automatically regenerate to their full capacity at the start of each turn, and hit points can be transferred between arcs during Initial Orders phase to reinforce particular arcs.</li>
    <h5>Contraction</h5>
        <li>The Mind's Eye vessel can choose to Contract itself during the Movement Phase, gaining +1 bonus to its Thought Shields and -5 bonus to its Defence Profiles for every level of Contraction.  
            In addition, for every 3 levels of Contraction ALL systems on the Mind's Eye gain +1 Armour.</li>
        <li>To use Contraction ability simply click on the + and - buttons by its special icon, and then apply thrust as usual. Contraction lasts until the end of the turn and then resets to 0.</li>                                                         
    <h5>Shield Reinforcement</h5>
        <li>Mindrider ships equipped with this system can use its output to reinforce their own shields and the shields of ally ships with EM Shield properties 
            (e.g. reduced hit chance and damage reduction as well as usual Thought Shield damage absorbance).</li>
        <li>To do so, boost the Shield Reinforcement system to the level of reinforcement you'd like and then target ally ships in the Initial Orders phase.  
            All shields on a ship must be reinforced equally, at a cost of 1 output per level of reinforcement per shield (e.g. reinforcing a Thoughtforce vessel with 4 shields by 3 levels would cost 12 output, 3 * 4).  
            Any unspent output will instead be used to reinforce the vessel equipped with the Shield Reinforcement system.</li>      
    <h5>Second Sight</h5>
        <li>This weapon reduces the Initiative of all enemy ships on the following turn.  To fire the weapon select it during during the Firing phase and press 'Select' button.</li>      
    <h5>Thought Wave</h5>
        <li>Ballistic area effect weapon that's fired in Initial Orders turn by selecting it and pressing the 'Select' button. 
            It will attempt to strike all non-Mindrider ships in the game and has a special calculation for both its Hit Chance and Damage:
                <ul class="circle-list">
                    <li><strong>Hit Chance:</strong> - 15 + OEW + d20 roll - Range Penalty - DEW - Target's Initiative,</li>
                    <li><strong>Damage:</strong> - (3d6/3) * (Target Defence Profile/5),</li>                   
                </ul>            
            </li>
        <li>Note - For ships with Advanced Armour, the first half of this calculation is (3d6/5), vastly reducing the damage potential of the Thought Wave.</li>
        <li>Only successful attacks will be shown in the Combat Log.</li>      
    <h5>Ultra Pulse Cannon</h5>
        <li>The ultimate in pulse technology.  Can fire in three modes, Heavy (24 damage, D3 pulses), Medium (16 damage, D5 pulses), Light (12 damage, D6 pulses).</li>  
    <h5>Telekinetic Cutter</h5>
        <li>Fires two shots per turn, with high damage range.</li>  
    <h5>Trioptic Pulsar</h5>
        <li>Fires three shots, the main light weapon on Mindrider ships.</li> 
    <h5>Thought Projections</h5>
        <li>Mindriders use their psychic energy to create 'fighter' units called Thought Projections.</li>
        <li>These Projections must have a Mindrider ship with appropriate hangar capacity present in the game, or they will destabilise and drop out at the end of the turn where this is no longer the case.  
            E.g. two Thoughtforce vessels can sustain 24 Projections. 
            During a battle, if one of the Thoughtforce ships is destroyed then the number of Thought Projections that can be sustained would drop to 12, and any Projection over 12 would instantly drop out.</li>      
    <h5>Minor Thought Pulsar</h5>
        <li>Fighter weapon for Mindriders, will be automatically boosted with unused Thrust when fired in Firing phase (one boost for every 3 unused Thrust), instead of this being done manually as in Tabletop.</li>
        <li>The type of boost will be decided by the Firing Mode selected and includes bonuses to Rate of Fire, Damage, Hit Chance, or a combination of these three benefits.</li>                                               
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    
    <h4 id="shadows" style="">SHADOW ASSOCIATION</h4>
    <p>Below is a list of systems used by the Shadows, with short description of effect and any notable design differences from their original tabletop versions.</p>
    <h5>Ship Layouts</h5>
        <li>Even capital-sized Shadow ships use MCV ship layout and systems on Shadow ships cannot be targeted by called shots.</li>
    <h5>Energy Diffusers</h5>
        <li>Absorb damage as it's being dealt (and diffuses it over time). In FV the diffuser always kicks in if possible, there's no option to withhold its use 
            e.g. waiting for more threatening damage and conserving resources.</li>
        <li>For visual reasons tendrils are shown as separate, untargetable systems and damage absorbed by ship tendrils is not shown in the log.</li>
        <li>Diffusers will not try to stop no-overkill damage that would kill system hits despite Diffuser interference. This is important for very strong Matter or Piercing shots.
        Flash damage is treated in a special way - the portion of damage enough to destroy a system is treated as an entire shot which allows systems to be saved even if total damage of shot is very high.</li>    
    <h5>Bio-Drive</h5>
        <li>Shadow ships have omnidirectional thrusters that generate their own thrust. In FV, for technical reasons, these are represented by a series of untargetable thrusters with 
            unlimited rating alongside an untargetable engine with rating that’s the sum of all biothruster ratings. This set up allows any thrusters to provide thrust in any direction.</li>
        <li>Biothrusters have their own set of criticals (which reduce output) in place of regular thruster criticals.  The Bio-Drive cannot be boosted.</li>         
    <h5>Half Phasing</h5>
        <li>During the Movement Phase, Shadow ships can become semi-immaterial until the end of turn by Half-Phasing.</li>
        <li>While Half-Phased ships become much more difficult to hit (-20% penalty to hit from normal weapon fire, -40% from ballistics) but also becomes less accurate itself (-50% to hit on all shots). 
            In addition, they cannot ram or get rammed at all, unless both ships are half-phasing.</li>
        <li>Performing Half-Phasing costs full thrust from two undamaged BioThrusters, as well as the presence of an online, undamaged  Phasing Drive. 
            Any damage to Phasing Drive during half-phasing destroys the ship.</li>
    <h5>Pilot</h5>
        <li>Shadow ships have no crew, but they do have a pilot. The pilot is shown on ship windows as the C&C, with the main differences from regular C&C being:</li> 
                <ul class="circle-list">
                    <li>Pilot cannot suffer criticals, but feels pain when ship is damaged (temporary) or when they’re wounded,</li>
                    <li>Pilots cannot be repaired by a self-repair system.  Telepathic attacks on Shadow ships are not implemented in Fiery Void.</li>                   
                </ul>            
            </li>            
    <h5>Molecular Slicer Beam</h5>
        <li> Uninterceptable, armour-gnoring with a high FC and good range, it has very high damage output and can literally cut ships apart when fully charged.  
            It has a number of other traits too, some of which has been simplified for Fiery Void.</li>
        <li>The most powerful version of this weapon, the Heavy Slicer found only on Primordial ships uses Piercing damage as its default mode - which can even overkill at full power so that its damage potential isn’t wasted. 
            The Raking damage mode is capped at 2-turns charge level.</li>
        <li>All other Slicers use Sweeping Mode as their default, which allows them to target as many different enemies as it has damage dice, assigning a number of d10 target dice to each shot up to their maximum amount. Simply select the weapon and choose who you want to target in this mode, although each ship can only be targeted once (whilst fighter flights can be targeted multiple times). 
            When splitting shots, the weapon will attract a cumulative -5% penalty for every additional shot after the first, as well as any modifier for defensive shots (see below).</li>
        <li>Slicers may commit 1d10 damage dice to gain -10 intercept by clicking the 'Self-Intercept' green shield icon, each self-intercept dice committed in this way increases the number of different shots Slicer may intercept, as well as the total interception amount. 
            If you choose to fire offensively, or select any amount self-intercept dice, but do not use all your available dice, then any unspent dice will be added to intercept so long as you committed as least ONE self-intercept dice.  You can use ship tooltip to track defensive fire and remaining dice.  
            NOTE - Each self intercept dice commited counts as a 'shot' for the purposes of the -5% penalty detailed above.".</li>
            <li>Slicer can make Called Shots against fighters without any penalty.</li>                                                                 
    <h5>Phasing Pulse Cannon</h5>
        <li>Pulse weapons that completely ignore any kind of shield or shield-like defense used by Younger races. 
            Note that EM shields, like those used on the White Star, are treated as Ancient even when used on Younger Race ships.</li>      
    <h5>Vortex Disruptor</h5>
        <li>A weapon that destabilizes hyperspace vortexes, preventing enemy escape. 
            As FV has no actual hyperspace vortexes, the weapon is purely scenario-related - to be fired at hex where vortex opening is declared.</li>
        <li>The game will mechanically resolve the shot (e.g. calculate hit chance and report result in firing log) and show the result in the Combat Log.  
            The Vortex Disruptor will not function properly while half-phased (will be able to fire, but not hit)</li>          
    <h5>Primordial Variants</h5>
        <li>These are ships with Primordial and Additional Tendril enhancements added as standard, as neither of these options is available in FV.</li>      
    <h5>Primordial Battle Cruiser</h5>
        <li>This ship differs from modern, quick-grown, Shadow vessels in a few ways:
            <ul class="circle-list">
                <li>Most important is primary weapon firing arc: The Heavy Slicer on the Shadow Battlecruiser has both forward and rear arcs, and you can split your sweeping fire between both forward and rear arcs.</li>
                <li>Six separate Energy Diffusers would have been difficult to set up on PRIMARY in a clear way - therefore in FV they've been moved to appropriate sides and hit charts adjusted.</li>                   
            </ul>            
        </li> 
    <h5>Destroyer</h5>
        <li>By original design should have acceleration cost of 1.5, which is not possible in FV so has been set to 2 instead.</li>  
    <h5>Shadow Fighters</h5>
        <li>Shadow fighters are semi-autonomous slivers made from the hull material of their carriers.</li> 
        <li>For this reason when a Shadow fighter is purchased the Shadow player should either add 
        a Fighter Launched enhancement to the ship that ‘spawned’ the fighter (reducing its structure by 1 point per fighter), 
        or the fighter should have the Uncontrolled enhancement (a penalty for operating independently from the carrier - primarily for scenarios). The game will not force players to do so.</li>
        <li>Also, controlled/uncontrolled status cannot change during the game, so if a carrier is later destroyed it will not affect fighters in any way as it would with Mindriders for example.</li>      
        <li>Shadow fighters get a massive -100 dropout bonus  which means they'll pass all appropriate tests easily, 
            but can still be dropped out by weapons that explicitly cause dropouts without a test. Most weapons that do so are Electromagnetic in nature, and actually will be disregarded by Advanced Armor anyway. 
            But technically some Ancient weapons causing automatic dropouts might still affect a Shadow fighter. </li>
        <li>To take advantage of their resilience,through the combination of Energy Diffuser protection and dropout immunity, Shadow fighters have their own algorithm for hit allocation which will tend to spread damage among the flight more than usual.</li>
        <li>Shadow fighters are equipped with an accelerator weapon, for this reason it will not be used for interception without explicit order to do so!</li>     
        <a class="back-to-top" href="#top">↩ Back to Top</a>     
    
    
    <h4 id="thirdspace" style="">THIRDSPACE</h4>
    <p>The ‘Thirdspace aliens’ are a mysterious and terrifying race of malevolent telepaths from an alternate dimension, in possession of technology considered more advanced than even the Shadows or Vorlons.  
        While not indestructible, their ships are protected by a powerful energy shield that can absorb a significant amount of weapons fire before opponents can inflict damage on their ships themselves.</p>
    <p>In Fiery Void, they have been created as a Custom faction (with credit to Amras Arfeiniel for providing the original image for the Thirdspace Battleship) with the following key features:</p>    
    <h5>Thidspace Shields</h5>
        <li>Thirdspace shields absorb all incoming damage in the arc that they cover.  Once these shields have been reduced to zero rating damage will start to be inflicted on their ships as normal.</li>
        <li>During Initial Orders players can freely move shield power around via the Shield Generator. This system must be at 0 in order to commit your orders e.g. you can't 'save' shield energy in the generator for future turns. 
            The maximum amount you can allocate to any given shield is three times its base value.</li>
        <li>The Shield Generator also comes with a number of preset options you can click on to assist in moving shield energy around.  
            At the end of the turn, Shield Projectors will restore their respective shields by an amount based on their current rating.</li>
    <h5>Psychic Field</h5>
        <li>Thirdspace capital ships produce a passive Psychic Field which affects all enemies within range and debilitates opponents by applying debuffs on the following turn.  
            It reduces initiative for all types of unit and enemy ships will either suffer decreased hit chances if it impacts on the ship’s structure, or cause a possible critical effect when it hits any other system.  
            For fighters, it reduces their offensive bonus.</li>
        <li>Ancients are not immune to this effect but suffer only 50% of the effect compared to Younger races.</li>    
    <h5>Gravitic Drives</h5>
        <li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>                    
    <h5>Psionic Weapons</h5>
        <li>Thirdspace have a number of other Psionic weapons, which typically deal a large amount of electromagnetic damage.  
            Many also have additional effects on Younger races such as an increased critical hit/fighter dropout chance or a reduction in ship armour.</li>
        <li>Thirdspace heavy weapons, Psionic Lances, can be boosted with EW during Initial Orders phase to increase their damage.</li>
        <li>Their Psionic Torpedo deals a reasonable amount of damage, and in addition also causes temporary penalties to initiative, power and sensors (which are cumulative with the Psychic Field, but not with other Psionic Torpedoes).</li>
        <li>Thirdspace aliens make significant use of the flexible Psionic Concentrator weapon.  Which will fire four separate shots in in it's default firing mode.   
            This is very effective against fighters, but the weapon can also combine these four shots into two stronger double shots or even powerful, but short-ranged, single attack.</li>
    <h5>Advanced Singularity Reactor</h5>
        <li>Like the Ipsha, Thirdspace ships use a singularity reactor which produces a set amount of power, regardless of whether systems have been destroyed or not.  
            As Thirdspace ships utilise power management more heavily than other Ancients, to boost shields, engines or EW for example, it means they can continue to fight effectively even when taking damage.</li>                                                        
    <h5>Psychic Focus</h5>
        <li>In addition to boosting systems with power, the Thirdspace faction can channel their psychic energy (represented by EW points) into improving their weapon strength, self-repair systems and Psychic Field.</li>
    <a class="back-to-top" href="#top">↩ Back to Top</a>   
   
    <h4 id="torvalus" style="">TORVALUS SPECULATORS</h4>
    <p>The Torvalus are known for their highly advanced stealth technology as well as their love for making cosmic gambles. Below is a list of systems used by the Torvalus, with short description of effect and any notable design differences from their original tabletop versions.</p>    
    <h5>Laser Weaponry</h5>
        <li>Torvalus weaponry is entirely Laser-based, making it uninterceptable.  Whilst these weapons do not have an particularly unusual abilities per se, their high damage, flexibility and low cooldowns makes them very effective.</li>
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
        <li>The Shading Field operates the same as the Minbari's Jammer system (e.g. prevents weapons locks even when targeted with OEW), including against other Ancients as well as Younger Races.</li>
        <li>In addition the Shading Field has two states, Normal Mode and Shading Mode, which are described below:
            <ul class="circle-list">
                <li>Normal Mode- the Shading Field provides a 4-point EM Shield in all directions, which operates the same as Vorlon shields, but cannot be flown under by fighters.</li>
                <li>Shading Mode - During Deployment and Pre-Turn Order phases the Shading Field can be activated to make its vessel 'Shaded' that turn.  
                    Whilst Shaded the vessel retains the Jammer effect, doubles the EM shield rating to on its defence profile and, if it is more than 15 hexes away from all enemy units, it becomes invisible to opponents.
                    However, on a turn when a Torvalus ship is Shaded it will not be able to fire any of its weapons.</li> 
            </ul>
        </li>         
        <li>Torvalus Stiletto Fighters have a smaller version of the Shading Field equipped.  
            This can also be toggled on and off during Deployment and Pre-Turn Order phases like the ship version, but does not provide the Jammer effect in either mode.  
            In Shading Mode it reduces the profile of the fighter flight (by 15) and cannot be detected from more that 15 hexes away like ships.</li>         
    <h5>Shade Modulator</h5>
        <li>The Shade Modulator is a versatile Support Weapons found on the Veiled Scimitar, it has four different firing modes which are described below. 
            <ul class="circle-list">
                <li>Blanket Shield Enhancement - The Modulator increases the shield rating of ever Shading Field within 3 hexes by 1 point at a cost of 4 capacity.</li>
                <li>Individual Shield Enhancement - The Modulator increases the shield rating of a single ally by 1 point at a cost of 2 capacity.</li> 
                <li>Blanket Shade Enhancement - The Modulator lowers the defensive profile of all Shaded allied ships within 15 hexes by 5% at a cost of 2 capacity.</li>
                <li>Individual Shade Enhancement - The Modulator lowers the defensive profile of a single Shaded ally by 5% point at a cost of 1 capacity.</li>                                     
            </ul>
        </li>    
        <li>During the Firing Phase the Shade Modulator can be used on different allies in different modes, multiple times up to its maximum capacity.  For example, it could provide 1 point of Blanket Shield Enhancement, 
        as well as 1 point of Individual Shield Enhancement and 2 points of Individual Shade Enhancement on the same turn.</li>
        <li>Blanket firing modes are activated by clicking 'Select' when in that firing mode, whereas Individual modes require the targeting of a specific ally.</li>        
    <h5>Transverse Drive</h5>
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
        <li>When a ship makes a transverse jump it puts a strain on the Jump Drive (a separate system), and so a ship can suffer a catastrophic failure (e.g be destroyed) if the Jump Engine has sustained damage in the battle.
            The chance of this failure is the % of the Jump Drive's health that has been lost.
        </li>
        <li>Additional effects of making a Transverse Jump is that all ballistic weapons will suffer a cumulative -20% chance to hit per hex travelled.  
            However, the light produced by a jump makes Shaded ships easier to detect and these will be revealed to enemy ships within 20 hexes instead of the usual 15 hexes at the start of the Firing Phase.</li>                  
        <li>Transverse Drive also has some interesting critical effects when damaged, including the effect whereby the ships Jump Drive takes d3 damage.</li>                      
    <h5>Agile/Jinking Ships</h5>
        <li>Torvalus ships are exceptionally maneuverable and this is reflected in even their largest ships having the Agile characteristic.  
            In addition, their Medium Ships have the ability to jink, an ability normally reserved only to Fighters.</li>                                                       
    <h5>Gravitic Drives</h5>
        <li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>                           
    <a class="back-to-top" href="#top">↩ Back to Top</a>  


    <h4 id="vorlons" style="">VORLON EMPIRE</h4>
    <p>Below is a list of systems used by the Vorlons, with short description of effect and any notable design differences from their original tabletop versions.</p>    
    <h5>Capital Ship Layout</h5>
        <li>In B5 Wars, Vorlon capital ships, Lightning Cannons are usually tied to both the forward and relevant side sections, to prevent them from easily falling off. 
            In FV, they are fitted to virtual "Side-front" sections that are not directly hittable and do not have an actual structure system.</li>
        <li>‘Side-aft’ sections are, for all intents and purposes, the actual sides of the ship, and behave as such. Lightning Cannons are present on both Front and Side hit charts, as in tabletop - but Front has separate entries for Port and Starboard guns.</li>
        <li>Weapons on pseudo-sections can still be engaged by called shots from their own arc.</li>
    <h5>EM Shield</h5>
        <li>Works exactly as Gravitic Shield, but doesn't require a separate generator E.g. shields reduce both the hit chance and damage of incoming fire on their respective arcs by the value of their rating.  
            At range 0 fighters are assumed to be flying underneath the shields and effectively ignore them.</li>
    <h5>Adaptive Armor</h5>
        <li>Additional protection that effectively increases all armor ratings on a unit against specific types of damage.</li>
        <li>Pre-Assigned AA points can be set at the start of battle on Turn 1 only (if ship design allows - White Star is not advanced enough) or in the Initial Orders phase of later turns after receiving damage from a weapon type.</li>
        <li>Protection is limited both in the total amount of AA points available, and in the maximum value that can be assigned to a particular kind of damage.</li>
        <li>The AA system propagation button allows you to share AA settings amongst your entire fleet. Assigned points cannot be unassigned during battle.</li>
        <li>For fighters, every fighter can use different AA settings and AA points are unlocked for the entire flight (unless Super Heavy Fighter flight).</li>  
    <h5>Power Capacitor</h5>
        <li>Produces power for Vorlon ships and can hold Power between turns up to its maximum capacity.  Some key points to note about it are:
                <ul class="circle-list">
                    <li>New power is produced in the Initial Orders phase.  The power produced can be doubled (along with Self Repair at end of turn) at the cost of deactivating all weapons and shields on that turn.</li>
                    <li>Separately, you can also open petals on some Vorlon ships but this will reduce the armor on all of the ship’s primary systems by 2 and increase Defence Profiles by 5% for that turn, but adds 50% to power generation.</li> 
                    <li>Capacitor destruction would leave the ship powerless, but doesn't cause a catastrophic explosion like Reactor destruction.  In FV it will leave ship powerless (as the Capacitor is the main power source on Vorlon ships), 
                        add Power reduction critical to Reactor (so ship goes out of control) and Self Repair system so that the damage isn't just repaired in a few turns.</li> 
                    <li>Vorlon Fighters also have petals that they can toggle open in Initial Orders. Doing so provides 2 extra thrust that turn, but increases their Defence Profiles by 5% and reduces side armour by 2.</li>                                         
                </ul>            
            </li>             
    <h5>Mag-Gravitic Reactor </h5>
        <li>For technical reasons, Vorlons in FV are using Mag-Gravitic Reactor. This means destroying power-using systems will free appropriate power. 
            Note that technically Vorlons in FV do not have power using systems.</li>
            <li>Scanner uses no power: its operating costs are base hull features and do not affect power usage. 
                Vorlon Jump Drives also uses no power as their operating costs are base hull features and do not affect power usage.</li>
            <li>All Vorlon weapons have nominally 0 Power usage - they still technically need Power to actually shoot, but that is only decided and taken from Reactor at the moment of firing.</li>                                            
    <h5>Gravitic Drives</h5>
        <li>Allows ships to undertake manoeuvres even while pivoted/pivoting using thrusters appropriate for their current orientation.  
        Gravitic Thrusters also ignore the first "Efficiency Reduced" reduced critical they receive, increasing their resistance to damage.</li>                    
    <h5>Lightning Cannon</h5>
        <li>These have the accelerator property just so that the weapon requires explicit permission to intercept (and draw power). 
            It does not affect the weapon in any other way e.g. leaving it to recharge for two turns will not increase power of shot.</li>
        <li>Can combine Lightning Cannons for higher power shots. To do so, the appropriate number of cannons must have the same firing mode and target assigned. 
            If player mis-declares and not enough weapons are assigned for declared mode, shot automatically misses and does not drain power.</li>
    <h5>Discharge Gun & Discharge Cannon</h5>
        <li>LCan split shots amongst multiple targets up to four shots.  Can also split amongst offensive and defensive fire, with any manually selected intercept shots using minimal power if required 
            e.g. they actually try to intercept an incoming shot.</li>
        <li>Increased power levels are implemented as firing modes, so select this before targeting any enemy ships. Each offensive shot will be fired at the same power level.</li>                                                            
    <h5>Vorlon Primordial Fighters</h5>
        <li>Super Heavy Fighters that use regular hangars, with one Primordial fighter counting as two heavy fighters for fleet design/hangar purposes. 
            They are treated as rare variant of Heavy Fighters in FV except in Primordial times, where Assault Fighters should be treated as a common availability.</li>
    <h5>Skin Enhancements</h5>
        <li>Vorlon units can pick only one Skin Color enhancement (this will not be enforced by FV though, it's up to the player to comply with this rule). Summaries of these options are given below:
            <ul class="circle-list">
                <li>Amethyst Skin Coloring: Improves Adaptive Armor for players desiring more flexibility,</li>
                <li>Azure Skin Coloring: EM Shield improvements, for greater overall protection,</li> 
                <li>Crimson Skin Coloring: Power Capacitor improvement, for greater…power.</li>                  
            </ul>  
        </li>            
    <a class="back-to-top" href="#top">↩ Back to Top</a>        
 

<h3 id="otherfactions" style="">OTHER FACTIONS</h3>

    <h4 id="civilians" style="">CIVILIANS</h4>
    <p>Commonly available/generic civilian ships - or, if they're faction-specific civilians, they're noted as such in name. 
        Not a real faction, but rather a convenient place to store all the various non-combatant scenario units.</p>                                                                                     
    <a class="back-to-top" href="#top">↩ Back to Top</a> 

    <h4 id="streib" style="">STREIB</h4>
    <p>Very strangely balanced faction which relies on disruption more than straight-up damage.  Their strange tactics and increibly high armour makes them unsuitable for pick-up games.</p>                                                                                     
    <a class="back-to-top" href="#top">↩ Back to Top</a> 

    <h4 id="terrain" style="">TERRAIN</h4>
    <p>Not a real faction, just a convenient place to store all Terrain units for scenarios where you want to manually place these.</p>                                                                                     
    <a class="back-to-top" href="#top">↩ Back to Top</a> 


<h3 id="customfactions" style="">CUSTOM FACTIONS</h3>
    <p>These factions have been created by the community using the Fiery Void framework.  Some of them should be considered balanced against each other (e.g. Nexus units will be fairly balanced against other Nexus units), 
        but will unusually be unbalanced one way or another against the official B5 Wars factions.</p>         

    <h4 id="bsg" style="">Battlestar Galactica</h4>
    <p>The Battlestar Galactica ‘Colonials’ (Tier 2) and the ‘12 Colonies of Kobol’ (Tier 1) factions are parallel creations by Fred and Kirill respectively. 
        Still works in progress but fun to try out if you like the setting.</p>
    <p>These factions are characterized by good armor and bulkheads, providing significant durability. Weaponry is a mix of missiles and matter weapons. 
        The primary heavy weapons are nearly identical to the Belt Alliance's blast cannons. The Colonials also use rapid gatling railguns for close-in work.</p>    
    <h5>Flak Battery</h5>
        <li>This is very similar to the Grome flak cannon and has similar features including friendly intercept and flash mode damage. The primary difference is a maximum range of 5 hexes.</li>
    <h5>Fighters</h5>
        <li>The Colonial fighters use a pulse style weapon that has an alternate mode with a bonus to hit fighters in a very narrow forward arc.</li>
    <h5>Raptor</h5>
        <li>The Raptor super-heavy fighter is a support unit that provides +5 initiative to friendly Colonial fighters.</li>                            
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="customs" style="">CUSTOM SHIPS</h4>
    <p>A place to store customs ships from the Babylon 5 Wars setting, either because they are unofficial designs created by players or one-off campaign/scenario ships. Not a real faction.</p>                                                                                     
    <a class="back-to-top" href="#top">↩ Back to Top</a> 


    <h4 id="escalationwars" style="">ESCALATION WARS</h4>
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


    <h4 id="bloodsword" style="">BLOOD SWORD RAIDERS</h4>
        <p>The Blood Swords were a pirate group active during the Circasian "Raider Wars" time period. They were a serious threat to the trade routes near the Thaline node of the Outward sector trade routes due to the size and capabilities of their warships. 
            The raiders were eventually defeated by the Circasians, and their defeat marked the unofficial end of the Raider Wars.</p>
    <a class="back-to-top" href="#escalationwars">↩ Back to Escalation Wars</a>

    <h4 id="choukaraiders" style="">CHOUKA RAIDERS</h4>
        <p>This group of raiders operated in and around Chouka controlled territories and were directly financed by the Chouka Theocracy. 
            They were an early threat to the Circasian Empire as it expanded its own sphere of influence. These raiders operate cast-off Chouka military and civilian ships and fighters. 
            Their weapons are sourced from the Chouka, along with limited supplies from other black market sources. 
            Because of their ties to the Chouka government, these raiders are fairly well armed compared to the Circasian and Chouka fleets and are comparable to other low tech raiders in the B5 universe.</p>
    <a class="back-to-top" href="#escalationwars">↩ Back to Nexus Universe</a>

    <h4 id="chouka" style="">CHOUKA THEOCRACY</h4>
        <p>The Chouka Theocracy was a minor regional power in the Escalation Wars universe, and the Circasian's opponents in their first major interstellar war. 
            The Chouka were more technologically advanced than the Circasians, but lacked the tactical experience and fleet assets to combat the more numerous and maneuverable Circasian fleet.</p>
        <p>The ships included here are the Chouka spacecraft that served during the Chouka War period. They are armed with a mix of plasma and laser weapons, with older Chouka ships relying on plasma weapons while their newer ships rely on lasers in the anti-shipping role. 
            Plasma weapons continued to be used for anti-fighter work into the Chouka War period, however.</p>    
        <h5>Point Plasma Guns</h5>
            <li>A specialized, flak-style system to defend their ships. This can intercept for friendly units and is capable of intercepting lasers.</li>
        <h5>Gravitic Tracting Rod</h5>
            <li>This rare device was only found on the Apostle Holy Cruiser. It was used to tractor on to smaller craft and draw them closer to dock with the ship for boarding. 
                In Fiery Void, the Tracting Rod instead gives the target an initiative a thrust penalty.</li>
        <h5>Light Energy Mine</h5>
            <li>This is a smaller version of the Energy Mine used by the Narn. It has a shorter range (25 hexes) and does less damage (10/5) than the conventional Energy Mine.</li>                
    <a class="back-to-top" href="#escalationwars">↩ Back to Nexus Universe</a>

    <h4 id="circasian" style="">CIRCASIAN EMPIRE</h4>
        <p>The Circasians are one of the two central powers in the Escalation Wars milieu. These Dilgar-like humanoids started as a small minor power surrounded by other, more established empires but through political maneuvering and conquest they were able to eventually carve out a major imperium of their own. 
            The ships included here are low tech units the Circasians used during the Chouka War period. 
            These vessels are principally armed with light particle, plasma, and laser weapons. Anything more powerful is beyond their level of technology at the start of the war. </p>    
        <h5>Rockets</h5>
            <li>The Circasians made use of these primitive torpedo launchers on their early ships. These weapons are treated like Torpedoes and benefit from OEW.</li>
        <h5>Particle Lance</h5>
            <li>An avenue of advanced R&D before the war, this weapon was an attempt to develop a heavier version of the Light Particle Cannon. 
                Like the Gravitic Lance, this weapon is capable of firing as one more powerful weapon or as two weaker Light Particle Cannons.</li>              
    <a class="back-to-top" href="#escalationwars">↩ Back to Nexus Universe</a>

    <h4 id="kastan" style="">KASTAN IMPERIAN MONARCHY</h4>
        <p>The Kastan inhabit a remote location of hyperspace deep within the Rapids of Rodirra, a treacherous section of hyperspace that makes travel to or from the Kastan territories difficult at best (and impossible at worst). 
            Navigating the rapids has forced the Kastan to be superb pilots.</p>
        <p>The Kastan have had limited interactions with their neighbours, although they do engage in trade and have been known to sell out their services as mercenaries. 
            Most of their active political ambitions have focused on the coreward sectors, having fought back against an invasion by the Ingalli in recent history.</p>    
        <h5>Laser Bolt</h5>
            <li>The Kastan developed an effective short range anti-fighter laser weapon for their ships.</li>
        <h5>Pulse Torpedo</h5>
            <li>This weapon fires a number of small munitions at a target, designed to strip targets of weapons and external systems prior to closing to close range.</li>               
    <a class="back-to-top" href="#escalationwars">↩ Back to Nexus Universe</a>
    
    <h4 id="sshelath" style="">SSHEL'ATH ALLIANCE</h4>
        <p>The Sshel'ath Alliance was formed out of the civil war between the two largest Sshel'ath factions. The early Alliance focused on growth of the homeworld, as they were blocked by the more established Chouka Theocracy and Novon Trade Lords. Two conflicts with the Novon showed that action against this faction  was ill-advised. 
            It was not until the later Circasian defeat of the Chouka that the Sshel'ath managed to expand into and capture several Chouka worlds.</p>
        <p>The Sshel'ath themselves are made of intertwined mass of rope-like elastic fibers, which allow for limited rearrangement of their body's form. 
            Only their faces are inelastic and they are highly resistant to radiation.</p>    
        <h5>Gatling Laser</h5>
            <li>Evolved from the basic light laser cannon, the gatling laser fires several short, discreet volleys in pulse mode.</li>
        <h5>Electromagnetic Torpedo</h5>
            <li>The EM Torpedo was developed to counter Novon shields. This ballistic weapon releases a large electromagnetic pulse and can destroy or short out enemy systems.</li>
        <h5>Electron Polarizer</h5>
            <li>A flash mode electromagnetic weapon, this can destroy or short out numerous systems on a target.</li>                
    <a class="back-to-top" href="#escalationwars">↩ Back to Nexus Universe</a>    




    <h4 id="nexus" style="">NEXUS UNIVERSE</h4>
    <p>The Nexus setting is designed by Jeremy and Geoffrey Stano. 
        The current factions in Fiery Void (and described below) are the Brixadii Clans, Union of Craytan States, Dalithorn Commonwealth, 
        Federation of Makar, Sal-bez Coalition, and the Velrax Republic. 
        The Polaren should make their first appearance by the end of 2025. </p>
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
            <li><a href="#salbez">SAL-BEZ COALITION</a></li>   
            <li><a href="#velrax">VELRAX REPUBLIC</a></li>                                                                      
        </ul>     
    <a class="back-to-top" href="#top">↩ Back to Top</a>

    <h4 id="brixadii" style="">BRIXADII CLANS</h4>
        <p>The Brixadii Clans are a spinward power within the Nexus sector, having fought with the Dalithorn Commonwealth early on and having hostile relations with the Velrax Republic.</p>
        <p>Brixadii's technology is primarily focused around particle weapons. These are rugged, easy to repair, and low mass. 
            The Clans have begun to specialize in pulsar technology as this affords superior mass to damage ratios. This last point is important as the key feature that defines the Clans are their extremely maneuverable ships. 
            They have few longer-ranged weapons, but prefer high-speed firing passes or getting in close and using maneuverability to get advantageous shots. 
            They do not field fighters and therefore rely on their rapid firing weapons, wide arcs, and light combat vessels to deal with fighters.</p>

        <h5>Chaff Launcher</h5>
            <li>Technically fired at an enemy ship, it will always hit (causing no damage or adverse effects) and apply interception to ALL fire from target hex at chaff-protected unit (including nominally uninterceptable weapons).</li>
        <h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Jump War (up through the 2050s).</li>
                <li>Tech Level 2: Jump War era through the War of Ascendancy (2050s through the 2110s).</li> 
                <li>Tech Level 3: War of Ascendancy through the First War of Control (2110s through 2150s).</li>                  
            </ul>  
      <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="crayton" style="">UNION OF CRAYTON STATES</h4>
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
            <li>The larger versions are designed to fire a small, dense projectile with enough velocity to generate a piercing effect. 
                It suffers from a slow rate of fire and limited arcs, but it is the longest ranged weapon available to the Craytan. It has remained in service due to its simplicity. 
                The light version fires faster and has wider arcs, but loses the piercing ability due to a slower projectile.</li>

        <h5>Close-In Defense System (CIDS)</h5>
            <li>This, along with an advanced version (ACIDS), operates much like a Scattergun. Each does a random number of shots (4 or 6) and only one shot can intercept an incoming shot. This represents the CIDS firing a tremendous volume of small projectiles. 
                It relies on volume of fire over specifically aiming at a target.</li>
                
        <h5>Enhanced Plasma</h5>
            <li>As the Craytan advanced they developed multiple improvements to the standard plasma cannon. These enhanced plasma cannons have superior range, rate of fire, and damage loss compared to standard plasma cannons. 
                They are an intermediary step between plasma cannons and plasma bolters. </li>                 

        <h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Craytan War (up through 2105).</li>
                <li>Tech Level 2: Craytan War era through the Daybreak War era (2090s through 2130s).</li> 
                <li>Tech Level 3: Daybreak War era through the First War of Control (2130s through 2150s).</li>                  
            </ul>  
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="dalithorn" style="">DALITHORN COMMONWEALTH</h4>
        <p>The Dalithorn Commonwealth are likely the lowest tech of the main Nexus powers. 
            They managed to leverage their position across a major trade route to keep their sovereignty up to the later years of the First War of Control in the 2150s.</p>
        <p>They primarily use terrestrial weapons converted for use in space combat. While individually weak, many of the weapons are treated as matter weapons, allowing the Dalithorn to cause damage against most targets. 
            When desired, many of the smaller weapons have a concentrated mode that produces a few, harder hitting shots, at the expense of some fire control. 
            This is often useful against shielded targets. While simple, a number of their weapons are still effective, such as the gas gun. 
            The Commonwealth does not use fighters in the traditional sense. They only field super-heavy fighters, but these are typically slow. 
            However, their size and armament allow them to serve as area of denial platforms against enemy fighters or as serviceable anti-ship units.</p>    
        <h5>Protector</h5>
            <li>A specialized, flak-style system to defend their ships. This can intercept for friendly units and is capable of intercepting lasers.</li>

        <h5>Laser Missile</h5>
            <li>A ballistic weapon that triggers a bomb-pumped laser near the target. It is intercepted as a normal, non-ballistic as the target attempts to shoot down the missile before it triggers.</li>
 
        <h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Jump War (up through the 2050s).</li>
                <li>Tech Level 2: Jump War era through the War of Ascendancy era (2050s through the 2110s).</li> 
                <li>Tech Level 3: War of Ascendancy through the First War of Control (2110s through th 2150s).</li>                  
            </ul>              
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="makar" style="">FEDERATION OF MAKAR</h4>
        <p>The Makar Federation is made up of two species from the homeworld of Makar; a blend of 'home' from both languages. The Federation consists of the land dwelling Hitat and the squid-like Qom Yomin. 
            The Hitat make up the majority of the Federation's combat forces, but the Qom Yomin contribute a variety of support units. Additionally, being aquatic, Qom Yomin ships have larger ramming values if they are rammed. 
            The Makar have been shaped by the Reshmiyar Invasion and their subsequent rebellion that took place early in their space faring history.</p> 

        <h5>Water Caster</h5>
            <li>Qom Yomin vessels have the ability to spray some of their onboard water to intercept incoming shots. This functions much like a plasma web, but with no offensive ability.</li>

        <h5>Plasma Charge</h5>
            <li>Another special Qom Yomin weapon. Qom Yomin ships have low thrust, but extra power. This can be used to charge this weapon or to increase thrust.</li>
        
        <h5>Drones</h5>
            <li>Except for a couple exceptions, the Makar use armed drones as fighters and hunter-killers. These are almost always on Qom Yomin vessels.</li> 

        <h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Jump War (up through the 2050s).</li>
                <li>Tech Level 2: Jump War era through the War of Ascendancy era (2050s through the 2110s).</li> 
                <li>Tech Level 3: War of Ascendancy through the First War of Control (2110s through th 2150s).</li>                  
            </ul> 
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="salbez" style="">SAL-BEZ COALITION</h4>
        <p>The Sal-bez Coalition is an insectoid race that started as a typical queen / worker societ, but during the Enlightenment of Fire, the Sal-bez won their freedom. They are now a fiercely individualistic society. 
            The Coalition did not have a traditional military upon reaching for the stars and their early units are exclusively civilian designs using industrial systems as makeshift weapons.</p>
        <p>As the Sal-bez grew (and discovered several technology caches from a previous power), they began to field purpose-built combat vessels that would ultimately lead to the Coalition becoming one of the most powerful of the Nexus factions. 
            If you are used to using the traditional B5 powers, the Sal-bez are the most similar without a lot of different systems or rules.</p>     

        <h5>Swarm Torpedo</h5>
            <li>One of the most iconic systems developed by the Sal-bez for their later generation of ships. It fires multiple, small ballistics at a target, which has led to its common name of swarm torpedo. 
                This system provides both long-range firepower and can strip systems off a target to enable more effective damage from follow-up laser hits.</li>

        <h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through the Craytan War (up through 2105).</li>
                <li>Tech Level 2: Craytan War era through the Polaren Conflict (2090s through the 2120s).</li> 
                <li>Tech Level 3: Polaren Conflict era through the First War of Control (2120s through the 2150s).</li>                  
            </ul> 
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>

    <h4 id="velrax" style="">VELRAX REPUBLIC</h4>
        <p>The Velrax Republic is potentially the most aggressive faction within the Nexus setting. 
            They engaged the Brixadii Clans, Dalithorn Commonwealth, and Makar Federation by the early 2100s. 
            The Velrax are the most prolific users of fighters, which are supported with a rigid fleet structure.</p>
        <p>The Velrax field well armed "strike" carriers supported by anti-ship focused "gunships". Everything revolves around supporting their fighter operations. 
            The carriers utilize plasma waves to break up enemy forces at range, and gunships with plasma and lasers to single out anti-fighter units. 
            Also, some Velrax carry plasma arcs used to strip armor off their targets to make them more vulnerable to laser and fighter fire. 
            Their heavy fighter mounts a potent anti-ship gun that was retrofitted to some of the interceptor fighters when they realized that their opponents did not use fighters extensively.</p>     

        <h5>Plasma Arc</h5>
            <li>Small, raking plasma weapon that is designed to strip armor from the target.</li>
        
        <h5>Dart/Streak Interceptors</h5>
            <li>These are small torpedoes that can accept OEW and are used to engage enemy fighters.</li>

        <h5>Tech Levels</h5>
            <ul class="circle-list">
                <li>Tech Level 1: Pre-contact through expansion era (up through 2050s).</li>
                <li>Tech Level 2: Expansion era through the War of Ascendancy (2050s through 2110s).</li> 
                <li>Tech Level 3: War of Ascendancy era through the First War of Control (2110s through 2150s).</li>                  
            </ul> 
    <a class="back-to-top" href="#nexus">↩ Back to Nexus Universe</a>



    <h4 id="startrek" style="">STAR TREK</h4>
    <p>Star Trek units, as designed by Wolfgang Lackner-Warton and Marcin Sawicki, based on original conversion by Tyrel Lohr.</p>
    <p>Due to vast timeline (and technology) differences between the series, every faction is (or at least might be) split into 3 sub-factions, 
        representing distinct arbitrary "eras": early (eg. Enterprise series), TOS (eg. original series) and TNG (eg. The Next Generation, Voyager, Deep Space Nine...).</p>  
    <p>StarTrek units utilize a common set of concepts and technologies, which make them behave differently from their B5 universe counterparts:</p>
    <h5>Impulse Drives</h5>
        <li>StarTrek units utilize a distinct method of generating and using thrust for performing maneuvers. 
            While they do possess an engine (which may get damaged or destroyed as usual, and which allows for overthrusting), its base output is very low (even 0 in some cases). 
            However, each Warp Nacelle also generates thrust - with their output being summed up and displayed as Engine output for the player. Application of said thrust is free (eg. no thrusters are required) 
            - FV ships do possess thrusters (which have unlimited capacity and cannot be damaged in any way) for technical reasons only.</li>
        <li>Besides generating thrust, Warp Nacelles are also jump drive equivalents (so essentially every ST ship - and some small craft as well - can perform FTL travel on their own). 
            Nacelle criticals cannot reduce their output below 0, but Engine criticals can 
            - also, if Engine is destroyed, the entire maneuvering system ceases to function (even if Nacelles are still operational).</li>              
    <h5>Star Trek Shileds</h5>    
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
    <h5>Independent Fighters</h5>
        <li>While most StarTrek fighters/shuttles require hangar slots to be deployed (just like their B5 counterparts do), some of them possess Warp Drive of their own, as well as high endurance. Such craft do not require hangars, despite being fighter-sized. 
            They are affected by "number of ships on a given hull limit" (with each flight counted as one "ship"), and usually are marked as Restricted Deployment.</li>
   <h5>Self-reliable LCVs</h5>
        <li>Most StarTrek LCV-sized ships are fully capable of operating on their own, do not require hangars and have regular sensors (rather than restricted B5 "LCV Sensors").</li>    
   <h5>Militarized Shuttles</h5>   
        <li>Most StarTrek factions don't use dedicated fighters. However, their shuttles are usually armed and combat capable if necessary. Ships of these factions do have shuttle capacity listed (which is usually omitted in FV) and have combat shuttle designs available. 
            Combat shuttles are not fighters, and as such are not necessary if the player doesn't wish to deploy them.</li>

    <h4 id="startrekfederation" style="">STAR TREK: FEDERATION</h4>            
    <p>The United Federation of Planets is a conglomerate of multiple separate species. They remain independent enough to operate their own warships, built to different specifications - which is marked in fleet list. This is NOT, however, intended as a fleet composition restriction
         - Federation fleet on a field of battle may use any mix of units desired (although small craft should be based on carriers of the same species).</p>  
    <a class="back-to-top" href="#top">↩ Back to Top</a>

     

    <h4 id="starwars" style="">STAR WARS</h4>
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
    
    <h4 id="starwarsclone" style="">STAR WARS: CLONE WARS ERA</h4>
    <p>This is a separate take on implementing Star Wars units to play against B5 fleets. This is deliberately focusing on the Clone Wars era so as to not cause confusion with the existing Star Wars work.</p>
    <p>The approach here is that Star Wars ships are typically seen firing rapidly (and technical manuals show they have a lot of weapons). The approach here is to use faster firing, but overall lower damage weapons.
    Weapons typically come in single, twin, and quad configurations. As turbolasers, these fire every other turn, once per turn, and twice per turn, respectively. Shields use the B5 Wars EM shield rules. Ion weapons use Burst Beam effects.</p>                                                                                           
    <a class="back-to-top" href="#top">↩ Back to Top</a>          



<h3 id="tiers" style="">TIER RATINGS</h3>  
    <p>The factions of Fiery Void have placed into Tiers by the community.  These are subjective and not set in stone, but provided simply to manage expecations on the relative power of one faction against another.</p>

    <h4 id="tier1" style="">TIER 1 - HIGH COMPETITIVENESS</h4>
    <p>This is the default power level of show era major factions ('Big 4') - more or less the 'baseline' of competitive play.</p>
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
    <a class="back-to-top" href="#top">↩ Back to Top</a>           
       
    <h4 id="tier2" style="">TIER 2 - MEDIUM COMPETITIVENESS</h4>
    <p>Factions that point for point will have a difficult time competing against Tier 1 opponents, yet otherwise are balanced and competitive. 
        Many of them have weaknesses more pronounced than Tier 1 factions but may offer a very distinct playstyle instead. 
        Should be interesting to play against each other, and if given some extra PV allowance - possibly against Tier 1 as well.</p>           
        <li>Abbai Matriarchate</li>
        <li>Balosian Underdwellers</li>
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
    <a class="back-to-top" href="#top">↩ Back to Top</a>    

    <h4 id="tier3" style="">TIER 3 - LOW COMPETITIVENESS </h4>
    <p>Factions that point for point will have uphill battle against Tier 2, and quite often have glaring holes in their capabilities that can easily be exploited by a more advanced opponent. 
        Yet, they can be competitive against each other, and can offer an interesting change of pace compared to more capable factions.</p>    
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
    <a class="back-to-top" href="#top">↩ Back to Top</a>    

    <h4 id="unbalanced" style="">UNBALANCED FLEETS</h4>
    <p>Of course, in a game as complex as Fiery Void, there was always going to be certain factions, or unit combinations, that are just too unbalanced to provide for a enjoyable pick-up game.  
        Below is a list that is considered by the community to fall into that category:</p>
       <li>Ancients (see 'Ancients' section),</li>        
       <li>Alacan (with Rotia swarms),</li>
       <li>Earth Alliance when using Warlock Advanced Destroyer or Shadow Omega Destroyer,</li>
       <li>Geoff,</li>         
       <li>Interstellar Alliance's all-White Star fleet,</li>
       <li>Narn Regime, where over 6 Energy Mines are taken,</li>
       <li>Sorithian, as they are simply too weak,</li>
       <li>Streib, as they are not really designed for normal competitve play,</li>
       <li>Yolu without points reevaluation</li>  
    <a class="back-to-top" href="#top">↩ Back to Top</a>    

  </section>
</main>
</body>
</html>