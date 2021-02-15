<?php
class Intona extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "Corillani";
        $this->phpclass = "Intona";
        $this->imagePath = "img/ships/CorillaniIntona.png";
        $this->shipClass = "Intona Strike Cruiser";
        $this->shipSizeClass = 3;
	    $this->isd = 2239;
		$this->notes = 'Orillani Space Forces (OSF)';	    
		
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        
        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 7));
        $this->addPrimarySystem(new Engine(4, 15, 0, 12, 3));
        $this->addPrimarySystem(new JumpEngine(4, 15, 4, 36));
        $this->addPrimarySystem(new Hangar(2, 2));
        
      
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 270, 90));	
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 270, 90)); 
        $this->addFrontSystem(new TwinArray(2, 6, 2, 270, 90));                
        $this->addFrontSystem(new PlasmaProjector(4, 8, 5, 300, 60));
        $this->addFrontSystem(new PlasmaProjector(4, 8, 5, 300, 60));

        $this->addAftSystem(new TwinArray(2, 6, 2, 90, 270));
        $this->addAftSystem(new Thruster(3, 5, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 5, 0, 2, 2));       
		
		$this->addLeftSystem(new PlasmaProjector(3, 8, 5, 240, 0));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
        $this->addLeftSystem(new Thruster(4, 10, 0, 3, 3));
        $this->addLeftSystem(new Thruster(3, 5, 0, 2, 3));
		
 		$this->addRightSystem(new PlasmaProjector(3, 8, 5, 0, 120));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
        $this->addRightSystem(new Thruster(4, 10, 0, 3, 4));
        $this->addRightSystem(new Thruster(3, 5, 0, 2, 4));
        
		
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 30));
        $this->addLeftSystem(new Structure(4, 44));
        $this->addRightSystem(new Structure(4, 44));
        $this->addPrimarySystem(new Structure(4, 36));
		
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				11 => "Jump Engine",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "Plasma Projector",
				10 => "Class-S Missile Rack",
				11 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				9 => "Thruster",
				10 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				9 => "Thruster",
				11 => "Plasma Projector",
				12 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				9 => "Thruster",
				11 => "Plasma Projector",
				12 => "Twin Array",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}



?>
