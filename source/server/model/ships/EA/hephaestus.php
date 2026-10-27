<?php
class hephaestus extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1000;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Earth Alliance";
		$this->phpclass = "hephaestus";
		$this->shipClass = "Hephaestus Small Base (Zeta)";
		$this->imagePath = "img/ships/hephaestus.png";
		$this->canvasSize = 200;
		$this->fighters = array("heavy"=>24);
		$this->isd = 2190;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 18;
		$this->sideDefense = 18;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(600); //pass magazine capacity - class-B launchers hold 60 rounds each!
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 600); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H      
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L	


		$cnc = new CnC(4, 15, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
		$this->addPrimarySystem($cnc);

		$this->addPrimarySystem(new Scanner(4, 14, 3, 5));
		$this->addPrimarySystem(new Scanner(4, 14, 3, 5));		
		$this->addPrimarySystem(new CargoBay(4, 36));
		$this->addPrimarySystem(new Reactor(4, 9, 0, 0));

		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		$this->addFrontSystem(new AmmoMissileRackS(4, 6, 0, 270, 90, $ammoMagazine, true));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
		$this->addFrontSystem(new InterceptorMkI(4, 4, 1, 270, 90));
		$this->addFrontSystem(new InterceptorMkI(4, 4, 1, 270, 90));

			$hangar = new Hangar(4, 7, 6);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);		
			$cargoBay = new CargoBay(4, 18);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 12);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);
						


		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		$this->addAftSystem(new AmmoMissileRackS(4, 6, 0, 90, 270, $ammoMagazine, true));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
		$this->addAftSystem(new InterceptorMkI(4, 4, 1, 90, 270));
		$this->addAftSystem(new InterceptorMkI(4, 4, 1, 90, 270));

			$hangar = new Hangar(4, 7, 6);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);		
			$cargoBay = new CargoBay(4, 18);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 12);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);
						


		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 360));
		$this->addLeftSystem(new AmmoMissileRackS(4, 6, 0, 180, 360, $ammoMagazine, true));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addLeftSystem(new InterceptorMkI(4, 4, 1, 180, 360));
		$this->addLeftSystem(new InterceptorMkI(4, 4, 1, 180, 360));

			$hangar = new Hangar(4, 7, 6);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);		
			$cargoBay = new CargoBay(4, 18);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 12);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);
						


		$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 180));
		$this->addRightSystem(new AmmoMissileRackS(4, 6, 0, 0, 180, $ammoMagazine, true));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new InterceptorMkI(4, 4, 1, 0, 180));
		$this->addRightSystem(new InterceptorMkI(4, 4, 1, 0, 180));

			$hangar = new Hangar(4, 7, 6);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);		
			$cargoBay = new CargoBay(4, 18);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 12);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);
		

		$this->addPrimarySystem(new Structure( 4, 80));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 80, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 80, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 80, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 80, 0, 180));		
		
		$this->hitChart = array(
			0=> array(
				10 => "Structure",
				14 => "Cargo Bay",
				16 => "Scanner",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
			1=> array(
				1 => "TAG:Standard Particle Beam",
				2 => "TAG:Interceptor I",
				3 => "TAG:Class-S Missile Rack",
				4 => "TAG:Particle Cannon",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Hangar",				
				10 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "TAG:Standard Particle Beam",
				2 => "TAG:Interceptor I",
				3 => "TAG:Class-S Missile Rack",
				4 => "TAG:Particle Cannon",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Hangar",				
				10 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				1 => "TAG:Standard Particle Beam",
				2 => "TAG:Interceptor I",
				3 => "TAG:Class-S Missile Rack",
				4 => "TAG:Particle Cannon",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Hangar",				
				10 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "TAG:Standard Particle Beam",
				2 => "TAG:Interceptor I",
				3 => "TAG:Class-S Missile Rack",
				4 => "TAG:Particle Cannon",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Hangar",				
				10 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>