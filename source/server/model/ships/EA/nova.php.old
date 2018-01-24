<?php
class Nova extends BaseShip{
    
    function __construct($id, $userid, $name, $movement){
        parent::__construct($id, $userid, $name, $movement);
        
		$this->pointCost = 1350;
		$this->faction = "EA";
        $this->phpclass = "Nova";
        $this->imagePath = "img/ships/nova.png";
        $this->shipClass = "Nova Dreadnought (Beta)";
	    $this->variantOf = 'DO NOT DISPLAY';
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
        $this->limited = 33;
        $this->fighters=array("normal"=>24);
	    $this->isd = 2242;
		
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
        
    
        
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 240, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 300, 120));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));        
        $this->addFrontSystem(new LaserPulseArray(4, 9, 5, 300, 0));
        $this->addFrontSystem(new LaserPulseArray(4, 9, 5, 300, 0));        
        $this->addFrontSystem(new LaserPulseArray(4, 9, 5, 0, 60));
        $this->addFrontSystem(new LaserPulseArray(4, 9, 5, 0, 60));
		
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));        
        $this->addAftSystem(new LaserPulseArray(3, 9, 5, 180, 300));
        $this->addAftSystem(new LaserPulseArray(3, 9, 5, 180, 300));        
        $this->addAftSystem(new LaserPulseArray(3, 9, 5, 60, 180));
        $this->addAftSystem(new LaserPulseArray(3, 9, 5, 60, 180));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 120, 300));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 240));
        
        $this->addLeftSystem(new LaserPulseArray(3, 9, 5, 240, 0));
		$this->addLeftSystem(new LaserPulseArray(3, 9, 5, 240, 0));
        $this->addLeftSystem(new LaserPulseArray(3, 9, 5, 240, 0));
        $this->addLeftSystem(new LaserPulseArray(3, 9, 5, 240, 0));
        $this->addLeftSystem(new LaserPulseArray(3, 9, 5, 240, 0));        
		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

		$this->addRightSystem(new LaserPulseArray(3, 9, 5, 0, 120));
        $this->addRightSystem(new LaserPulseArray(3, 9, 5, 0, 120));
        $this->addRightSystem(new LaserPulseArray(3, 9, 5, 0, 120));
        $this->addRightSystem(new LaserPulseArray(3, 9, 5, 0, 120));
        $this->addRightSystem(new LaserPulseArray(3, 9, 5, 0, 120));        
		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
        
		
		//structures
        $this->addFrontSystem(new Structure(6, 60));
        $this->addAftSystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(6, 55));


        $this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        10 => "Jump Engine",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        7 => "Laser/Pulse Array",
                        10 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        7 => "Laser/Pulse Array",
                        12 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        11 => "Laser/Pulse Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        11 => "Laser/Pulse Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );  
        
    }

}



