<?php

class DroftaN extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 46*6;
        $this->faction = "Markab";
        $this->phpclass = "DroftaN";
        $this->shipClass = "Drofta Medium Fighters";
        $this->imagePath = "img/ships/auroraStarfury.png";
		$this->isd = 2000;
        
        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 7;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 80;
        $this->populate();        
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){        
            $armour = array(3, 1, 2, 2);
            $fighter = new Fighter("Drofta", $armour, 10, $this->id);
            $fighter->displayName = "Drofta";
            $fighter->imagePath = "img/ships/auroraStarfury.png";
            $fighter->iconPath = "img/ships/auroraStarfury_largei.png";

            $frontGun = new LightScattergun(330, 30); //always a single mount for this weapon
            $fighter->addFrontSystem($frontGun);
		
            $this->addSystem($fighter);
	   }
    }
}

?>
