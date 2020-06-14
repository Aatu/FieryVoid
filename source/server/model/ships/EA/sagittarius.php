<?php
class Sagittarius extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 700;
        $this->faction = "EA";
        $this->phpclass = "Sagittarius";
        $this->imagePath = "img/ships/sagittarius.png";
        $this->shipClass = "Sagittarius Missile Cruiser (Beta Model)";
        $this->shipSizeClass = 3;
        $this->limited = 33;

        $this->forwardDefense = 14;
        $this->sideDefense = 15;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;


        $this->addPrimarySystem(new Reactor(5, 20, 0, 6));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 3, 6));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));

        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new LMissileRack(3, 6, 0, 240, 120));
        $this->addFrontSystem(new LMissileRack(3, 6, 0, 240, 120));
        $this->addFrontSystem(new LMissileRack(3, 6, 0, 180, 60));
        $this->addFrontSystem(new LMissileRack(3, 6, 0, 300, 180));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
		
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));

        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
        $this->addLeftSystem(new LMissileRack(3, 6, 0, 180, 60));
        $this->addLeftSystem(new LMissileRack(3, 6, 0, 60, 300));

        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        $this->addRightSystem(new LMissileRack(3, 6, 0, 300,180));
        $this->addRightSystem(new LMissileRack(3, 6, 0, 60, 300 ));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 50));
        $this->addRightSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 5, 48));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        10 => "Reload Rack",
                        12 => "Standard Particle Beam",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        8 => "Class-L Missile Rack",
                        10 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Class-L Missile Rack",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Class-L Missile Rack",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}

?>
