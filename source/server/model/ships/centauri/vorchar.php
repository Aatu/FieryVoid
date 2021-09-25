<?php
class Vorchar extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
		$this->faction = "Centauri";
        $this->phpclass = "Vorchar";
        $this->imagePath = "img/ships/vorchar.png";
        $this->shipClass = "Vorchar Warscout";
        $this->occurence = "uncommon";
        $this->variantOf = "Vorchan Warship";
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 40;
         
        $this->addPrimarySystem(new Reactor(7, 12, 0, 6));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new ElintScanner(6, 24, 7, 10));
        $this->addPrimarySystem(new Engine(7, 11, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(6, 2));
		$this->addPrimarySystem(new Thruster(5, 15, 0, 5, 3));
		$this->addPrimarySystem(new Thruster(5, 15, 0, 5, 4));
		
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
		$this->addFrontSystem(new TwinArray(3, 6, 2, 300, 60));
		
        $this->addAftSystem(new Thruster(4, 8, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 5, 2));
		$this->addAftSystem(new JumpEngine(6, 16, 3, 16));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 4, 44));
        $this->addPrimarySystem(new Structure( 6, 28));
		
			$this->hitChart = array(
			0=> array(
				7 => "Structure",
				10 => "Thruster",
				12 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				9 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				5 => "Thruster",
				9 => "Jump Engine",
				18 => "Structure",
				20 => "Primary",
			),
			); 	
    }

}



?>
