<?php
class Faithful extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 630;
		$this->faction = "Orieni";
        $this->phpclass = "Faithful";
        $this->imagePath = "img/ships/faithful.png";
        $this->shipClass = "Faithful Search Explorer Scout";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
        $this->canvasSize = 200;
		
        $this->forwardDefense = 18;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;

        $this->limited = 10;

        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new CnC(6, 30, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 25, 5, 6));
        $this->addPrimarySystem(new Engine(5, 25, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(4, 15, 12));
        $this->addPrimarySystem(new JumpEngine(5, 35, 6, 25));
        $this->addPrimarySystem(new CargoBay(4, 25));
        
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 2, 1));        
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new HeavyLaserLance(3, 6, 4, 240, 60));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));

        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        
        $this->addLeftSystem(new Thruster(4, 23, 0, 6, 3));
        $this->addLeftSystem(new CargoBay(2, 25));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new HeavyLaserLance(3, 6, 4, 0, 180));

        $this->addRightSystem(new Thruster(4, 23, 0, 6, 4));
        $this->addRightSystem(new CargoBay(2, 25));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new HeavyLaserLance(3, 6, 4, 180, 360));


		//structures
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 45));
        $this->addRightSystem(new Structure(4, 45));
        $this->addPrimarySystem(new Structure(5, 56));
        
    }

}



?>
