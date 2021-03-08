<?php
class DalithornHeavyFrigate extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 275;
        $this->faction = "ZPlaytest Dalithorn";
        $this->phpclass = "DalithornHeavyFrigate";
        $this->imagePath = "img/ships/Nexus/DalithornHeavyFrigate.png";
        $this->shipClass = "Heavy Frigate";
		$this->unofficial = true;
        $this->canvasSize = 80;
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
        $this->addPrimarySystem(new Scanner(3, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new CargoBay(2, 10));
        $this->addPrimarySystem(new Thruster(2, 14, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 14, 0, 4, 4));        
        
		$this->addFrontSystem(new NexusGasGun(3, 7, 2, 300, 60));
		$this->addFrontSystem(new NexusMinigun(2, 4, 1, 240, 60));
		$this->addFrontSystem(new NexusMinigun(2, 4, 1, 300, 120));
        $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
	    
        $this->addAftSystem(new Thruster(1, 5, 0, 2, 2));    
        $this->addAftSystem(new Thruster(1, 5, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));    
		$this->addAftSystem(new NexusProtector(1, 4, 1, 180, 60));
		$this->addAftSystem(new NexusProtector(1, 4, 1, 300, 180));
		$this->addAftSystem(new NexusAutocannon(2, 4, 1, 240, 60));
		$this->addAftSystem(new NexusAutocannon(2, 4, 1, 300, 120));
       
        $this->addPrimarySystem(new Structure(4, 48));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			10 => "Cargo Bay",
			12 => "Scanner",
			16 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			6 => "Thruster",
			8 => "Gas Gun",
			10 => "Minigun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			7 => "Protector",
			9 => "Autocannon",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
