<?php
class Elutai extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
        $this->pointCost = 575;
        $this->faction = "Centauri Republic";
        $this->phpclass = "Elutai";
        $this->imagePath = "img/ships/kutai.png";
        $this->shipClass = "Elutai Bombardment Destroyer";
			$this->variantOf = "Kutai Gunship";
			$this->occurence = "rare";
	    $this->isd = 2234;
		$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
         
        $this->addPrimarySystem(new Reactor(6, 17, 0, -2));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 15, 5, 8));
        $this->addPrimarySystem(new Engine(6, 15, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(6, 1));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new GuardianArray(0, 4, 2, 240,120));
        $this->addFrontSystem(new BallisticTorpedo(4, 5, 6, 240, 0));
		$this->addFrontSystem(new BallisticTorpedo(4, 5, 6, 240, 0));
        $this->addFrontSystem(new BallisticTorpedo(4, 5, 6, 0, 120));
        $this->addFrontSystem(new BallisticTorpedo(4, 5, 6, 0, 120));
       
		$this->addAftSystem(new GuardianArray(0, 4, 2, 60, 300));  
        $this->addAftSystem(new BallisticTorpedo(4, 5, 6, 180, 300));
		$this->addAftSystem(new BallisticTorpedo(4, 5, 6, 60, 180));
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
                    9 => "BallisticTorpedo",
                    11 => "GuardianArray",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    4 => "Thruster",
                    7 => "BallisticTorpedo",
                    9 => "GuardianArray",
					18 => "Structure",
                    20 => "Primary",
		),
	); 
    
    }
}
