<?php
class SalbezZefjemRefit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 375;
        $this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezZefjemRefit";
			$this->variantOf = "Zef'jem Heavy Frigate";
			$this->occurence = "common";
        $this->imagePath = "img/ships/Nexus/salbez_zefjem.png";
		$this->canvasSize = 115; //img has 200px per side
        $this->shipClass = "Zef'jem Heavy Frigate (2100 refit)";
		$this->unofficial = true;
        $this->isd = 2100;
		
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 6*5;
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new NexusHeavyLaserCutter(2, 8, 5, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
		$this->addFrontSystem(new NexusSwarmTorpedo(2, 5, 2, 300, 60));
                
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new LaserCutter(2, 6, 4, 240, 360));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 60, 240));
        $this->addAftSystem(new LaserCutter(2, 6, 4, 0, 120));
		$this->addAftSystem(new CargoBay(1, 12));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 35));
        $this->addAftSystem(new Structure( 3, 35));
        $this->addPrimarySystem(new Structure( 4, 40));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Heavy Laser Cutter",
                    9 => "Swarm Torpedo",
					10 => "Light Particle Beam",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    8 => "Thruster",
                    10 => "Laser Cutter",
					11 => "Cargo Bay",
					12 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
