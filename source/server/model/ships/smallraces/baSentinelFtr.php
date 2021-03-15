<?php
class BASentinelFtr extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 228;
	$this->faction = "Small Races";
    $this->phpclass = "BASentinelFtr";
    $this->shipClass = "BA Sentinel Fighters";
	$this->imagePath = "img/ships/deltaV.png";

        $this->isd = 2261;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 6;
        $this->freethrust = 11;
        $this->offensivebonus = 3;
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
		$fighter = new Fighter("Sentinel", $armour, 8, $this->id);
		$fighter->displayName = "Sentinel";
		$fighter->imagePath = "img/ships/deltaV.png";
		$fighter->iconPath = "img/ships/deltaV_large.png";
			
            $frontGun = new PairedParticleGun(330, 30, 2);
            $frontGun->displayName = "Light Particle Gun";
            $fighter->addFrontSystem($frontGun);

            $missileRack = new FighterMissileRack(2, 330, 30);
            $missileRack->firingModes = array(
                1 => "FY"
            );
            $missileRack->missileArray = array(
                1 => new MissileFY(330, 30)
            );
            $fighter->addFrontSystem($missileRack);		
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
		$this->addSystem($fighter);
	}
    }
}


?>
