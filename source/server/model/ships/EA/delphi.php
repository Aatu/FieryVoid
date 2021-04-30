<?php
class Delphi extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

	$this->pointCost = 825;
	$this->faction = "EA";
        $this->phpclass = "Delphi";
        $this->imagePath = "img/ships/delphi.png";
        $this->shipClass = "Delphi Advanced Scout";
        $this->shipSizeClass = 3;
        $this->canvasSize = 200;
  	    $this->isd = 2268;        

        $this->forwardDefense = 15;
        $this->sideDefense = 16;

        $this->turncost = 1.25;
        $this->turndelaycost = 1.25;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 24, 6, 12));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 3));
        $this->addPrimarySystem(new JumpEngine(5, 20, 5, 24));
        $this->addPrimarySystem(new Hangar(4, 4));

        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new MediumPulse(4, 6, 3, 240, 60));
        $this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 120));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));

        $this->addAftSystem(new Thruster(4, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 5, 2));
        $this->addAftSystem(new MediumPulse(4, 6, 3, 120, 300));
        $this->addAftSystem(new MediumPulse(4, 6, 3, 60, 240));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 90, 270));

        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 0));

        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 50));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 5, 48));


        $this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        12 => "Jump Engine",
                        14 => "ELINT Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
                        7 => "Medium Pulse Cannon",
                        10 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Medium Pulse Cannon",
                        11 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        8 => "Standard Particle Beam",
                        11 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        8 => "Standard Particle Beam",
                        11 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
