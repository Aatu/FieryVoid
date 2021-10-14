<?php
class baseTestbed extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 10;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Custom Ships";
		$this->phpclass = "baseTestbed";
		$this->shipClass = "Base Testbed";
		$this->imagePath = "img/ships/orion.png";
		$this->fighters = array("heavy"=>6); 

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->canvasSize = 280; 

		$this->addFrontSystem(new Structure( 2, 50));
		$this->addAftSystem(new Structure( 2, 50));
		$this->addLeftSystem(new Structure( 2, 50));
		$this->addRightSystem(new Structure( 2, 50));
		$this->addPrimarySystem(new Structure( 4, 70));
		
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				13 => "Cargo Bay",
				14 => "Hangar",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				1 => "Standard Particle Beam",
				2 => "Medium Plasma Cannon",
				7 => "Cargo Bay",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "Standard Particle Beam",
				2 => "Medium Plasma Cannon",
				7 => "Cargo Bay",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "Standard Particle Beam",
				2 => "Medium Plasma Cannon",
				7 => "Cargo Bay",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "Standard Particle Beam",
				2 => "Medium Plasma Cannon",
				7 => "Cargo Bay",
				8 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);


		$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
		$this->addPrimarySystem(new CnC(4, 15, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 14, 3, 4));
		$this->addPrimarySystem(new AMissileRack(5, 6, 0, 0, 360, true));
		$this->addPrimarySystem(new BMissileRack(6, 9, 0, 0, 360, true));
        $this->addPrimarySystem(new MultiMissileLauncher(3, 'B', 0, 360, true));
		$this->addPrimarySystem(new LMissileRack(5, 6, 0, 0, 360, true));
        $this->addPrimarySystem(new MultiMissileLauncher(3, 'L', 0, 360, true));
		$this->addPrimarySystem(new LHMissileRack(5, 6, 0, 0, 360, true));
        $this->addPrimarySystem(new MultiMissileLauncher(3, 'LH', 0, 360, true));
        $this->addPrimarySystem(new SoMissileRack(3, 6, 0, 0, 360, true));
        $this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 360, true));
		$this->addPrimarySystem(new MultiMissileLauncher(3, 'S', 0, 360, true));
        $this->addPrimarySystem(new EWOMissileRack(3, 6, 0, 0, 360, true));
        $this->addPrimarySystem(new RMissileRack(3, 6, 0, 0, 360, true));

		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
		$this->addFrontSystem(new Hangar(2, 1));
		$this->addFrontSystem(new CargoBay(2, 36));

		$this->addAftSystem(new MediumPlasma(2, 5, 3, 90, 270));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
		$this->addAftSystem(new Hangar(2, 1));
		$this->addAftSystem(new CargoBay(2, 36));
		
		$this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new Hangar(2, 1));
		$this->addRightSystem(new CargoBay(2, 36));
		
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new Hangar(2, 1));
		$this->addLeftSystem(new CargoBay(2, 36));
		
		}
    }
?>
