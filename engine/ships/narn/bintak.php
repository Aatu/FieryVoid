<?php
class Bintak extends BaseShip{
    
    function __construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement){
        parent::__construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement);
        
		$this->pointCost = 1250;
		$this->faction = "Narn";
        $this->phpclass = "Bintak";
        $this->imagePath = "ships/bintak.png";
        $this->shipClass = "Bin'Tak";
        $this->shipSizeClass = 3;
        $this->canvasSize = 275;
		
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;

        
        $this->addSystem(new Reactor(7, 30, 0, 0, 0));
        $this->addSystem(new CnC(7, 28, 0, 0, 0));
        $this->addSystem(new Scanner(6, 32, 0, 6, 10));
        $this->addSystem(new Engine(6, 28, 0, 0, 12, 3));
		$this->addSystem(new JumpEngine(6, 30, 0, 5, 20));
		$this->addSystem(new Hangar(6, 22, 0));
        
        $this->addSystem(new LightPulse(2, 4, 1, 2, 300, 60));
        $this->addSystem(new MagGun(5, 9, 1, 8, 300, 60));
        
        $this->addSystem(new HeavyLaser(4, 8, 1, 6, 300, 60));
        
        $this->addSystem(new Thruster(5, 15, 1, 0, 5, 1));
        $this->addSystem(new Thruster(5, 15, 1, 0, 5, 1));
        
        $this->addSystem(new HeavyLaser(4, 8, 1, 6, 300, 60));
        
		$this->addSystem(new EnergyMine(4, 5, 1, 4, 300, 60));
		$this->addSystem(new EnergyMine(4, 5, 1, 4, 300, 60));
		//aft
		          

		$this->addSystem(new HeavyLaser(4, 8, 2, 6, 180, 300));
		$this->addSystem(new HeavyLaser(4, 8, 2, 6, 60, 180));
		$this->addSystem(new LightPulse(2, 4, 2, 2, 90, 270));
		$this->addSystem(new LightPulse(2, 4, 2, 2, 90, 270));
		$this->addSystem(new TwinArray(3, 6, 2, 2, 90, 270));
		
        $this->addSystem(new Thruster(5, 12, 2, 0, 3, 2));
        $this->addSystem(new Thruster(5, 12, 2, 0, 3, 2));
        $this->addSystem(new Thruster(5, 12, 2, 0, 3, 2));
		$this->addSystem(new Thruster(5, 12, 2, 0, 3, 2));
        
		//left
		
		$this->addSystem(new HeavyLaser(4, 8, 3, 6, 240, 0));
		$this->addSystem(new LightPulse(2, 4, 3, 2, 240, 60));
		$this->addSystem(new TwinArray(3, 6, 3, 2, 240, 0));
		$this->addSystem(new TwinArray(3, 6, 3, 2, 240, 0));
		$this->addSystem(new IonTorpedo(4, 5, 3, 4, 240, 0));
		$this->addSystem(new Thruster(5, 20, 3, 0, 6, 3));
              

		//right
		$this->addSystem(new HeavyLaser(4, 8, 4, 6, 0, 120));
		$this->addSystem(new LightPulse(2, 4, 4, 2, 300, 120));
		$this->addSystem(new TwinArray(3, 6, 4, 2, 0, 120));
		$this->addSystem(new TwinArray(3, 6, 4, 2, 0, 120));
		$this->addSystem(new IonTorpedo(4, 5, 4, 4, 0, 120));
		$this->addSystem(new Thruster(5, 20, 4, 0, 6, 4));
        
		
		//structures
        $this->addSystem(new Structure(6, 108, 1));
        $this->addSystem(new Structure(5, 87, 2));
        $this->addSystem(new Structure(5, 96, 3));
        $this->addSystem(new Structure(5, 96, 4));
        $this->addSystem(new Structure(6, 50, 0));
        
    }

}



?>
