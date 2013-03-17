<?php
class OracleScout extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "EA";
        $this->phpclass = "OracleScout";
        $this->imagePath = "img/ships/oracle.png";
        $this->shipClass = "Oracle Scout Cruiser";
        $this->shipSizeClass = 3;
        
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
        $this->addPrimarySystem(new ElintScanner(5, 16, 3, 6));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
        $this->addPrimarySystem(new JumpEngine(5, 20, 4, 24));
	$this->addPrimarySystem(new Hangar(4, 4));
        $this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 360));
        
	$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
	$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
	$this->addFrontSystem(new ElintScanner(4, 10, 2, 4));
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
    }
}