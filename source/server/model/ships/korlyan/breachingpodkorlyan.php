<?php
class breachingpodkorlyan extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 40 * 6;
		$this->faction = "Kor-Lyan Kingdoms";
        $this->phpclass = "breachingpodkorlyan";
        $this->shipClass = "Ailyan Breaching Pods";
		$this->imagePath = "img/ships/korlyanMerkul2.png"; 

	    $this->isd = 2223;

        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 7;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->pivotcost = 2; //shuttles have pivot cost higher
        $this->turncost = 0.33;

        $this->maxFlightSize = 3;//this is an unusual type of 'fighter', limit flight size.         
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->iniativebonus = 9*5;
      
        $this->populate();
    
    	$this->enhancementOptionsEnabled[] = 'ELT_MAR'; //To enable Elite Marines enhancement
		$this->enhancementOptionsEnabled[] = 'EXT_MAR'; //To enable extra Marines enhancement
		
    }
    
    
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(4, 4, 4, 4);
			$fighter = new Fighter("breachingpodkorlyan", $armour, 16, $this->id);
			$fighter->displayName = "Ailyan";
			$fighter->imagePath = "img/ships/korlyanMerkul2.png";
			$fighter->iconPath = "img/ships/korlyanMerkul_large2.png";
						
			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.

			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }
}
?>
