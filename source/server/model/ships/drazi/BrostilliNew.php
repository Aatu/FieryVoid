<?php
class BrostilliNew extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3000;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Drazi Freehold";
		$this->phpclass = "BrostilliNew";
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

		$this->addFrontSystem(new ParticleCannon(5, 8, 7, 300, 60));
		$this->addFrontSystem(new ParticleBlaster(5, 8, 5, 300, 60));
		$this->addFrontSystem(new ParticleBlaster(5, 8, 5, 300, 60));
			$cargoBay = new CargoBay(5, 20);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$catapult = new Catapult(5,  6);
			$catapult->startArc = 270;
			$catapult->endArc = 90;
			$this->addFrontSystem($catapult);					
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);	

		$this->addAftSystem(new ParticleCannon(5, 8, 7, 120, 240));
		$this->addAftSystem(new ParticleBlaster(5, 8, 5, 120, 240));
		$this->addAftSystem(new ParticleBlaster(5, 8, 5, 120, 240));
			$cargoBay = new CargoBay(5, 20);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$catapult = new Catapult(5,  6);
			$catapult->startArc = 90;
			$catapult->endArc = 270;
			$this->addAftSystem($catapult);						
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);	

			//Fwd Port section
			$hangar = new Hangar(5, 8, 6);
			$hangar->startArc = 270;
			$hangar->endArc = 360;
			$hangar->addTag("External Hangar");				
			$hangar->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(1, 3));				
			$this->addLeftFrontSystem($hangar);						
			$subReactorSide = new SubReactorUniversal(5, 13, 0, 0);
			$subReactorSide->startArc = 270;
			$subReactorSide->endArc = 360;			
			$hangar->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(1, 3));				
			$this->addLeftFrontSystem($subReactorSide);
			$hvyCannon = new HvyParticleCannon(5, 12, 9, 270, 360);	
			$hvyCannon->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$hvyCannon->setStructureHome(array(1, 3));				
			$this->addLeftFrontSystem($hvyCannon);				
			$stdBeam1 = new StdParticleBeam(5, 4, 1, 270, 360);		
			$stdBeam1->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$stdBeam1->setStructureHome(array(1, 3));				
			$this->addLeftFrontSystem($stdBeam1);				
			$stdBeam2 = new StdParticleBeam(5, 4, 1, 270, 360);			
			$stdBeam2->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$stdBeam2->setStructureHome(array(1, 3));				
			$this->addLeftFrontSystem($stdBeam2);							

		$this->addLeftSystem(new ParticleCannon(5, 8, 7, 210, 330));
		$this->addLeftSystem(new ParticleBlaster(5, 8, 5, 210, 330));
		$this->addLeftSystem(new ParticleBlaster(5, 8, 5, 210, 330));
			$cargoBay = new CargoBay(5, 20);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$catapult = new Catapult(5,  6);
			$catapult->startArc = 180;
			$catapult->endArc = 360;
			$this->addLeftSystem($catapult);					
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);			

			//Aft Port section
			$hangar = new Hangar(5, 8, 6);
			$hangar->startArc = 180;
			$hangar->endArc = 270;
			$hangar->addTag("External Hangar");				
			$hangar->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(2, 3));				
			$this->addLeftAftSystem($hangar);						
			$subReactorSide = new SubReactorUniversal(5, 13, 0, 0);
			$subReactorSide->startArc = 180;
			$subReactorSide->endArc = 270;			
			$hangar->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(2, 3));				
			$this->addLeftAftSystem($subReactorSide);
			$hvyCannon = new HvyParticleCannon(5, 12, 9, 180, 270);	
			$hvyCannon->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$hvyCannon->setStructureHome(array(2, 3));				
			$this->addLeftAftSystem($hvyCannon);				
			$stdBeam1 = new StdParticleBeam(5, 4, 1, 180, 270);	
			$stdBeam1->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$stdBeam1->setStructureHome(array(2, 3));				
			$this->addLeftAftSystem($stdBeam1);				
			$stdBeam2 = new StdParticleBeam(5, 4, 1, 180, 270);		
			$stdBeam2->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$stdBeam2->setStructureHome(array(2, 3));				
			$this->addLeftAftSystem($stdBeam2);	

			//Fwd Stbd section
			$hangar = new Hangar(5, 8, 6);
			$hangar->startArc = 0;
			$hangar->endArc = 90;
			$hangar->addTag("External Hangar");				
			$hangar->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(1, 4));				
			$this->addRightFrontSystem($hangar);						
			$subReactorSide = new SubReactorUniversal(5, 13, 0, 0);
			$subReactorSide->startArc = 0;
			$subReactorSide->endArc = 90;			
			$hangar->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(1, 4));				
			$this->addRightFrontSystem($subReactorSide);
			$hvyCannon = new HvyParticleCannon(5, 12, 9, 0, 90);	
			$hvyCannon->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$hvyCannon->setStructureHome(array(1, 4));				
			$this->addRightFrontSystem($hvyCannon);				
			$stdBeam1 = new StdParticleBeam(5, 4, 1, 0, 90);
			$stdBeam1->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$stdBeam1->setStructureHome(array(1, 4));				
			$this->addRightFrontSystem($stdBeam1);				
			$stdBeam2 = new StdParticleBeam(5, 4, 1, 0, 90);			
			$stdBeam2->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$stdBeam2->setStructureHome(array(1, 4));				
			$this->addRightFrontSystem($stdBeam2);	

		$this->addRightSystem(new ParticleCannon(5, 8, 7, 30, 150));
		$this->addRightSystem(new ParticleBlaster(5, 8, 5, 30, 150));
		$this->addRightSystem(new ParticleBlaster(5, 8, 5, 30, 150));
			$cargoBay = new CargoBay(5, 20);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$catapult = new Catapult(5,  6);
			$catapult->startArc = 0;
			$catapult->endArc = 180;
			$this->addRightSystem($catapult);						
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);	

			//Aft Stbd section
			$hangar = new Hangar(5, 8, 6);
			$hangar->startArc = 90;
			$hangar->endArc = 180;
			$hangar->addTag("External Hangar");				
			$hangar->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(2, 4));				
			$this->addRightAftSystem($hangar);						
			$subReactorSide = new SubReactorUniversal(5, 13, 0, 0);
			$subReactorSide->startArc = 90;
			$subReactorSide->endArc = 180;			
			$hangar->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(2, 4));				
			$this->addRightAftSystem($subReactorSide);
			$hvyCannon = new HvyParticleCannon(5, 12, 9, 90, 180);	
			$hvyCannon->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$hvyCannon->setStructureHome(array(2, 4));				
			$this->addRightAftSystem($hvyCannon);				
			$stdBeam1 = new StdParticleBeam(5, 4, 1, 90, 180);	
			$stdBeam1->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$stdBeam1->setStructureHome(array(2, 4));				
			$this->addRightAftSystem($stdBeam1);				
			$stdBeam2 = new StdParticleBeam(5, 4, 1, 90, 180);		
			$stdBeam2->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$stdBeam2->setStructureHome(array(2, 4));				
			$this->addRightAftSystem($stdBeam2);				

		$this->addPrimarySystem(new Structure( 6, 120));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(5, 120, 300, 60));
		$this->addAftSystem(Structure::createAsOuter(5, 120, 120, 240));
		$this->addLeftSystem(Structure::createAsOuter(5, 120, 210, 330));
		$this->addRightSystem(Structure::createAsOuter(5, 120, 30, 150));		
		
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
				10 => "TAG:External Hangar",
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
				10 => "TAG:External Hangar",
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
				10 => "TAG:External Hangar",
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
				10 => "TAG:External Hangar",
				11 => "TAG:Catapult",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>