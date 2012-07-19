<?php
class Nova extends BaseShip{
    
    function __construct($id, $userid, $name, $movement){
        parent::__construct($id, $userid, $name, $movement);
        
		$this->pointCost = 1350;
		$this->faction = "EA";
        $this->phpclass = "Nova";
        $this->imagePath = "img/ships/nova.png";
        $this->shipClass = "Nova Dreadnought";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
		
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.33;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        
        $this->addPrimarySystem(new Reactor(6, 40, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 20, 4, 8));
        $this->addPrimarySystem(new Engine(6, 20, 0, 6, 3));
		$this->addPrimarySystem(new JumpEngine(6, 20, 3, 24));
		$this->addPrimarySystem(new Hangar(6, 26));
        
    
        
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        
        $this->addFrontSystem(new LaserPulseArray(5, 9, 5, 300, 0));
        $this->addFrontSystem(new LaserPulseArray(5, 9, 5, 300, 0));
        
        $this->addFrontSystem(new LaserPulseArray(5, 9, 5, 0, 60));
        $this->addFrontSystem(new LaserPulseArray(5, 9, 5, 0, 60));
		//aft
		          

		
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        
        $this->addAftSystem(new LaserPulseArray(5, 9, 5, 180, 300));
        $this->addAftSystem(new LaserPulseArray(5, 9, 5, 180, 300));
        
        $this->addAftSystem(new LaserPulseArray(5, 9, 5, 60, 180));
        $this->addAftSystem(new LaserPulseArray(5, 9, 5, 60, 180));
        
		//left
        
        $this->addLeftSystem(new LaserPulseArray(5, 9, 5, 240, 0));
		$this->addLeftSystem(new LaserPulseArray(5, 9, 5, 240, 0));
        $this->addLeftSystem(new LaserPulseArray(5, 9, 5, 240, 0));
        $this->addLeftSystem(new LaserPulseArray(5, 9, 5, 240, 0));
        $this->addLeftSystem(new LaserPulseArray(5, 9, 5, 240, 0));
        
		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
              

		//right
		$this->addRightSystem(new LaserPulseArray(5, 9, 5, 0, 120));
        $this->addRightSystem(new LaserPulseArray(5, 9, 5, 0, 120));
        $this->addRightSystem(new LaserPulseArray(5, 9, 5, 0, 120));
        $this->addRightSystem(new LaserPulseArray(5, 9, 5, 0, 120));
        $this->addRightSystem(new LaserPulseArray(5, 9, 5, 0, 120));
        
		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
        
		
		//structures
        $this->addFrontSystem(new Structure(6, 60));
        $this->addAftSystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(6, 55));
        
    }

}



