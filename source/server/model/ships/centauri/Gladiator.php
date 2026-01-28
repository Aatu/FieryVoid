<?php
class Gladiator extends BaseShip{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
        $this->pointCost = 675;
        $this->faction = "Centauri Republic";
        $this->phpclass = "Gladiator";
	    $this->isd = 2202;
        $this->imagePath = "img/ships/Gladiator.png";
        $this->shipClass = "Gladiator Trench Runner";
			$this->variantOf = "Centurion Attack Cruiser";
			$this->occurence = "rare";
        $this->shipSizeClass = 3;
		$this->unofficial = true;

	    $this->notes = 'Common variant if part of a House Valheru only force.';
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        $this->iniativebonus = 10; //2 *5
         
        $this->addPrimarySystem(new Reactor(7, 22, 0, 0));
        $this->addPrimarySystem(new CnC(7, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 20, 4, 10));
        $this->addPrimarySystem(new Engine(6, 20, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(7, 2));

        $this->addFrontSystem(new TwinArray(3, 6, 2, 180, 0));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 180, 0));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 0, 180));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 0, 180));
        $this->addFrontSystem(new CustomHeavyMatterCannon(4, 10, 6, 300, 60));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 10, 0, 3, 1));
		
        $this->addAftSystem(new JumpEngine(6, 25, 3, 16));
		$this->addAftSystem(new TwinArray(3, 6, 2, 90, 270));
        $this->addAftSystem(new Thruster(5, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(5, 14, 0, 6, 2));
		
        $this->addLeftSystem(new CustomHeavyMatterCannon(4, 10, 6, 240, 360));
        $this->addLeftSystem(new BallisticTorpedo(4, 5, 6, 240, 360));
        $this->addLeftSystem(new Thruster(5, 15, 0, 5, 3));

        $this->addRightSystem(new CustomHeavyMatterCannon(4, 10, 6, 0, 120));
        $this->addRightSystem(new BallisticTorpedo(4, 5, 6, 0, 120));
        $this->addRightSystem(new Thruster(5, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 44));
        $this->addAftSystem(new Structure( 5, 44));
        $this->addLeftSystem(new Structure( 5, 56));
        $this->addRightSystem(new Structure( 5, 56));
        $this->addPrimarySystem(new Structure( 7, 44));
  
	//d20 hit chart
        $this->hitChart = array(
            0=> array( //PRIMARY
                    9 => "Structure",
                    12 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array( //Forward
                    3 => "Thruster",
                    5 => "Heavy Matter Cannon",
                    9 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array( //Aft
                    7 => "Thruster",
					9 => "Twin Array",
                    12 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array( //Port
                    3 => "Thruster",
                    6 => "Heavy Matter Cannon",
                    9 => "Ballistic Torpedo",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array( //Starboard
                    3 => "Thruster",
                    6 => "Heavy Matter Cannon",
                    9 => "Ballistic Torpedo",
                    18 => "Structure",
                    20 => "Primary",
            )
        );	    
	    
    }
}

?>
