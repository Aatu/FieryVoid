<?php
class Brostilli extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3000;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Drazi Freehold";
		$this->phpclass = "Brostilli";
		$this->shipClass = "Brostilli Warbase";
		$this->imagePath = "img/ships/Brostilli.png";
		$this->canvasSize = 350;
		$this->fighters = array("light" => 24, "superheavy" => 4); //4 hangars for 6 fighters each, 4 catapults for 1 SHF each
		$this->isd = 2234;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 21;
		$this->sideDefense = 21;

		$this->enhancementOptionsEnabled[] = 'GUNSIGHT'; //can equip particle repeaters with Gunsights

		$this->addPrimarySystem(new Reactor(6, 26, 0, 4));
		$cnc = new CnC(6, 20, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
		$this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(6, 20, 0, 0);//all-around by default
		$this->addPrimarySystem($cnc);		
		
		$this->addPrimarySystem(new Scanner(6, 24, 5, 8));
		$this->addPrimarySystem(new Scanner(6, 24, 5, 8));
		$this->addPrimarySystem(new TwinArray(6, 6, 2, 0, 360));
		$this->addPrimarySystem(new TwinArray(6, 6, 2, 0, 360));
		$this->addPrimarySystem(new TwinArray(6, 6, 2, 0, 360));
		$this->addPrimarySystem(new TwinArray(6, 6, 2, 0, 360));
		$this->addPrimarySystem(new ParticleRepeater(6, 6, 4, 0, 360));
		$this->addPrimarySystem(new ParticleRepeater(6, 6, 4, 0, 360));

		//$this->addFrontSystem(new Hangar(5, 8));
        //$this->addFrontSystem(new Catapult(5, 6));		
		//$this->addFrontSystem(new CargoBay(5, 20));
		//$this->addFrontSystem(new SubReactorUniversal(5, 38, 0, 0));
		$this->addFrontSystem(new ParticleCannon(5, 8, 7, 300, 60));
		$this->addFrontSystem(new ParticleBlaster(5, 8, 5, 300, 60));
		$this->addFrontSystem(new ParticleBlaster(5, 8, 5, 300, 60));
		$this->addFrontSystem(new HvyParticleCannon(5, 12, 9, 0, 90));
		$this->addFrontSystem(new StdParticleBeam(5, 4, 1, 0, 90));
		$this->addFrontSystem(new StdParticleBeam(5, 4, 1, 0, 90));

			$cargoBay = new CargoBay(5, 20);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$catapult = new Catapult(5,  6);
			$catapult->startArc = 270;
			$catapult->endArc = 90;
			$this->addFrontSystem($catapult);
			$hangar = new Hangar(5,  8);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);						
			$subReactor = new SubReactorUniversal(5, 38, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);	

		//$this->addAftSystem(new Hangar(5, 8));
        //$this->addAftSystem(new Catapult(5, 6));		
		//$this->addAftSystem(new CargoBay(5, 20));
		//$this->addAftSystem(new SubReactorUniversal(5, 38, 0, 0));
		$this->addAftSystem(new ParticleCannon(5, 8, 7, 120, 240));
		$this->addAftSystem(new ParticleBlaster(5, 8, 5, 120, 240));
		$this->addAftSystem(new ParticleBlaster(5, 8, 5, 120, 240));
		$this->addAftSystem(new HvyParticleCannon(5, 12, 9, 180, 270));
		$this->addAftSystem(new StdParticleBeam(5, 4, 1, 180, 270));
		$this->addAftSystem(new StdParticleBeam(5, 4, 1, 180, 270));

			$cargoBay = new CargoBay(5, 20);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$catapult = new Catapult(5,  6);
			$catapult->startArc = 90;
			$catapult->endArc = 270;
			$this->addAftSystem($catapult);
			$hangar = new Hangar(5,  8);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);						
			$subReactor = new SubReactorUniversal(5, 38, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);	

		//$this->addLeftSystem(new Hangar(5, 8));
        //$this->addLeftSystem(new Catapult(5, 6));		
		//$this->addLeftSystem(new CargoBay(5, 20));
		//$this->addLeftSystem(new SubReactorUniversal(5, 38, 0, 0));
		$this->addLeftSystem(new ParticleCannon(5, 8, 7, 210, 330));
		$this->addLeftSystem(new ParticleBlaster(5, 8, 5, 210, 330));
		$this->addLeftSystem(new ParticleBlaster(5, 8, 5, 210, 330));
		$this->addLeftSystem(new HvyParticleCannon(5, 12, 9, 270, 360));
		$this->addLeftSystem(new StdParticleBeam(5, 4, 1, 270, 360));
		$this->addLeftSystem(new StdParticleBeam(5, 4, 1, 270, 360));

			$cargoBay = new CargoBay(5, 20);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$catapult = new Catapult(5,  6);
			$catapult->startArc = 180;
			$catapult->endArc = 360;
			$this->addLeftSystem($catapult);
			$hangar = new Hangar(5,  8);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);						
			$subReactor = new SubReactorUniversal(5, 38, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);			

		//$this->addRightSystem(new Hangar(5, 8));
        //$this->addRightSystem(new Catapult(5, 6));		
		//$this->addRightSystem(new CargoBay(5, 20));
		//$this->addRightSystem(new SubReactorUniversal(5, 38, 0, 0));
		$this->addRightSystem(new ParticleCannon(5, 8, 7, 30, 150));
		$this->addRightSystem(new ParticleBlaster(5, 8, 5, 30, 150));
		$this->addRightSystem(new ParticleBlaster(5, 8, 5, 30, 150));
		$this->addRightSystem(new HvyParticleCannon(5, 12, 9, 90, 180));
		$this->addRightSystem(new StdParticleBeam(5, 4, 1, 90, 180));
		$this->addRightSystem(new StdParticleBeam(5, 4, 1, 90, 180));

			$cargoBay = new CargoBay(5, 20);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$catapult = new Catapult(5,  6);
			$catapult->startArc = 0;
			$catapult->endArc = 180;
			$this->addRightSystem($catapult);
			$hangar = new Hangar(5,  8);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);						
			$subReactor = new SubReactorUniversal(5, 38, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);	
				
		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 5, 120));
		$this->addAftSystem(new Structure( 5, 120));
		$this->addLeftSystem(new Structure( 5, 120));
		$this->addRightSystem(new Structure( 5, 120));
		$this->addPrimarySystem(new Structure( 6, 120));		
		*/
		$this->addPrimarySystem(new Structure( 6, 120));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(5, 120, 270,90));
		$this->addAftSystem(Structure::createAsOuter(5, 120, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(5, 120, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(5, 120, 0, 180));		
		
		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				12 => "Particle Repeater",
				14 => "Twin Array",
				16 => "Scanner",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
			1=> array(
				1 => "TAG:Heavy Particle Cannon",
				3 => "TAG:Particle Blaster",
				4 => "TAG:Particle Cannon",
				6 => "TAG:Standard Particle Beam",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				10 => "TAG:Hangar",
				11 => "TAG:Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "TAG:Heavy Particle Cannon",
				3 => "TAG:Particle Blaster",
				4 => "TAG:Particle Cannon",
				6 => "TAG:Standard Particle Beam",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				10 => "TAG:Hangar",
				11 => "TAG:Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				1 => "TAG:Heavy Particle Cannon",
				3 => "TAG:Particle Blaster",
				4 => "TAG:Particle Cannon",
				6 => "TAG:Standard Particle Beam",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				10 => "TAG:Hangar",
				11 => "TAG:Catapult",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "TAG:Heavy Particle Cannon",
				3 => "TAG:Particle Blaster",
				4 => "TAG:Particle Cannon",
				6 => "TAG:Standard Particle Beam",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				10 => "TAG:Hangar",
				11 => "TAG:Catapult",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>