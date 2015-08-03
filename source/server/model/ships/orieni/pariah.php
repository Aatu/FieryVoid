<?php
class Pariah extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 790;
		$this->faction = "Orieni";
        $this->phpclass = "Pariah";
        $this->imagePath = "img/ships/enlightenment.png";
        $this->shipClass = "Pariah Light Command Ship";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>24);
        $this->canvasSize = 200;
		
        $this->forwardDefense = 19;
        $this->sideDefense = 18;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 3;

        $this->occurence = "uncommon";

        
        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new CnC(5, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 25, 4, 7));
        $this->addPrimarySystem(new Engine(5, 25, 0, 8, 4));
        $this->addPrimarySystem(new HKControlNode(5, 12, 1, 1));
        $this->addPrimarySystem(new Hangar(4, 15, 12));        
        
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));        
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));
        $this->addFrontSystem(new HeavyLaserLance(2, 6, 4, 240, 60));
        $this->addFrontSystem(new HeavyLaserLance(2, 6, 4, 300, 120));
        $this->addFrontSystem(new RapidGatling(2, 4, 1, 240, 120));

        $this->addAftSystem(new RapidGatling(1, 4, 1, 120, 240));
        $this->addAftSystem(new RapidGatling(1, 4, 1, 120, 240));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
		
        $this->addLeftSystem(new Thruster(4, 16, 0, 4, 3));
        $this->addLeftSystem(new Hangar(3, 14, 6));
        $this->addLeftSystem(new HeavyGaussCannon(3, 10, 4, 240, 360));
        $this->addLeftSystem(new SoMissileRack(3, 6, 0, 240, 360));
        $this->addLeftSystem(new SoMissileRack(3, 6, 0, 180, 300));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(2, 4, 1, 180, 360));

        $this->addRightSystem(new Thruster(4, 16, 0, 4, 4));
        $this->addRightSystem(new Hangar(3, 14, 6));     
        $this->addRightSystem(new HeavyLaserLance(3, 10, 4, 0, 120));
        $this->addRightSystem(new SoMissileRack(3, 6, 0, 0, 120));
        $this->addRightSystem(new SoMissileRack(3, 6, 0, 60, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(2, 4, 1, 0, 180));

		//structures
        $this->addFrontSystem(new Structure(4, 51));
        $this->addAftSystem(new Structure(4, 51));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 54));
        $this->addPrimarySystem(new Structure(5, 51));
        
    }

}



?>
