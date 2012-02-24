<?php
class Gquan extends BaseShip{
    
    function __construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement){
        parent::__construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement);
        
		$this->pointCost = 625;
		$this->faction = "Narn";
        $this->phpclass = "Gquan";
        $this->imagePath = "ships/gquan.png";
        $this->shipClass = "G'Quan";
        $this->shipSizeClass = 3;
        
		
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;

        
        $this->addSystem(new Reactor(6, 22, 0, 0, 0));
        $this->addSystem(new CnC(6, 20, 0, 0, 0));
        $this->addSystem(new Scanner(6, 25, 0, 5, 8));
        $this->addSystem(new Engine(6, 20, 0, 0, 12, 3));
		$this->addSystem(new JumpEngine(6, 30, 0, 3, 20));
		$this->addSystem(new Hangar(6, 14, 0));
        
        $this->addSystem(new HeavyLaser(4, 8, 1, 6, 300, 60));
        
        $this->addSystem(new Thruster(5, 10, 1, 0, 4, 1));
        $this->addSystem(new Thruster(5, 10, 1, 0, 4, 1));
        
        $this->addSystem(new HeavyLaser(4, 8, 1, 6, 300, 60));
        
		$this->addSystem(new EnergyMine(4, 5, 1, 4, 300, 60));
		$this->addSystem(new EnergyMine(4, 5, 1, 4, 300, 60));
		//aft
		          

		$this->addSystem(new TwinArray(3, 6, 2, 2, 90, 270));	
		$this->addSystem(new LightPulse(2, 4, 2, 2, 90, 270));
		$this->addSystem(new LightPulse(2, 4, 2, 2, 90, 270));
		$this->addSystem(new TwinArray(3, 6, 2, 2, 90, 270));
		
        $this->addSystem(new Thruster(4, 12, 2, 0, 4, 2));
        $this->addSystem(new Thruster(4, 12, 2, 0, 4, 2));
        $this->addSystem(new Thruster(4, 12, 2, 0, 4, 2));
		
        
		//left
		
		$this->addSystem(new LightPulse(2, 4, 3, 2, 270, 90));
		$this->addSystem(new TwinArray(3, 6, 3, 2, 270, 90));
        $this->addSystem(new Thruster(4, 15, 3, 0, 5, 3));
              

		//right
		$this->addSystem(new LightPulse(2, 4, 4, 2, 270, 90));
		$this->addSystem(new TwinArray(3, 6, 4, 2, 270, 90));	
		$this->addSystem(new Thruster(4, 15, 4, 0, 5, 4));
        
		
		//structures
        $this->addSystem(new Structure(5, 70, 1));
        $this->addSystem(new Structure(4, 50, 2));
        $this->addSystem(new Structure(4, 70, 3));
        $this->addSystem(new Structure(4, 70, 4));
        $this->addSystem(new Structure(6, 50, 0));
        
    }

}



?>
