<?php
class Hephaestus extends SmallStarBaseFourSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1000;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "EA";
		$this->phpclass = "Hephaestus";
		$this->shipClass = "Hephaestus Small Base (Zeta)";
		$this->imagePath = "img/ships/orion.png";
		$this->canvasSize = 140; 
		$this->fighters = array("heavy"=>24); 
		$this->isd = 2190;

		$this->unofficial = true;

		$this->shipSizeClass = 3; 
		$this->Enormous = false;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

		$this->addPrimarySystem(new Reactor(5, 9, 0, 0));
		$this->addPrimarySystem(new CnC(5, 15, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 14, 3, 5));
		$this->addPrimarySystem(new Scanner(5, 14, 3, 5));
		$this->addPrimarySystem(new CargoBay(5, 36));
		
		$this->addFrontSystem(new Hangar(4, 7));
		$this->addFrontSystem(new CargoBay(4, 18));
		$this->addFrontSystem(new SubReactorUniversal(4, 12, 0, 0));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		$this->addFrontSystem(new SMissileRack(4, 6, 0, 270, 90, true));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
        $this->addFrontSystem(new InterceptorMkI(4, 4, 1, 270, 90));        
        $this->addFrontSystem(new InterceptorMkI(4, 4, 1, 270, 90));        

		$this->addAftSystem(new Hangar(4, 7));
		$this->addAftSystem(new CargoBay(4, 18));
		$this->addAftSystem(new SubReactorUniversal(4, 12, 0, 0));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		$this->addAftSystem(new SMissileRack(4, 6, 0, 90, 270, true));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
        $this->addAftSystem(new InterceptorMkI(4, 4, 1, 90, 270));        
        $this->addAftSystem(new InterceptorMkI(4, 4, 1, 90, 270));        
			
		$this->addLeftSystem(new Hangar(4, 7));
		$this->addLeftSystem(new CargoBay(4, 18));
		$this->addLeftSystem(new SubReactorUniversal(4, 12, 0, 0));
		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 360));
		$this->addLeftSystem(new SMissileRack(4, 6, 0, 180, 360, true));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
        $this->addLeftSystem(new InterceptorMkI(4, 4, 1, 180, 360));        
        $this->addLeftSystem(new InterceptorMkI(4, 4, 1, 180, 360));        

		$this->addRightSystem(new Hangar(4, 7));
		$this->addRightSystem(new CargoBay(4, 18));
		$this->addRightSystem(new SubReactorUniversal(4, 12, 0, 0));
		$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 180));
		$this->addRightSystem(new SMissileRack(4, 6, 0, 0, 180, true));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
        $this->addFrontSystem(new InterceptorMkI(4, 4, 1, 0, 180));        
        $this->addFrontSystem(new InterceptorMkI(4, 4, 1, 0, 180));        

		$this->addFrontSystem(new Structure( 4, 80));
		$this->addAftSystem(new Structure( 4, 80));
		$this->addLeftSystem(new Structure( 4, 80));
		$this->addRightSystem(new Structure( 4, 80));
		$this->addPrimarySystem(new Structure( 5, 80));
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				14 => "Cargo Bay",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				1 => "Standard Particle Beam",
				2 => "Interceptor I",
				3 => "Class-S Missile Rack",
				4 => "Particle Cannon",
				8 => "Cargo Bay",
				9 => "Hangar",
				10 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "Standard Particle Beam",
				2 => "Interceptor I",
				3 => "Class-S Missile Rack",
				4 => "Particle Cannon",
				8 => "Cargo Bay",
				9 => "Hangar",
				10 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "Standard Particle Beam",
				2 => "Interceptor I",
				3 => "Class-S Missile Rack",
				4 => "Particle Cannon",
				8 => "Cargo Bay",
				9 => "Hangar",
				10 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "Standard Particle Beam",
				2 => "Interceptor I",
				3 => "Class-S Missile Rack",
				4 => "Particle Cannon",
				8 => "Cargo Bay",
				9 => "Hangar",
				10 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);

    	}
   	}
?>