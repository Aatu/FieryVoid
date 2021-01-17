<?php
class Vaconi extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "Corillani";
        $this->phpclass = "Vaconi";
        $this->imagePath = "img/ships/CorillaniVaconi.png";
        $this->shipClass = "Vaconi Strike Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>24);
	    $this->isd = 2229;
		$this->notes = 'Defenders of Corillan (DoC)';	    
		
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;

        
        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 8));
        $this->addPrimarySystem(new Engine(4, 18, 0, 12, 3));
        
        $this->addFrontSystem(new PlasmaAccelerator(3, 10, 5, 300, 60));
        $this->addFrontSystem(new PlasmaAccelerator(3, 10, 5, 300, 60));                 
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));        
		
        $this->addAftSystem(new PlasmaAccelerator(3, 6, 0, 120, 240));	
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));       
		
        
 		$this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 0));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 240, 60));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 120, 300));
        $this->addLeftSystem(new Thruster(3, 8, 0, 3, 3));
        $this->addLeftSystem(new Thruster(3, 8, 0, 3, 3));
        $this->addLeftSystem(new Hangar(3, 14));
		
 		$this->addRightSystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addRightSystem(new TwinArray(2, 6, 2, 300, 120));
        $this->addRightSystem(new TwinArray(2, 6, 2, 60, 240));
        $this->addRightSystem(new Thruster(3, 8, 0, 3, 4));
        $this->addRightSystem(new Thruster(3, 8, 0, 3, 4));
        $this->addRightSystem(new Hangar(3, 14));        
		
		
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 48));
        $this->addRightSystem(new Structure(4, 48));
        $this->addPrimarySystem(new Structure(5, 48));
		
		
		$this->hitChart = array(
			0=> array(
				12 => "Structure",
				15 => "Scanner",
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				9 => "Plasma Accelerator",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				8 => "Thruster",
				10 => "Plasma Accelerator",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				5 => "Thruster",
				7 => "Heavy Plasma Cannon",
				11 => "Twin Array",
				13 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				5 => "Thruster",
				7 => "Heavy Plasma Cannon",
				11 => "Twin Array",
				13 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}



?>
