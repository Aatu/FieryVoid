<?php
class OmegaGamma  extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 1100;
        $this->faction = "EA";
        $this->phpclass = "omegaGamma";
        $this->imagePath = "img/ships/omega.png";
        $this->shipClass = "Omega Command Destroyer (Gamma)";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
        $this->fighters = array("normal"=>24);
		$this->customFighter = array("Thunderbolt"=>24);
	    $this->notes = 'Thunderbolt capable.';
	    
	$this->isd = 2259;
	$this->variantOf = 'Omega Destroyer (Alpha)';
	$this->occurence = 'rare';
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	    
	$this->initiative = 1*5; 
         
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new ProtectedCnC(7, 20, 0, 0)); //One armor 6/str16 and a second one 4/4
        $this->addPrimarySystem(new Scanner(6, 20, 4, 8));
        $this->addPrimarySystem(new Engine(6, 20, 0, 8, 3));
	$this->addPrimarySystem(new Hangar(6, 26, 24));
        $this->addPrimarySystem(new JumpEngine(6, 20, 3, 20));
	    
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new HLPA(4, 0, 0, 300, 0));
		$this->addFrontSystem(new HLPA(4, 0, 0, 300, 0));
		$this->addFrontSystem(new HLPA(4, 0, 0, 0, 60));
		$this->addFrontSystem(new HLPA(4, 0, 0, 0, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));
	    
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
	$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new HLPA(4, 0, 0, 180, 240));
		$this->addAftSystem(new HLPA(4, 0, 0, 120, 180));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
        
	$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 0));
	    
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 60));
        $this->addAftSystem(new Structure( 4, 50 ));
        $this->addLeftSystem(new Structure( 4, 70));
        $this->addRightSystem(new Structure( 4, 70));
        $this->addPrimarySystem(new Structure( 6,60));
		
		
		$this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "Jump Engine",
                        14 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        8 => "Heavy Laser-Pulse Array",
                        11 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Heavy Laser-Pulse Array",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Standard Particle Beam",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Standard Particle Beam",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );		
    }
}
?>
