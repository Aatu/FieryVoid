<?php
class triadCherub extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 1860;
        $this->faction = "The Triad";
        $this->phpclass = "triadCherub";
        $this->shipClass = "Order: Cherub Super-heavy Fighters";
        $this->imagePath = "img/ships/triadCherub.png";
		$this->isd = 'Primordial';
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadFighter');

		$this->triadOrder = true;	//Important to ensure immunity from Flare Generator!	

        $this->gravitic = true;
		$this->advancedArmor = true;  

        $this->forwardDefense = 10;
        $this->sideDefense = 12;
		
        $this->freethrust = 13;
        $this->offensivebonus = 10;
        $this->jinkinglimit = 4;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
		
        $this->iniativebonus = 85;
    	$this->superheavy = true;
        $this->maxFlightSize = 3;//this is a superheavy fighter originally intended as single unit, limit flight size to 3

		$this->hangarRequired = "Triad Fighter"; 
		$this->unitSize = 0.5; //one craft requires 2 hangar slots
	
		$this->populate();
	
	}

    public function populate(){        

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
		
		for ($i = 0; $i < $toAdd; $i++) {
			$armour = array(5, 6, 6, 6);
			$fighter = new Fighter("Cherub", $armour, 30, $this->id);
			$fighter->displayName = "Cherub";
			$fighter->imagePath = "img/ships/triadCherub.png";
			$fighter->iconPath = "img/ships/triadCherub_large.png";

			$fighter->addFrontSystem(new LtPrismBeam(330, 30));
        
			//ramming attack 			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
       	    //EM Shield Level 3
            $fighter->addAftSystem(new FtrShield(3, 0, 360));
			//Advanced Sensors
            $fighter->addAftSystem(new Fighteradvsensors(0, 1, 0));			
			
			$this->addSystem($fighter);
		}
    }

//    public function populate(){
//        return;
//    }
    

}

?>
