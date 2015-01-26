<?php
class Notali extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1000;
	$this->faction = "Yolu";
        $this->phpclass = "Notali";
        $this->imagePath = "img/ships/notali.png";
        $this->shipClass = "Notali Carrier";
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 28, 12, 4);
        $this->addPrimarySystem(new Engine(5, 23, 0, 10, 5));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new JumpDrive(5, 15, 4));
   
   
        $this->addFrontSystem(new GraviticThruster(5, 21, 0, 6, 1));

		
        $this->addAftSystem(new GraviticThruster(5, 32, 0, 10, 2));

		
        $this->addLeftSystem(new GraviticThruster(5, 18, 0, 6, 3));

		
        $this->addRightSystem(new GraviticThruster(5, 18, 0, 5, 4));
		
		
   
		$this->addPrimarySystem(new Hangar(5, 12));
		$this->addPrimarySystem(new Hangar(4, 12));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
		primary 6, 58
		front 6 52
		aft 5 58
		left 6, 66
		
		24 fighter
    }
}
