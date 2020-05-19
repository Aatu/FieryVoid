<?php
class TechnicalTestbedFtr extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 150*6;
		$this->faction = "Custom Ships";
		$this->phpclass = "TechnicalTestbedFtr";
		$this->shipClass = "Testbed Medium Fighters";
		$this->imagePath = "img/ships/sentri.png";
	    
		$this->isd = 2202;
        
		$this->forwardDefense = 70;
		$this->sideDefense = 50;
		$this->freethrust = 12;
		$this->offensivebonus = 7;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
        
		
	    $this->advancedArmor = true; 
        $this->gravitic = true;
		$this->critRollMod = -100; //cannot drop out 
		
		$this->iniativebonus = 90;
		$this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(3, 2, 3, 3);
			$fighter = new Fighter("sentri", $armour, 10, $this->id);
			$fighter->displayName = "Medium Fighter";
			$fighter->imagePath = "img/ships/sentri.png";
			$fighter->iconPath = "img/ships/sentri_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 2));
			
			
			
			
			$diffuser = new EnergyDiffuser(0, 1, 1, 0, 360);//($armour, $maxhealth, $dissipation, $startArc, $endArc)
			$tendril=new DiffuserTendrilFtr(3,'L');//absorbtion capacity,side
			$diffuser->addTendril($tendril);
			$fighter->addAftSystem($tendril);
			$fighter->addAftSystem($diffuser);
			$tendril=new DiffuserTendrilFtr(3,'R');//absorbtion capacity,side
			$diffuser->addTendril($tendril);
			$fighter->addAftSystem($tendril);
			
			
			//Advanced Sensors
            $fighter->addFrontSystem(new Fighteradvsensors(0, 1, 0));
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }


	/*remaking damage allocation routine - this fighter is special enough (no dropouts, Diffuser) that it should actually have different priorities when handling damage allocation*/
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
		priority of allocation FOR SHADOW FIGHTER SPECIFICALLY:
		 1. no chance of being destroyed
		 2. being LESS damaged already (unless destruction is guaranteed)
		 4. having higher ID (last craft in flight first - if any craft is special, it'll be the first one)
		*/
		//fill data about eligible craft...
		$craftWithData = array();
		foreach ($systems as $craft){
			$dmgPotential = 0;
			$dmgPotential = $weapon->maxDamage; //potential = maximum damage weapon can do	
			$armor = $weapon->getSystemArmourComplete($this, $craft, $gamedata, $fire);
			//modify by Diffuser! 
			$protection=0;
			$diffuser = $this->getSystemProtectingFromDamage($shooter, null, $gamedata->turn, $weapon, $craft);	
			if($diffuser){ //may be unavailable, eg. already filled
				$protection = $diffuser->doesProtectFromDamage();
			}
			$armor += $protection;		
			$dmgPotential = max(0, $dmgPotential-$armor);//never negative damage ;)
		
			/*for linked weapons - multiply by number of shots!*/
			if ($weapon->isLinked){
				$dmgPotential = $dmgPotential*$weapon->shots;
			}
			$remainingHP = $craft->getRemainingHealth()+$protection; //let's count Tendril capacity as remaining hp!
			$minRemainingHP = $remainingHP-$dmgPotential;
			$canBeKilled = false;
			if ($minRemainingHP<1) $canBeKilled = true;
			$singleCraft = array("id"=>$craft->id, "hp"=>$remainingHP, "canDrop"=>false, "canDie"=>$canBeKilled, "fighter"=>$craft);
			$craftWithData[] = $singleCraft;
		}
	    
		//sort by priorities, return first one on list!
		usort($craftWithData, function($a, $b){
			if (($a["canDie"] == true) && ($b["canDie"] == false)){ //prefer craft with no death chance
				return 1;	
			} else if (($b["canDie"] == true) && ($a["canDie"] == false)){ 
				return -1;	
			} else if ($a["hp"] > $b["hp"]){ //prefer LESS damaged craft
				return 1;
			} else if ($b["hp"] > $a["hp"]){ 
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

}



?>
