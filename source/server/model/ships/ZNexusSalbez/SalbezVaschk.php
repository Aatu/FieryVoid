<?php
class SalbezVaschk extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 440;
        $this->faction = "ZNexus Sal-bez Coalition";
        $this->phpclass = "SalbezVaschk";
        $this->imagePath = "img/ships/Nexus/salbez_vaschk.png";
			$this->canvasSize = 115; //img has 200px per side
        $this->shipClass = "Vas-chk Support Destroyer";
			$this->variantOf = "Vas-ren Destroyer";
			$this->occurence = "uncommon";
			$this->unofficial = true;
       $this->isd = 2131;

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
        $this->addPrimarySystem(new Scanner(4, 14, 5, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 5, 4));
      
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addFrontSystem(new NexusSwarmTorpedo(3, 5, 2, 300, 60));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
                
        $this->addAftSystem(new Thruster(4, 28, 0, 10, 2));
        $this->addAftSystem(new NexusSwarmTorpedo(3, 5, 2, 240, 360));
        $this->addAftSystem(new LightLaser(2, 4, 3, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        $this->addAftSystem(new LightLaser(2, 4, 3, 0, 180));
        $this->addAftSystem(new NexusSwarmTorpedo(3, 5, 2, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 35));
        $this->addAftSystem(new Structure( 4, 34));
        $this->addPrimarySystem(new Structure( 4, 36));
		
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
                    7 => "Medium Laser",
					9 => "Swarm Torpedo",
					11 => "Light Particle Beam",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Swarm Torpedo",
					10 => "Light Laser",
					12 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
