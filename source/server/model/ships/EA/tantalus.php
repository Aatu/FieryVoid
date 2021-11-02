<?php
class Tantalus  extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "EA";
        $this->phpclass = "Tantalus";
        $this->imagePath = "img/ships/tantalus.png";
        $this->shipClass = "Tantalus Assault Transport (Alpha)";
        $this->shipSizeClass = 3;
        $this->canvasSize = 200;
        $this->fighters = array("normal"=>12, "assault shuttles"=>24);
		$this->customFighter = array("Thunderbolt"=>12);
	    
        $this->limited = 33;
	    $this->isd = 2238;
	    $this->notes = 'Thunderbolt capable.';
        
        $this->forwardDefense = 17;
        $this->sideDefense = 18;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 4;
         
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(5, 42, 36));
        $this->addPrimarySystem(new Quarters(5, 21));
        $this->addPrimarySystem(new Quarters(5, 21));
        $this->addPrimarySystem(new JumpEngine(5, 20, 3, 24));
		
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 360));
		$this->addFrontSystem(new MediumPulse(4, 6, 3, 0, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));

        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 120, 300));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 240));
        
		$this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new InterceptorMkI(2, 4, 1, 240, 60));
		
		$this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new InterceptorMkI(2, 4, 1, 300, 120));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 50));
        $this->addAftSystem(new Structure( 5, 50 ));
        $this->addLeftSystem(new Structure( 5, 75));
        $this->addRightSystem(new Structure( 5, 75));
        $this->addPrimarySystem(new Structure( 5, 60));

        $this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "Jump Engine",
						12 => "Quarters",
                        14 => "Scanner",
                        16 => "Engine",
                        18 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
                        7 => "Medium Pulse Cannon",
                        9 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        10 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        8 => "Standard Particle Beam",
                        12 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        8 => "Standard Particle Beam",
                        12 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
