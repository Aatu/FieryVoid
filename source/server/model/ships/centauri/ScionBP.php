<?php
class scionbp extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40 * 6;
        $this->faction = "Centauri Republic";
        $this->phpclass = "scionbp";
        $this->shipClass = "Scion Breaching Pod";
        $this->imagePath = "img/ships/phalan.png";
	    $this->isd = 2220;

        $this->forwardDefense = 9;
        $this->sideDefense = 8;
        $this->freethrust = 9;
        $this->offensivebonus = 0; //irrelevant
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
        
        $this->maxFlightSize = 6;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 
		
        $this->iniativebonus = 9 *5;		
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
	        $fighter = new Fighter("scionbp", $armour, 16, $this->id);
	        $fighter->displayName = "Scion";
			$fighter->imagePath = "img/ships/phalan.png";
			$fighter->iconPath = "img/ships/phalan_large.png";

			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            $this->addSystem($fighter);
       }
       
    }

}

?>
