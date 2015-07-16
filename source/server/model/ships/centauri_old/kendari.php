<?php
class Kendari extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 540;
        $this->faction = "The Lion";
        $this->phpclass = "Kendari";
        $this->imagePath = "img/ships/kendari.png";
        $this->shipClass = "Kendari Fleet Scout";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>6);
        $this->limited = 33;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		
        
         
        $this->addPrimarySystem(new Reactor(6, 22, 0, 0));
        $this->addPrimarySystem(new CnC(7, 18, 0, 0));
        $this->addPrimarySystem(new ElintScanner(6, 20, 4, 10));
        $this->addPrimarySystem(new Engine(6, 20, 0, 12, 2));
		$this->addPrimarySystem(new Hangar(6, 14));
        $this->addPrimarySystem(new TwinArray(4, 6, 2, 90, 270));
        
		
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));        

        $this->addFrontSystem(new LightParticleBeamShip(2, 6, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(2, 6, 1, 270, 90));
        $this->addFrontSystem(new SentinelPD(1, 4, 1, 240, 60));
        $this->addFrontSystem(new SentinelPD(1, 4, 1, 300, 120));


        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new JumpEngine(5, 25, 3, 20));
        $this->addAftSystem(new SentinelPD(1, 4, 1, 120, 240));
        
		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new LightParticleBeamShip(2, 6, 1, 180, 360));
		
		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new LightParticleBeamShip(2, 6, 1, 0 , 180));
		

        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 44));
        $this->addLeftSystem(new Structure( 4, 45));
        $this->addRightSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 5, 40));
    }
}