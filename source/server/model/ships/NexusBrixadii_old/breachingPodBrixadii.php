<?php
class breachingPodBrixadii extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40 * 6;
        $this->faction = "Nexus Brixadii Clans (early)";
        $this->phpclass = "breachingPodBrixadii";
        $this->shipClass = "Zrenix Breaching Pods";
        $this->imagePath = "img/ships/Lamprey.png";
        $this->isd = 2035;
        
	    $this->notes = 'Atmospheric.';
        
        $this->forwardDefense = 7;
        $this->sideDefense = 8;
        $this->freethrust = 8;
        $this->offensivebonus = 1;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;

        $this->maxFlightSize = 2;//this is an unusual type of 'fighter', limit flight size.      
        $this->hangarRequired = 'Breaching Pods'; //for fleet check   
		$this->unitSize = 1; 		
		
    	$this->iniativebonus = 11 * 5;
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
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("breachingPodBrixadii", $armour, 16, $this->id);
            $fighter->displayName = "Zrenix";
            $fighter->imagePath = "img/ships/Lamprey.png";
            $fighter->iconPath = "img/ships/Lamprey_large.png";

			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
    }
}
?>
