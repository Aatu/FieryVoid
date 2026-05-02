<?php
class PolarenTyreeBP extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 40*6;
		$this->faction = "Nexus Polaren Confederacy (early)";
        $this->phpclass = "PolarenTyreeBP";
        $this->shipClass = "Tyree Breaching Pods";
		$this->imagePath = "img/ships/Nexus/polarenTyree.png";

		$this->isd = 1770;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 8;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;
        
        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'Breaching Pods'; //for fleet check
		$this->unitSize = 1; 
		
		$this->iniativebonus = 10*5;
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
			
			$armour = array(3, 3, 3, 3);
			$fighter = new Fighter("PolarenTyreeBP", $armour, 18, $this->id);
			$fighter->displayName = "Tyree";
			$fighter->imagePath = "img/ships/Nexus/polarenTyree.png";
			$fighter->iconPath = "img/ships/Nexus/polarenTyree_large.png";
			
			
			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
						
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
