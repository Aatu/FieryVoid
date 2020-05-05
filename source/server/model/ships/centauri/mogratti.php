<?php
class Mogratti extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 575;
	$this->faction = "Centauri";
        $this->phpclass = "Mogratti";
        $this->imagePath = "img/ships/mograth.png";
        $this->shipClass = "Mogratti Battle Frigate";
        $this->agile = true;
        $this->canvasSize = 100;
        $this->occurence = "uncommon";
        $this->variantOf = "Mograth Frigate";
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(5, 12, 0, 2));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 8));
        $this->addPrimarySystem(new Engine(4, 11, 0, 10, 2));
	$this->addPrimarySystem(new Hangar(4, 1));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
	$this->addPrimarySystem(new TwinArray(4, 6, 2, 0, 0));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 180, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 180));
	$this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
	$this->addFrontSystem(new MatterCannon(3, 7, 4, 240, 0));
        $this->addFrontSystem(new MatterCannon(3, 7, 4, 0, 120));
		
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
       
        $this->addPrimarySystem(new Structure( 5, 50));
    
    	$this->hitChart = array(
                0=> array(
                        7 => "Thruster",
                        9 => "Twin Array",
                        12 => "Scanner",
			15 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        5 => "Plasma Accelerator",
			8 => "Matter Cannon",			
			11 => "Twin Array",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );    
    
    }

}



?>
