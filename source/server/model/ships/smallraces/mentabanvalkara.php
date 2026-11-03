<?php

class mentabanvalkara extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 192;
        $this->faction = "Small Races";
        $this->phpclass = "mentabanvalkara";
        $this->shipClass = "Mentaban Valkara Light flight";
        $this->imagePath = "img/ships/mentabanDeltaV.png";
        
        $this->isd = 2250;
		$this->unofficial = true;

        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 11;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 100;
        $this->populate();        
    
        //$this->enhancementOptionsEnabled[] = 'EXT_AMMO'; //To enable extra Ammo for main gun.
        	
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("mentabanvalkara", $armour, 7, $this->id);
            $fighter->displayName = "Valkara";
            $fighter->imagePath = "img/ships/mentabanDeltaV.png";
            $fighter->iconPath = "img/ships/mentabanDeltaV_Large.png";

            $fighter->addFrontSystem(new SlugCannon(330, 30));

			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			

            $this->addSystem($fighter);
        }
    }
}

?>
