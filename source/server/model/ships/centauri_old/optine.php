<?php
class Optine extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 750;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Optine";
        $this->imagePath = "img/ships/optine.png";
        $this->shipClass = "Optine Battlecruiser";
        $this->shipSizeClass = 3;
	    $this->isd = 2005;

        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
       
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 23, 4, 8));
        $this->addPrimarySystem(new Engine(5, 10, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
        
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 240, 360));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 300, 60));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 300, 60));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 0, 120));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 270, 90));
		
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new JumpEngine(5, 25, 3, 20));
		$this->addAftSystem(new AssaultLaser(3, 6, 4, 180, 300));
		$this->addAftSystem(new AssaultLaser(3, 6, 4, 60, 180));
		$this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 60, 240));
		$this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 120, 300));
		
		$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
        $this->addLeftSystem(new AssaultLaser(3, 6, 4, 300, 360));
		$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
		
		$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
        $this->addRightSystem(new AssaultLaser(3, 6, 4, 0, 60));
		$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 38));
        $this->addAftSystem(new Structure( 4, 35));
        $this->addLeftSystem(new Structure( 4, 48));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 6, 55));
    }
}
