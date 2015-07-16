<?php
class Paragon extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1160;
		$this->faction = "Orieni";
        $this->phpclass = "Paragon";
        $this->imagePath = "img/ships/prophet.png";
        $this->shipClass = "Paragon Strike Force Command Ship";
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

        $this->occurence = "rare";
        $this->limited = 33;
        
        $this->addPrimarySystem(new Reactor(5, 34, 0, 0));
        $this->addPrimarySystem(new CnC(6, 30, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 30, 4, 8));
        $this->addPrimarySystem(new Engine(5, 30, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(5, 38, 30));
        $this->addPrimarySystem(new JumpEngine(5, 40, 6, 25));
        $this->addPrimarySystem(new HKControlNode(5, 24, 3, 3));
        $this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));
        $this->addPrimarySystem(new SMissileRack(5, 6, 4, 0, 360));
        $this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));
        
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new SMissileRack(5, 6, 4, 270, 90));
        $this->addFrontSystem(new SMissileRack(5, 6, 4, 270, 90));
        $this->addFrontSystem(new HeavyGausscannon(4, 10, 4, 270, 90));
        $this->addFrontSystem(new HeavyGausscannon(4, 10, 4, 270, 90));

        $this->addAftSystem(new SMissileRack(5, 6, 4, 90, 270));
        $this->addAftSystem(new HeavyGausscannon(4, 10, 4, 90, 270));
        $this->addAftSystem(new HeavyGausscannon(4, 10, 4, 90, 270));
        $this->addAftSystem(new RapidGatling(2, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(2, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(2, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(2, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
		
        $this->addLeftSystem(new Thruster(4, 25, 0, 6, 3));
        $this->addLeftSystem(new HeavyLaserLance(3, 10, 4, 180, 360));
        $this->addLeftSystem(new HeavyLaserLance(3, 10, 4, 180, 360));
        $this->addLeftSystem(new HeavyGausscannon(3, 10, 4, 180, 360));
        $this->addLeftSystem(new HeavyGausscannon(3, 10, 4, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new SMissileRack(5, 6, 4, 240, 60));

        $this->addRightSystem(new Thruster(4, 25, 0, 6, 4));
        $this->addRightSystem(new HeavyLaserLance(3, 10, 4, 0, 180));
        $this->addRightSystem(new HeavyLaserLance(3, 10, 4, 0, 180));
        $this->addRightSystem(new HeavyGausscannon(3, 10, 4, 0, 180));
        $this->addRightSystem(new HeavyGausscannon(3, 10, 4, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new SMissileRack(5, 6, 4, 300, 120));

		//structures
        $this->addFrontSystem(new Structure(4, 60));
        $this->addAftSystem(new Structure(4, 60));
        $this->addLeftSystem(new Structure(4, 68));
        $this->addRightSystem(new Structure(4, 68));
        $this->addPrimarySystem(new Structure(5, 60));
        
    }

}



?>
