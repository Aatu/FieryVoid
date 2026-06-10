<?php
require_once("FighterFlight.php");

/* Generic single-craft shuttle. Used to auto-populate empty hangar slots
 * (see HangarOps::populateInitialHangarUsage) and as the base class for
 * specialised shuttles (e.g. MinesweepingShuttle, Flyer).
 *
 * Per B5W §10.1: "shuttles may be operated from any fighter box, but under
 * no circumstances may any fighter replace a shuttle." Shuttles are not
 * combat units and don't count toward fleet point totals — $isCombatUnit
 * is set false (existing flag, see civilians/skylark.php for prior art).
 *
 * Image resolution goes through getImage(), which keys off $this->faction.
 * That lets faction-flavoured art (e.g. Minbari Flyer) be picked up either
 * by a dedicated subclass that sets $this->faction in setShuttleDefaults
 * (preferred for stat-different variants like Flyer) or by adding a faction
 * branch to getImage()'s switch (cheaper for image-only variants).
 */
class Shuttle extends FighterFlight
{
    public $shuttle = true;       //distinguishes shuttles from non-combat freighters
    public $maxFlightSize = 6;    //shuttles can launch in flights of 1-6 (stored 1-per-record in $hangarUsage)

    function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
        $this->setShuttleDefaults();   //subclass can override to change phpclass / faction / stats
        //Resolve image AFTER setShuttleDefaults so subclass faction overrides are honoured.
        list($this->imagePath, $this->iconPath) = $this->getImage();
        $this->populate();             //populate() uses $this->phpclass + $this->imagePath set above
    }

    /* Override in subclasses to change phpclass / faction / stats.
     * Called before getImage(), so set $this->faction here to get the right
     * art automatically. Do NOT set $this->imagePath / $this->iconPath here
     * unless your subclass overrides getImage() to bypass the faction map.
     */
    protected function setShuttleDefaults()
    {
        $this->pointCost = 0;                     //auto-populated, never bought
        $this->isCombatUnit = false;              //skipped by fleet builder / battlegroup checks
        $this->faction = "Generic";
        $this->phpclass = "Shuttle";
        $this->shipClass = "Shuttle";

        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 3;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2;                     //shuttles pivot slowly
        $this->turncost = 0.33;
        $this->minesweeper = false; //denotes if shuttle is a minesweeping type

        $this->hangarRequired = 'shuttles';       //matches "shuttles" key in carrier $fighters
        $this->iniativebonus = 9 * 5;             //slow
    }

    /* Returns array($imagePath, $iconPath) for this craft. Default lookup
     * keys off $this->faction so the base Shuttle / MinesweepingShuttle
     * classes can show faction-flavoured art without a dedicated subclass.
     * Subclasses with their own art (or stat-different variants like Flyer)
     * may instead just set $this->faction in setShuttleDefaults and let
     * this switch pick the right files.
     */
    public function getImage()
    {
        switch ($this->faction) {
            case 'Abbai Matriarchate':  
            case 'Abbai Matriarchate (WotCR)':    
                return array('img/ships/ShuttleAbbai.png', 'img/ships/ShuttleAbbai_large.png');  
            case 'Centauri Republic':
            case 'Centauri Republic (WotCR)': 
                return array('img/ships/ShuttleCent.png', 'img/ships/ShuttleCent_large.png'); 
            case 'Dilgar Imperium': 
                return array('img/ships/shuttleDilgar.png', 'img/ships/ShuttleDilgar_large.png');                                                                         
            case 'Minbari Federation':
            case 'Minbari Protectorate':
                return array('img/ships/MinbariFlyer.png', 'img/ships/MinbariFlyer_Large.png');
            case 'Narn Regime':
                return array('img/ships/ShuttleNarn.png', 'img/ships/ShuttleNarn_large.png'); 
            case 'Orieni Imperium':
                return array('img/ships/shuttleOrieni.png', 'img/ships/shuttleOrieni_large.png');
            case 'Raiders':
                return array('img/ships/shuttleRaiders.png', 'img/ships/shuttleRaider_large.png');                                                     
            default:
                return array('img/ships/shuttle.png', 'img/ships/shuttle_large.png');
        }
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 10, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;

            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}

/* Minbari Flyer — the Minbari Federation / Minbari Protectorate equivalent
 * of the generic Shuttle. Gravitic with higher base thrust. Selected for
 * Minbari carriers by HangarOps::factionShuttleClass during initial hangar
 * population; the inherited getImage() picks the MinbariFlyer art via the
 * faction switch above.
 */
class Flyer extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "Flyer";
        $this->shipClass = "Cargo Flyer";
        $this->faction = "Minbari Federation";
        $this->forwardDefense = 9;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->gravitic = true;
        $this->iniativebonus = 10 * 5;             //slow        
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 16, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;

            $fighter->addAftSystem(new Jammer(0, 1, 0));	            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}



class FlyerProtectorate extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "FlyerProtectorate";
        $this->shipClass = "Cargo Flyer";
        $this->faction = "Minbari Protectorate";
        $this->forwardDefense = 9;
        $this->sideDefense = 7;
        $this->freethrust = 10;
        $this->gravitic = true;
        $this->iniativebonus = 10 * 5;             //slow        
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 16, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


/* Minesweeping shuttle variant. Auto-populated when a carrier declares
 * 'minesweeping shuttles' in $fighters, or when leftover hangar capacity
 * exists on a ship with $minesweeperbonus > 0. Kept faction-agnostic at
 * present — getImage() inherits Shuttle's switch, so if a future launch
 * path passes a carrier faction through, faction-flavoured minesweeping
 * art will follow automatically.
 */
class MinesweepingShuttle extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "MinesweepingShuttle";
        $this->shipClass = "Minesweeping Shuttle";
        $this->hangarRequired = 'minesweeping shuttles';
        $this->offensivebonus = 4;
        $this->minesweeper = true;
    }
}

class CargoShuttle extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "CargoShuttle";
        $this->shipClass = "Cargo Shuttle";
        $this->hangarRequired = 'cargo shuttles';
        $this->offensivebonus = 0;
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        $this->freethrust = 3;        
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 24, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class MedicalShuttle extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "MedicalShuttle";
        $this->shipClass = "Medical Shuttle";
        $this->hangarRequired = 'medical shuttles';
        $this->faction = "Markab Theocracy";        
        $this->offensivebonus = 0;
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        $this->freethrust = 3;        
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 13, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}

class ShuttleAbbai extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleAbbai";
        $this->shipClass = "Shuttle";
        $this->faction = "Abbai Matriarchate";
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 9, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleAlacan extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleAlacan";
        $this->shipClass = "Shuttle";
        $this->faction = "Alacan Republic";
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 7, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleBalosian extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleBalosian";
        $this->shipClass = "Shuttle";
        $this->faction = "Balosian Underdwellers";
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter($this->phpclass, $armour, 10, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleBelt extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleBelt";
        $this->shipClass = "Shuttle";
        $this->faction = "Belt Alliance";
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 10, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleBrakiri extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleBrakiri";
        $this->shipClass = "Shuttle";
        $this->faction = "Brakiri Syndicracy";
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 9, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleCascor extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleCascor";
        $this->shipClass = "Shuttle";
        $this->faction = "Cascor Commonwealth";
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 6;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 7, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}

class ShuttleCent extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleCent";
        $this->shipClass = "Shuttle";
        $this->faction = "Centauri Republic";
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 10, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleCentWotCR extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleCentWotCR";
        $this->shipClass = "Shuttle";
        $this->faction = "Centauri Republic";
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 9, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleCorillani extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleCorillani";
        $this->shipClass = "Shuttle";
        $this->faction = "Corillani Theocracy";
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 6;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 8, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleDeneth extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleDeneth";
        $this->shipClass = "Shuttle";
        $this->faction = "Deneth Tribes";
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 10, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleDescari extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleDescari";
        $this->shipClass = "Shuttle";
        $this->faction = "Descari Committees";
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 8, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleDilgar extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleDilgar";
        $this->shipClass = "Shuttle";
        $this->faction = "Centauri Republic";
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 5;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 8, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleDrazi extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleDrazi";
        $this->shipClass = "Shuttle";
        $this->faction = "Drazi Freehold";
        $this->forwardDefense = 8;
        $this->sideDefense = 9;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 9, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleDraziWotCR extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleDraziWotCR";
        $this->shipClass = "Shuttle";
        $this->faction = "Drazi Freehold (WotCR)";
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 10, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleEA extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleEA";
        $this->shipClass = "Shuttle";
        $this->faction = "Earth Alliance";
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

}


class ShuttleGaim extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleGaim";
        $this->shipClass = "Shuttle";
        $this->faction = "Gaim Intelligence";
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 10, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


class ShuttleGrome extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleGrome";
        $this->shipClass = "Shuttle";
        $this->faction = "Grome Autocracy";
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 9, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleHurr extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleHurr";
        $this->shipClass = "Shuttle";
        $this->faction = "Hurr Republic";
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 9, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleHyach extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleHyach";
        $this->shipClass = "Shuttle";
        $this->faction = "Hyach Gerontocracy";
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 13, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleIpsha extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleIpsha";
        $this->shipClass = "Shuttle";
        $this->faction = "Ipsha Baronies";
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 5;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 10, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleKL extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleKL";
        $this->shipClass = "Shuttle";
        $this->faction = "Kor-Lyan Kingdoms";
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 8, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleLlort extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleLlort";
        $this->shipClass = "Shuttle";
        $this->faction = "Llort";
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter($this->phpclass, $armour, 9, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleMarkab extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleMarkab";
        $this->shipClass = "Shuttle";
        $this->faction = "Markab Theocracy";
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 9, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleNarn extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleNarn";
        $this->shipClass = "Shuttle";
        $this->faction = "Narn Regime";
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 8, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}


class ShuttleOrieni extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleOrieni";
        $this->shipClass = "Shuttle";
        $this->faction = "Centauri Republic";
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 8, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


class ShuttlePakMaRa extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttlePakMaRa";
        $this->shipClass = "Shuttle";
        $this->faction = "Pak'ma'ra Confederacy";
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 6;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 9, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


class ShuttleRogolon extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleRogolon";
        $this->shipClass = "Shuttle";
        $this->faction = "Rogolon Dynasty";
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 8, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }

}



class ShuttleTorata extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleTorata";
        $this->shipClass = "Shuttle";
        $this->faction = "Torata Regency";
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 5;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(0, 0, 0, 0);
            $fighter = new Fighter($this->phpclass, $armour, 11, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


class ShuttleUsuuth extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleUsuuth";
        $this->shipClass = "Shuttle";
        $this->faction = "Usuuth Coalition";
        $this->forwardDefense = 8;
        $this->sideDefense = 10;
        $this->freethrust = 3;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 8, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


class ShuttleVorlons extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleVorlons";
        $this->shipClass = "Shuttle";
        $this->faction = "Vorlon Empire";
        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 6;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(4, 4, 4, 4);
            $fighter = new Fighter($this->phpclass, $armour, 12, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


class ShuttleVree extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleVree";
        $this->shipClass = "Shuttle";
        $this->faction = "Vree Conglomerate";
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 4;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter($this->phpclass, $armour, 7, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


class ShuttleYolu extends Shuttle
{
    protected function setShuttleDefaults()
    {
        parent::setShuttleDefaults();
        $this->phpclass = "ShuttleYolu";
        $this->shipClass = "Shuttle";
        $this->faction = "Yolu Confederation";
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->iniativebonus = 9 * 5;             
    }

    public function populate()
    {
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++) {
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter($this->phpclass, $armour, 12, $this->id);
            $fighter->displayName = $this->shipClass;
            $fighter->imagePath = $this->imagePath;
            $fighter->iconPath = $this->iconPath;
            
            $fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0));

            $this->addSystem($fighter);
        }
    }
}


/* === Faction minesweeping shuttle variants ===========================
 * Each extends its faction default shuttle (inheriting that faction's defence,
 * thrust, art and populate()) and layers on the minesweeping behaviour.
 * Resolved by HangarOps::factionMinesweepingShuttleClass(); generic
 * MinesweepingShuttle (above) is the fallback for unlisted factions. */
class MinesweepingShuttleAbbai extends ShuttleAbbai { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleAbbai"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleAlacan extends ShuttleAlacan { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleAlacan"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleBalosian extends ShuttleBalosian { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleBalosian"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleBelt extends ShuttleBelt { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleBelt"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleBrakiri extends ShuttleBrakiri { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleBrakiri"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleCascor extends ShuttleCascor { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleCascor"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleCent extends ShuttleCent { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleCent"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleCentWotCR extends ShuttleCentWotCR { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleCentWotCR"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleCorillani extends ShuttleCorillani { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleCorillani"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleDeneth extends ShuttleDeneth { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleDeneth"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleDescari extends ShuttleDescari { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleDescari"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleDilgar extends ShuttleDilgar { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleDilgar"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleDrazi extends ShuttleDrazi { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleDrazi"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleDraziWotCR extends ShuttleDraziWotCR { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleDraziWotCR"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleEA extends ShuttleEA { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleEA"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleGaim extends ShuttleGaim { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleGaim"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleGrome extends ShuttleGrome { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleGrome"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleHurr extends ShuttleHurr { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleHurr"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleHyach extends ShuttleHyach { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleHyach"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleKL extends ShuttleKL { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleKL"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleLlort extends ShuttleLlort { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleLlort"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleMarkab extends ShuttleMarkab { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleMarkab"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingFlyer extends Flyer { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingFlyer"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingFlyerProtectorate extends FlyerProtectorate { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingFlyerProtectorate"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleNarn extends ShuttleNarn { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleNarn"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleOrieni extends ShuttleOrieni { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleOrieni"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttlePakMaRa extends ShuttlePakMaRa { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttlePakMaRa"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleRogolon extends ShuttleRogolon { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleRogolon"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleTorata extends ShuttleTorata { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleTorata"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleUsuuth extends ShuttleUsuuth { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleUsuuth"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleVorlons extends ShuttleVorlons { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleVorlons"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleVree extends ShuttleVree { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleVree"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }
class MinesweepingShuttleYolu extends ShuttleYolu { protected function setShuttleDefaults(){ parent::setShuttleDefaults(); $this->phpclass="MinesweepingShuttleYolu"; $this->shipClass="Minesweeping Shuttle"; $this->hangarRequired='minesweeping shuttles'; $this->offensivebonus=4; $this->minesweeper=true; } }

