<?php
class BrixadiiBattleDestroyerBase extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 390;
        $this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiBattleDestroyerBase";
        $this->imagePath = "img/ships/Nexus/BrixadiiBattleDestroyer.png";
			$this->canvasSize = 120; //img has 200px per side
        $this->shipClass = "Battle Destroyer";
			$this->unofficial = true;
        $this->isd = 2049;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 7*5;
         
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 5, 4));
        $this->addPrimarySystem(new Engine(4, 14, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 14, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(3, 14, 0, 6, 1));
    	$this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 240, 0));
        $this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 0, 120));
        $this->addFrontSystem(new NexusProjectorArray(2, 6, 1, 240, 60));
        $this->addFrontSystem(new NexusProjectorArray(2, 6, 1, 300, 120));
                
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new NexusProjectorArray(2, 6, 1, 180, 360));
        $this->addAftSystem(new NexusProjectorArray(2, 6, 1, 0, 180));
		$this->addAftSystem(new NexusDefensePulsar(1, 4, 2, 120, 360));
		$this->addAftSystem(new NexusDefensePulsar(1, 4, 2, 0, 240));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 35));
        $this->addAftSystem(new Structure( 3, 32));
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
                    7 => "Projector Array",
                    10 => "Heavy Particle Projector",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
					9 => "Defense Pulsar",
                    11 => "Projector Array",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
