<?php
class Primo extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 750;
		$this->faction = "Centauri Republic";
        $this->phpclass = "Primo";
        $this->imagePath = "img/ships/Primo3.png";
        $this->shipClass = "Primo Gunship";
        $this->shipSizeClass = 3;
        $this->isd = 2202;
        $this->variantOf = "Primus Battlecruiser";
		$this->occurence = "rare";
		$this->unofficial = true;

	    $this->notes = 'Common variant if part of a House Valheru only force.';
	    
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
         
        $this->addPrimarySystem(new Reactor(8, 22, 0, 0));
        $this->addPrimarySystem(new CnC(7, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 20, 5, 9));
        $this->addPrimarySystem(new Engine(7, 18, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(7, 2));
        
		$this->addFrontSystem(new TwinArray(3, 6, 2, 270, 90));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
		
		$this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		$this->addAftSystem(new JumpEngine(6, 25, 3, 16));
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        
		$this->addLeftSystem(new MatterCannon(4, 7, 4, 210, 330));
		$this->addLeftSystem(new MatterCannon(4, 7, 4, 210, 330));
		$this->addLeftSystem(new MatterCannon(4, 7, 4, 210, 330));
		$this->addLeftSystem(new MatterCannon(4, 7, 4, 210, 330));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		
		$this->addRightSystem(new MatterCannon(4, 7, 4, 30, 150));
		$this->addRightSystem(new MatterCannon(4, 7, 4, 30, 150));
		$this->addRightSystem(new MatterCannon(4, 7, 4, 30, 150));
		$this->addRightSystem(new MatterCannon(4, 7, 4, 30, 150));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 56));
        $this->addRightSystem(new Structure( 5, 56));
        $this->addPrimarySystem(new Structure( 7, 40));

	//d20 hit chart
        $this->hitChart = array(
            0=> array( //PRIMARY
                    10 => "Structure",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array( //Forward
                    3 => "Thruster",
                    5 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array( //Aft
                    6 => "Thruster",
					7 => "Twin Array",
                    12 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array( //Port
                    3 => "Thruster",
					9 => "Matter Cannon",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array( //Starboard
                    3 => "Thruster",
					9 => "Matter Cannon",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            )
        );
	    
		
    }

}



?>
