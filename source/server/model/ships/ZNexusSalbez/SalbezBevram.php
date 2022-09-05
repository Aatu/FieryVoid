<?php
class SalbezBevram extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 300;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "ZNexus Sal-bez";
		$this->phpclass = "SalbezBevram";
		$this->shipClass = "Bev'ram Mining Center";
		$this->imagePath = "img/ships/Nexus/salbez_civilianBase.png";
		$this->canvasSize = 175; 
		$this->unofficial = true;
		$this->isd = 2021;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->fighters = array("normal"=>6);

		$this->forwardDefense = 17;
		$this->sideDefense = 17;

		$this->addPrimarySystem(new Reactor(3, 17, 0, 0));
		$this->addPrimarySystem(new CnC(3, 10, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 10, 5, 5));
		$this->addPrimarySystem(new Hangar(3, 12));
		$this->addPrimarySystem(new CargoBay(3, 40));
		
		$this->addFrontSystem(new NexusIndustrialLaser(3, 6, 3, 270, 90));
		$this->addFrontSystem(new NexusIndustrialLaser(3, 6, 3, 270, 90));
		$this->addFrontSystem(new NexusParticleGrid(3, 3, 1, 270, 90));
		$this->addFrontSystem(new NexusParticleGrid(3, 3, 1, 270, 90));
		$this->addFrontSystem(new CustomIndustrialGrappler(3, 5, 0, 270, 90));
		$this->addFrontSystem(new CargoBay(3, 30));

		$this->addAftSystem(new NexusIndustrialLaser(3, 6, 3, 90, 270));
		$this->addAftSystem(new NexusIndustrialLaser(3, 6, 3, 90, 270));
		$this->addAftSystem(new NexusParticleGrid(3, 3, 1, 90, 270));
		$this->addAftSystem(new NexusParticleGrid(3, 3, 1, 90, 270));
		$this->addAftSystem(new CustomIndustrialGrappler(3, 5, 0, 90, 270));
		$this->addAftSystem(new CargoBay(3, 30));
			
		$this->addLeftSystem(new NexusIndustrialLaser(3, 6, 3, 180, 360));
		$this->addLeftSystem(new NexusIndustrialLaser(3, 6, 3, 180, 360));
		$this->addLeftSystem(new NexusParticleGrid(3, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusParticleGrid(3, 3, 1, 180, 360));
		$this->addLeftSystem(new CustomIndustrialGrappler(3, 5, 0, 180, 360));
		$this->addLeftSystem(new CargoBay(3, 30));

		$this->addRightSystem(new NexusIndustrialLaser(3, 6, 3, 0, 180));
		$this->addRightSystem(new NexusIndustrialLaser(3, 6, 3, 0, 180));
		$this->addRightSystem(new NexusParticleGrid(3, 3, 1, 0, 180));
		$this->addRightSystem(new NexusParticleGrid(3, 3, 1, 0, 180));
		$this->addRightSystem(new CustomIndustrialGrappler(3, 5, 0, 0, 180));
		$this->addRightSystem(new CargoBay(3, 30));

		$this->addFrontSystem(new Structure( 3, 60));
		$this->addAftSystem(new Structure( 3, 60));
		$this->addLeftSystem(new Structure( 3, 60));
		$this->addRightSystem(new Structure( 3, 60));
		$this->addPrimarySystem(new Structure( 3, 98));
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Cargo Bay",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Industrial Laser",
				4 => "Particle Grid",
				5 => "Industrial Grappler",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Industrial Laser",
				4 => "Particle Grid",
				5 => "Industrial Grappler",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Industrial Laser",
				4 => "Particle Grid",
				5 => "Industrial Grappler",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Industrial Laser",
				4 => "Particle Grid",
				5 => "Industrial Grappler",
				9 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>