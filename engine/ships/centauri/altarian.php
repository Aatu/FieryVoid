<?php
class Altarian extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement){
        parent::__construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement);
        
		$this->pointCost = 510;
		$this->faction = "Centauri";
        $this->phpclass = "Altarian";
        $this->imagePath = "ships/altarian.png";
        $this->shipClass = "Altarian";
        
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 30;
        
         
        $this->addSystem(new Reactor(6, 17, 0, 0, 0));
        $this->addSystem(new CnC(6, 12, 0, 0, 0));
        $this->addSystem(new Scanner(5, 16, 0, 5, 8));
        $this->addSystem(new Engine(5, 15, 0, 0, 10, 3));
		$this->addSystem(new Hangar(5, 7, 0));
		$this->addSystem(new Thruster(3, 10, 0, 0, 4, 3));
		$this->addSystem(new Thruster(3, 10, 0, 0, 4, 4));
		
        
		
        $this->addSystem(new Thruster(4, 10, 1, 0, 3, 1));
        $this->addSystem(new Thruster(4, 10, 1, 0, 3, 1));
        $this->addSystem(new TwinArray(3, 6, 1, 2, 180, 60));
        $this->addSystem(new TwinArray(3, 6, 1, 2, 240, 120));
		$this->addSystem(new TwinArray(3, 6, 1, 2, 300, 180));
		$this->addSystem(new MatterCannon(4, 7, 1, 4, 240, 0));
		$this->addSystem(new MatterCannon(4, 7, 1, 4, 0, 120));
		
		$this->addSystem(new MatterCannon(4, 7, 2, 4, 120, 240));
        $this->addSystem(new Thruster(4, 14, 2, 0, 5, 2));
        $this->addSystem(new Thruster(4, 14, 2, 0, 5, 2));
		$this->addSystem(new JumpEngine(3, 10, 2, 3, 20));
		$this->addSystem(new TwinArray(3, 6, 2, 2, 120, 0));
		$this->addSystem(new TwinArray(3, 6, 2, 2, 0, 240));
		

        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addSystem(new Structure( 4, 60, 1));
        $this->addSystem(new Structure( 4, 60, 2));
        $this->addSystem(new Structure( 6, 46, 0));
		
		
    }

}



?>