<?php
class BrixadiiPlasmaDestroyer extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "Nexus Brixadii Clans";
        $this->phpclass = "BrixadiiPlasmaDestroyer";
        $this->imagePath = "img/ships/Nexus/brixadii_battle_destroyer.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Plasma Destroyer";
			$this->variantOf = "Battle Destroyer (2108)";
			$this->occurence = "uncommon";
			$this->unofficial = true;
        $this->isd = 2110;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;
         
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
    	$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 240, 0));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 0, 120));
        $this->addFrontSystem(new EnergyPulsar(2, 6, 3, 240, 60));
        $this->addFrontSystem(new EnergyPulsar(2, 6, 3, 300, 120));
		$this->addFrontSystem(new NexusKineticBoxLauncher(0, 4, 0, 300, 60));
                
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 180, 360));
        $this->addAftSystem(new EnergyPulsar(2, 6, 3, 180, 360));
        $this->addAftSystem(new EnergyPulsar(2, 6, 3, 0, 180));
        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 0, 180));
		$this->addAftSystem(new NexusChaffLauncher(2, 0, 0, 0, 0));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 3, 45));
        $this->addPrimarySystem(new Structure( 4, 40));

        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    11 => "Thruster",
                    13 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Energy Pulsar",
                    10 => "Heavy Plasma Cannon",
					12 => "Kinetic Box Launcher",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					7 => "Chaff Launcher",
					9 => "Energy Pulsar",
					11 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
