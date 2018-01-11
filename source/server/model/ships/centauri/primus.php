<?php
class Primus extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 830;
		$this->faction = "Centauri";
        $this->phpclass = "Primus";
        $this->imagePath = "img/ships/primus.png";
        $this->shipClass = "Primus Battlecruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
	    $this->isd = 2202;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
         
        $this->addPrimarySystem(new Reactor(8, 22, 0, 0));
        $this->addPrimarySystem(new CnC(7, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 20, 5, 10));
        $this->addPrimarySystem(new Engine(7, 18, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(7, 14));
		
        
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
		
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
		$this->addAftSystem(new JumpEngine(6, 25, 3, 16));
        
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		$this->addLeftSystem(new BattleLaser(5, 6, 6, 240, 0));
		$this->addLeftSystem(new BattleLaser(5, 6, 6, 240, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 60));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 60));
		
        
        
		
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		$this->addRightSystem(new BattleLaser(5, 6, 6, 0, 120));
		$this->addRightSystem(new BattleLaser(5, 6, 6, 0, 120));
		$this->addRightSystem(new TwinArray(3, 6, 2, 300, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 300, 180));
		
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 56));
        $this->addRightSystem(new Structure( 5, 56));
        $this->addPrimarySystem(new Structure( 7, 40));
		
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
                    7 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
                    12 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    3 => "Thruster",
                    7 => "Battle Laser",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            )
        );
    }
}



?>
