<?php
class KaltiAM extends FighterFlight{
				
		function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 276;
		$this->faction = "Kor-Lyan Kingdoms";
		$this->phpclass = "KaltiAM";
		$this->shipClass = "Kalti Interceptor Flight";
		$this->imagePath = "img/ships/korlyanKalti_v2.png";

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
		$fighter->imagePath = "img/ships/korlyanKalti_v2.png";
		$fighter->iconPath = "img/ships/korlyanKalti_large2.png";

		//ammo magazine itself (AND its missile options)
		$ammoMagazine = new AmmoMagazine(4); //pass magazine capacity - actual number of rounds, NOT number of salvoes
		$fighter->addAftSystem($ammoMagazine); //fit to ship immediately
		$ammoMagazine->addAmmoEntry(new AmmoMissileFB(), 0); //add basic missile as an option - but do NOT load any actual missiles at this moment - so weapon data is actually filled with _something_!
		$this->enhancementOptionsEnabled[] = 'AMMO_FB';//add enhancement options for missiles - Class-FB
		$this->enhancementOptionsEnabled[] = 'AMMO_FL';//add enhancement options for missiles - Class-FL
		$this->enhancementOptionsEnabled[] = 'AMMO_FH';//add enhancement options for missiles - Class-FH
		$this->enhancementOptionsEnabled[] = 'AMMO_FY';//add enhancement options for missiles - Class-FY
		$this->enhancementOptionsEnabled[] = 'AMMO_FD';//add enhancement options for missiles - Class-FD
			
		$fighter->addFrontSystem(new LightParticleBeam(330, 30, 3));
//		$fighter->addFrontSystem(new FighterMissileRack(4, 330, 30));
		$fighter->addFrontSystem(new AmmoFighterRack(330, 30, $ammoMagazine, false)); //$startArc, $endArc, $magazine, $base
			
		$fighter->addAftSystem(new RammingAttack(0, 0, 360, $fighter->getRammingFactor(), 0)); //ramming attack
			
		$this->addSystem($fighter);
	}
				}
}

?>