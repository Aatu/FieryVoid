<?php
class BrixadiiDestroyerEscortRefit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 470;
        $this->faction = "ZNexus Brixadii Clans";
        $this->phpclass = "BrixadiiDestroyerEscortRefit";
        $this->imagePath = "img/ships/Nexus/BrixadiiPlasmaDestroyer.png";
			$this->canvasSize = 120; //img has 200px per side
        $this->shipClass = "Destroyer Escort (2108 refit)";
			$this->variantOf = "Battle Destroyer";
			$this->occurence = "uncommon";
			$this->unofficial = true;
        $this->isd = 2108;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 35;
         
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 5, 6));
        $this->addPrimarySystem(new Engine(4, 14, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new ScatterPulsar(3, 4, 2, 240, 60));
		$this->addFrontSystem(new ScatterPulsar(3, 4, 2, 300, 120));
        $this->addFrontSystem(new EnergyPulsar(3, 6, 3, 240, 60));
        $this->addFrontSystem(new EnergyPulsar(3, 6, 3, 300, 120));
		$this->addFrontSystem(new NexusAntifighterLauncher(0, 6, 0, 270, 90));
                
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
		$this->addAftSystem(new ScatterPulsar(3, 4, 2, 180, 360));
		$this->addAftSystem(new ScatterPulsar(3, 4, 2, 120, 360));
		$this->addAftSystem(new ScatterPulsar(3, 4, 2, 0, 240));
		$this->addAftSystem(new ScatterPulsar(3, 4, 2, 0, 180));
		$this->addAftSystem(new NexusChaffLauncher(2, 0, 0, 0, 0));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 45));
        $this->addAftSystem(new Structure( 3, 42));
        $this->addPrimarySystem(new Structure( 4, 40));

        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    10 => "Thruster",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Scatter Pulsar",
                    10 => "Energy Pulsar",
					12 => "Anti-fighter Launcher",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					7 => "Chaff Launcher",
					11 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
