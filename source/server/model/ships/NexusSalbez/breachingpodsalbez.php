<?php
class breachingpodsalbez extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 40*6;
		$this->faction = "Nexus Sal-bez Coalition";
        $this->phpclass = "breachingpodsalbez";
        $this->shipClass = "Ark-ven Breaching Pods";
		$this->imagePath = "img/ships/Nexus/salbez_arkven.png";

		$this->isd = 2105;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 6;
        $this->offensivebonus = 1;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 
		
		$this->iniativebonus = 9*5;
		$this->notes = "Bonus to attaching to enemy ships.";	

        $this->populate();
    
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("breachingpodsalbez", $armour, 21, $this->id);
			$fighter->displayName = "Ark-ven";
			$fighter->imagePath = "img/ships/Nexus/salbez_arkven.png";
			$fighter->iconPath = "img/ships/Nexus/salbez_arkven_large.png";
			
			
			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
						
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
