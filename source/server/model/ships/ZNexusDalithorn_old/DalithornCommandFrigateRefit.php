<?php
class DalithornCommandFrigateRefit extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "ZNexus Dalithorn Commonwealth (early)";
        $this->phpclass = "DalithornCommandFrigateRefit";
        $this->imagePath = "img/ships/Nexus/Dalithorn_CommandFrigate2.png";
        $this->shipClass = "Command Frigate (2044)";
			$this->variantOf = "Frigate";
			$this->occurence = "rare";
		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 2044;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 65;
         
        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 4));
        $this->addPrimarySystem(new Engine(3, 12, 0, 7, 3));
        $this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new Magazine(3, 10));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 4, 4));        
        
		$this->addFrontSystem(new NexusLightCoilgun(3, 7, 3, 330, 30));
		$this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 240, 360));
		$this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 0, 120));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 3, 4));        
	    
        $this->addAftSystem(new Thruster(1, 4, 0, 1, 2));    
        $this->addAftSystem(new Thruster(2, 8, 0, 5, 2));    
        $this->addAftSystem(new Thruster(1, 4, 0, 1, 2));    
		$this->addAftSystem(new NexusLightGasGun(2, 5, 1, 240, 60));
		$this->addAftSystem(new NexusLightGasGun(2, 5, 1, 300, 120));
		$this->addAftSystem(new NexusShatterGun(1, 2, 1, 180, 60));
		$this->addAftSystem(new NexusShatterGun(1, 2, 1, 300, 180));
       
        $this->addPrimarySystem(new Structure(4, 39));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			10 => "Magazine",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			6 => "Light Coilgun",
			8 => "Light Gas Gun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Shatter Gun",
			10 => "Light Gas Gun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
