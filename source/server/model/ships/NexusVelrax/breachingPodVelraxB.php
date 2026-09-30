<?php
class breachingPodVelraxB extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 30 * 6;
        $this->faction = "Nexus Velrax Republic";
        $this->phpclass = "breachingPodVelraxB";
        $this->shipClass = "Korveth Breaching Pods";
        $this->imagePath = "img/ships/Nexus/velraxBP.png";
        $this->isd = 2005;
        
	    $this->notes = 'Atmospheric.';
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 6;
        $this->offensivebonus = 1;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;

        $this->maxFlightSize = 2;//this is an unusual type of 'fighter', limit flight size.      
        $this->hangarRequired = 'Breaching Pods'; //for fleet check   
		$this->unitSize = 1; 		
		
    	$this->iniativebonus = 9 * 5;
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
            $fighter = new Fighter("breachingPodVelraxB", $armour, 16, $this->id);
            $fighter->displayName = "Korveth";
            $fighter->imagePath = "img/ships/Nexus/velraxBP.png";
            $fighter->iconPath = "img/ships/Nexus/velraxBP_large.png";

			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
    }
}
?>
