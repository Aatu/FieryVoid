<?php
class Kabrik extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 350;
	$this->faction = "Brakiri";
        $this->phpclass = "Kabrik";
        $this->imagePath = "img/ships/kabrik.png";
        $this->shipClass = "Kabrik Police Ship";
        $this->agile = true;
        $this->gravitic = true;
        $this->canvasSize = 100;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 1;
	$this->iniativebonus = 60;

        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 8, 4, 6));
        $this->addPrimarySystem(new Engine(4, 7, 0, 7, 2));
	$this->addPrimarySystem(new Hangar(3, 7));
	$this->addPrimarySystem(new GraviticThruster(4, 8, 0, 4, 3));
	$this->addPrimarySystem(new GraviticThruster(4, 8, 0, 4, 4));
		
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GraviticBolt(3, 5, 2, 300, 120));
        $this->addFrontSystem(new GraviticCannon(3, 6, 5, 270, 90));
        $this->addFrontSystem(new GraviticThruster(4, 6, 0, 2, 1));
        $this->addFrontSystem(new GraviticThruster(4, 6, 0, 2, 1));
        
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 90, 270));
        $this->addAftSystem(new GraviticBolt(3, 5, 2, 90, 270));
        $this->addAftSystem(new GraviticThruster(4, 15, 0, 7, 2));
		
        $this->addPrimarySystem(new Structure( 4, 48));
    }
}
?>
