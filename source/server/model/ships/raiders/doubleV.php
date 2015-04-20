<?php
class DoubleV extends FighterFlight{
				
				function __construct($id, $userid, $name,  $slot){
								parent::__construct($id, $userid, $name,  $slot);
								
	$this->pointCost = 288;
	$this->faction = "Raiders";
						$this->phpclass = "DoubleV";
							$this->shipClass = "Double-V Medium Fighters";
	$this->imagePath = "img/ships/doubleV.png";
								
								$this->forwardDefense = 5;
								$this->sideDefense = 7;
								$this->freethrust = 10;
								$this->offensivebonus = 4;
								$this->jinkinglimit = 8;
								$this->turncost = 0.33;
								
	$this->iniativebonus = 90;
								
								for ($i = 0; $i<6; $i++){
		$armour = array(3, 0, 2, 2);
		$fighter = new Fighter("doubleV", $armour, 10, $this->id);
		$fighter->displayName = "Double-V Medium Fighter";
		$fighter->imagePath = "img/ships/doubleV.png";
		$fighter->iconPath = "img/ships/doubleV_large.png";
			
		$fighter->addFrontSystem(new PairedParticleGun(330, 30, 3));
		$fighter->addFrontSystem(new FighterMissileRack(2, 330, 30));
			
		$this->addSystem($fighter);
	}
				}
}

