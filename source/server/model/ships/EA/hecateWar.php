<?php
class HecateWar extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 800;
	$this->faction = "EA";
        $this->phpclass = "HecateWar";
        $this->imagePath = "img/ships/hyperion.png";
        $this->shipClass = "Hecate War Cruiser (Rho)";
        $this->shipSizeClass = 3;

        $this->occurence = "common";
        $this->limited = 33;
	$this->variantOf = 'Hyperion Heavy Cruiser (Theta)';
	$this->unofficial = true;
        $this->fighters = array("normal"=>6);

	$this->isd = 2257;
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;        
         
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 3, 7));
        $this->addPrimarySystem(new Engine(5, 18, 0, 7, 4));
	$this->addPrimarySystem(new Hangar(5, 8));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new CustomBPAMedium(3, 0, 0, 300, 60));
	$this->addFrontSystem(new CustomBPAMedium(3, 0, 0, 300, 60));
	$this->addFrontSystem(new CustomBPAMedium(3, 0, 0, 300, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
	$this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new JumpEngine(4, 16, 3, 24));
	$this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
        
	$this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
	$this->addLeftSystem(new CustomBPAHeavy(4, 0, 0, 300, 0));
	$this->addLeftSystem(new CustomBPAHeavy(4, 0, 0, 180, 240));
	$this->addLeftSystem(new CustomBPALight(2, 0, 0, 180, 0));

	$this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
	$this->addRightSystem(new CustomBPAHeavy(4, 0, 0, 0, 60));
	$this->addRightSystem(new CustomBPAHeavy(4, 0, 0, 120, 180));
	$this->addRightSystem(new CustomBPALight(4, 0, 0, 0, 180));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 54));
        $this->hitChart = array(
                0=> array(
                        11 => "Structure",
                        13 => "Standard Particle Beam",
                        15 => "Scanner",
                        17 => "Engine",
                        18 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        8 => "Medium Bolter-Pulse Array",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        10 => "Jump Engine",
                        13 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Heavy Bolter-Pulse Array",
                        11 => "Light Bolter-Pulse Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Heavy Bolter-Pulse Array",
                        11 => "Light Bolter-Pulse Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
