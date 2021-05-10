<?php
class BrixadiiPursuitFrigateBase extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 270;
        $this->faction = "ZNexus Brixadii";
        $this->phpclass = "BrixadiiPursuitFrigateBase";
        $this->imagePath = "img/ships/Nexus/BrixadiiPursuitFrigate.png";
			$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Pursuit Frigate";
		//$this->variantOf = "Pursuit Frigate";
		$this->unofficial = true;
   		$this->isd = 1989;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
		$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 13*5;
         
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 4));
        $this->addPrimarySystem(new Engine(4, 9, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(1, 2));
        $this->addPrimarySystem(new Thruster(2, 14, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 14, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(3, 14, 0, 6, 1));
		$this->addFrontSystem(new NexusProjectorArray(2, 6, 1, 240, 60));
		$this->addFrontSystem(new HvyParticleProjector(3, 8, 4, 300, 60));
		$this->addFrontSystem(new NexusProjectorArray(2, 6, 1, 300, 120));
                
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new NexusDefensePulsar(1, 4, 2, 120, 360));
        $this->addAftSystem(new NexusDefensePulsar(1, 4, 2, 0, 240));
        
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
                    6 => "Thruster",
                    8 => "Projector Array",
					10 => "Heavy Particle Projector",
					17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    8 => "Thruster",
                    10 => "Defense Pulsar",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
