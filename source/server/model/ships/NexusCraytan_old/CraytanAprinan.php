<?php
class CraytanAprinan extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 230;
        $this->faction = "Nexus Craytan Union (early)";
        $this->phpclass = "CraytanAprinan";
        $this->imagePath = "img/ships/Nexus/craytan_aprinan.png";
        $this->shipClass = "Aprinan Patroller";
		$this->unofficial = true;
        $this->canvasSize = 90;
	    $this->isd = 1906;

	    $this->notes = 'Atmospheric capable';

        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 9, 3, 4));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 2, 4));        
        $this->addPrimarySystem(new Magazine(3, 8));
        
		$this->addFrontSystem(new NexusMedSentryGun(2, 6, 2, 300, 60));
		$this->addFrontSystem(new NexusLightSentryGun(2, 5, 1, 240, 60));
		$this->addFrontSystem(new NexusLightSentryGun(2, 5, 1, 300, 120));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
	    
		$this->addAftSystem(new NexusCIDS(1, 4, 2, 180, 360));
        $this->addAftSystem(new Engine(3, 9, 0, 5, 3));
		$this->addAftSystem(new NexusCIDS(1, 4, 2, 0, 180));
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));    
		$this->addAftSystem(new Hangar(0, 1));
       
        $this->addPrimarySystem(new Structure(3, 36));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			12 => "Magazine",
			16 => "Scanner",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			6 => "Thruster",
			9 => "Light Sentry Gun",
			11 => "Medium Sentry Gun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Close-In Defense System",
			10 => "Engine",
			11 => "Hangar",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
