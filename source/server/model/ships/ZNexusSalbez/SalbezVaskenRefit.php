<?php
class SalbezVaskenRefit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 525;
        $this->faction = "ZNexus Sal-bez";
        $this->phpclass = "SalbezVaskenRefit";
        $this->imagePath = "img/ships/Nexus/salbez_destroyer.png";
			$this->canvasSize = 115; //img has 200px per side
        $this->shipClass = "Vas-ken Scout Destroyer (2145 refit)";
			$this->variantOf = "Vas-ren Destroyer";
			$this->occurence = "common";
			$this->unofficial = true;
        $this->limited = 10;
        $this->isd = 2145;
		
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 6*5;
         
        $this->addPrimarySystem(new Reactor(5, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 14, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 21, 8, 10));
        $this->addPrimarySystem(new Engine(4, 18, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new NexusSwarmTorpedo(3, 5, 2, 300, 60));
        $this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 240, 60));
        $this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 270, 90));
        $this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 270, 90));
        $this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 300, 120));
                
        $this->addAftSystem(new Thruster(4, 28, 0, 10, 2));
        $this->addAftSystem(new NexusImprovedParticleBeam(3, 6, 5, 240, 360));
        $this->addAftSystem(new NexusImprovedParticleBeam(2, 3, 1, 90, 270));
        $this->addAftSystem(new NexusImprovedParticleBeam(2, 3, 1, 90, 270));
        $this->addAftSystem(new NexusImprovedParticleBeam(3, 6, 5, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 35));
        $this->addAftSystem(new Structure( 4, 34));
        $this->addPrimarySystem(new Structure( 5, 36));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
                    13 => "ELINT Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Swarm Torpedo",
					0 => "Improved Particle Beam",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					9 => "Improved Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
