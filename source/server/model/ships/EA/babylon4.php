<?php
class Babylon4 extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 4400;
        $this->faction = "Earth Alliance";       
		$this->phpclass = "Babylon4";
		$this->shipClass = "Babylon 4 Battle Station";
		$this->fighters = array("heavy"=>72); 
		$this->unofficial = true;

        $this->isd = 2254;
		$this->shipSizeClass = 3; //Enormous is not implemented
        $this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->nonRotating = true; //some bases do not rotate - this attribute is used in combination with $base or $smallBase

		$this->forwardDefense = 22;
		$this->sideDefense = 26;

		$this->imagePath = "img/ships/Babylon4.png";
		$this->canvasSize = 512; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);

		$this->addPrimarySystem(new Reactor(6, 30, 0, 0));
		$this->addPrimarySystem(new CnC(6, 30, 0, 0)); 
		$this->addPrimarySystem(new Scanner(6, 20, 4, 8));
		$this->addPrimarySystem(new Scanner(6, 20, 4, 8));
		$this->addPrimarySystem(new Hangar(6, 36, 12, 0, 'fighters'));
		$this->addPrimarySystem(new Hangar(6, 36, 12, 0, 'fighters'));
		$this->addPrimarySystem(new CargoBay(6, 25));
		$this->addPrimarySystem(new HeavyLaser(6, 8, 6, 0, 360));
		$this->addPrimarySystem(new HeavyLaser(6, 8, 6, 0, 360));
		$this->addPrimarySystem(new HeavyLaser(6, 8, 6, 0, 360));
		$this->addPrimarySystem(new HeavyLaser(6, 8, 6, 0, 360));

		$this->addFrontSystem(new InterceptorMkII(5, 4, 1, 300, 60));
		$this->addFrontSystem(new InterceptorMkII(5, 4, 1, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(5, 4, 1, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(5, 4, 1, 300, 60));
		$this->addFrontSystem(new QuadParticleBeam(5, 8, 4, 300, 60));

			$hangar = new Hangar(5, 12, 1, 0, 'shuttles');
				$hangar->startArc = 300;
				$hangar->endArc = 60;
				$this->addFrontSystem($hangar);		
			$cargoBay = new CargoBay(5, 25);
				$cargoBay->startArc = 300;
				$cargoBay->endArc = 60;
				$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 24, 0, 0);
				$subReactor->startArc = 300;
				$subReactor->endArc = 60;
				$this->addFrontSystem($subReactor);		

        $this->addAftSystem(new StdParticleBeam(5, 4, 1, 120, 240));
        $this->addAftSystem(new StdParticleBeam(5, 4, 1, 120, 240));
        $this->addAftSystem(new InterceptorMkII(5, 4, 1, 120, 240));
        $this->addAftSystem(new StdParticleBeam(5, 4, 1, 120, 240));
        $this->addAftSystem(new InterceptorMkII(5, 4, 1, 120, 240));
        $this->addAftSystem(new StdParticleBeam(5, 4, 1, 120, 240));
        $this->addAftSystem(new StdParticleBeam(5, 4, 1, 120, 240));

			$cargoBay = new CargoBay(5, 25);
				$cargoBay->startArc = 120;
				$cargoBay->endArc = 240;
				$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 24, 0, 0);
				$subReactor->startArc = 120;
				$subReactor->endArc = 240;
				$this->addAftSystem($subReactor);			

        $this->addLeftFrontSystem(new StdParticleBeam(5, 4, 1, 240, 360));
        $this->addLeftFrontSystem(new StdParticleBeam(5, 4, 1, 240, 360));
		$this->addLeftFrontSystem(new InterceptorMkII(5, 4, 1, 240, 360));
		$this->addLeftFrontSystem(new InterceptorMkII(5, 4, 1, 240, 360));
		$this->addLeftFrontSystem(new QuadParticleBeam(5, 8, 4, 240, 360));
		$this->addLeftFrontSystem(new HeavyInterceptorBattery(5, 6, 3, 240, 360));

			$cargoBay = new CargoBay(5, 25);
				$cargoBay->startArc = 240;
				$cargoBay->endArc = 360;
				$this->addLeftFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 24, 0, 0);
				$subReactor->startArc = 240;
				$subReactor->endArc = 360;
				$this->addLeftFrontSystem($subReactor);

        $this->addLeftAftSystem(new StdParticleBeam(5, 4, 1, 180, 300));
        $this->addLeftAftSystem(new StdParticleBeam(5, 4, 1, 180, 300));
		$this->addLeftAftSystem(new InterceptorMkII(5, 4, 1, 180, 300));
		$this->addLeftAftSystem(new InterceptorMkII(5, 4, 1, 180, 300));
		$this->addLeftAftSystem(new QuadParticleBeam(5, 8, 4, 180, 300));
		$this->addLeftAftSystem(new HeavyInterceptorBattery(5, 6, 3, 180, 300));

			$cargoBay = new CargoBay(5, 25);
				$cargoBay->startArc = 180;
				$cargoBay->endArc = 300;
				$this->addLeftAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 24, 0, 0);
				$subReactor->startArc = 180;
				$subReactor->endArc = 300;
				$this->addLeftAftSystem($subReactor);

        $this->addRightFrontSystem(new StdParticleBeam(5, 4, 1, 0, 120));
        $this->addRightFrontSystem(new StdParticleBeam(5, 4, 1, 0, 120));
		$this->addRightFrontSystem(new InterceptorMkII(5, 4, 1, 0, 120));
		$this->addRightFrontSystem(new InterceptorMkII(5, 4, 1, 0, 120));
		$this->addRightFrontSystem(new QuadParticleBeam(5, 8, 4, 0, 120));
		$this->addRightFrontSystem(new HeavyInterceptorBattery(5, 6, 3, 0, 120));

			$cargoBay = new CargoBay(5, 25);
				$cargoBay->startArc = 0;
				$cargoBay->endArc = 120;
				$this->addRightFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 24, 0, 0);
				$subReactor->startArc = 0;
				$subReactor->endArc = 120;
				$this->addRightFrontSystem($subReactor);

        $this->addRightAftSystem(new StdParticleBeam(5, 4, 1, 60, 180));
        $this->addRightAftSystem(new StdParticleBeam(5, 4, 1, 60, 180));
		$this->addRightAftSystem(new InterceptorMkII(5, 4, 1, 60, 180));
		$this->addRightAftSystem(new InterceptorMkII(5, 4, 1, 60, 180));
		$this->addRightAftSystem(new QuadParticleBeam(5, 8, 4, 60, 180));
		$this->addRightAftSystem(new HeavyInterceptorBattery(5, 6, 3, 60, 180));

			$cargoBay = new CargoBay(5, 25);
				$cargoBay->startArc = 60;
				$cargoBay->endArc = 180;
				$this->addRightAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 24, 0, 0);
				$subReactor->startArc = 60;
				$subReactor->endArc = 180;
				$this->addRightAftSystem($subReactor);

		$this->addPrimarySystem(new Structure( 6, 270));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(5, 130, 300,60));
		$this->addAftSystem(Structure::createAsOuter(5, 150, 120, 240));
		$this->addLeftFrontSystem(Structure::createAsOuter(5, 180, 240, 360));
		$this->addLeftAftSystem(Structure::createAsOuter(5, 180, 180, 300));
		$this->addRightFrontSystem(Structure::createAsOuter(5, 180, 0, 120));
		$this->addRightAftSystem(Structure::createAsOuter(5, 180, 60, 180));

	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
					13 => "Heavy Laser",
                    15 => "Scanner",
                    17 => "Hangar",
					18 => "Cargo Bay",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    1 => "TAG:Standard Particle Beam",
					2 => "TAG:Interceptor II",
					4 => "TAG:Quad Particle Beam",
					6 => "TAG:Hangar",
					8 => "TAG:Cargo Bay",
					10 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    3 => "TAG:Standard Particle Beam",
					4 => "TAG:Interceptor II",
					6 => "TAG:Cargo Bay",
					8 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    2 => "TAG:Quad Particle Beam",
                    3 => "TAG:Interceptor II",
					5 => "TAG:Heavy Interceptor Battery",
					6 => "TAG:Standard Particle Beam",
                    8 => "TAG:Cargo Bay",
					10 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    2 => "TAG:Quad Particle Beam",
                    3 => "TAG:Interceptor II",
					5 => "TAG:Heavy Interceptor Battery",
					6 => "TAG:Standard Particle Beam",
                    8 => "TAG:Cargo Bay",
					10 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    2 => "TAG:Quad Particle Beam",
                    3 => "TAG:Interceptor II",
					5 => "TAG:Heavy Interceptor Battery",
					6 => "TAG:Standard Particle Beam",
                    8 => "TAG:Cargo Bay",
					10 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    2 => "TAG:Quad Particle Beam",
                    3 => "TAG:Interceptor II",
					5 => "TAG:Heavy Interceptor Battery",
					6 => "TAG:Standard Particle Beam",
                    8 => "TAG:Cargo Bay",
					10 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
            	),
           	);

    }
}
