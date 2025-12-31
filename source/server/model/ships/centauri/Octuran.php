<?php
class Octuran extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1250;
		$this->faction = "Centauri Republic";
        $this->phpclass = "Octuran";
        $this->imagePath = "img/ships/Octuran.png";
        $this->shipClass = "Octuran Battleship";
			$this->variantOf = "Octurion Battleship";
			$this->occurence = "rare";
        $this->shipSizeClass = 3;
        $this->limited = 33;
        $this->fighters = array("normal"=>12);

		$this->unofficial = true;

	    $this->notes = 'Common variant if part of a House Valheru only force.';

	    $this->isd = 2202;

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
		$this->addPrimarySystem(new Hangar(7, 13, 12));
        
        $this->addFrontSystem(new SniperCannon(5, 10, 12, 330, 30));	
		$this->addFrontSystem(new TwinArray(3, 6, 2, 270, 90));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 270, 90));
		$this->addFrontSystem(new BallisticTorpedo(4, 5, 6, 270, 90));
		$this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(6, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
    
		$this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		$this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		$this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		$this->addAftSystem(new BallisticTorpedo(4, 5, 6, 90, 270));
		$this->addAftSystem(new JumpEngine(6, 30, 4, 16)); 
		$this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 16, 0, 3, 2));   
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
		
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 0));
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		$this->addLeftSystem(new CustomHeavyMatterCannon(4, 10, 6, 210, 330));
		$this->addLeftSystem(new CustomHeavyMatterCannon(4, 10, 6, 210, 330));
		$this->addLeftSystem(new CustomHeavyMatterCannon(4, 10, 6, 210, 330));

		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		$this->addRightSystem(new CustomHeavyMatterCannon(4, 10, 6, 30, 150));
		$this->addRightSystem(new CustomHeavyMatterCannon(4, 10, 6, 30, 150));
		$this->addRightSystem(new CustomHeavyMatterCannon(4, 10, 6, 30, 150));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 68));
        $this->addAftSystem(new Structure( 5, 68));
        $this->addLeftSystem(new Structure( 5, 80));
        $this->addRightSystem(new Structure( 5, 80));
        $this->addPrimarySystem(new Structure( 8, 60));

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
                    9 => "Sniper Cannon",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array( //Aft
                    4 => "Thruster",
                    6 => "Ballistic Torpedo",
                    8 => "Matter Cannon",
                    10 => "Twin Array",
                    12 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array( //Port
                    3 => "Thruster",
                    8 => "Heavy Matter Cannon",
                    10 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array( //Starboard
                    3 => "Thruster",
                    8 => "Heavy Matter Cannon",
                    10 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            )
        );
	    
    }
}

?>
