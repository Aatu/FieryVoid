<?php
class Primus extends BaseShip{
    
    function __construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement){
        parent::__construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement);
        
		$this->pointCost = 800;
		$this->faction = "Centauri";
        $this->phpclass = "Primus";
        $this->imagePath = "ships/primus.png";
        $this->shipClass = "Primus";
        $this->shipSizeClass = 3;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
         
        $this->addSystem(new Reactor(8, 22, 0, 0, 0));
        $this->addSystem(new CnC(7, 20, 0, 0, 0));
        $this->addSystem(new Scanner(7, 20, 0, 5, 10));
        $this->addSystem(new Engine(7, 18, 0, 0, 10, 2));
		$this->addSystem(new Hangar(7, 14, 0));
		
        
		$this->addSystem(new TwinArray(3, 6, 1, 2, 240, 120));
        $this->addSystem(new Thruster(6, 10, 1, 0, 3, 1));
        $this->addSystem(new Thruster(6, 10, 1, 0, 3, 1));
        $this->addSystem(new TwinArray(3, 6, 1, 2, 240, 120));
        $this->addSystem(new TwinArray(3, 6, 1, 2, 240, 120));
		$this->addSystem(new TwinArray(3, 6, 1, 2, 240, 120));
		
        $this->addSystem(new Thruster(5, 8, 2, 0, 3, 2));
        $this->addSystem(new Thruster(5, 8, 2, 0, 3, 2));
        $this->addSystem(new Thruster(5, 8, 2, 0, 3, 2));
        $this->addSystem(new Thruster(5, 8, 2, 0, 3, 2));
		$this->addSystem(new JumpEngine(6, 25, 2, 3, 16));
        
		$this->addSystem(new Thruster(5, 15, 3, 0, 5, 3));
		$this->addSystem(new BattleLaser(5, 6, 3, 6, 240, 0));
		$this->addSystem(new BattleLaser(5, 6, 3, 6, 240, 0));
		$this->addSystem(new TwinArray(3, 6, 3, 2, 180, 60));
		$this->addSystem(new TwinArray(3, 6, 3, 2, 180, 60));
		
        
        
		
		$this->addSystem(new Thruster(5, 15, 4, 0, 5, 4));
		$this->addSystem(new BattleLaser(5, 6, 4, 6, 0, 120));
		$this->addSystem(new BattleLaser(5, 6, 4, 6, 0, 120));
		$this->addSystem(new TwinArray(3, 6, 4, 2, 300, 180));
		$this->addSystem(new TwinArray(3, 6, 4, 2, 300, 180));
		
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addSystem(new Structure( 5, 40, 1));
        $this->addSystem(new Structure( 5, 40, 2));
        $this->addSystem(new Structure( 5, 56, 3));
        $this->addSystem(new Structure( 5, 56, 4));
        $this->addSystem(new Structure( 7, 42, 0));
		
		
    }

}



?>
