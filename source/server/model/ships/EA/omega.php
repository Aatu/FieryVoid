<?php
class Omega  extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 925;
		$this->faction = "EA";
        $this->phpclass = "Omega";
        $this->imagePath = "img/ships/omega.png";
        $this->shipClass = "Omega";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		
        
         
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 20, 4, 8));
        $this->addPrimarySystem(new Engine(6, 20, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(6, 26));
        $this->addPrimarySystem(new JumpEngine(6, 20, 3, 20));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
		$this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 0));
		$this->addFrontSystem(new HeavyLaser(4, 8, 6, 0, 60));
		$this->addFrontSystem(new HeavyPulse(4, 6, 4, 300, 0));
		$this->addFrontSystem(new HeavyPulse(4, 6, 4, 0, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 1, 240, 60));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 1, 300, 120));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new HeavyLaser(4, 8, 6, 180, 240));
		$this->addAftSystem(new HeavyLaser(4, 8, 6, 120, 180));
        $this->addAftSystem(new InterceptorMkII(2, 4, 1, 120, 300));
        $this->addAftSystem(new InterceptorMkII(2, 4, 1, 60, 240));
        
		$this->addLeftSystem(new Thruster(5, 13, 0, 5, 3));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new InterceptorMkII(2, 4, 1, 180, 0));

		
		$this->addRightSystem(new Thruster(5, 13, 0, 5, 4));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new InterceptorMkII(2, 4, 1, 0, 180));
		

        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 60));
        $this->addAftSystem(new Structure( 4, 50 ));
        $this->addLeftSystem(new Structure( 4, 70));
        $this->addRightSystem(new Structure( 4, 70));
        $this->addPrimarySystem(new Structure( 6,60));
    }
}