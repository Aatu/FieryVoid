<?php
class ThunderboltStarfury extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 80*6;
        $this->faction = "EA";
        $this->phpclass = "ThunderboltStarfury";
        $this->shipClass = "Starfury: Thunderbolt Heavy flight";
        $this->imagePath = "img/ships/thunderboltStarfury.png";
        $this->customFtrName = "Thunderbolt";
		
        $this->isd = 2259;
        $this->notes = 'Needs updated hangars to handle.';
        
        $this->forwardDefense = 8;
        $this->sideDefense = 7;
        $this->freethrust = 13;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
        
        $this->iniativebonus = 80;
        $this->populate();       

		$this->enhancementOptionsEnabled[] = 'NAVIGATOR'; //this flight can have Navigator enhancement option	
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){            
            $armour = array(3, 2, 2, 2);
            $fighter = new Fighter("thunderboltStarfury", $armour, 15, $this->id);
            $fighter->displayName = "Thunderbolt";
            $fighter->imagePath = "img/ships/thunderboltStarfury.png";
            $fighter->iconPath = "img/ships/thunderboltStarfury_large.png";

            $fighter->addFrontSystem(new FighterMissileRack(3, 330, 30));
            $fighter->addFrontSystem(new GatlingPulseCannon(330, 30));
            $fighter->addFrontSystem(new FighterMissileRack(3, 330, 30));
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack	
            
            $this->addSystem($fighter);
        }
    }
}
?>
