<?php
class BrixadiiBattleDestroyerLeader2108 extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 520;
        $this->faction = "ZNexus Brixadii Clans";
        $this->phpclass = "BrixadiiBattleDestroyerLeader2108";
        $this->imagePath = "img/ships/Nexus/BrixadiiBDDLeader.png";
			$this->canvasSize = 120; //img has 200px per side
        $this->shipClass = "Battle Destroyer Leader (2108)";
        $this->variantOf = "Battle Destroyer";
			$this->occurence = "rare";
			$this->unofficial = true;
        $this->isd = 2108;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;
         
        $this->addPrimarySystem(new Reactor(4, 19, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 5, 7));
        $this->addPrimarySystem(new Engine(4, 14, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));

        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
    	$this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 240, 0));
        $this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 0, 120));
		$this->addFrontSystem(new NexusKineticBoxLauncher(0, 4, 0, 300, 60));
		$this->addFrontSystem(new NexusKineticBoxLauncher(0, 4, 0, 300, 60));
        $this->addFrontSystem(new EnergyPulsar(2, 6, 3, 240, 60));
        $this->addFrontSystem(new EnergyPulsar(2, 6, 3, 300, 120));
                
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new EnergyPulsar(2, 6, 3, 180, 360));
        $this->addAftSystem(new EnergyPulsar(2, 6, 3, 0, 180));
		$this->addAftSystem(new ScatterPulsar(1, 4, 2, 180, 360));
		$this->addAftSystem(new ScatterPulsar(1, 4, 2, 0, 180));
		$this->addAftSystem(new NexusChaffLauncher(2, 2, 1, 0, 360));
		$this->addAftSystem(new NexusChaffLauncher(2, 2, 1, 0, 360));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 45));
        $this->addAftSystem(new Structure( 3, 42));
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
                    10 => "Heavy Particle Projector",
					12 => "Kinetic Box Launcher",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
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
