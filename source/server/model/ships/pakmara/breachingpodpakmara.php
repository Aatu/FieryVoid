<?php
class breachingpodpakmara extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40*6;
        $this->faction = "Pak'ma'ra Confederacy";
        $this->phpclass = "breachingpodpakmara";
        $this->shipClass = "Or'Ti'Nam Breaching Pods";
        $this->imagePath = "img/ships/PakmaraOrshilti.png";
		
        $this->isd = 2195;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 0;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
		$this->turndelay = 0;
        
        $this->maxFlightSize = 6;//this is an unusual type of 'fighter', limit flight size.      
		$this->hangarRequired = 'assault shuttles'; //for fleet check
		$this->unitSize = 1; 
		
        $this->iniativebonus = 6*5;
        $this->populate();       

    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(4, 4, 4, 4);
            $fighter = new Fighter("breachingpodpakmara", $armour, 20, $this->id);
            $fighter->displayName = "Or'Ti'Nam";
            $fighter->imagePath = "img/ships/PakmaraOrshilti.png";
            $fighter->iconPath = "img/ships/PakmaraOrshilti_Large.png";

			$fighter->addFrontSystem(new Marines(330, 30, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
