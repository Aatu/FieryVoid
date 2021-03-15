<?php
class DeltaFtr extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 150;
	$this->faction = "EA";
        $this->phpclass = "DeltaFtr";
        $this->shipClass = "EA/BA Delta Multi-Role Light Fighters";
	$this->imagePath = "img/ships/deltaV.png";
	    $this->isd = 2158;
 		$this->unofficial = true;
        
        $this->forwardDefense = 5;
        $this->sideDefense = 5;
        $this->freethrust = 8;
        $this->offensivebonus = 2;
        $this->jinkinglimit = 10;
        $this->turncost = 0.33;
        
	$this->iniativebonus = 95;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(1, 1, 1, 1);
		$fighter = new Fighter("DeltaFtr", $armour, 7, $this->id);
		$fighter->displayName = "Delta";
		$fighter->imagePath = "img/ships/deltaV.png";
		$fighter->iconPath = "img/ships/deltaV_large.png";
			
        $frontGun = new PairedParticleGun(330, 30, 1);
        $frontGun->displayName = "Ultralight Particle Beam";
        $fighter->addFrontSystem($frontGun);
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
		
		$this->addSystem($fighter);
	}
    }
}


?>
