<?php
class Octuran extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1200;
		$this->faction = "House Valheru";
        $this->phpclass = "Octuran";
        $this->imagePath = "img/ships/Octuran.png";
        $this->shipClass = "Octuran Command Dreadnought";
//			$this->variantOf = "Octurion Battleship";
//			$this->occurence = "rare";
        $this->shipSizeClass = 3;
        $this->limited = 33;
        $this->fighters = array("normal"=>12);

		$this->unofficial = true;

	    $this->notes = 'Common variant if part of a House Valheru only force.';
	    $this->notes .= '<br>Provides +5 initiative to all House Valheru forces.';

	    $this->isd = 2240;

        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
         
        $this->addPrimarySystem(new Reactor(7, 35, 0, 0));
//        $this->addPrimarySystem(new CnC(8, 25, 0, 0));
		$this->addPrimarySystem(new FlagBridge(8, 25, 0, 1, 'Valheru Command Bonus', 60,  true, true, true, false, array("House Valheru")));
        $this->addPrimarySystem(new Scanner(7, 22, 6, 10));
        $this->addPrimarySystem(new Engine(7, 26, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(7, 13, 12));
        
        $this->addFrontSystem(new SniperCannon(5, 12, 10, 330, 30));	
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
		
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		$this->addLeftSystem(new CustomHeavyMatterCannon(4, 10, 6, 225, 315));
		$this->addLeftSystem(new CustomHeavyMatterCannon(4, 10, 6, 225, 315));
		$this->addLeftSystem(new CustomHeavyMatterCannon(4, 10, 6, 225, 315));

		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		$this->addRightSystem(new CustomHeavyMatterCannon(4, 10, 6, 45, 135));
		$this->addRightSystem(new CustomHeavyMatterCannon(4, 10, 6, 45, 135));
		$this->addRightSystem(new CustomHeavyMatterCannon(4, 10, 6, 45, 135));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 98));
        $this->addAftSystem(new Structure( 5, 88));
        $this->addLeftSystem(new Structure( 5, 92));
        $this->addRightSystem(new Structure( 5, 92));
        $this->addPrimarySystem(new Structure( 8, 70));

	//d20 hit chart
        $this->hitChart = array(
            0=> array( //PRIMARY
                    10 => "Structure",
                    13 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array( //Forward
                    3 => "Thruster",
                    9 => "Sniper Cannon",
					11 => "Ballistic Torpedo",
                    13 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array( //Aft
                    4 => "Thruster",
                    6 => "Ballistic Torpedo",
                    8 => "Twin Array",
                    10 => "Jump Engine",
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
