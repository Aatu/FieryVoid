<?php
class daverous extends SmallStarBaseFourSections{

		function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 850;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Centauri Republic (WotCR)";
		$this->phpclass = "daverous";
		$this->shipClass = "Daverous Civilian Base";
		$this->imagePath = "img/ships/daverous.png";
		$this->fighters = array("heavy"=>12); 

		$this->isd = 2003;

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->canvasSize = 280; 

		/*replace outer Structures with tagged ones
		$this->addFrontSystem(new Structure(4, 80));
		$this->addAftSystem(new Structure(4, 80));
		$this->addLeftSystem(new Structure(4, 80));
		$this->addRightSystem(new Structure(4, 80));
		*/				
		$this->addPrimarySystem(new Structure(4, 100)); //needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 80,270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 80, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 80, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 80, 0, 180));
		
		
		
		$this->hitChart = array(			
			0=> array(
				11 => "Structure",
				12 => "Light Particle Beam",
				14 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			
			1=> array(
				3 => "TAG:Light Particle Beam",
				5 => "TAG:Tactical Laser",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				18 => "Outer Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "TAG:Light Particle Beam",
				5 => "TAG:Tactical Laser",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				18 => "Outer Structure",
				20 => "Primary",
			),	
			3=> array(
				3 => "TAG:Light Particle Beam",
				5 => "TAG:Tactical Laser",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				18 => "Outer Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "TAG:Light Particle Beam",
				5 => "TAG:Tactical Laser",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Reactor",
				18 => "Outer Structure",
				20 => "Primary",
			),
			
			/* replaced with TAG system!
			1=> array(
				3 => "Light Particle Beam",
				5 => "Tactical Laser",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "Light Particle Beam",
				5 => "Tactical Laser",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				3 => "Light Particle Beam",
				5 => "Tactical Laser",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "Light Particle Beam",
				5 => "Tactical Laser",
				9 => "Cargo Bay",
				10 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			*/
		);


		$this->addPrimarySystem(new Reactor(4, 24, 0, 0));
		$this->addPrimarySystem(new CnC(4, 25, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 16, 3, 6));
		$this->addPrimarySystem(new Scanner(4, 16, 3, 6));
		$this->addPrimarySystem(new Hangar(4, 16));
       	$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));
       	$this->addPrimarySystem(new LightParticleBeamShip(4, 2, 1, 0, 360));;

		$this->addFrontSystem(new TacLaser(4, 5, 4, 270, 90));
       	$this->addFrontSystem(new LightParticleBeamShip(4, 2, 1, 270, 90));
       	$this->addFrontSystem(new LightParticleBeamShip(4, 2, 1, 270, 90));
		/*replaced by TAGged versions
		$this->addFrontSystem(new CargoBay(4, 20));
		$this->addFrontSystem(new SubReactorUniversal(4, 16, 0, 0));
		*/
			$cargoBay = new CargoBay(4, 20);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 16, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);

		$this->addAftSystem(new TacLaser(4, 5, 4, 90, 270));
       	$this->addAftSystem(new LightParticleBeamShip(4, 2, 1, 90, 270));
       	$this->addAftSystem(new LightParticleBeamShip(4, 2, 1, 90, 270));		
		/*replaced by TAGged versions
		$this->addAftSystem(new CargoBay(4, 20));
		$this->addAftSystem(new SubReactorUniversal(4, 16, 0, 0));*/
			$cargoBay = new CargoBay(4, 20);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 16, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);
		
		$this->addLeftSystem(new TacLaser(4, 5, 4, 180, 360));
       	$this->addLeftSystem(new LightParticleBeamShip(4, 2, 1, 180, 360));
       	$this->addLeftSystem(new LightParticleBeamShip(4, 2, 1, 180, 360));
		/*replaced by TAGged versions
		$this->addLeftSystem(new CargoBay(4, 20));
		$this->addLeftSystem(new SubReactorUniversal(4, 16, 0, 0));*/
			$cargoBay = new CargoBay(4, 20);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 16, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);
		
		$this->addRightSystem(new TacLaser(4, 5, 4, 0, 180));
       	$this->addRightSystem(new LightParticleBeamShip(4, 2, 1, 0, 180));
       	$this->addRightSystem(new LightParticleBeamShip(4, 2, 1, 0, 180));
		/*replaced by TAGged versions
		$this->addRightSystem(new CargoBay(4, 20));
		$this->addRightSystem(new SubReactorUniversal(4, 16, 0, 0));*/
			$cargoBay = new CargoBay(4, 20);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 16, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);
		
		}
    }
?>