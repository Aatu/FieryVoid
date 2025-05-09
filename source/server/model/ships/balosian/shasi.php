<?php
class Shasi extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 150;
	$this->faction = "Balosian Underdwellers";
	$this->phpclass = "Shasi";
	$this->shipClass = "Shasi Light Fighters";
	$this->imagePath = "img/ships/shasi.png";
		$this->isd = 2219;

	$this->forwardDefense = 7;
	$this->sideDefense = 6;
	$this->freethrust = 12;
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
		$armour = array(1, 1, 2, 2);
		$fighter = new Fighter("shasi", $armour, 9, $this->id);
		$fighter->displayName = "Shasi";
		$fighter->imagePath = "img/ships/shasi.png";
		$fighter->iconPath = "img/ships/shasi_large.png";
			
		$gun = new LightParticleBeam(330, 30, 1);
		$gun->displayName = "Ultralight Particle Beam";
		$fighter->addFrontSystem($gun);
			
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
		$this->addSystem($fighter);
	}
    }
}


