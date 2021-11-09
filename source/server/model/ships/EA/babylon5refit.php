<?php
class Babylon5Refit extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3000;
		$this->faction = 'EA';//"EA defenses";
		$this->phpclass = "Babylon5Refit";
		$this->shipClass = "Babylon 5 Battle Station";
			$this->occurence = "common";
			$this->variantOf = 'Babylon 5 Diplomatic Station';
		$this->fighters = array("heavy"=>48); 
		$this->customFighter = array("Thunderbolt"=>48);
	    $this->notes = 'Thunderbolt capable.';

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
		$this->canvasSize = 260; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);

		$this->addPrimarySystem(new Reactor(6, 25, 0, 0));
		$this->addPrimarySystem(new CnC(6, 27, 0, 0)); 
		$this->addPrimarySystem(new Scanner(6, 20, 4, 8));
		$this->addPrimarySystem(new Scanner(6, 20, 4, 8));
		$this->addPrimarySystem(new Hangar(6, 26));
		$this->addPrimarySystem(new Hangar(6, 26));
		$this->addPrimarySystem(new CargoBay(6, 25));
		$this->addPrimarySystem(new HeavyPulse(6, 6, 4, 0, 360));
		$this->addPrimarySystem(new HeavyPulse(6, 6, 4, 0, 360));
		$this->addPrimarySystem(new HeavyPulse(6, 6, 4, 0, 360));
		$this->addPrimarySystem(new HeavyPulse(6, 6, 4, 0, 360));
		$this->addPrimarySystem(new EnergyMine(6, 5, 4, 0, 360));
		$this->addPrimarySystem(new EnergyMine(6, 5, 4, 0, 360));

		$this->addFrontSystem(new Hangar(5, 8));
		$this->addFrontSystem(new CargoBay(5, 25));
		$this->addFrontSystem(new SubReactorUniversal(5, 20, 0, 0));
		$this->addFrontSystem(new InterceptorMkII(5, 4, 1, 300, 60));
		$this->addFrontSystem(new InterceptorMkII(5, 4, 1, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(5, 4, 1, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(5, 4, 1, 300, 60));
		
        $this->addAftSystem(new CargoBay(5, 25));
        $this->addAftSystem(new SubReactorUniversal(5, 20, 0, 0));
        $this->addAftSystem(new InterceptorMkII(5, 4, 1, 120, 240));
        $this->addAftSystem(new InterceptorMkII(5, 4, 1, 120, 240));
        $this->addAftSystem(new StdParticleBeam(5, 4, 1, 120, 240));
        $this->addAftSystem(new StdParticleBeam(5, 4, 1, 120, 240));
		
		$this->addLeftFrontSystem(new SubReactorUniversal(5, 18, 0, 0));
		$this->addLeftFrontSystem(new CargoBay(5, 25));
		$this->addLeftFrontSystem(new InterceptorMkII(3, 5, 1, 240, 360));
		$this->addLeftFrontSystem(new InterceptorMkII(3, 5, 1, 240, 360));
		$this->addLeftFrontSystem(new QuadParticleBeam(5, 8, 4, 240, 360));

		$this->addLeftAftSystem(new SubReactorUniversal(5, 18, 0, 0));
		$this->addLeftAftSystem(new CargoBay(5, 25));
		$this->addLeftAftSystem(new InterceptorMkII(5, 4, 1, 180, 300));
		$this->addLeftAftSystem(new InterceptorMkII(5, 4, 1, 180, 300));
		$this->addLeftAftSystem(new QuadParticleBeam(5, 8, 4, 180, 300));

		$this->addRightFrontSystem(new SubReactorUniversal(5, 18, 0, 0));
		$this->addRightFrontSystem(new CargoBay(5, 25));
		$this->addRightFrontSystem(new InterceptorMkII(5, 4, 1, 0, 120));
		$this->addRightFrontSystem(new InterceptorMkII(5, 4, 1, 0, 120));
		$this->addRightFrontSystem(new QuadParticleBeam(5, 8, 4, 0, 120));

		$this->addRightAftSystem(new SubReactorUniversal(5, 18, 0, 0));
		$this->addRightAftSystem(new CargoBay(5, 25));
		$this->addRightAftSystem(new InterceptorMkII(5, 4, 1, 60, 180));
		$this->addRightAftSystem(new InterceptorMkII(5, 4, 1, 60, 180));
		$this->addRightAftSystem(new QuadParticleBeam(5, 8, 4, 60, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 130));
        $this->addAftSystem(new Structure( 5, 130));
        $this->addLeftFrontSystem(new Structure( 5, 150));
        $this->addLeftAftSystem(new Structure( 5, 150));
        $this->addRightFrontSystem(new Structure( 5, 150));
        $this->addRightAftSystem(new Structure( 5, 150));        
		$this->addPrimarySystem(new Structure( 6, 180));

	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
					11 => "Energy Mine",
					13 => "Heavy Pulse Cannon",
                    15 => "Scanner",
                    17 => "Hangar",
					18 => "Cargo Bay",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    1 => "Standard Particle Beam",
					3 => "Interceptor II",
					4 => "Hangar",
					6 => "Cargo Bay",
					7 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    1 => "Standard Particle Beam",
					3 => "Interceptor II",
					6 => "Cargo Bay",
					7 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    1 => "Quad Particle Beam",
                    3 => "Interceptor II",
                    6 => "Cargo Bay",
					7 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    1 => "Quad Particle Beam",
                    3 => "Interceptor II",
                    6 => "Cargo Bay",
					7 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    1 => "Quad Particle Beam",
                    3 => "Interceptor II",
                    6 => "Cargo Bay",
					7 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    1 => "Quad Particle Beam",
                    3 => "Interceptor II",
                    6 => "Cargo Bay",
					7 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
            	),
           	);

    }
}
