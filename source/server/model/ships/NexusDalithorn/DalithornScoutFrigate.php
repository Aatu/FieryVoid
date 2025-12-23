<?php
class DalithornScoutFrigate extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 335;
        $this->faction = "Nexus Dalithorn Commonwealth";
        $this->phpclass = "DalithornScoutFrigate";
        $this->imagePath = "img/ships/Nexus/Dalithorn_SmallScout2.png";
        $this->shipClass = "Scout Frigate";
		$this->unofficial = true;
        $this->canvasSize = 100;
        $this->limited = 33;

	    $this->isd = 2108;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(3, 10, 4, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new Magazine(3, 10));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 4, 4));        
        
        $this->addFrontSystem(new ELINTScanner(3, 10, 4, 2));
		$this->addFrontSystem(new NexusMinigun(2, 4, 1, 240, 60));
		$this->addFrontSystem(new NexusMinigun(2, 4, 1, 300, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
	    
        $this->addAftSystem(new Thruster(1, 5, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));    
        $this->addAftSystem(new Thruster(1, 5, 0, 2, 2));    
		$this->addAftSystem(new NexusProtector(1, 4, 1, 180, 60));
		$this->addAftSystem(new NexusProtector(1, 4, 1, 300, 180));
		$this->addAftSystem(new NexusAutocannon(2, 4, 1, 240, 60));
		$this->addAftSystem(new NexusAutocannon(2, 4, 1, 300, 120));
       
        $this->addPrimarySystem(new Structure(4, 48));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			10 => "Magazine",
			12 => "ELINT Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			6 => "ELINT Scanner",
			8 => "Minigun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Protector",
			10 => "Autocannon",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
