<?php
class CircasianIstale extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 168;
	$this->faction = "ZEscalation Circasian";
	$this->phpclass = "CircasianIstale";
	$this->shipClass = "Istale Light Fighters";
	$this->imagePath = "img/ships/EscalationWars/CircasianIstale.png";
		$this->isd = 1952;
		$this->unofficial = true;
		

	$this->forwardDefense = 6;
	$this->sideDefense = 5;
	$this->freethrust = 10;
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
		$armour = array(1, 1, 1, 1);
		$fighter = new Fighter("CircasianIstale", $armour, 8, $this->id);
		$fighter->displayName = "Istale";
		$fighter->imagePath = "img/ships/EscalationWars/CircasianIstale.png";
		$fighter->iconPath = "img/ships/EscalationWars/CircasianIstale_Large.png";
			
		$gun = new LightParticleBeam(330, 30, 1);
		$gun->displayName = "Ultralight Particle Beam";
		$fighter->addFrontSystem($gun);
			
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
		$this->addSystem($fighter);
	}
    }
}


