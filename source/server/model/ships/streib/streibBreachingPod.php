<?php
class StreibBreachingPod extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 50*6;
	$this->faction = "Streib";
        $this->phpclass = "StreibBreachingPod";
        $this->shipClass = "Breaching Pods";
	 $this->imagePath = "img/ships/streibbreachingpod.png";
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 8;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 2;
        $this->pivotcost = 2; //shuttles have pivot cost higher
	    $this->gravitic = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        
	$this->hangarRequired = 'shuttles'; //for fleet check
	$this->iniativebonus = 8*5;
      
        $this->populate();
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

			
		$this->addSystem($fighter);
			
	}
		
		
    }
}
?>
