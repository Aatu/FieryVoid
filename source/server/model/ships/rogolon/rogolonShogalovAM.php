<?php
class RogolonShogalovAM extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 700;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Rogolon Dynasty";
		$this->phpclass = "RogolonShogalovAM";
		$this->shipClass = "Shogalov Planetary Defense Base";
		$this->imagePath = "img/ships/RogolonShogalov.png";
		$this->canvasSize = 200;
        $this->fighters = array("normal" => 36, "superheavy" => 6);
		$this->isd = 1962;

		$this->shipSizeClass = 3;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 19;
		$this->sideDefense = 19;


        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(96); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 96); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
		//Rogolons have ONLY Heavy Missiles available (besides Basic)

		$this->addPrimarySystem(new Reactor(4, 26, 0, 0));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 24, 5, 7));
		$this->addPrimarySystem(new Hangar(4, 20));
		$this->addPrimarySystem(new Hangar(4, 20));
		$this->addPrimarySystem(new Catapult(3, 6));		
		$this->addPrimarySystem(new Catapult(3, 6));		
		$this->addPrimarySystem(new CargoBay(4, 36));		

		//$this->addFrontSystem(new Catapult(3, 6));
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 240, 60, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(3, 0, 0, 300, 120, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
			
			$catapult = new Catapult(3, 6);
			$catapult->startArc = 270;
			$catapult->endArc = 90;
			$this->addFrontSystem($catapult);

		//$this->addAftSystem(new Catapult(3, 6));
		$this->addAftSystem(new AmmoMissileRackSO(3, 0, 0, 60, 240, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackSO(3, 0, 0, 120, 300, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new HeavyPlasma(3, 8, 5, 120, 240));
			
			$catapult = new Catapult(3, 6);
			$catapult->startArc = 90;
			$catapult->endArc = 270;
			$this->addAftSystem($catapult);

		//$this->addLeftSystem(new Catapult(3, 6));
		$this->addLeftSystem(new AmmoMissileRackSO(3, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new AmmoMissileRackSO(3, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new HeavyPlasma(3, 8, 5, 210, 330));
			
			$catapult = new Catapult(3, 6);
			$catapult->startArc = 180;
			$catapult->endArc = 360;
			$this->addLeftSystem($catapult);

		//$this->addRightSystem(new Catapult(3, 6));
		$this->addRightSystem(new AmmoMissileRackSO(3, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new AmmoMissileRackSO(3, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new HeavyPlasma(3, 8, 5, 30, 150));
			
			$catapult = new Catapult(3, 6);
			$catapult->startArc = 0;
			$catapult->endArc = 180;
			$this->addRightSystem($catapult);

		/*replaced by TAGed versions!		
		$this->addFrontSystem(new Structure( 4, 70));
		$this->addAftSystem(new Structure( 4, 70));
		$this->addLeftSystem(new Structure( 4, 70));
		$this->addRightSystem(new Structure( 4, 70));
		$this->addPrimarySystem(new Structure( 4, 92));
		*/		
		$this->addPrimarySystem(new Structure( 4, 92));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 70, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 70, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 70, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 70, 0, 180));

		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				11 => "Catapult",
				13 => "Cargo",
				15 => "Scanner",
				18 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "TAG:Heavy Plasma Cannon",
				5 => "TAG:Class-SO Missile Rack",
				7 => "TAG:Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Heavy Plasma Cannon",
				5 => "TAG:Class-SO Missile Rack",
				7 => "TAG:Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				2 => "TAG:Heavy Plasma Cannon",
				5 => "TAG:Class-SO Missile Rack",
				7 => "TAG:Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Heavy Plasma Cannon",
				5 => "TAG:Class-SO Missile Rack",
				7 => "TAG:Catapult",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>