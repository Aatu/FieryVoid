<?php
class SalbezNavren extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 330;
        $this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezNavren";
        $this->imagePath = "img/ships/Nexus/salbez_zefjem.png";
			$this->canvasSize = 115; //img has 200px per side
        $this->shipClass = "Nav-ren Prospector";
			$this->unofficial = true;
        $this->isd = 2025;
		
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 4));
        $this->addPrimarySystem(new Engine(3, 11, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 3, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new NexusIndustrialLaser(2, 6, 3, 300, 60));
        $this->addFrontSystem(new NexusParticleGrid(1, 3, 1, 270, 90));
        $this->addFrontSystem(new NexusParticleGrid(1, 3, 1, 270, 90));
		$this->addFrontSystem(new NexusBoltTorpedo(2, 5, 2, 300, 60));
		$this->addFrontSystem(new CargoBay(1, 16));
                
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new NexusIndustrialLaser(2, 6, 3, 300, 360));
        $this->addAftSystem(new NexusParticleGrid(1, 3, 1, 120, 300));
        $this->addAftSystem(new NexusParticleGrid(1, 3, 1, 60, 240));
        $this->addAftSystem(new NexusIndustrialLaser(2, 6, 3, 0, 60));
 		$this->addAftSystem(new CargoBay(1, 16));
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 33));
        $this->addAftSystem(new Structure( 3, 33));
        $this->addPrimarySystem(new Structure( 3, 32));
		
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
                    7 => "Industrial Laser",
					9 => "Cargo Bay",
                    10 => "Bolt Torpedo",
					12 => "Particle Grid",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Laser Cutter",
					10 => "Particle Grid",
					12 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
