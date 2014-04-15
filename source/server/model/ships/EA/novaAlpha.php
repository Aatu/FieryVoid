<?php
class NovaAlpha extends BaseShip{
    
    function __construct($id, $userid, $name, $movement){
        parent::__construct($id, $userid, $name, $movement);
        
		$this->pointCost = 950;
		$this->faction = "EA";
        $this->phpclass = "NovaAlpha";
        $this->imagePath = "img/ships/nova.png";
        $this->shipClass = "Nova Alpha Dreadnought";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
        $this->limited = 33;
        $this->fighters=array("normal"=>24);
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.33;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        
        $this->addPrimarySystem(new Reactor(5, 30, 0, -5));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 6, 4));
		$this->addPrimarySystem(new JumpEngine(5, 20, 3, 24));
		$this->addPrimarySystem(new Hangar(5, 26));
        
    
        
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 240, 60));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 300, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        
        $this->addFrontSystem(new MediumLaser(4, 6, 5, 300, 0));
        $this->addFrontSystem(new MediumLaser(4, 6, 5, 300, 0));
        
        $this->addFrontSystem(new MediumLaser(4, 6, 5, 0, 60));
        $this->addFrontSystem(new MediumLaser(4, 6, 5, 0, 60));
		//aft



        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 1, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 1, 2));
        
        $this->addAftSystem(new MediumLaser(4, 6, 5, 180, 300));
        $this->addAftSystem(new MediumLaser(4, 6, 5, 180, 300));
        
        $this->addAftSystem(new MediumLaser(4, 6, 5, 60, 180));
        $this->addAftSystem(new MediumLaser(4, 6, 5, 60, 180));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 120, 300));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 60, 240));
        
		//left
        
        $this->addLeftSystem(new MediumLaser(4, 6, 5, 240, 0));
		$this->addLeftSystem(new MediumLaser(4, 6, 5, 240, 0));
        $this->addLeftSystem(new MediumLaser(4, 6, 5, 240, 0));
        $this->addLeftSystem(new MediumLaser(4, 6, 5, 240, 0));
        $this->addLeftSystem(new MediumLaser(4, 6, 5, 240, 0));
        
		$this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
              

		//right
		$this->addRightSystem(new MediumLaser(4, 6, 5, 0, 120));
        $this->addRightSystem(new MediumLaser(4, 6, 5, 0, 120));
        $this->addRightSystem(new MediumLaser(4, 6, 5, 0, 120));
        $this->addRightSystem(new MediumLaser(4, 6, 5, 0, 120));
        $this->addRightSystem(new MediumLaser(4, 6, 5, 0, 120));
        
		$this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
        

		//structures
        $this->addFrontSystem(new Structure(5, 60));
        $this->addAftSystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(5, 55));
        
    }

}