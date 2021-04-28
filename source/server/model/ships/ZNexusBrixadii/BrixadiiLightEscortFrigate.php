<?php
class BrixadiiLightEscortFrigate extends MediumShip{
    /*Brixadii Light Escort Frigate, initial deployment 2108 (Nexus) */
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 385;
        $this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiLightEscortFrigate";
        $this->imagePath = "img/ships/Nexus/BrixadiiLightEscort.png";
			$this->canvasSize = 70; //img has 100px per side
        $this->shipClass = "Light Escort Frigate";
		$this->variantOf = "Pursuit Frigate";
		$this->occurence = "uncommon";
		$this->unofficial = true;
       	$this->isd = 2108;
		
		$this->agile = true;
		$this->forwardDefense = 10;
		$this->sideDefense = 12;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 12*5;

		$this->addPrimarySystem(new Reactor(4, 9, 0, 0));
		$this->addPrimarySystem(new CnC(4, 8, 0, 0));
		$this->addPrimarySystem(new Scanner(3, 10, 4, 5));
		$this->addPrimarySystem(new Engine(4, 9, 0, 9, 4));
		$this->addPrimarySystem(new Hangar(1, 2));
		$this->addPrimarySystem(new Thruster(2, 14, 0, 6, 3));
		$this->addPrimarySystem(new Thruster(2, 14, 0, 6, 4));
      
        $this->addFrontSystem(new Thruster(3, 14, 0, 6, 1));
		$this->addFrontSystem(new ScatterPulsar(2, 4, 2, 180, 60));
		$this->addFrontSystem(new EnergyPulsar(2, 6, 3, 300, 60));
		$this->addFrontSystem(new EnergyPulsar(2, 6, 3, 300, 60));
		$this->addFrontSystem(new ScatterPulsar(2, 4, 2, 300, 180));
		$this->addFrontSystem(new NexusKineticBoxLauncher(0, 4, 0, 300, 60));
		
		$this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new ScatterPulsar(2, 4, 2, 120, 360));
        $this->addAftSystem(new ScatterPulsar(2, 4, 2, 0, 240));
		$this->addAftSystem(new NexusChaffLauncher(2, 2, 1, 0, 360));
		
		$this->addPrimarySystem(new Structure(3, 40));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;

		//d20 hit chart
        $this->hitChart = array(
            0=> array(
				9 => "Thruster",
				10 => "Hangar",
				13 => "Scanner",
				17 => "Engine",
				19 => "Reactor",
				20 => "C&C",
            ),
            1=> array(
				5 => "Thruster",
				6 => "Kinetic Box Launcher",
				8 => "Energy Pulsar",
				10 => "Scatter Pulsar",
				17 => "Structure",
				20 => "Primary",
            ),
            2=> array(
				7 => "Thruster",
				8 => "Chaff Launcer",
				10 => "Scatter Pulsar",
				17 => "Structure",
				20 => "Primary",
            ),
        );
    }
}

?>
