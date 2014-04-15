<?php
class Bashnar extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
	$this->faction = "Brakiri";
        $this->phpclass = "Bashnar";
        $this->imagePath = "img/ships/brathon.png";
        $this->shipClass = "Bashnar Auxiliary Carrier";
        $this->shipSizeClass = 3;
        
        $this->occurence = "uncommon";
        $this->fighters = array("light"=>6);
        
        $this->forwardDefense = 15;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(3, 15, 0, 2));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 16, 2, 4));
        $this->addPrimarySystem(new Engine(3, 16, 0, 6, 3));
	$this->addPrimarySystem(new Hangar(2, 8));
   
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));

        $this->addAftSystem(new GraviticBolt(3, 5, 2, 120, 300));
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 60, 240));

        $this->addLeftSystem(new GraviticThruster(4, 10, 0, 3, 1));
        $this->addLeftSystem(new Hangar(3, 6));
        $this->addLeftSystem(new GraviticThruster(3, 13, 0, 3, 3));
        $this->addLeftSystem(new GraviticThruster(3, 10, 0, 3, 2));

        $this->addRightSystem(new GraviticThruster(4, 10, 0, 3, 1));
        $this->addRightSystem(new Hangar(3, 6));
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