<?php
class Octurion extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1350;
		$this->faction = "Centauri";
        $this->phpclass = "Octurion";
        $this->imagePath = "img/ships/octurion.png";
        $this->shipClass = "Octurion ";
        $this->shipSizeClass = 3;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;

		
        
         
        $this->addPrimarySystem(new Reactor(8, 35, 0, 0));
        $this->addPrimarySystem(new CnC(8, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(8, 22, 6, 10));
        $this->addPrimarySystem(new Engine(7, 26, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(7, 26));

        
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));
		$this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(6, 15, 0, 4, 1));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 240, 0));
		$this->addFrontSystem(new MatterCannon(4, 7, 4, 0, 120));

		
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 16, 0, 3, 2));
		$this->addAftSystem(new JumpEngine(6, 30, 4, 16));
        
		$this->addAftSystem(new TwinArray(3, 6, 2, 120, 300));
		$this->addAftSystem(new TwinArray(3, 6, 2, 60, 240));
        $this->addAftSystem(new MatterCannon(4, 7, 4, 180, 300));
		$this->addAftSystem(new MatterCannon(4, 7, 4, 60, 180));
		$this->addAftSystem(new BattleLaser(5, 6, 6, 180, 300));
		$this->addAftSystem(new BattleLaser(5, 6, 6, 60, 180));
		
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		$this->addLeftSystem(new BattleLaser(5, 6, 6, 240, 0));
		$this->addLeftSystem(new BattleLaser(5, 6, 6, 240, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
		$this->addLeftSystem(new MatterCannon(4, 7, 4, 240, 0));

		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		$this->addRightSystem(new BattleLaser(5, 6, 6, 0, 120));
		$this->addRightSystem(new BattleLaser(5, 6, 6, 0, 120));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new MatterCannon(4, 7, 4, 0, 120));

        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 68));
        $this->addAftSystem(new Structure( 5, 68));
        $this->addLeftSystem(new Structure( 5, 80));
        $this->addRightSystem(new Structure( 5, 80));
        $this->addPrimarySystem(new Structure( 8, 60));
    }
}