<?php
class KutaiPlasma extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
        $this->pointCost = 500;
        $this->faction = "Centauri";
        $this->phpclass = "KutaiPlasma";
        $this->imagePath = "img/ships/kutai.png";
        $this->shipClass = "Kutai Plasma Gunship";
        $this->variantOf = "Kutai Gunship";
	    $this->isd = 2047;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        // ???: Unreliable should be implemented.
         
        $this->addPrimarySystem(new Reactor(6, 15, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 13, 5, 7));
        $this->addPrimarySystem(new Engine(6, 15, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(6, 1));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240,120));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 240, 0));
	$this->addFrontSystem(new MediumPlasma(3, 5, 3, 0, 120));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 240, 0));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 0, 120));
       
	$this->addAftSystem(new TwinArray(3, 6, 2, 60, 300));  
        $this->addAftSystem(new MediumPlasma(3, 5, 3, 180, 300));
	$this->addAftSystem(new MediumPlasma(3, 5, 3, 60, 180));
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
                    6 => "Medium Plasma Cannon",
		    9 => "Heavy Plasma Cannon",
                    11 => "Twin Array",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    4 => "Thruster",
                    7 => "Medium Plasma Cannon",
                    9 => "Twin array",
		    18 => "Structure",
                    20 => "Primary",
		),
	);     
    
    
    }
}
?>
