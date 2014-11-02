<?php
class HyperionPatrol extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 750;
		$this->faction = "EA";
        $this->phpclass = "HyperionPatrol";
        $this->imagePath = "img/ships/hyperion.png";
        $this->shipClass = "Hyperion Patrol Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>6);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        
         
        $this->addPrimarySystem(new Reactor(5, 23, 0, 0));
        $this->addPrimarySystem(new CnC(5, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 3, 6));
        $this->addPrimarySystem(new Engine(6, 18, 0, 7, 3));
		$this->addPrimarySystem(new Hangar(5, 8));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
        

        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new MediumPulse(3, 6, 3, 300, 60));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 240, 120));
		$this->addFrontSystem(new MediumPulse(3, 6, 3, 300, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 240, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 300, 120));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new JumpEngine(4, 16, 3, 24));
		$this->addAftSystem(new InterceptorMkI(2, 4, 1, 120, 300));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 240));
		$this->addAftSystem(new MediumPulse(3, 6, 3, 60, 300));
        $this->addAftSystem(new MediumPulse(3, 6, 3, 60, 300));
        
		$this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));
		$this->addLeftSystem(new HeavyLaser(4, 8, 6, 300, 0));
	    $this->addLeftSystem(new MediumPulse(3, 6, 3, 180, 0));

		$this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
		$this->addRightSystem(new HeavyLaser(4, 8, 6, 0, 60));
		$this->addRightSystem(new MediumPulse(3, 6, 3, 0, 180));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addLeftSystem(new Structure( 4, 60));
        $this->addRightSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 5, 54));
    }
}