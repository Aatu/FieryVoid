<?php
class CraytanMaprin extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 290;
        $this->faction = "ZNexus Playtest Craytan";
        $this->phpclass = "CraytanMaprin";
        $this->imagePath = "img/ships/Nexus/CraytanCorvette.png";
        $this->shipClass = "Maprin Corvette";
		$this->unofficial = true;
        $this->canvasSize = 60;
        $this->agile = true;
	    $this->isd = 2048;

        $this->fighters = array("assault shuttles"=>2);
        
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
        $this->addPrimarySystem(new Scanner(3, 12, 4, 5));
        $this->addPrimarySystem(new Engine(3, 11, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 3, 4));        
        $this->addPrimarySystem(new CargoBay(4, 9));
        
		$this->addFrontSystem(new NexusHeavySentryGun(2, 6, 2, 300, 60));
		$this->addFrontSystem(new NexusCIDS(2, 4, 2, 240, 120));
		$this->addFrontSystem(new NexusHeavySentryGun(2, 6, 2, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
	    
		$this->addAftSystem(new NexusSentryGun(2, 5, 1, 180, 360));
		$this->addAftSystem(new NexusCIDS(2, 4, 2, 60, 300));
		$this->addAftSystem(new NexusSentryGun(2, 5, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));    
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));    
       
        $this->addPrimarySystem(new Structure(3, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Cargo Bay",
			14 => "Scanner",
			17 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			6 => "Thruster",
			8 => "Heavy Sentry Gun",
			9 => "Close-In Defense System",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Sentry Gun",
			9 => "Close-In Defense System",
			11 => "Hangar",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>