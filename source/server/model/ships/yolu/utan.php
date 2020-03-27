<?php
class Utan extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
	        
		$this->pointCost = 660;
		$this->faction = "Yolu";
		$this->phpclass = "Utan";
		$this->shipClass = "Utan Heavy Fighters";
		$this->imagePath = "img/ships/utan.png";
		
		$this->forwardDefense = 7;
		$this->sideDefense = 9;
		$this->freethrust = 8;
		$this->offensivebonus = 5;
		$this->jinkinglimit = 6;
		$this->turncost = 0.33;
		$this->iniativebonus = 80;
		
		$this->gravitic = true; 
		$this->dropOutBonus = -2; 
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
            $armour = array(5, 4, 4, 4);
            $fighter = new Fighter("utan", $armour, 15, $this->id);
            $fighter->displayName = "Utan Heavy Fighter";
            $fighter->imagePath = "img/ships/utan.png";
            $fighter->iconPath = "img/ships/utan_large.png";

            $fighter->addFrontSystem(new LightFusionCannon(330, 30, 4, 2));
            $fighter->addFrontSystem(new LightMolecularDisruptor(330, 30, 0));
		
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			

            $this->addSystem($fighter);

        }
    }
}

?>
