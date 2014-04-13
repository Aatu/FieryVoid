<?php
class Brathon extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 325;
	$this->faction = "Brakiri";
        $this->phpclass = "Brathon";
        $this->imagePath = "img/ships/brathon.png";
        $this->shipClass = "Brathon Auxiliary Cruiser";
        $this->shipSizeClass = 3;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 2, 5));
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 3));
	$this->addPrimarySystem(new Hangar(2, 4));
   
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));

        $this->addAftSystem(new GraviticBolt(3, 5, 2, 120, 300));
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 60, 240));

        $this->addLeftSystem(new GraviticThruster(4, 10, 0, 3, 1));
        $this->addLeftSystem(new GraviticCutter(3, 6, 5, 240, 0));
        $this->addLeftSystem(new GraviticThruster(3, 13, 0, 3, 3));
        $this->addLeftSystem(new GraviticThruster(3, 10, 0, 3, 2));

        $this->addRightSystem(new GraviticThruster(4, 10, 0, 3, 1));
        $this->addRightSystem(new GraviticCutter(3, 6, 5, 0, 120));
        $this->addRightSystem(new GraviticThruster(3, 13, 0, 3, 4));
        $this->addRightSystem(new GraviticThruster(3, 10, 0, 3, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 32));
        $this->addAftSystem(new Structure(4, 32));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(4, 64));
    }
}
?>