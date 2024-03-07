<?php
class TrekSulibanCellshipMed extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 33 *6; //for 6; a bit add-on for not requiring carrier
        $this->faction = "ZTrek Playtest Other Factions";
        $this->phpclass = "TrekSulibanCellshipMed";
        $this->shipClass = "Suliban Medium Cellships";
        $this->imagePath = "img/ships/StarTrek/SulibanCellshipMed.png";
		$this->unofficial = true;
		
        $this->isd = 2146;

        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        $this->freethrust = 7;
        $this->offensivebonus = 4;
        $this->jinkinglimit = 6;
        $this->turncost = 0.33;
       	$this->iniativebonus = 15 *5; 

        $this->populate();
    }
    public function populate(){
        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;
        for ($i = 0; $i < $toAdd; $i++){   
            $armour = array(1, 1, 1, 1);
            $fighter = new Fighter("TrekSulibanCellshipMed", $armour, 10, $this->id);
            $fighter->displayName = "Suliban Medium Cellship";
			
            $fighter->imagePath = "img/ships/StarTrek/SulibanCellshipMed.png";
            $fighter->iconPath = "img/ships/StarTrek/SulibanCellship_Large.png";
			
            $frontGun = new LightParticleBeam(0, 360, 2, 2);
            $frontGun->displayName = "Ultralight Dual Phase Cannon";
            $fighter->addFrontSystem($frontGun);


			$fighter->addAftSystem(new TrekShieldFtr(0, 4, 4, 1) ); //armor, health, rating, recharge
			
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
            $this->addSystem($fighter);
        }
    }
}
?>