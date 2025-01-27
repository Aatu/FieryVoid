<?php
class TrekSulibanCellshipSmall extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 25 *6; //for 6; a bit add-on for not requiring carrier
        $this->faction = "ZStarTrek (early) Suliban";
        $this->phpclass = "TrekSulibanCellshipSmall";
        $this->shipClass = "Suliban Small Cellships";
        $this->imagePath = "img/ships/StarTrek/SulibanCellship.png";
		$this->unofficial = true;
		
        $this->isd = 2144;

        $this->forwardDefense = 6;
        $this->sideDefense = 6;
        $this->freethrust = 8;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6; //deliberate!
        $this->turncost = 0.33;
       	$this->iniativebonus = 16 *5; 
        $this->maxFlightSize = 9;//this is a light fighter, but quite a tough one - hence the limitation

        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("TrekSulibanCellshipSmall", $armour, 8, $this->id);
            $fighter->displayName = "Suliban Small Cellship";
			
            $fighter->imagePath = "img/ships/StarTrek/SulibanCellship.png";
            $fighter->iconPath = "img/ships/StarTrek/SulibanCellship_Large.png";
			

			$frontGun = new TrekFtrPhaser(0, 360, 2, 1, "Phase Cannon");
            $fighter->addFrontSystem($frontGun);


			$fighter->addAftSystem(new TrekShieldFtr(0, 3, 3, 1) ); //armor, health, rating, recharge
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
            $this->addSystem($fighter);
        }
    }
}
?>