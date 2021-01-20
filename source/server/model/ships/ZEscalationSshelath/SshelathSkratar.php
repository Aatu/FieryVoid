<?php
class SshelathSkratar extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 14*6;
	$this->faction = "ZEscalation Sshelath";
	$this->phpclass = "SshelathSkratar";
	$this->shipClass = "Skratar Light Bomber";
		$this->variantOf = "Skregha-A Light Fighters";
		$this->occurence = "uncommon";	
	$this->imagePath = "img/ships/EscalationWars/SshelathSkregha.png";
		$this->isd = 1960;
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
		$fighter = new Fighter("SshelathSkratar", $armour, 8, $this->id);
		$fighter->displayName = "Skratar";
		$fighter->imagePath = "img/ships/EscalationWars/SshelathSkregha.png";
		$fighter->iconPath = "img/ships/EscalationWars/SshelathSkregha_Large.png";
			
		$gun = new LightParticleBeam(330, 30, 1, 1);
		$gun->displayName = "Light Particle Beam";
		$fighter->addFrontSystem($gun);
        $fighter->addFrontSystem(new FighterMissileRack(3, 330, 30));
		
			
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
		$this->addSystem($fighter);
	}
    }
}


