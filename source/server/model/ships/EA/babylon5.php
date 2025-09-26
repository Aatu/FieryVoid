<?php
class Babylon5 extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1000;
		//$this->faction = "Earth Alliance (defenses)";
        $this->faction = "Earth Alliance";       
		$this->phpclass = "Babylon5";
		$this->shipClass = "Babylon 5 Diplomatic Station";
		$this->fighters = array("heavy"=>24); 

        $this->isd = 2259;
		$this->shipSizeClass = 3; //Enormous is not implemented
        $this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->nonRotating = true; //some bases do not rotate - this attribute is used in combination with $base or $smallBase

		$this->forwardDefense = 20;
		$this->sideDefense = 24;

		$this->imagePath = "img/ships/Babylon5.png";
		$this->canvasSize = 512; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);

		$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
		$this->addPrimarySystem(new CnC(4, 27, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 20, 4, 7));
		$this->addPrimarySystem(new Scanner(4, 20, 4, 7));
		$this->addPrimarySystem(new Hangar(4, 26));
		$this->addPrimarySystem(new CargoBay(4, 25));

		//$this->addFrontSystem(new Hangar(3, 8));
		//$this->addFrontSystem(new CargoBay(3, 25));
		//$this->addFrontSystem(new SubReactorUniversal(3, 18, 0, 0));
		$this->addFrontSystem(new InterceptorMkI(3, 4, 1, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 60));

			$hangar = new Hangar(3, 8);
			$hangar->startArc = 300;
			$hangar->endArc = 60;
			$this->addFrontSystem($hangar);		
			$cargoBay = new CargoBay(3, 25);
			$cargoBay->startArc = 300;
			$cargoBay->endArc = 60;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 18, 0, 0);
			$subReactor->startArc = 300;
			$subReactor->endArc = 60;
			$this->addFrontSystem($subReactor);		

        //$this->addAftSystem(new CargoBay(3, 25));
        //$this->addAftSystem(new SubReactorUniversal(3, 18, 0, 0));
        $this->addAftSystem(new InterceptorMkI(3, 4, 1, 120, 240));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 120, 240));

			$cargoBay = new CargoBay(3, 25);
			$cargoBay->startArc = 120;
			$cargoBay->endArc = 240;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 18, 0, 0);
			$subReactor->startArc = 120;
			$subReactor->endArc = 240;
			$this->addAftSystem($subReactor);			

		//$this->addLeftFrontSystem(new SubReactorUniversal(3, 18, 0, 0));
		//$this->addLeftFrontSystem(new CargoBay(3, 25));
		$this->addLeftFrontSystem(new InterceptorMkI(3, 4, 1, 240, 360));
		$this->addLeftFrontSystem(new QuadParticleBeam(3, 8, 4, 240, 360));

			$cargoBay = new CargoBay(3, 25);
			$cargoBay->startArc = 240;
			$cargoBay->endArc = 360;
			$this->addLeftFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 18, 0, 0);
			$subReactor->startArc = 240;
			$subReactor->endArc = 360;
			$this->addLeftFrontSystem($subReactor);

		//$this->addLeftAftSystem(new SubReactorUniversal(3, 18, 0, 0));
		//$this->addLeftAftSystem(new CargoBay(3, 25));
		$this->addLeftAftSystem(new InterceptorMkI(3, 4, 1, 180, 300));
		$this->addLeftAftSystem(new QuadParticleBeam(3, 8, 4, 180, 300));

			$cargoBay = new CargoBay(3, 25);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 300;
			$this->addLeftAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 18, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 300;
			$this->addLeftAftSystem($subReactor);

		//$this->addRightFrontSystem(new SubReactorUniversal(3, 18, 0, 0));
		//$this->addRightFrontSystem(new CargoBay(3, 25));
		$this->addRightFrontSystem(new InterceptorMkI(3, 4, 1, 0, 120));
		$this->addRightFrontSystem(new QuadParticleBeam(3, 8, 4, 0, 120));

			$cargoBay = new CargoBay(3, 25);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 120;
			$this->addRightFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 18, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 120;
			$this->addRightFrontSystem($subReactor);

		//$this->addRightAftSystem(new SubReactorUniversal(3, 18, 0, 0));
		//$this->addRightAftSystem(new CargoBay(3, 25));
		$this->addRightAftSystem(new InterceptorMkI(3, 4, 1, 60, 180));
		$this->addRightAftSystem(new QuadParticleBeam(3, 8, 4, 60, 180));

			$cargoBay = new CargoBay(3, 25);
			$cargoBay->startArc = 60;
			$cargoBay->endArc = 180;
			$this->addRightAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(3, 18, 0, 0);
			$subReactor->startArc = 60;
			$subReactor->endArc = 180;
			$this->addRightAftSystem($subReactor);

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
		/*replaced by TAGed versions!		
        $this->addFrontSystem(new Structure( 3, 130));
        $this->addAftSystem(new Structure( 3, 130));
        $this->addLeftFrontSystem(new Structure( 3, 150));
        $this->addLeftAftSystem(new Structure( 3, 150));
        $this->addRightFrontSystem(new Structure( 3, 150));
        $this->addRightAftSystem(new Structure( 3, 150));        
		$this->addPrimarySystem(new Structure( 4, 180));
		*/
		$this->addPrimarySystem(new Structure( 4, 180));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(3, 130, 300,60));
		$this->addAftSystem(Structure::createAsOuter(3, 130, 120, 240));
		$this->addLeftFrontSystem(Structure::createAsOuter(3, 150, 240, 360));
		$this->addLeftAftSystem(Structure::createAsOuter(3, 150, 180, 300));
		$this->addRightFrontSystem(Structure::createAsOuter(3, 150, 0, 120));
		$this->addRightAftSystem(Structure::createAsOuter(3, 150, 60, 180));

	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    13 => "Structure",
                    15 => "Scanner",
                    17 => "Hangar",
					18 => "Cargo Bay",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    1 => "TAG:Standard Particle Beam",
					2 => "TAG:Interceptor I",
					3 => "TAG:Hangar",
					5 => "TAG:Cargo Bay",
					6 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    1 => "TAG:Standard Particle Beam",
					2 => "TAG:Interceptor I",
					5 => "TAG:Cargo Bay",
					6 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    4 => "TAG:Quad Particle Beam",
                    2 => "TAG:Interceptor I",
                    5 => "TAG:Cargo Bay",
					6 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    4 => "TAG:Quad Particle Beam",
                    2 => "TAG:Interceptor I",
                    5 => "TAG:Cargo Bay",
					6 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    4 => "TAG:Quad Particle Beam",
                    2 => "TAG:Interceptor I",
                    5 => "TAG:Cargo Bay",
					6 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    4 => "TAG:Quad Particle Beam",
                    2 => "TAG:Interceptor I",
                    5 => "TAG:Cargo Bay",
					6 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
            	),
           	);

    }
}
