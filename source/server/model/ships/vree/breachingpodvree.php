<?php
class breachingpodvree extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
		$this->faction = "Vree Conglomerate";
		$this->phpclass = "breachingpodvree";
        $this->shipClass = "Zyleen Breaching Pods";
		$this->imagePath = "img/ships/VreeZeoth.png";
        $this->isd = 2225;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 5;
        $this->gravitic = true;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->pivotcost = 0; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 
		
		$this->iniativebonus = 9*5;
      
        $this->populate();
    
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(3, 3, 3, 3);
			$fighter = new Fighter("breachingpodvree", $armour, 18, $this->id);
			$fighter->displayName = "Zyleen";
			$fighter->imagePath = "img/ships/VreeZeoth.png";
			$fighter->iconPath = "img/ships/VreeZeoth_Large.png";		
			
			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
