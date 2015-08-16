<?php
class Prophet extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 880;
		$this->faction = "Orieni";
        $this->phpclass = "Prophet";
        $this->imagePath = "img/ships/prophet.png";
        $this->shipClass = "Prophet Command Ship";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>30);
        $this->canvasSize = 280;
		
        $this->forwardDefense = 19;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 6;
        $this->rollcost = 4;
        $this->pivotcost = 3;

        $this->limited = 33;
        
        $this->addPrimarySystem(new Reactor(5, 34, 0, 0));
        $this->addPrimarySystem(new CnC(6, 30, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 30, 4, 7));
        $this->addPrimarySystem(new Engine(5, 30, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(5, 38, 30));
        $this->addPrimarySystem(new JumpEngine(5, 40, 6, 25));
        $this->addPrimarySystem(new HKControlNode(5, 24, 3, 3));
        
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new CargoBay(2, 25));
        $this->addFrontSystem(new SMissileRack(5, 6, 0, 270, 90));
        $this->addFrontSystem(new HeavyGausscannon(4, 10, 4, 270, 90));
        $this->addFrontSystem(new SMissileRack(5, 6, 0, 270, 90));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));

        $this->addAftSystem(new SMissileRack(5, 6, 0, 90, 270));
        $this->addAftSystem(new HeavyGausscannon(4, 10, 4, 90, 270));
        $this->addAftSystem(new RapidGatling(2, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(2, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(2, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));

		
        $this->addLeftSystem(new Thruster(4, 25, 0, 6, 3));
        $this->addLeftSystem(new CargoBay(2, 25));
        $this->addLeftSystem(new SMissileRack(5, 6, 0, 240, 60));
        $this->addLeftSystem(new HeavyLaserLance(3, 10, 4, 180, 360));
        $this->addLeftSystem(new HeavyLaserLance(3, 10, 4, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new HeavyGausscannon(4, 10, 4, 180, 360));


        $this->addRightSystem(new Thruster(4, 25, 0, 6, 4));
        $this->addRightSystem(new CargoBay(2, 25));
        $this->addRightSystem(new SMissileRack(5, 6, 0, 300, 120));
        $this->addRightSystem(new HeavyLaserLance(3, 10, 4, 0, 180));
        $this->addRightSystem(new HeavyLaserLance(3, 10, 4, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new HeavyGausscannon(4, 10, 4, 0, 180));


		//structures
        $this->addFrontSystem(new Structure(4, 60));
        $this->addAftSystem(new Structure(4, 60));
        $this->addLeftSystem(new Structure(4, 68));
        $this->addRightSystem(new Structure(4, 68));
        $this->addPrimarySystem(new Structure(5, 60));
        
    }

}



?>
