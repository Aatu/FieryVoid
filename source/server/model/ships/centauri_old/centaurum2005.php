<?php
class Centaurum extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 850;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Centaurum";
        $this->imagePath = "img/ships/octurion.png";
        $this->shipClass = "Centaurum Battleship";
        $this->shipSizeClass = 3;
        $this->limited = 33;
        $this->fighters = array("normal"=>12);
	    $this->isd = 2005;
        $this->forwardDefense = 17;
        $this->sideDefense = 19;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.33;
        $this->accelcost = 5;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        
         
        $this->addPrimarySystem(new Reactor(6, 30, 0, 0));
        $this->addPrimarySystem(new CnC(6, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 25, 4, 8));
        $this->addPrimarySystem(new Engine(6, 25, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(5, 16));
        
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 240, 120));
		$this->addFrontSystem(new AssaultLaser(4, 6, 4, 300, 60));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		
        $this->addAftSystem(new Thruster(4, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 2, 2));
		$this->addAftSystem(new JumpEngine(6, 24, 4, 20));      
        $this->addAftSystem(new LightParticleBeamShip(3, 2, 1, 90, 270));  
        $this->addAftSystem(new AssaultLaser(4, 6, 4, 180, 300));
		$this->addAftSystem(new AssaultLaser(4, 6, 4, 120, 240));
        $this->addAftSystem(new AssaultLaser(4, 6, 4, 120, 240));
        $this->addAftSystem(new AssaultLaser(4, 6, 4, 60, 180));
		$this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
        $this->addLeftSystem(new AssaultLaser(4, 6, 4, 240, 360));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
		$this->addRightSystem(new Thruster(4, 20, 0, 6, 4));
        $this->addRightSystem(new AssaultLaser(4, 6, 4, 0, 120));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
       
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 55));
        $this->addAftSystem(new Structure( 5, 55));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 6, 56));
    }
}
