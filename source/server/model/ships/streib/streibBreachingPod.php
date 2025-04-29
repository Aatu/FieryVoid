<?php
class StreibBreachingPod extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 50*6;
		$this->faction = "Streib";
        $this->phpclass = "StreibBreachingPod";
        $this->shipClass = "Breaching Pods";
	 	$this->imagePath = "img/ships/streibbreachingpod.png";
         $this->isd = 2205;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 8;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 2;
        $this->pivotcost = 2; //shuttles have pivot cost higher
	    $this->gravitic = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;

        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.          
		$this->hangarRequired = 'shuttles'; //for fleet check
		$this->iniativebonus = 8*5;
      
        $this->populate();
    
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(6, 6, 6, 6);
			$fighter = new Fighter("StreibBreachingPod", $armour, 10, $this->id);
			$fighter->displayName = "Breaching Pod";
			$fighter->imagePath = "img/ships/streibbreachingpod.png";
			$fighter->iconPath = "img/ships/streibbreachingpod_Large.png";
			
				
			$fighter->addFrontSystem(new LtEMWaveDisruptor(240, 120, 1));
			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.			

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
				
			$this->addSystem($fighter);
				
		}
		
		
    }
}
?>
