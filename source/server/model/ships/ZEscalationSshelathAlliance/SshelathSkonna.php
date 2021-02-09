<?php
class SshelathSkonna extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "ZEscalation Sshel'ath Alliance";
        $this->phpclass = "SshelathSkonna";
        $this->imagePath = "img/ships/EscalationWars/SshelathSkonna.png";
        $this->shipClass = "Skonna Corvette";
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 80;
	    $this->isd = 1934;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(2, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 4));
        $this->addPrimarySystem(new Engine(3, 10, 0, 6, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));   
        
		$this->addFrontSystem(new EWDefenseLaser(1, 2, 1, 240, 60));
		$this->addFrontSystem(new EWDefenseLaser(1, 2, 1, 300, 120));
		$this->addFrontSystem(new NexusLightLaserCutter(2, 4, 3, 240, 360));
		$this->addFrontSystem(new NexusLightLaserCutter(2, 4, 3, 0, 120));
		$this->addFrontSystem(new EWEarlyRailgun(2, 9, 6, 240, 120));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
	    
		$this->addAftSystem(new EWDefenseLaser(1, 2, 1, 120, 300));
		$this->addAftSystem(new EWDefenseLaser(1, 2, 1, 60, 240));
		$this->addAftSystem(new NexusLightLaserCutter(2, 4, 3, 90, 270));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));    
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));    
       
        $this->addPrimarySystem(new Structure(3, 32));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Thruster",
			13 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			3 => "Thruster",
			6 => "Early Railgun",
			8 => "Defense Laser",
			10 => "Light Laser Cutter",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			7 => "Light Laser Cutter",
			9 => "Defense Laser",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
