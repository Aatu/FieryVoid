<?php
class breachingpodyolu extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
	        
		$this->pointCost = 50*6;
		$this->faction = "Yolu Confederation";
		$this->phpclass = "breachingpodyolu";
		$this->shipClass = "Yonor Breaching Pods";
		$this->imagePath = "img/ships/Nathor.png";

        $this->isd = 2096;
		
		$this->forwardDefense = 10;
		$this->sideDefense = 10;
		$this->freethrust = 8;
		$this->offensivebonus = 3;
		$this->jinkinglimit = 0;
		$this->turncost = 0.33;
		$this->iniativebonus = 9*5;

        $this->maxFlightSize = 6;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 

		$this->notes = "Bonus to delivery roll for marines.";
		
		$this->gravitic = true; //not 100% clear from SCS if these ships are gravitic, but every other Yolu ship is so seems safe to assume.
//		$this->dropOutBonus = -2; //Less easier to argue they should get the same bonus to dropout rolls that the Utan gets
		 
        $this->populate();
    
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(5, 5, 5, 5);
            $fighter = new Fighter("breachingpodyolu", $armour, 21, $this->id);
            $fighter->displayName = "Yonor";
            $fighter->imagePath = "img/ships/Nathor.png";
            $fighter->iconPath = "img/ships/Nathor_large.png";

			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

            $this->addSystem($fighter);

        }
    }
}

?>
