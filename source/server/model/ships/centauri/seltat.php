<?php
class Seltat extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
        $this->pointCost = 550;
        $this->faction = "Centauri Republic";
        $this->phpclass = "Seltat";
        $this->imagePath = "img/ships/kutai.png";
        $this->shipClass = "Seltat Minesweeper";
	    $this->variantOf = "Kutai Gunship";
        $this->occurence = "uncommon";
        $this->isd = 2134;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        $this->minesweeperbonus = 4;

        $this->addPrimarySystem(new Reactor(6, 17, 0, 6));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 16, 5, 8));
        $this->addPrimarySystem(new Engine(6, 15, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(6, 2));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240,120));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 240, 0));
	$this->addFrontSystem(new MatterCannon(4, 7, 4, 240, 0));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 0, 120));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 0, 120));
       
	$this->addAftSystem(new TwinArray(4, 6, 2, 120, 0));
        $this->addAftSystem(new TwinArray(4, 6, 2, 0, 240));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addPrimarySystem(new Structure( 6, 36 ));
    
            $this->hitChart = array(
                0=> array(
                    8 => "Structure",
                    10 => "Thruster",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    9 => "Matter Cannon",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    4 => "Thruster",
                    8 => "Twin array",
		    18 => "Structure",
                    20 => "Primary",
		),
	); 
    
    
    }
}
?>
