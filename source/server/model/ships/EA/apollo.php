<?php
class Apollo extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 900;
        $this->faction = "EA";
        $this->phpclass = "Apollo";
        $this->imagePath = "img/ships/apollo.png";
        $this->shipClass = "Apollo Bombardment Cruiser";
        $this->shipSizeClass = 3;
        $this->canvasSize = 200;
        $this->limited = 33;

        $this->forwardDefense = 16;
        $this->sideDefense = 17;

        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 4));
        $this->addPrimarySystem(new JumpEngine(5, 20, 5, 24));
        $this->addPrimarySystem(new ReloadRack(5, 9));

        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new LMissileRack(4, 6, 0, 240, 60));
        $this->addFrontSystem(new LMissileRack(4, 6, 0, 300, 120));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 270, 90));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 270, 90));

        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
        $this->addAftSystem(new LMissileRack(4, 6, 0, 120, 300));
        $this->addAftSystem(new LMissileRack(4, 6, 0, 60, 240));

        $this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));
        $this->addLeftSystem(new LMissileRack(4, 6, 0, 240, 60));
        $this->addLeftSystem(new LHMissileRack(4, 8, 0, 240, 60));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
    	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
    	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 0));

        $this->addRightSystem(new Thruster(4, 15, 0, 6, 4));
        $this->addRightSystem(new LMissileRack(4, 6, 0, 300, 120));
        $this->addRightSystem(new LHMissileRack(4, 8, 0, 300, 120 ));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
    	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
    	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 5, 56));

    
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Reload Rack",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
                    8 => "Class-L Missile Rack",
                    11 => "Interceptor II",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Class-L Missile Rack",
                    11 => "Interceptor II",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
                    6 => "Class-LH Missile Rack",
                    8 => "Class-L Missile Rack",
                    10 => "Standard Particle Beam",
                    12 => "Interceptor II",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
                    6 => "Class-LH Missile Rack",
                    8 => "Class-L Missile Rack",
                    10 => "Standard Particle Beam",
                    12 => "Interceptor II",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}

?>
