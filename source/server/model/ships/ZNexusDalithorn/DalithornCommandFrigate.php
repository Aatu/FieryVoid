<?php
class DalithornCommandFrigate extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 245;
        $this->faction = "ZNexus Dalithorn";
        $this->phpclass = "DalithornCommandFrigate";
        $this->imagePath = "img/ships/Nexus/DalithornCommandFrigate.png";
        $this->shipClass = "Command Frigate";
			$this->variantOf = "Frigate";
			$this->occurence = "uncommon";
        $this->limited = 10;
		$this->unofficial = true;
        $this->canvasSize = 80;
	    $this->isd = 1921;
        
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
        $this->addPrimarySystem(new Scanner(3, 10, 3, 5));
        $this->addPrimarySystem(new Engine(3, 12, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(1, 1));
		$this->addPrimarySystem(new CargoBay(2, 10));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 4));        
        
		$this->addFrontSystem(new NexusLightCoilgun(3, 7, 3, 330, 30));
		$this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 240, 360));
		$this->addFrontSystem(new NexusLightGasGun(2, 5, 1, 0, 120));
        $this->addFrontSystem(new Thruster(2, 8, 0, 4, 1));
	    
        $this->addAftSystem(new Thruster(1, 4, 0, 1, 2));    
        $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));    
        $this->addAftSystem(new Thruster(1, 4, 0, 1, 2));    
		$this->addAftSystem(new NexusLightGasGun(2, 5, 1, 240, 60));
		$this->addAftSystem(new NexusLightGasGun(2, 5, 1, 300, 120));
		$this->addAftSystem(new NexusShatterGun(1, 2, 1, 180, 60));
		$this->addAftSystem(new NexusShatterGun(1, 2, 1, 300, 180));
       
        $this->addPrimarySystem(new Structure(3, 39));


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
			8 => "Light Coilgun",
			10 => "Light Gas Gun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			7 => "Shatter Gun",
			9 => "Light Gas Gun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>