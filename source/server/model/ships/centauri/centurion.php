<?php
class Centurion extends BaseShip{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
		$this->pointCost = 725;
		$this->faction = "Centauri";
        $this->phpclass = "Centurion";
        $this->imagePath = "img/ships/centurion.png";
        $this->shipClass = "Centurion";
        $this->shipSizeClass = 3;
		
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

		
        
         
        $this->addPrimarySystem(new Reactor(7, 22, 0, 0));
        $this->addPrimarySystem(new CnC(7, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 20, 4, 10));
        $this->addPrimarySystem(new Engine(6, 20, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(7, 2));

        
		$this->addFrontSystem(new TwinArray(3, 6, 2, 180, 45));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 315, 180));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 180, 45));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 315, 180));
		$this->addFrontSystem(new BattleLaser(5, 6, 6, 315, 45));
		
		$this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
		
       

		
        $this->addAftSystem(new Thruster(5, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(5, 14, 0, 6, 2));
        $this->addAftSystem(new JumpEngine(6, 25, 3, 16));
	
		
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		$this->addLeftSystem(new BattleLaser(5, 6, 6, 225, 0));
		$this->addLeftSystem(new MatterCannon(4, 7, 4, 225, 0));

		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		$this->addRightSystem(new BattleLaser(5, 6, 6, 0, 135));
		$this->addRightSystem(new MatterCannon(4, 7, 4, 0, 135));

        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 44));
        $this->addAftSystem(new Structure( 5, 44));
        $this->addLeftSystem(new Structure( 5, 56));
        $this->addRightSystem(new Structure( 5, 56));
        $this->addPrimarySystem(new Structure( 8, 44));
    }
}