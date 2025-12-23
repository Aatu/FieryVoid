<?php
class SshelathNiskiraA extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 204;
	$this->faction = "Escalation Wars Sshel'ath Alliance";
	$this->phpclass = "SshelathNiskiraA";
	$this->shipClass = "Niskira-A Medium Fighters";
	$this->imagePath = "img/ships/EscalationWars/SshelathNiskira.png";
		$this->isd = 1935;
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
		$fighter = new Fighter("SshelathNiskiraA", $armour, 10, $this->id);
		$fighter->displayName = "Niskira-A";
		$fighter->imagePath = "img/ships/EscalationWars/SshelathNiskira.png";
		$fighter->iconPath = "img/ships/EscalationWars/SshelathNiskira_Large.png";
			
		$gun = new LightParticleBeam(330, 30, 2, 2);
		$gun->displayName = "Paired Particle Beam";
		$fighter->addFrontSystem($gun);
            
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
		$this->addSystem($fighter);
	}
    }
}


