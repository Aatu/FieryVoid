<?php
class Adira extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 2500;
		$this->faction = "Custom Ships";
        $this->phpclass = "Adira";
        $this->imagePath = "img/ships/adira.png";
        $this->shipClass = "Adira Royal Battleship";
        $this->shipSizeClass = 3;
        $this->limited = 10;
		$this->canvasSize = 165; //img has 200px per side
        $this->fighters = array("medium"=>36);
		$this->customFighter = array("Rutarian"=>36);
	    $this->isd = 2269;
		$this->unofficial = true;
	    $this->notes = 'Rutarian capable';
		
        $this->forwardDefense = 19;
        $this->sideDefense = 19;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.33;
        $this->accelcost = 5;
        $this->rollcost = 5;
        $this->pivotcost = 5;
         
        $this->addPrimarySystem(new Reactor(8, 35, 0, 0));
        $this->addPrimarySystem(new CnC(8, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(8, 24, 6, 12));
        $this->addPrimarySystem(new Engine(7, 26, 0, 10, 5));
		$this->addPrimarySystem(new Hangar(7, 38, 12));
		$this->addPrimarySystem(new GuardianArray(0, 4, 2, 0, 360));
        
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 240, 360));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 240, 360));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));
		$this->addFrontSystem(new MatterCannon(4, 7, 4, 0, 120));
		$this->addFrontSystem(new MatterCannon(4, 7, 4, 0, 120));
		$this->addFrontSystem(new Thruster(6, 15, 0, 3, 1));
		$this->addFrontSystem(new Thruster(6, 20, 0, 4, 1));
        $this->addFrontSystem(new Thruster(6, 15, 0, 3, 1));
    
		$this->addAftSystem(new BattleLaser(4, 6, 6, 180, 300));
        $this->addAftSystem(new MatterCannon(4, 7, 4, 180, 300));
		$this->addAftSystem(new TwinArray(3, 6, 2, 120, 300));
		$this->addAftSystem(new TwinArray(3, 6, 2, 60, 240));
		$this->addAftSystem(new MatterCannon(4, 7, 4, 60, 180));
		$this->addAftSystem(new BattleLaser(4, 6, 6, 60, 180)); 
		$this->addAftSystem(new JumpEngine(6, 32, 4, 14)); 
        $this->addAftSystem(new Thruster(5, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 18, 0, 4, 2));
        $this->addAftSystem(new Thruster(5, 16, 0, 3, 2));
		
		$this->addLeftSystem(new BattleLaser(4, 6, 6, 240, 360));
		$this->addLeftSystem(new BattleLaser(4, 6, 6, 240, 360));
		$this->addLeftSystem(new MatterCannon(4, 7, 4, 240, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new Thruster(5, 20, 0, 6, 3));

		$this->addRightSystem(new BattleLaser(4, 6, 6, 0, 120));
		$this->addRightSystem(new BattleLaser(4, 6, 6, 0, 120));
		$this->addRightSystem(new MatterCannon(4, 7, 4, 0, 120));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new Thruster(5, 20, 0, 6, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 88));
        $this->addAftSystem(new Structure( 5, 76));
        $this->addLeftSystem(new Structure( 5, 80));
        $this->addRightSystem(new Structure( 5, 80));
        $this->addPrimarySystem(new Structure( 8, 80));

	//d20 hit chart
        $this->hitChart = array(
            0=> array( //PRIMARY
                    8 => "Structure",
					9 => "Guardian Array",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array( //Forward
                    3 => "Thruster",
                    5 => "Matter Cannon",
                    9 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array( //Aft
                    4 => "Thruster",
                    6 => "Battle Laser",
                    8 => "Matter Cannon",
                    10 => "Twin Array",
                    12 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array( //Port
                    3 => "Thruster",
                    6 => "Battle Laser",
                    8 => "Matter Cannon",
                    10 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array( //Starboard
                    3 => "Thruster",
                    6 => "Battle Laser",
                    8 => "Matter Cannon",
                    10 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            )
        );
	    
    }
}

?>
