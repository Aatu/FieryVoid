<?php
class Nathor extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
	        
		$this->pointCost = 40*6;
		$this->faction = "Yolu";
		$this->phpclass = "Nathor";
		$this->shipClass = "Nathor Assault Shuttles";
		$this->imagePath = "img/ships/Nathor.png";

        $this->isd = 2096;
		
		$this->forwardDefense = 10;
		$this->sideDefense = 10;
		$this->freethrust = 8;
		$this->offensivebonus = 3;
		$this->jinkinglimit = 0;
		$this->turncost = 0.33;
		$this->iniativebonus = 9*5;
		
//		$this->gravitic = true; 
//		$this->dropOutBonus = -2; 
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(5, 5, 5, 5);
            $fighter = new Fighter("Nathor", $armour, 21, $this->id);
            $fighter->displayName = "Nathor Assault Shuttle";
            $fighter->imagePath = "img/ships/Nathor.png";
            $fighter->iconPath = "img/ships/Nathor_large.png";

            $fighter->addFrontSystem(new LightFusionCannon(330, 30, 4, 1));
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack

            $this->addSystem($fighter);

        }
    }
}

?>