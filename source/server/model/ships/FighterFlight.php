<?php
require_once("ShipClasses.php");

class FighterFlight extends BaseShip
{
    public $shipSizeClass = -1; //0:Light, 1:Medium, 2:Heavy, 3:Capital, 4:Enormous
    public $imagePath = "img/ships/null.png";
    public $iconPath, $shipClass;
    public $systems = array();
    public $agile = true;
    public $turncost;
    public $turndelaycost = 0;
    public $accelcost = 1;
    public $rollcost = 1;
    public $pivotcost = 1;
    public $currentturndelay = 0;
    public $iniative = "N/A";
    public $iniativebonus = 0;
    public $gravitic = false;
    public $phpclass;
    public $forwardDefense, $sideDefense;
    public $destroyed = false;
    public $pointCost = 0;
    public $faction = null;
    public $flight = true;
    public $hasNavigator = false;
    public $superheavy = false;
    public $flightSize = 1;
    public $maxFlightSize = 0; //maximum size of flight; for single fighter that should be 1 (set in SUperHeavyFighter class)
	//for regular flight of fighters that should be 0 (auto-choose, 12 for light fighters and 9 for others)
	//or set explicitly; guidelines:
	//light fighters: 12
	//medium/heavy: 9
	//particularly tough heavy (Pikitos, Tzymm): 6
	//superheavy (implemented as flight): 3
    protected $flightLeader = null;

    public $offensivebonus, $freethrust;
    public $jinkinglimit = 0;
	public $hangarRequired = 'fighters'; //if left 'fighters', will be classified based on Ini; fleet check only
	public $unitSize = 1; //most fighters are taken one per slot - but some are not
	//B5Wars example are some ultralight fighters, that can be carried two per hangar slot
	//and some superheavies that can use hangars (eg. Vorlon SHF) but take more slots
	//custom StarWars fighters are carried on squadron basis - allowing different squadron sizes for diffeerent craft
	
	public $customFtrName = ""; //to be filled if fighter has special hangar requirements - see Balvarix/Rutarian for usage
		


    public $canvasSize = 200;

    public $fireOrders = array();

    //following values from DB
    public $id, $userid, $name, $campaignX, $campaignY;
    public $rolled = false;
    public $rolling = false;
    public $team;

    protected $dropOutBonus = 0;

    public $movement = array();

    function __construct($id, $userid, $name, $slot)
    {
        $this->id = (int)$id;
        $this->userid = (int)$userid;
        $this->name = $name;
        $this->slot = $slot;
    }

    public function stripForJson() {
        $strippedShip = parent::stripForJson();

        $strippedShip->flightSize = $this->flightSize;
       
        return $strippedShip;
    }

    private $autoid = 1;


    public function getInitiativebonus($gamedata)
    {
        $initiativeBonusRet = parent::getInitiativebonus($gamedata);

        if ($this->hasNavigator) {
            $initiativeBonusRet += 5;
        }

        return $initiativeBonusRet;
    }

    public function getDropOutBonus()
    {
        return $this->dropOutBonus;
    }

    public function getSystemById($id)
    {
        foreach ($this->systems as $system) {
            if ($system->id == $id) {
                return $system;
            }
            foreach ($system->systems as $fs) {
                if ($fs->id == $id) {
                    return $fs;
                }
            }
        }

        return null;
    }


    /*returns a sample fighter, if one needs to review example of what's in flight*/
    public function getSampleFighter()
    {
        return $this->systems[1];
    }


    /*redefinition - as defensive systems will be on actual fighters*/
    /*assuming all fighters are equal, it's enough to get system from first fighter, whether it's alive or not!*/
    public function getDamageMod($shooter, $pos, $turn, $weapon)
    {
        if ($pos !== null) {
            $pos = Mathlib::hexCoToPixel($pos);
        }
        $affectingSystems = array();
        $fighter = $this->systems[1];
        foreach ($fighter->systems as $system) {
            if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)) continue;
            $mod = $system->getDefensiveDamageMod($this, $shooter, $pos, $turn, $weapon);
			//weapon might have something to say about that as well...
			$mod = $weapon->shieldInteractionDamage($this, $shooter, $pos, $turn, $system, $mod);
            if (!isset($affectingSystems[$system->getDefensiveType()])
                || $affectingSystems[$system->getDefensiveType()] < $mod) {
                $affectingSystems[$system->getDefensiveType()] = $mod;
            }
        }
        return array_sum($affectingSystems);
    }

    /*redefinition - as defensive systems will be on actual fighters*/
    /*assuming all fighters are equal, it's enough to get system from first fighter, whether it's alive or not!*/
    public function getHitChanceMod($shooter, $pos, $turn, $weapon)
    {
        if ($pos !== null) {
            $pos = Mathlib::hexCoToPixel($pos);
        }
        $affectingSystems = array();
        $fighter = $this->systems[1];
        foreach ($fighter->systems as $system) {
            if (!$this->checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)) continue;
            $mod = $system->getDefensiveHitChangeMod($this, $shooter, $pos, $turn, $weapon);
			//weapon might have something to say about that as well...
			$mod = $weapon->shieldInteractionDefense($this, $shooter, $pos, $turn, $system, $mod);
            if (!isset($affectingSystems[$system->getDefensiveType()]) //no system of this kind is taken into account yet, or it is but it's weaker
                || $affectingSystems[$system->getDefensiveType()] < $mod) {
                $affectingSystems[$system->getDefensiveType()] = $mod;
            }
        }
        return (-array_sum($affectingSystems));
    }


    /*redefinition; for fighter, don't check whether system is destroyed - it doesn't matter as long as entire flight isn't!*/
    /*also, fighter systems don't get disabled :)*/
    private function checkIsValidAffectingSystem($system, $shooter, $pos, $turn, $weapon)
    {
        if (!($system instanceof DefensiveSystem)) return false; //this isn't a defensive system at all

        //if the system has arcs, check that the position is on arc
        if (is_int($system->startArc) && is_int($system->endArc)) {
            //get bearing on incoming fire...
            if ($weapon->ballistic) {
                $relativeBearing = $this->getBearingOnPos($pos);
            } else { //direct fire weapon - check from shooter...
                $relativeBearing = $this->getBearingOnUnit($shooter);
            }
            //if not on arc, continue!
            if (!mathlib::isInArc($relativeBearing, $system->startArc, $system->endArc)) {
                return false;
            }
        }

        return true;
    }//endof function checkIsValidAffectingSystem


    public function getSystemByName($name)
    {
        foreach ($this->systems as $fighter) {
            foreach ($fighter->systems as $fs) {
                if ($fs->name == $name) return $fs;
            }
        }
    }

    public function getFighterBySystem($id)
    {
        foreach ($this->systems as $fighter) {
            foreach ($fighter->systems as $fs) {
                if ($fs->id == $id) return $fighter;
            }
        }
    }

    protected function addSystem($fighter, $loc = null)
    {
        $fighter->setUnit($this);
        $fighter->id = $this->autoid;
        $fighter->location = sizeof($this->systems);		

        $this->autoid++;
        $fighterSys = array();
        foreach ($fighter->systems as $system) {
            $system->setUnit($this);
            $system->id = $this->autoid;
            $this->autoid++;
            $fighterSys[$system->id] = $system;
        }
        $fighter->systems = $fighterSys;
        $this->systems[$fighter->id] = $fighter;
		
		
		//add to Notes information about miscellanous attributes - with first fighter being added
		if($fighter->id == 1){
			$this->notesFill($fighter);
		}
		//add ramming attack if not equipped already$rammingExists = false;
		$rammingExists = false;
		foreach($fighter->systems as $sys)  if ($sys instanceof RammingAttack){
			$rammingExists = true;
		}
		if(!$rammingExists){
			/* breaks games :(
			//add ramming attack
			//check whether game id is safe (can be safely be deleted in March 2020 or so)
			if ((TacGamedata::$currentGameID >= TacGamedata::$safeGameID) || (TacGamedata::$currentGameID<1)){
				if((($this instanceof FighterFlight)) && (!$this.osat)  ){
					$fighter->addAftSystem(new RammingAttack(0, 0, 360, $this->getRammingFactor(), 0));
				}
			}
			*/
		}
    } //endof function addSystem


	/*for fighter flights - notes must be saved for fighters themselves as well as their subsystems!*/
	/*saves individual notes systems might have generated*/
	public function saveIndividualNotes(DBManager $dbManager) {
		foreach ($this->systems as $fighter) if ($fighter->fighter){ //only for actual fighters - and then handle the rest as subsystems!
            $fighter->saveIndividualNotes($dbManager);
				foreach ($fighter->systems as $subsystem){
					$subsystem->saveIndividualNotes($dbManager);
				}
        }
	}
	
	/*calls systems to generate notes if necessary*/
	public function generateIndividualNotes($gamedata, $dbManager) {
		foreach ($this->systems as $fighter) if ($fighter->fighter){ //only for actual fighters - and then handle the rest as subsystems!
            $fighter->generateIndividualNotes($gamedata, $dbManager);
				foreach ($fighter->systems as $subsystem){
					$subsystem->generateIndividualNotes($gamedata, $dbManager);
				}
        }		
	}
	
	
	/*calls systems to act on notes just loaded if necessary*/
	public function onIndividualNotesLoaded($gamedata) {
		foreach ($this->systems as $fighter) if ($fighter->fighter){ //only for actual fighters - and then handle the rest as subsystems!
            $fighter->onIndividualNotesLoaded($gamedata);
			foreach ($fighter->systems as $subsystem){
				$subsystem->onIndividualNotesLoaded($gamedata);
			}
        }	
	}


    public function getPreviousCoPos()
    {
        $pos = $this->getCoPos();

        for ($i = sizeof($this->movement) - 1; $i >= 0; $i--) {
            $move = $this->movement[$i];
            $pPos = $move->getCoPos();

            if ($pPos["x"] != $pos["x"] || $pPos["y"] != $pos["y"])
                return $pPos;
        }

        return $pos;
    }

    public function getDEW($turn)
    {

        foreach ($this->EW as $EW) {
            if ($EW->type == "DEW" && $EW->turn == $turn)
                return $EW->amount;
        }

        return 0;

    }

    public function getOEW($target, $turn)
    {

        foreach ($this->EW as $EW) {
            if ($EW->type == "OEW" && $EW->targetid == $target->id && $EW->turn == $turn)
                return $EW->amount;
        }

        return 0;
    }

    public function getFacingAngle()
    {
        $movement = null;

        foreach ($this->movement as $move) {
            $movement = $move;
        }

        return $movement->getFacingAngle();
    }

    /*returns number of still active craft in flight*/
    public function countActiveCraft($turn)
    {
        $countActive = 0;
        foreach ($this->systems as $ftr) {
            if (!$ftr->isDestroyed($turn)) $countActive++;
        }
        return $countActive;
    }//endof function countActiveCraft

    public function getLocations()
    {
        $locs = array();
        foreach ($this->systems as $fighter) {
            $exampleFtr = $fighter; //whether still alive or not; any fighter in flight will do, as they're all the same!
        }
        $health = $exampleFtr->maxhealth;

        $locs[] = array("loc" => 0, "min" => 330, "max" => 30, "profile" => $this->forwardDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[0]);
        $locs[] = array("loc" => 0, "min" => 30, "max" => 150, "profile" => $this->sideDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[3]);
        $locs[] = array("loc" => 0, "min" => 150, "max" => 210, "profile" => $this->forwardDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[1]);
        $locs[] = array("loc" => 0, "min" => 210, "max" => 330, "profile" => $this->sideDefense, "remHealth" => $health, "armour" => $exampleFtr->armour[2]);

        return $locs;
    }

    public function fillLocations($locs)
    { //for fighters, armour and health are already defined by getLocations
        return $locs;
    }


    public function getStructureSystem($location)
    {
        return null;
    }

    public function getFireControlIndex()
    {
        return 0;

    }

    public function isDestroyed($turn = false)
    {
        foreach ($this->systems as $system) {
            if (!$system->isDestroyed($turn) && !$system->isDisengaged($turn)) {
                return false;
            }

        }

        return true;
    }

    public function isPowerless()
    {
        return false;
    }


    public function getHitSystem($shooter, $fire, $weapon, $gamedata, $location = null)
    {
        $skipStandard = false;
        $systems = array();
        if ($fire->calledid != -1) {
            $system = $this->getSystemById($fire->calledid);
			//if system is not actual fighter - redirect to fighter it's mounted on!
			if(!$system instanceof Fighter){
				$system = $this->getFighterBySystem($system->id);
			}
			
            if (!$system->isDestroyed()) { //called shot at particular fighter, which is still living
                $systems[] = $system;
                $skipStandard = true;
            }
        }

        if (!$skipStandard) {
            foreach ($this->systems as $system) {
                if (!$system->isDestroyed()) {
                    $systems[] = $system;
                }
            }
        }

        if (sizeof($systems) == 0) return null;
	    
	/* AF fire is normally allocated by player, and it's very important for fighter toughness
	in FV there is no information about actual amount of incoming damage
	but let's try to make an algorithm based on damage _potential_ of incoming shot - won't be as good, but far better than random allocation
	priority of allocation:
	 1. no chance of forcing dropout (prefer destroyed fighter to two fighters dropping out)
	 2. no chance of being destroyed
	 3. being more damaged already
	 4. having higher ID (last craft in flight first - if any craft is special, it'll be the first one)
	*/
	//fill data about eligible craft...
	$craftWithData = array();
	foreach ($systems as $craft){
		$dmgPotential = 0;
		//actually vs fighters Raking degenerates into Standard, rake size is irrelevant!
			$dmgPotential = $weapon->maxDamage; //potential = maximum damage weapon can do	
		//modify by armor properties
		$armor = $weapon->getSystemArmourComplete($this, $craft, $gamedata, $fire);		
		//modify by defensive system (like Diffuser)! 
		$protection=0;
		$protectingSystem = $this->getSystemProtectingFromDamage($shooter, null, $gamedata->turn, $weapon, $craft,$dmgPotential);//let's find biggest one!
		if($protectingSystem){ //may be unavailable, eg. already filled
			$protection = $protectingSystem->doesProtectFromDamage($dmgPotential);
		}
		$armor += $protection;		
		
		$dmgPotential = max(0, $dmgPotential-$armor);//never negative damage ;)
		/*for linked weapons - multiply by number of shots!*/
		if ($weapon->isLinked){
			$dmgPotential = $dmgPotential*$weapon->shots;
		}
		$remainingHP = $craft->getRemainingHealth();
		$damagedThisTurn = false;
		foreach($craft->damage as $dmgEntry){
			if($dmgEntry->turn == $gamedata->turn){
				$damagedThisTurn = true;	
			}
		}
		$alreadyDropoutSubject = false;
		//dropout threshold for this particular fighter - include dropout bonuses/penalties!
		$dropoutThreshold = 10;
		$dropoutThreshold += $this->dropOutBonus + $this->critRollMod + $craft->critRollMod;//negative values will lower the threshold, positive will increase it		
		$dropoutThreshold = min($dropoutThreshold,10); //never higher than 10!
		//subject to dropout if damaged this turn and brought below 10 points
		if (($damagedThisTurn==true) && ($remainingHP<$dropoutThreshold)) $alreadyDropoutSubject = true;
		//can be dropped out if can be brought below 10 hp by this shot and NOT subject to dropout already
		$minRemainingHP = $remainingHP-$dmgPotential;
		$canBeDroppedOut = false;
		if (($minRemainingHP<$dropoutThreshold) && ($alreadyDropoutSubject==false)) $canBeDroppedOut = true;
		$canBeKilled = false;
		if ($minRemainingHP<1) $canBeKilled = true;
		$singleCraft = array("id"=>$craft->id, "hp"=>$remainingHP, "canDrop"=>$canBeDroppedOut, "canDie"=>$canBeKilled, "fighter"=>$craft);
		$craftWithData[] = $singleCraft;
	}
	    
	//sort by priorities, return first one on list!
	usort($craftWithData, function($a, $b){
		if (($a["canDrop"] == true) && ($b["canDrop"] == false)){ //prefer craft with no dropout chance
		    return 1;
		}else if (($b["canDrop"] == true) && ($a["canDrop"] == false)){
		    return -1;	
		} else if (($a["canDie"] == true) && ($b["canDie"] == false)){ //prefer craft with no death chance
		    return 1;	
		} else if (($b["canDie"] == true) && ($a["canDie"] == false)){ 
		    return -1;	
		} else if ($a["hp"] < $b["hp"]){ //prefer already damaged craft
		    return 1;
		} else if ($b["hp"] < $a["hp"]){ 
		    return -1;	
		} else if ($a["id"] < $b["id"]){ //lastly - prefer one that's further in order of IDs
		    return 1;
		} else if ($b["id"] < $a["id"]){ 
		    return -1;	
		}	
		else return 0; //should never happen, IDs are different!
	});
	    
	return $craftWithData[0]["fighter"];

        //return $systems[(Dice::d(sizeof($systems)) - 1)];
    }


    public function getAllFireOrders($turn = -1)
    {
        $orders = array();

        foreach ($this->systems as $fighter) {
            foreach ($fighter->systems as $system) {
                $orders = array_merge($orders, $system->getFireOrders($turn));
                //$orders = array_merge($orders, $system->fireOrders); //old version
            }
        }

        return $orders;
    }


    /*always nothing to do for fighters*/
    public function setExpectedDamage($hitLoc, $hitChance, $weapon, $shooter)
    {
        return;
    }


    /*returns calculated ramming factor for fighter (so will never use explosive charge if, say, Delegor or HK is rammed instead of ramming itself!*/
    /*approximate raming factor as Structure + all Armors of example fighter (so always full ramming factor is used, not reduced by damage received) */
    public function getRammingFactor()
    {
        $dmg = 0;
        $ftr = $this->getSampleFighter();
        $dmg += $ftr->maxhealth;
        foreach ($ftr->armour as $armorvalue) {
            $dmg += $armorvalue;
        }
        return $dmg;
    } //endof function getRammingFactor

}//endof class FighterFlight

class SuperHeavyFighter extends FighterFlight
{
    public $superheavy = true;
	public $maxFlightSize = 1;
    public $jinkinglimit = 4; //SHF standard

    function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }
}

class MicroSAT extends SuperHeavyFighter{
	public $osat = true;
	public $accelcost = 100; //not supposed to actually move anywhere, may move/pivot normally
    public $jinkinglimit = 4; //they are actually allowed to jink!!! Can't imagine how (without being able to seriously accelerate away), but sure useful in game
	
	function __construct($id, $userid, $name, $slot)
    {
        parent::__construct($id, $userid, $name, $slot);
    }
}

?>
