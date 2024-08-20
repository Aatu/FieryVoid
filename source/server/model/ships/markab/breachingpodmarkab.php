<?php

class breachingpodmarkab extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
        $this->faction = "Markab Theocracy";
        $this->phpclass = "breachingpodmarkab";
        $this->shipClass = "Dojafa Breaching Pods";
        $this->imagePath = "img/ships/MarkabDojafa.png";
		$this->isd = 2005;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 6;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;

        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 
        
    	$this->iniativebonus = 8 *5;
  		
        $this->populate();        

		$this->enhancementOptionsEnabled[] = 'FTR_FERV'; //To activate Religious Fervor attributes.  
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }



    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){        
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("breachingpodmarkab", $armour, 18, $this->id);
            $fighter->displayName = "Dojafa";
            $fighter->imagePath = "img/ships/MarkabDojafa.png";
            $fighter->iconPath = "img/ships/MarkabDojafa_large.png";

			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
            $this->addSystem($fighter);
	   }
    }
}

?>
