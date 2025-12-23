<?php
class SshelathAlnekra extends FighterFlight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 72;
	$this->faction = "Escalation Wars Sshel'ath Alliance";
	$this->phpclass = "SshelathAlnekra";
	$this->shipClass = "Alnekra Light Fighters";
	$this->imagePath = "img/ships/EscalationWars/SshelathAlnekra.png";
		$this->isd = 1900;
		$this->unofficial = true;
		
	$this->forwardDefense = 5;
	$this->sideDefense = 5;
	$this->freethrust = 7;
	$this->offensivebonus = 2;
	$this->jinkinglimit = 10;
	$this->turncost = 0.33;

	$this->iniativebonus = 100;
	$this->populate();

    $this->enhancementOptionsEnabled[] = 'EXT_AMMO'; //To enable extra Ammo for main gun.

	}

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(1, 1, 1, 1);
		$fighter = new Fighter("SshelathAlnekra", $armour, 6, $this->id);
		$fighter->displayName = "Alnekra";
		$fighter->imagePath = "img/ships/EscalationWars/SshelathAlnekra.png";
		$fighter->iconPath = "img/ships/EscalationWars/SshelathAlnekra_Large.png";
			
            $fighter->addFrontSystem(new SingleSlugCannon(330, 30, 1));
            
			$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
            
		$this->addSystem($fighter);
	}
    }
}


