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
		$this->imagePath = "img/ships/Babylon5.png";
		$this->fighters = array("heavy"=>6); 
		$this->isd = 0;

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->nonRotating = true; //some bases do not rotate - this attribute is used in combination with $base or $smallBase

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->canvasSize = 280; 

		/*replaced by TAGed versions!		
		$this->addFrontSystem(new Structure( 2, 50));
		$this->addAftSystem(new Structure( 2, 50));
		$this->addLeftSystem(new Structure( 2, 50));
		$this->addRightSystem(new Structure( 2, 50));
		$this->addPrimarySystem(new Structure( 4, 70));
		*/
		$this->addPrimarySystem(new Structure( 4, 70));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(2, 50, 270,90));
		$this->addAftSystem(Structure::createAsOuter(2, 50, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(2, 50, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(2, 50, 0, 180));		


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
				1 => "TAG:Standard Particle Beam",
				2 => "TAG:Medium Plasma Cannon",
				7 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "TAG:Standard Particle Beam",
				2 => "TAG:Medium Plasma Cannon",
				7 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "TAG:Standard Particle Beam",
				2 => "TAG:Medium Plasma Cannon",
				7 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "TAG:Standard Particle Beam",
				2 => "TAG:Medium Plasma Cannon",
				7 => "TAG:Cargo Bay",
				8 => "TAG:Hangar",
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
		//$this->addFrontSystem(new Hangar(2, 1));
		//$this->addFrontSystem(new CargoBay(2, 36));

			$cargoBay = new CargoBay(2, 36);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$hangar = new Hangar(2, 1);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
				
		$this->addAftSystem(new MediumPlasma(2, 5, 3, 90, 270));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 90, 270));
		//$this->addAftSystem(new Hangar(2, 1));
		//$this->addAftSystem(new CargoBay(2, 36));

			$cargoBay = new CargoBay(2, 36);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$hangar = new Hangar(2, 1);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
						
		$this->addRightSystem(new MediumPlasma(2, 5, 3, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		//$this->addRightSystem(new Hangar(2, 1));
		//$this->addRightSystem(new CargoBay(2, 36));

			$cargoBay = new CargoBay(2, 36);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$hangar = new Hangar(2, 1);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
				
		$this->addLeftSystem(new MediumPlasma(2, 5, 3, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		//$this->addLeftSystem(new Hangar(2, 1));
		//$this->addLeftSystem(new CargoBay(2, 36));

			$cargoBay = new CargoBay(2, 36);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$hangar = new Hangar(2, 1);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);		

		}
    }
?>
