<?php

class templarGC extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 240;
        $this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "templarGC";
        $this->shipClass = "Templar II Interceptor flight";
        $this->imagePath = "img/ships/GCtemplar.png";
        
        $this->isd = 2220;

		$this->unofficial = true;

        $this->forwardDefense = 7;
        $this->sideDefense = 6;
        $this->freethrust = 14;
        $this->offensivebonus = 5;
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
            $fighter = new Fighter("templarGC", $armour, 9, $this->id);
            $fighter->displayName = "Templar II";
            $fighter->imagePath = "img/ships/GCtemplar.png";
            $fighter->iconPath = "img/ships/GCtemplar_large.png";

            $fighter->addFrontSystem(new PairedGatlingGun(330, 30));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

            $this->addSystem($fighter);
        }
    }
}

?>
