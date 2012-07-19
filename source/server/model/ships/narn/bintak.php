<?php
class Bintak extends BaseShip{
    
    function __construct($id, $userid, $name, $movement){
        parent::__construct($id, $userid, $name, $movement);
        
		$this->pointCost = 1250;
		$this->faction = "Narn";
        $this->phpclass = "Bintak";
        $this->imagePath = "img/ships/bintak.png";
        $this->shipClass = "Bin'Tak";
        $this->shipSizeClass = 3;
        $this->canvasSize = 280;
		
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;

        
        $this->addPrimarySystem(new Reactor(7, 30, 0, 0));
        $this->addPrimarySystem(new CnC(7, 28, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 32, 6, 10));
        $this->addPrimarySystem(new Engine(6, 28, 0, 12, 3));
		$this->addPrimarySystem(new JumpEngine(6, 30, 5, 20));
		$this->addPrimarySystem(new Hangar(6, 22));
        
        $this->addFrontSystem(new LightPulse(2, 4, 2, 300, 60));
        $this->addFrontSystem(new MagGun(5, 9, 8, 300, 60));
        
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        
        $this->addFrontSystem(new Thruster(5, 15, 0, 5, 1));
        $this->addFrontSystem(new Thruster(5, 15, 0, 5, 1));
        
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        
		$this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
		$this->addFrontSystem(new EnergyMine(4, 5, 4, 300, 60));
		//aft
		          

		$this->addAftSystem(new HeavyLaser(4, 8, 6, 180, 300));
		$this->addAftSystem(new HeavyLaser(4, 8, 6, 60, 180));
		$this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
		$this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
		$this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		
        $this->addAftSystem(new Thruster(5, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 3, 2));
		$this->addAftSystem(new Thruster(5, 12, 0, 3, 2));
        
		//left
		
		$this->addLeftSystem(new HeavyLaser(4, 8, 6, 240, 0));
		$this->addLeftSystem(new LightPulse(2, 4, 2, 240, 60));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 240, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 240, 0));
		$this->addLeftSystem(new IonTorpedo(4, 5, 4, 240, 0));
		$this->addLeftSystem(new Thruster(5, 20, 0, 6, 3));
              

		//right
		$this->addRightSystem(new HeavyLaser(4, 8, 6, 0, 120));
		$this->addRightSystem(new LightPulse(2, 4, 2, 300, 120));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 120));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 120));
		$this->addRightSystem(new IonTorpedo(4, 5, 4, 0, 120));
		$this->addRightSystem(new Thruster(5, 20, 0, 6, 4));
        
		
		//structures
        $this->addFrontSystem(new Structure(6, 108));
        $this->addAftSystem(new Structure(5, 87));
        $this->addLeftSystem(new Structure(5, 96));
        $this->addRightSystem(new Structure(5, 96));
        $this->addPrimarySystem(new Structure(6, 50));
        
    }

}



?>
