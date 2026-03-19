<?php
class PrimoSecundus extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 725;
		$this->faction = "House Valheru";
        $this->phpclass = "PrimoSecundus";
        $this->imagePath = "img/ships/Primo3.png";
        $this->shipClass = "Primo Secundus Brawler";
			$this->variantOf = "Primo Gunship";
			$this->occurence = "uncommon";
        $this->shipSizeClass = 3;
        $this->isd = 2240;
//        $this->variantOf = "Primus Battlecruiser";
//		$this->occurence = "rare";
		$this->unofficial = true;

	    $this->notes = 'Common variant if part of a House Valheru only force.';
	    
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
         
        $this->addPrimarySystem(new Reactor(7, 22, 0, 0));
        $this->addPrimarySystem(new CnC(7, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 20, 5, 9));
        $this->addPrimarySystem(new Engine(7, 18, 0, 12, 2));
		$this->addPrimarySystem(new Hangar(7, 2));
        
		$this->addFrontSystem(new TwinArray(3, 6, 2, 270, 90));
		$this->addFrontSystem(new BallisticTorpedo(4, 5, 6, 315, 45));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
		
		$this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
		$this->addAftSystem(new JumpEngine(6, 25, 3, 16));
 		$this->addAftSystem(new BallisticTorpedo(4, 5, 6, 135, 225));
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 8, 0, 2, 2));
        
		$this->addLeftSystem(new TwinArray(3, 6, 2, 180, 360));
		$this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));
		$this->addLeftSystem(new CustomHeavyMatterCannon(5, 10, 6, 225, 315));
		$this->addLeftSystem(new CustomHeavyMatterCannon(5, 10, 6, 225, 315));
		
		$this->addRightSystem(new TwinArray(3, 6, 2, 0, 180));
		$this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
		$this->addRightSystem(new CustomHeavyMatterCannon(5, 10, 6, 45, 135));
		$this->addRightSystem(new CustomHeavyMatterCannon(5, 10, 6, 45, 135));
        
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
					7 => "Ballistic Torpedo",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array( //Aft
                    5 => "Thruster",
					7 => "Twin Array",
					9 => "Ballistic Torpedo",
                    12 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array( //Port
                    3 => "Thruster",
					7 => "Heavy Matter Cannon",
                    9 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array( //Starboard
                    3 => "Thruster",
					7 => "Heavy Matter Cannon",
                    9 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            )
        );
	    
		
    }

}



?>
