<?php
class breachingPodEA extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40 * 6;
        $this->faction = "Earth Alliance";
        $this->phpclass = "breachingPodEA";
        $this->shipClass = "Lamprey Breaching Pod";
        $this->imagePath = "img/ships/Hades.png";
        $this->isd = 2238;
        
//	    $this->notes = 'Non-atmospheric.';
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        $this->freethrust = 6;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher        
        $this->turncost = 0.33;

        $this->maxFlightSize = 6;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 		
		
		
    	$this->iniativebonus = 9 * 5;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("breachingPodEA", $armour, 18, $this->id);
            $fighter->displayName = "Lamprey";
            $fighter->imagePath = "img/ships/Hades.png";
            $fighter->iconPath = "img/ships/Hades_Large.png";

			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
    }
}
?>
