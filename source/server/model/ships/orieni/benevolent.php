<?php
class Benevolent extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 640;
		$this->faction = "Orieni";
        $this->phpclass = "Benevolent";
        $this->imagePath = "img/ships/benevolent.png";
        $this->shipClass = "Benevolent Heavy Scout";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
        $this->canvasSize = 200;
		
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;

        $this->limited = 33;

        $this->addPrimarySystem(new Reactor(5, 24, 0, 0));
        $this->addPrimarySystem(new CnC(6, 25, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 25, 8, 9));
        $this->addPrimarySystem(new Engine(5, 25, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(4, 14, 12));
        $this->addPrimarySystem(new JumpEngine(5, 30, 6, 25));
        $this->addPrimarySystem(new SMissileRack(5, 6, 0, 0, 360));
        
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
        $this->addFrontSystem(new HeavyLaserLance(3, 6, 4, 240, 60));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new HeavyLaserLance(3, 6, 4, 300, 120));

        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        
        $this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));

        $this->addRightSystem(new Thruster(4, 20, 0, 6, 4));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));


		//structures
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 45));
        $this->addRightSystem(new Structure(4, 45));
        $this->addPrimarySystem(new Structure(5, 56));
        
    }

}



?>
