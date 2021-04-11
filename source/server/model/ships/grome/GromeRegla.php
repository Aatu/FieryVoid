<?php

class GromeRegla extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 192;
        $this->faction = "Grome";
        $this->phpclass = "GromeRegla";
        $this->shipClass = "Regla Light flight";
        $this->imagePath = "img/ships/GromeRegla.png";
        
        $this->isd = 2235;

        $this->forwardDefense = 6;
        $this->sideDefense = 8;
        $this->freethrust = 11;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
    	$this->iniativebonus = 100;
        $this->populate();        
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(2, 1, 1, 1);
            $fighter = new Fighter("Regla", $armour, 7, $this->id);
            $fighter->displayName = "Regla";
            $fighter->imagePath = "img/ships/GromeRegla.png";
            $fighter->iconPath = "img/ships/GromeRegla_Large.png";

            $fighter->addFrontSystem(new SlugCannon(330, 30));

			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			

            $this->addSystem($fighter);
        }
    }
}

?>
