<?php
class CraytanCorvette extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "ZNexus Playtest Craytan";
        $this->phpclass = "CraytanCorvette";
        $this->imagePath = "img/ships/Nexus/CraytanCorvette.png";
        $this->shipClass = "Corvette";
		$this->unofficial = true;
        $this->canvasSize = 75;
        $this->agile = true;
	    $this->isd = 2048;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 4));
        $this->addPrimarySystem(new Engine(3, 11, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 3, 4));        
        $this->addPrimarySystem(new CargoBay(4, 9));
        
		$this->addFrontSystem(new NexusHeavyAutocannon(2, 6, 2, 300, 60));
		$this->addFrontSystem(new NexusCIDS(2, 4, 2, 240, 120));
		$this->addFrontSystem(new NexusHeavyAutocannon(2, 6, 2, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
	    
		$this->addAftSystem(new NexusMedAutocannon(2, 5, 1, 180, 360));
		$this->addAftSystem(new NexusCIDS(2, 4, 2, 90, 270));
		$this->addAftSystem(new NexusMedAutocannon(2, 5, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));    
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));    
       
        $this->addPrimarySystem(new Structure(3, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Scanner",
			14 => "Engine",
			16 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Medium Laser",
			9 => "Light Laser",
			10 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			9 => "Light Laser",
			10 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
