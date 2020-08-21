<?php
class RogolonShogalovBase extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 700;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Small Races";
		$this->phpclass = "RogolonShogalovBase";
		$this->shipClass = "Rogolon Shogalov Planetary Defense Base";
		$this->imagePath = "img/ships/rogolonShogalov.png";
			$this->canvasSize = 200; //img has 200px per side
		$this->isd = 1962;
		
		$this->shipSizeClass = 3; 
		$this->fighters = array("normal"=>36, "superheavy" => 6);
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 19;
		$this->sideDefense = 19;

		$this->addPrimarySystem(new Reactor(4, 26, 0, 0));
		$this->addPrimarySystem(new CnC(4, 20, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 24, 5, 7));
		$this->addPrimarySystem(new Hangar(4, 20));
		$this->addPrimarySystem(new Hangar(4, 20));
		$this->addPrimarySystem(new Catapult(3, 6));
		$this->addPrimarySystem(new Catapult(3, 6));
		$this->addPrimarySystem(new CargoBay(4, 36));

		$this->addFrontSystem(new SoMissileRack(3, 6, 0, 240, 60));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		$this->addFrontSystem(new SoMissileRack(3, 6, 0, 300, 120));
		$this->addFrontSystem(new Catapult(3, 6));
		
		$this->addAftSystem(new SoMissileRack(3, 6, 0, 60, 240));
		$this->addAftSystem(new HeavyPlasma(3, 8, 5, 120, 240));
		$this->addAftSystem(new SoMissileRack(3, 6, 0, 120, 300));
		$this->addAftSystem(new Catapult(3, 6));
		
		$this->addLeftSystem(new SoMissileRack(3, 6, 0, 180, 360));
		$this->addLeftSystem(new HeavyPlasma(3, 8, 5, 210, 330));
		$this->addLeftSystem(new SoMissileRack(3, 6, 0, 180, 360));
		$this->addLeftSystem(new Catapult(3, 6));
		
		$this->addRightSystem(new SoMissileRack(3, 6, 0, 0, 180));
		$this->addRightSystem(new HeavyPlasma(3, 8, 5, 30, 150));
		$this->addRightSystem(new SoMissileRack(3, 6, 0, 0, 180));
		$this->addRightSystem(new Catapult(3, 6));

		$this->addFrontSystem(new Structure( 4, 70));
		$this->addAftSystem(new Structure( 4, 70));
		$this->addLeftSystem(new Structure( 4, 70));
		$this->addRightSystem(new Structure( 4, 70));
		$this->addPrimarySystem(new Structure( 4, 92));
		
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
				1 => "Chaff Launcher",
				3 => "Particle Projector",
				6 => "Heavy Particle Projector",
				10 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Heavy Plasma Cannon",
				5 => "SO-Missile Rack",
				7 => "Catapult",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Heavy Plasma Cannon",
				5 => "SO-Missile Rack",
				7 => "Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Heavy Plasma Cannon",
				5 => "SO-Missile Rack",
				7 => "Catapult",
				18 => "Structure",
				20 => "Primary",
			),
		);


		}
    }
?>
