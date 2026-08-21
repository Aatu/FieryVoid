<?php
class WorthusNew extends SmallStarBaseFourSections{
	/*two-section weapons are simply moved to PRIMARY. Hit chart modified to make PRIMARY weapons hittable from outer chart. */
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2000;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Centauri Republic (WotCR)";
		$this->phpclass = "WorthusNew";
		$this->shipClass = "Worthus Starbase";
		$this->imagePath = "img/ships/worthus.png";
		$this->canvasSize = 280; 
		$this->fighters = array("heavy"=>24); 
		$this->shipSizeClass = 3; 
		$this->Enormous = true; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 21;
		$this->sideDefense = 21;
		$this->isd = 2001;
		
		$cnc = new CnC(6, 25, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(6, 25, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);
		
		$this->addPrimarySystem(new Reactor(6, 48, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 24, 4, 8));
		$this->addPrimarySystem(new Scanner(6, 24, 4, 8));
		//4 Hangars from between sections, plus small PRIMARY hangar for shuttles - I make them into one PRIMARY hangar with extra armor
		$this->addPrimarySystem(new Hangar(7, 4));
		//2 all-around Imperial Lasers on PRIMARY, plus four 90-degrees from between outer sections
        	/*$this->addPrimarySystem(new ImperialLaser(5, 8, 5, 0, 90));
        	$this->addPrimarySystem(new ImperialLaser(5, 8, 5, 90, 180));
        	$this->addPrimarySystem(new ImperialLaser(5, 8, 5, 180, 270));
        	$this->addPrimarySystem(new ImperialLaser(5, 8, 5, 270, 360));*/
        	$this->addPrimarySystem(new ImperialLaser(6, 8, 5, 0, 360));
        	$this->addPrimarySystem(new ImperialLaser(6, 8, 5, 0, 360));
		//2 all-around Tactical Lasers on PRIMARY
        	$this->addPrimarySystem(new TacLaser(6, 5, 4, 0, 360));
        	$this->addPrimarySystem(new TacLaser(6, 5, 4, 0, 360));
		//8 90-degrees Light Particle Beams from between outer sections
        	/*$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 0, 90));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 0, 90));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 90, 180));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 90, 180));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 180, 270));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 180, 270));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 270, 360));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 270, 360));
			*/

			$this->addFrontSystem(new TacLaser(5, 5, 4, 300, 60));
        	$this->addFrontSystem(new LightParticleBeamShip(5, 2, 1, 300, 60));
        	$this->addFrontSystem(new LightParticleBeamShip(5, 2, 1, 300, 60));
			$cargoBay = new CargoBay(5, 25);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);		
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);			

			$this->addAftSystem(new TacLaser(5, 5, 4, 120, 240));
        	$this->addAftSystem(new LightParticleBeamShip(5, 2, 1, 120, 240));
        	$this->addAftSystem(new LightParticleBeamShip(5, 2, 1, 120, 240));
			$cargoBay = new CargoBay(5, 25);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);			
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);

			//Fwd Port systems			
			$hangar = new Hangar(5, 6, 6);
			$hangar->startArc = 270;
			$hangar->endArc = 360;
			$hangar->addTag("External Hangar");				
			$hangar->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(1, 3));				
			$this->addLeftFrontSystem($hangar);	
			$impLaser = new ImperialLaser(5, 8, 5, 270, 360);
			$impLaser->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc		
			$impLaser->addTag("Outer Imperial Laser");
			$impLaser->setStructureHome(array(1, 3));			
			$this->addLeftFrontSystem($impLaser);			
			$lPBeam = new LightParticleBeamShip(5, 2, 1, 270, 360);
			$lPBeam->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc		
			$lPBeam->addTag("Light Particle Beam");
			$lPBeam->setStructureHome(array(1, 3));			
			$this->addLeftFrontSystem($lPBeam);
			$lPBeam2 = new LightParticleBeamShip(5, 2, 1, 270, 360);
			$lPBeam2->overkillArcStructures = array(1, 3); //overkill spills to whichever Port quarter is in arc		
			$lPBeam2->addTag("Light Particle Beam");
			$lPBeam2->setStructureHome(array(1, 3));			
			$this->addLeftFrontSystem($lPBeam2);			

			$this->addLeftSystem(new TacLaser(5, 5, 4, 210, 330));
        	$this->addLeftSystem(new LightParticleBeamShip(5, 2, 1, 210, 330));
        	$this->addLeftSystem(new LightParticleBeamShip(5, 2, 1, 210, 330));
			$cargoBay = new CargoBay(5, 25);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);				
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);
		
			//Fwd Aft systems			
			$hangar = new Hangar(5, 6, 6);
			$hangar->startArc = 180;
			$hangar->endArc = 270;
			$hangar->addTag("External Hangar");				
			$hangar->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(2, 3));				
			$this->addLeftAftSystem($hangar);	
			$impLaser = new ImperialLaser(5, 8, 5, 180, 270);
			$impLaser->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc		
			$impLaser->addTag("Outer Imperial Laser");
			$impLaser->setStructureHome(array(2, 3));			
			$this->addLeftAftSystem($impLaser);			
			$lPBeam = new LightParticleBeamShip(5, 2, 1, 180, 270);
			$lPBeam->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc		
			$lPBeam->addTag("Light Particle Beam");
			$lPBeam->setStructureHome(array(2, 3));			
			$this->addLeftAftSystem($lPBeam);
			$lPBeam2 = new LightParticleBeamShip(5, 2, 1, 180, 270);
			$lPBeam2->overkillArcStructures = array(2, 3); //overkill spills to whichever Port quarter is in arc		
			$lPBeam2->addTag("Light Particle Beam");
			$lPBeam2->setStructureHome(array(2, 3));			
			$this->addLeftAftSystem($lPBeam2);	

			//Fwd Stbd systems			
			$hangar = new Hangar(5, 6, 6);
			$hangar->startArc = 0;
			$hangar->endArc = 90;
			$hangar->addTag("External Hangar");				
			$hangar->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(1, 4));				
			$this->addRightFrontSystem($hangar);	
			$impLaser = new ImperialLaser(5, 8, 5, 0, 90);
			$impLaser->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc		
			$impLaser->addTag("Outer Imperial Laser");
			$impLaser->setStructureHome(array(1, 4));			
			$this->addRightFrontSystem($impLaser);			
			$lPBeam = new LightParticleBeamShip(5, 2, 1, 0, 90);
			$lPBeam->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc		
			$lPBeam->addTag("Light Particle Beam");
			$lPBeam->setStructureHome(array(1, 4));			
			$this->addRightFrontSystem($lPBeam);
			$lPBeam2 = new LightParticleBeamShip(5, 2, 1, 0, 90);
			$lPBeam2->overkillArcStructures = array(1, 4); //overkill spills to whichever Port quarter is in arc		
			$lPBeam2->addTag("Light Particle Beam");
			$lPBeam2->setStructureHome(array(1, 4));			
			$this->addRightFrontSystem($lPBeam2);

			$this->addRightSystem(new TacLaser(5, 5, 4, 30, 150));
        	$this->addRightSystem(new LightParticleBeamShip(5, 2, 1, 30, 150));
        	$this->addRightSystem(new LightParticleBeamShip(5, 2, 1, 30, 150));
			$cargoBay = new CargoBay(5, 25);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);					
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);

			//Aft Stbd systems			
			$hangar = new Hangar(5, 6, 6);
			$hangar->startArc = 90;
			$hangar->endArc = 180;
			$hangar->addTag("External Hangar");			
			$hangar->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc					
			$hangar->setStructureHome(array(2, 4));				
			$this->addRightAftSystem($hangar);	
			$impLaser = new ImperialLaser(5, 8, 5, 90, 180);
			$impLaser->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc		
			$impLaser->addTag("Outer Imperial Laser");
			$impLaser->setStructureHome(array(2, 4));			
			$this->addRightAftSystem($impLaser);			
			$lPBeam = new LightParticleBeamShip(5, 2, 1, 90, 180);
			$lPBeam->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc		
			$lPBeam->addTag("Light Particle Beam");
			$lPBeam->setStructureHome(array(2, 4));			
			$this->addRightAftSystem($lPBeam);
			$lPBeam2 = new LightParticleBeamShip(5, 2, 1, 90, 180);
			$lPBeam2->overkillArcStructures = array(2, 4); //overkill spills to whichever Port quarter is in arc		
			//$lPBeam->addTag("Light Particle Beam");
			$lPBeam2->setStructureHome(array(2, 4));			
			$this->addRightAftSystem($lPBeam2);		


		$this->addPrimarySystem(new Structure( 6, 140));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(5, 108, 300,60));
		$this->addAftSystem(Structure::createAsOuter(5, 108, 120, 240));
		$this->addLeftSystem(Structure::createAsOuter(5, 108, 210, 330));
		$this->addRightSystem(Structure::createAsOuter(5, 108, 30, 150));		
		
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				11 => "Imperial Laser",
				13 => "Tactical Laser",
				16 => "Scanner",
				17 => "Hangar", 
				18 => "Reactor",
				20 => "TAG:C&C",
			),
			
			1=> array(
				2 => "TAG:Outer Imperial Laser",
				5 => "TAG:Light Particle Beam",
				7 => "TAG:Tactical Laser",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				11 => "TAG:External Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Outer Imperial Laser",
				5 => "TAG:Light Particle Beam",
				7 => "TAG:Tactical Laser",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				11 => "TAG:External Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "TAG:Outer Imperial Laser",
				5 => "TAG:Light Particle Beam",
				7 => "TAG:Tactical Laser",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				11 => "TAG:External Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Outer Imperial Laser",
				5 => "TAG:Light Particle Beam",
				7 => "TAG:Tactical Laser",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				11 => "TAG:External Hangar",
				18 => "Structure",
				20 => "Primary",
			),

		);
		
    }
}
?>
