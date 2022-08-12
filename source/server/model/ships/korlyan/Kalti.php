<?php
class Kalti extends FighterFlight{
				
		function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 276;
		$this->faction = "Kor-Lyan";
		$this->phpclass = "Kalti";
		$this->shipClass = "Kalti Interceptor Flight";
		$this->imagePath = "img/ships/korlyanKalti.png";

		$this->forwardDefense = 7;
		$this->sideDefense = 8;
		$this->freethrust = 12;
		$this->offensivebonus = 6;
		$this->jinkinglimit = 8;
		$this->turncost = 0.33;
		
		$this->isd = 2218;
        
		$this->iniativebonus = 90;
        $this->populate();
    }

    public function populate(){

        $current = count($this->systems);
        $new = $this->flightSize;
        $toAdd = $new - $current;

        for ($i = 0; $i < $toAdd; $i++){
		$armour = array(3, 1, 1, 1);
		$fighter = new Fighter("Kalti", $armour, 10, $this->id);
		$fighter->displayName = "Kalti";
		$fighter->imagePath = "img/ships/korlyanKalti.png";
		$fighter->iconPath = "img/ships/korlyanKalti_large.png";
			
		$fighter->addFrontSystem(new LightParticleBeam(330, 30, 3));
		$fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
		$this->addSystem($fighter);
	}
				}
}

?>