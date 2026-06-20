<?php
class CircasianJenholen extends SmallStarBaseFourSections{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 900;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Escalation Wars Circasian Empire";
		$this->phpclass = "CircasianJenholen";
		$this->shipClass = "Jenholen Space Station";
		$this->imagePath = "img/ships/EscalationWars/CircasianJenholen.png";
		$this->fighters = array("normal"=>12); 
		$this->shipSizeClass = 3; 
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
        $this->unofficial = true;

		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 20;
		$this->sideDefense = 20;
		$this->canvasSize = 200; 
		$this->isd = 1948;
		
		$this->addPrimarySystem(new Structure( 4, 120)); //needs to be called first for some reason - static call apparently fails fot the first time...
		$this->addFrontSystem(Structure::createAsOuter(3, 110,270,90));
		$this->addAftSystem(Structure::createAsOuter(3, 110, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(3, 108, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(3, 108, 0, 180));
		
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				12 => "Quarters",
				15 => "Scanner",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C", 
			),
			
			1=> array(
				1 => "TAG:Light Particle Beam", 
				2 => "TAG:Light Particle Cannon",
				3 => "TAG:Light Plasma Cannon",
				5 => "TAG:Ranged Dual Rocket Launcher",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				10 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "TAG:Light Particle Beam", 
				2 => "TAG:Light Particle Cannon",
				3 => "TAG:Light Plasma Cannon",
				5 => "TAG:Ranged Dual Rocket Launcher",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				10 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "TAG:Light Particle Beam",
				4 => "TAG:Ranged Dual Rocket Launcher",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				10 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Light Particle Beam",
				4 => "TAG:Ranged Dual Rocket Launcher",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				10 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),

		);

		$this->addPrimarySystem(new Reactor(4, 25, 0, 0));
		$this->addPrimarySystem(new CnC(4, 24, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new Hangar(4, 12, 6));
		$this->addPrimarySystem(new Quarters(4, 15));
		$this->addPrimarySystem(new Quarters(4, 15));

		$this->addFrontSystem(new LightParticleCannon(3, 6, 5, 270, 90));
        	$this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 270, 90));
        	$this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 270, 90));
			$this->addFrontSystem(new EWRangedDualRocketLauncher(3, 6, 2, 270, 90)); 
			$this->addFrontSystem(new EWRangedDualRocketLauncher(3, 6, 2, 270, 90)); 
			$this->addFrontSystem(new LightPlasma(3, 4, 2, 270, 90)); 
			$this->addFrontSystem(new LightPlasma(3, 4, 2, 270, 90)); 
			$cargoBay = new CargoBay(3, 20);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 15, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);
			$hangar = new Hangar(3, 1, 1);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);

		$this->addAftSystem(new LightParticleCannon(3, 6, 5, 90, 270));
        	$this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 90, 270));
        	$this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 90, 270));
			$this->addAftSystem(new EWRangedDualRocketLauncher(3, 6, 2, 90, 270)); 
			$this->addAftSystem(new EWRangedDualRocketLauncher(3, 6, 2, 90, 270)); 
			$this->addAftSystem(new LightPlasma(3, 4, 2, 90, 270)); 
			$this->addAftSystem(new LightPlasma(3, 4, 2, 90, 270)); 
			$cargoBay = new CargoBay(3, 20);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 15, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);
			$hangar = new Hangar(3, 1, 1);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);

		$this->addRightSystem(new EWRangedDualRocketLauncher(3, 6, 2, 0, 180));
			$this->addRightSystem(new EWRangedDualRocketLauncher(3, 6, 2, 0, 180));
        	$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
        	$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
        	$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
			$cargoBay = new CargoBay(3, 30);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$cargoBay = new CargoBay(3, 30);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 15, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);
			$hangar = new Hangar(3, 1, 1);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);

		$this->addLeftSystem(new EWRangedDualRocketLauncher(3, 6, 2, 180, 360));
			$this->addLeftSystem(new EWRangedDualRocketLauncher(3, 6, 2, 180, 360));
        	$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
        	$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
        	$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
			$cargoBay = new CargoBay(3, 30);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$cargoBay = new CargoBay(3, 30);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 15, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);
			$hangar = new Hangar(3, 1, 1);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
		
    }
}
?>
