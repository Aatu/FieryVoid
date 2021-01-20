<?php
class SshelathSkreghaA extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 96;
	$this->faction = "ZEscalation Sshelath";
	$this->phpclass = "SshelathSkreghaA";
	$this->shipClass = "Skregha-A Light Fighters";
	$this->imagePath = "img/ships/EscalationWars/SshelathSkregha.png";
		$this->isd = 1924;
		$this->unofficial = true;
		
	$this->forwardDefense = 6;
	$this->sideDefense = 6;
	$this->freethrust = 8;
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
		$fighter = new Fighter("SshelathSkreghaA", $armour, 8, $this->id);
		$fighter->displayName = "Skregha-A";
		$fighter->imagePath = "img/ships/EscalationWars/SshelathSkregha.png";
		$fighter->iconPath = "img/ships/EscalationWars/SshelathSkregha_Large.png";
			
		$gun = new LightParticleBeam(330, 30, 2);
		$gun->displayName = "Paired Particle Beam";
		$fighter->addFrontSystem($gun);
			
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
		$this->addSystem($fighter);
	}
    }
}


