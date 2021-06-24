<?php
class PrivateerGorith extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 40*6;
		$this->faction = "Raiders";
        $this->phpclass = "PrivateerGorith";
        $this->shipClass = "Narn Privateer Gorith Medium Fighters";
		$this->imagePath = "img/ships/gorith.png";
        $this->limited = 33;

		$this->notes = "Used by Narn privateers, Tirrith Free State, and Junkyard Dogs (1 only)";
        
        $this->forwardDefense = 7;
        $this->sideDefense = 9;
        $this->freethrust = 12;
        $this->offensivebonus = 5;
        $this->jinkinglimit = 8;
        $this->turncost = 0.33;
        
		$this->iniativebonus = 90;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
			
			$armour = array(2, 2, 2, 2);
			$fighter = new Fighter("gorith", $armour, 10, $this->id);
			$fighter->displayName = "Gorith";
			$fighter->imagePath = "img/ships/gorith.png";
			$fighter->iconPath = "img/ships/gorith_large.png";
			
			
			$fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
			
			$this->addSystem($fighter);
			
		}
		
		
    }

}



?>
