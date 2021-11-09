<?php
class EarthforceOne  extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "EA";
        $this->phpclass = "EarthforceOne";
        $this->imagePath = "img/ships/earthforce_one.png";
        $this->shipClass = "EarthForce One (Delta)";
        $this->shipSizeClass = 3;
        $this->canvasSize = 180;
        $this->fighters = array("normal"=>12);
		$this->customFighter = array("Thunderbolt"=>12);
        $this->occurence = "unique"; 
	    
	    $this->isd = 2251;
	    $this->notes = 'Thunderbolt capable.';
	    $this->notes .= '<br>Only one in service.';
        
        $this->forwardDefense = 17;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;	
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 9));
		$this->addPrimarySystem(new ProtectedCnC(6, 28, 0, 0)); //originally 2 systems (one in forward structure) with sructure 12+16, armor 5 each
        $this->addPrimarySystem(new Scanner(5, 16, 3, 6));
        $this->addPrimarySystem(new Engine(6, 20, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(5, 7));
        $this->addPrimarySystem(new JumpEngine(6, 15, 5, 20));
		$this->addPrimarySystem(new Quarters(5, 21));
		$this->addPrimarySystem(new Quarters(5, 21));
		
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new InterceptorMkII(3, 4, 2, 240, 120));

        $this->addAftSystem(new Thruster(5, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(5, 12, 0, 4, 2));
        $this->addAftSystem(new InterceptorMkII(3, 4, 2, 120, 300));
        $this->addAftSystem(new InterceptorMkII(3, 4, 2, 60, 240));
        
		$this->addLeftSystem(new Thruster(4, 13, 0, 5, 3));
		$this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 0));
		
		$this->addRightSystem(new Thruster(4, 13, 0, 5, 4));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 5, 42 ));
        $this->addLeftSystem(new Structure( 5, 50));
        $this->addRightSystem(new Structure( 5, 50));
        $this->addPrimarySystem(new Structure( 5, 54));

        $this->hitChart = array(
                0=> array(
                        9 => "Structure",
						11 => "Quarters",
                        12 => "Jump Engine",
                        14 => "Scanner",
                        16 => "Engine",
                        18 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        8 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        6 => "Thruster",
                        8 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        6 => "Thruster",
                        8 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
