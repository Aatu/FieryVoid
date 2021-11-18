<?php
class SalbezBezrak extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 650;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Sal-bez";
		$this->phpclass = "SalbezBezrak";
		$this->shipClass = "Bez'rak Armed Station";
		$this->imagePath = "img/ships/Nexus/salbez_combatBase.png";
		$this->canvasSize = 175; 
		$this->unofficial = true;
		$this->isd = 2081;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->fighters = array("normal"=>12);

		$this->forwardDefense = 17;
		$this->sideDefense = 17;

		$this->addPrimarySystem(new Reactor(4, 23, 0, 0));
		$this->addPrimarySystem(new CnC(4, 20, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 18, 6, 6));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new CargoBay(4, 10));
		$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));
		
		$this->addFrontSystem(new LaserCutter(3, 6, 4, 270, 90));
		$this->addFrontSystem(new LaserCutter(3, 6, 4, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 270, 90));
		$this->addFrontSystem(new NexusBoltTorpedo(3, 5, 2, 270, 90));
		$this->addFrontSystem(new Hangar(3, 3));
		$this->addFrontSystem(new CargoBay(3, 10));

		$this->addAftSystem(new LaserCutter(3, 6, 4, 90, 270));
		$this->addAftSystem(new LaserCutter(3, 6, 4, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 90, 270));
		$this->addAftSystem(new NexusBoltTorpedo(3, 5, 2, 90, 270));
		$this->addAftSystem(new Hangar(3, 3));
		$this->addAftSystem(new CargoBay(3, 10));
			
		$this->addLeftSystem(new LaserCutter(3, 6, 4, 180, 360));
		$this->addLeftSystem(new LaserCutter(3, 6, 4, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
		$this->addLeftSystem(new NexusBoltTorpedo(3, 5, 2, 180, 360));
		$this->addLeftSystem(new Hangar(3, 3));
		$this->addLeftSystem(new CargoBay(3, 10));

		$this->addRightSystem(new LaserCutter(3, 6, 4, 0, 180));
		$this->addRightSystem(new LaserCutter(3, 6, 4, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
		$this->addRightSystem(new NexusBoltTorpedo(3, 5, 2, 0, 180));
		$this->addRightSystem(new Hangar(3, 3));
		$this->addRightSystem(new CargoBay(3, 10));

		$this->addFrontSystem(new Structure( 3, 60));
		$this->addAftSystem(new Structure( 3, 60));
		$this->addLeftSystem(new Structure( 3, 60));
		$this->addRightSystem(new Structure( 3, 60));
		$this->addPrimarySystem(new Structure( 4, 98));
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				11 => "Cargo Bay",
				12 => "Light Particle Beam",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Laser Cutter",
				4 => "Light Particle Beam",
				5 => "Bolt Torpedo",
				7 => "Cargo Bay",
				9 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Laser Cutter",
				4 => "Light Particle Beam",
				5 => "Bolt Torpedo",
				7 => "Cargo Bay",
				9 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Laser Cutter",
				4 => "Light Particle Beam",
				5 => "Bolt Torpedo",
				7 => "Cargo Bay",
				9 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Laser Cutter",
				4 => "Light Particle Beam",
				5 => "Bolt Torpedo",
				7 => "Cargo Bay",
				9 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>