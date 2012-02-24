<?php
class Demos extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement){
        parent::__construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement);
        
		$this->pointCost = 575;
		$this->faction = "Centauri";
        $this->phpclass = "Demos";
        $this->imagePath = "ships/demos.png";
        $this->shipClass = "Demos";
        
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 30;
        
         
        $this->addSystem(new Reactor(7, 15, 0, 0, 2));
        $this->addSystem(new CnC(6, 14, 0, 0, 0));
        $this->addSystem(new Scanner(6, 20, 0, 4, 9));
        $this->addSystem(new Engine(7, 13, 0, 0, 10, 2));
		$this->addSystem(new Hangar(6, 2, 0));
		$this->addSystem(new Thruster(5, 15, 0, 0, 5, 3));
		$this->addSystem(new Thruster(5, 15, 0, 0, 5, 4));
		
        
		
        $this->addSystem(new Thruster(5, 10, 1, 0, 3, 1));
        $this->addSystem(new Thruster(5, 10, 1, 0, 3, 1));
        $this->addSystem(new HeavyArray(3, 8, 1, 4, 240, 120));
        $this->addSystem(new HeavyArray(3, 8, 1, 4, 240, 120));
		$this->addSystem(new PlasmaAccelerator(4, 10, 1, 5, 300, 60));
		$this->addSystem(new BallisticTorpedo(4, 5, 1, 6, 300, 60));
		
		
        $this->addSystem(new Thruster(4, 8, 2, 0, 5, 2));
        $this->addSystem(new Thruster(4, 8, 2, 0, 5, 2));
		$this->addSystem(new JumpEngine(6, 16, 2, 3, 16));
	
		

        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addSystem(new Structure( 5, 50, 1));
        $this->addSystem(new Structure( 4, 45, 2));
        $this->addSystem(new Structure( 6, 30, 0));
		
		
    }

}



?>