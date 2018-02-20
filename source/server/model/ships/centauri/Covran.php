<?php
class Covran extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 730;
		$this->faction = "Centauri";
        $this->phpclass = "Covran";
        $this->imagePath = "img/ships/covran.png";
        $this->shipClass = "Covran Scout";
        $this->shipSizeClass = 3;
        $this->limited = 33;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 15;
        
         
        $this->addPrimarySystem(new Reactor(6, 16, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(6, 20, 7, 14));
        //$this->addPrimarySystem(new ElintArray(6, 6, 2));
        $this->addPrimarySystem(new Engine(6, 16, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(6, 2));
		
        
        $this->addFrontSystem(new Thruster(5, 15, 0, 6, 1));
        $this->addFrontSystem(new GuardianArray(3, 4, 2, 180, 60));
        $this->addFrontSystem(new GuardianArray(3, 4, 2, 300, 180));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
		
        $this->addAftSystem(new TwinArray(3, 6, 2, 120, 0));
        $this->addAftSystem(new TwinArray(3, 6, 2, 0, 240));
        $this->addAftSystem(new GuardianArray(3, 4, 2, 120, 0));
        $this->addAftSystem(new GuardianArray(3, 4, 2, 0, 240));

        $this->addAftSystem(new Thruster(5, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(5, 10, 0, 4, 2));
		$this->addAftSystem(new JumpEngine(5, 18, 3, 16));
        
		$this->addLeftSystem(new Thruster(5, 15, 0, 4, 3));
		
		$this->addRightSystem(new Thruster(5, 15, 0, 4, 4));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 40));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 45));
        $this->addRightSystem(new Structure( 5, 45));
        $this->addPrimarySystem(new Structure( 6, 36));
		
		
    }

}



?>
