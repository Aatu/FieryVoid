<?php
class BrixadiiAttackFrigate extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 265;
        $this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiAttackFrigate";
        $this->imagePath = "img/ships/Nexus/BrixadiiPursuitFrigate.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Attack Frigate";
//			$this->variantOf = "Pursuit Frigate";
//			$this->occurence = "common";
		$this->unofficial = true;
   		$this->isd = 2056;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
		$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 12*5;
        
         
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 4));
        $this->addPrimarySystem(new Engine(4, 9, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(2, 14, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(2, 14, 0, 6, 4));
      
        $this->addFrontSystem(new Thruster(3, 14, 0, 6, 1));
		$this->addFrontSystem(new NexusParticleAgitator(3, 8, 3, 240, 360));
		$this->addFrontSystem(new NexusParticleAgitator(3, 8, 3, 0, 120));
		$this->addFrontSystem(new LightParticleProjector(1, 3, 1, 180, 60));
		$this->addFrontSystem(new LightParticleProjector(1, 2, 1, 300, 180));
		$this->addFrontSystem(new NexusKineticBoxLauncher(0, 4, 0, 300, 60));
                
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new LightParticleProjector(1, 3, 1, 120, 360));
        $this->addAftSystem(new LightParticleProjector(1, 3, 1, 0, 240));
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
                    5 => "Thruster",
					6 => "Kinetic Box Launcher",
					8 => "Particle Agitator",
                    10 => "Light Particle Projector",
					17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
					8 => "Chaff Launcher",
                    10 => "Light Particle Projector",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}

?>
