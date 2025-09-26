<?php
class TorataColotnarBase2220 extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2000;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Torata Regency";
		$this->phpclass = "TorataColotnarBase2220";
		$this->shipClass = "Colotnar Defense Base (2220)";
		$this->variantOf = "Colotnar Defense Base";
		$this->imagePath = "img/ships/TorataColotnar.png";
		$this->canvasSize = 350;
		$this->fighters = array("heavy"=>48);
		$this->isd = 2220;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 19;
		$this->sideDefense = 19;



		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
//		$this->addPrimarySystem(new ProtectedCnC(6, 32, 0, 0)); //originally 2 systems with structure 16, armor 5 each

		$cnc = new CnC(5, 16, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
		$this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 16, 0, 0);//all-around by default
		$this->addPrimarySystem($cnc);

		$this->addPrimarySystem(new Scanner(5, 20, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 20, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 4));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
		$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));

		//$this->addFrontSystem(new Hangar(4, 12));
		//$this->addFrontSystem(new CargoBay(4, 30));
		//$this->addFrontSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new ParticleAccelerator(4, 8, 8, 270, 90));
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 270, 90));
		$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));

			$hangar = new Hangar(4, 12);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);		
			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);
					
		//$this->addAftSystem(new Hangar(4, 12));
		//$this->addAftSystem(new CargoBay(4, 30));
		//$this->addAftSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new ParticleAccelerator(4, 8, 8, 90, 270));
		$this->addAftSystem(new PlasmaAccelerator(4, 10, 5, 90, 270));
		$this->addAftSystem(new PlasmaAccelerator(4, 10, 5, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));

			$hangar = new Hangar(4, 12);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);		
			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);
				
		//$this->addLeftSystem(new Hangar(4, 12));
		//$this->addLeftSystem(new CargoBay(4, 30));
		//$this->addLeftSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new ParticleAccelerator(4, 8, 8, 180, 0));
		$this->addLeftSystem(new PlasmaAccelerator(4, 10, 5, 180, 0));
		$this->addLeftSystem(new PlasmaAccelerator(4, 10, 5, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));

			$hangar = new Hangar(4, 12);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);		
			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);
										
		//$this->addRightSystem(new Hangar(4, 12));
		//$this->addRightSystem(new CargoBay(4, 30));
		//$this->addRightSystem(new SubReactorUniversal(4, 25, 0, 0));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new ParticleAccelerator(4, 8, 8, 0, 180));
		$this->addRightSystem(new PlasmaAccelerator(4, 10, 5, 0, 180));
		$this->addRightSystem(new PlasmaAccelerator(4, 10, 5, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));

			$hangar = new Hangar(4, 12);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);		
			$cargoBay = new CargoBay(4, 30);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);
		
		/*replaced by TAGed versions!			
		$this->addFrontSystem(new Structure( 4, 120));
		$this->addAftSystem(new Structure( 4, 120));
		$this->addLeftSystem(new Structure( 4, 120));
		$this->addRightSystem(new Structure( 4, 120));
		$this->addPrimarySystem(new Structure( 5, 160));		
		*/
		$this->addPrimarySystem(new Structure( 5, 160));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 120, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 120, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 120, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 120, 0, 180));		
		
		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				12 => "Light Particle Beam",
				14 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
			1=> array(
				1 => "TAG:Light Particle Beam",
				3 => "TAG:Plasma Accelerator",
				7 => "TAG:Particle Accelerator",
				8 => "TAG:Hangar",
				10 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "TAG:Light Particle Beam",
				3 => "TAG:Plasma Accelerator",
				7 => "TAG:Particle Accelerator",
				8 => "TAG:Hangar",
				10 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				1 => "TAG:Light Particle Beam",
				3 => "TAG:Plasma Accelerator",
				7 => "TAG:Particle Accelerator",
				8 => "TAG:Hangar",
				10 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "TAG:Light Particle Beam",
				3 => "TAG:Plasma Accelerator",
				7 => "TAG:Particle Accelerator",
				8 => "TAG:Hangar",
				10 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>