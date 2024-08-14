<?php
class breachingpodbalosian extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 75*6;
        $this->faction = "Balosian Underdwellers";
        $this->phpclass = "breachingpodbalosian";
        $this->shipClass = "Shappa Breaching Pods";
        $this->imagePath = "img/ships/Tortra.png";
		
        $this->isd = 2223;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 10;
        $this->freethrust = 8;
        $this->offensivebonus = 3;
        $this->jinkinglimit = 0;
        $this->turncost = 0.33;
		$this->turndelay = 0;

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
            $armour = array(3, 3, 3, 3);
            $fighter = new Fighter("breachingpodbalosian", $armour, 16, $this->id);
            $fighter->displayName = "Shappa";
            $fighter->imagePath = "img/ships/Tortra.png";
            $fighter->iconPath = "img/ships/Tortra_large.png";

			$gun = new LightParticleBeam(330, 30, 2, 2);
			$gun->displayName = "Light Particle Gun";
			$fighter->addFrontSystem($gun);
						
			$fighter->addFrontSystem(new Marines(0, 360, 0, false)); //startarc, endarc, damagebonus, elite.
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack			
            
            $this->addSystem($fighter);
        }
    }
}
?>
