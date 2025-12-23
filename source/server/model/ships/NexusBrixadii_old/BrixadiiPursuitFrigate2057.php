<?php
class BrixadiiPursuitFrigate2057 extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 330;
        $this->faction = "Nexus Brixadii Clans (early)";
        $this->phpclass = "BrixadiiPursuitFrigate2057";
        $this->imagePath = "img/ships/Nexus/brixadii_pursuit_frigate.png";
			$this->canvasSize = 100; //img has 200px per side
        $this->shipClass = "Pursuit Frigate (2057)";
			$this->variantOf = "Pursuit Frigate";
			$this->occurence = "common";
		$this->unofficial = true;
   		$this->isd = 2057;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
		$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14*5;
         
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 5));
        $this->addPrimarySystem(new Engine(4, 9, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(2, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 7, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 7, 0, 3, 4));
        $this->addPrimarySystem(new Thruster(2, 7, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new NexusParticleBolter(2, 6, 2, 300, 60));
		$this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 300, 60));
		$this->addFrontSystem(new NexusParticleBolter(2, 6, 2, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addFrontSystem(new NexusKineticBoxLauncher(0, 4, 0, 300, 60));
                
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 120, 360));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 0, 240));
		$this->addAftSystem(new NexusChaffLauncher(2, 2, 1, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 3, 40));
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
                    4 => "Thruster",
					6 => "Kinetic Box Launcher",
                    8 => "Particle Bolter",
					10 => "Light Particle Beam",
					12 => "Heavy Particle Projector",
					17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
					9 => "Chaff Launcher",
                    11 => "Light Particle Beam",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}

?>
