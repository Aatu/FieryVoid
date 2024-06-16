<?php
class breachingpodbrakiri extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 42*6;
    	$this->faction = "Brakiri Syndicracy";
        $this->phpclass = "breachingpodbrakiri";
        $this->shipClass = "Pri-Thentat Breaching Pod";
    	$this->imagePath = "img/ships/falkosi.png";
        
		$this->notes = 'Pri-Wakat Concepts & Solutions';//Corporation producing the design
		$this->isd = 2198;
		
        $this->forwardDefense = 7;
        $this->sideDefense = 10;
        $this->freethrust = 7;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
		$this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
		
        $this->maxFlightSize = 6;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 
		
        $this->iniativebonus = 9*5;
        
        $this->gravitic = true;
        $this->populate();
    }


    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(2, 2, 2, 2);
            $fighter = new Fighter("breachingpodbrakiri", $armour, 18, $this->id);
            $fighter->displayName = "Pri-Thentat";
            $fighter->imagePath = "img/ships/falkosi.png";
            $fighter->iconPath = "img/ships/falkosi_large.png";
            
			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
        }
    }
}
?>
