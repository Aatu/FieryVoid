<?php
class breachingpodcorillani extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 40*6;
		$this->faction = "Corillani Theocracy";
        $this->phpclass = "breachingpodcorillani";
        $this->shipClass = "Tural Breaching Pods";
		$this->imagePath = "img/ships/CorillaniDrolla.png";
        $this->isd = 2242;
        
        $this->forwardDefense = 8;
        $this->sideDefense = 8;
        $this->freethrust = 7;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 
		
		$this->iniativebonus = 6*5;
      
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
			$fighter = new Fighter("breachingpodcorillani", $armour, 21, $this->id);
			$fighter->displayName = "Tural";
			$fighter->imagePath = "img/ships/CorillaniDrolla.png";
			$fighter->iconPath = "img/ships/CorillaniDrolla_large.png";
						
			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			$this->addSystem($fighter);
			
		}	
		
    }
}
?>
