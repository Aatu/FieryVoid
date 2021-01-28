<?php
class SshelathSkreghaB extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 120;
	$this->faction = "ZEscalation Sshelath";
	$this->phpclass = "SshelathSkreghaB";
	$this->shipClass = "Skregha-B Light Fighters";
		$this->variantOf = "Skregha-A Light Fighters";
		$this->occurence = "common";	
	$this->imagePath = "img/ships/EscalationWars/SshelathSkregha.png";
		$this->isd = 1959;
		$this->unofficial = true;
		
	$this->forwardDefense = 6;
	$this->sideDefense = 6;
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
		$armour = array(2, 1, 1, 1);
		$fighter = new Fighter("SshelathSkreghaB", $armour, 8, $this->id);
		$fighter->displayName = "Skregha-B";
		$fighter->imagePath = "img/ships/EscalationWars/SshelathSkregha.png";
		$fighter->iconPath = "img/ships/EscalationWars/SshelathSkregha_Large.png";
			
		$gun = new LightParticleBeam(330, 30, 3, 1);
		$gun->displayName = "Light Particle Beam";
		$fighter->addFrontSystem($gun);
			
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
		$this->addSystem($fighter);
	}
    }
}


