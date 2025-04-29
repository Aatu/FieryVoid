<?php
class breachingpoddilgar extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	    $this->pointCost = 40*6;
	    $this->faction = "Dilgar Imperium";
        $this->phpclass = "breachingpoddilgar";
        $this->shipClass = "Jortavo Breaching Pods";
	    $this->imagePath = "img/ships/Jortavo.png";
        $this->isd = 2226;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 7;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 0.5; //Dilgar get half the number of breaching pods as others.
		
		$this->iniativebonus = 9*5;
		$this->notes = "Takes up two hanger slots.";
      
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
			$fighter = new Fighter("breachingpoddilgar", $armour, 18, $this->id);
			$fighter->displayName = "Jortavo";
			$fighter->imagePath = "img/ships/Jortavo.png";
			$fighter->iconPath = "img/ships/Jortavo_large.png";
			
			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
