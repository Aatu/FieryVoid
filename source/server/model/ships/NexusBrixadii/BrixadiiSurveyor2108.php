<?php
class BrixadiiSurveyor2108 extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 480;
        $this->faction = "Nexus Brixadii Clans";
        $this->phpclass = "BrixadiiSurveyor2108";
        $this->imagePath = "img/ships/Nexus/brixadii_surveyor.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Surveyor (2108)";
			$this->limited = 10;
			$this->unofficial = true;
        $this->isd = 2108;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 8*5;
         
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 10, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 10, 5, 9));
        $this->addPrimarySystem(new Engine(4, 14, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));
        $this->addPrimarySystem(new Thruster(3, 7, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(3, 14, 0, 6, 1));
    	$this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 240, 0));
        $this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 0, 120));
        $this->addFrontSystem(new ScatterPulsar(2, 4, 2, 180, 60));
        $this->addFrontSystem(new ScatterPulsar(2, 4, 2, 300, 180));
                
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
		$this->addAftSystem(new NexusChaffLauncher(1, 2, 1, 0, 360));
		$this->addAftSystem(new EnergyPulsar(2, 6, 3, 180, 360));
		$this->addAftSystem(new EnergyPulsar(2, 6, 3, 0, 180));
		$this->addAftSystem(new NexusChaffLauncher(1, 2, 1, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 3, 45));
        $this->addPrimarySystem(new Structure( 4, 40));

        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    11 => "Thruster",
                    13 => "ELINT Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    8 => "Heavy Particle Projector",
                    10 => "Scatter Pulsar",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
                    9 => "Chaff Launcher",
					11 => "Energy Pulsar",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
