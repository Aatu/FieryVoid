<?php
class SshelathNiskiraB extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 228;
	$this->faction = "ZEscalation Sshel'ath Alliance";
	$this->phpclass = "SshelathNiskiraB";
	$this->shipClass = "Niskira-B Medium Fighters";
		$this->variantOf = "Niskira-A Medium Fighters";
		$this->occurence = "common";	
	$this->imagePath = "img/ships/EscalationWars/SshelathNiskira.png";
		$this->isd = 1959;
		$this->unofficial = true;
		
	$this->forwardDefense = 7;
	$this->sideDefense = 7;
	$this->freethrust = 8;
	$this->offensivebonus = 4;
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
		$armour = array(2, 1, 2, 2);
		$fighter = new Fighter("SshelathNiskiraB", $armour, 10, $this->id);
		$fighter->displayName = "Niskira-B";
		$fighter->imagePath = "img/ships/EscalationWars/SshelathNiskira.png";
		$fighter->iconPath = "img/ships/EscalationWars/SshelathNiskira_Large.png";
			
		$gun = new LightParticleBeam(330, 30, 3, 2);
		$gun->displayName = "Light Particle Beam";
		$fighter->addFrontSystem($gun);
            
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
		$this->addSystem($fighter);
	}
    }
}


