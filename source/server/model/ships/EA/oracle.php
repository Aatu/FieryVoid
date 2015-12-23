<?php
class Oracle extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "EA";
		$this->phpclass = "Oracle";
		$this->imagePath = "img/ships/oracle.png";
		$this->shipClass = "Oracle Light Cruiser";
		$this->shipSizeClass = 3;
		$this->occurence = "rare";

		$this->forwardDefense = 13;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 2;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 0;

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 20, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 3, 6));
		$this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
		$this->addPrimarySystem(new JumpEngine(5, 20, 4, 24));
		$this->addPrimarySystem(new Hangar(4, 4));
		$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 0, 360));

		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
		$this->addFrontSystem(new MediumPlasma(4, 5, 3, 300, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
		$this->addFrontSystem(new InterceptorMkI(2, 4, 1, 240, 60));
		$this->addFrontSystem(new InterceptorMkI(2, 4, 1, 300, 120));

		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));
		$this->addAftSystem(new InterceptorMkI(2, 4, 1, 120, 300));
		$this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 240));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));

		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new MediumLaser(4, 6, 5, 300, 360));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));

		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
		$this->addRightSystem(new MediumLaser(4, 6, 5, 0, 60));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure( 4, 48));
		$this->addAftSystem(new Structure( 4, 42));
		$this->addLeftSystem(new Structure( 4, 55));
        $this->addRightSystem(new Structure( 4, 55));
        $this->addPrimarySystem(new Structure( 5, 48));
		
		
		$this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        11 => "Jump Engine",
                        12 => "Standard Particle Beam",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Standard Particle Beam",
                        8 => "Medium Plasma Cannon",
                        11 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Standard Particle Beam",
                        11 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        6 => "Medium Laser",
                        8 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        6 => "Medium Laser",
                        8 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}