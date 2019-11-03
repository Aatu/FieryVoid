<?php
class Navask extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 380;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Navask";
        $this->imagePath = "img/ships/navask.png";
        $this->shipClass = "Navask Escort Destroyer";    
		$this->isd = 1966;		    

        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(6, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 15, 3, 6));
        $this->addPrimarySystem(new Engine(5, 16, 0, 9, 2));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 4));        
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1)); 
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 240, 120));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 240, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 120));        
        $this->addFrontSystem(new SentinelPointDefense(1, 4, 1, 240, 60));
        $this->addFrontSystem(new SentinelPointDefense(1, 4, 2, 300, 120));
        $this->addFrontSystem(new SentinelPointDefense(1, 4, 2, 270, 90));
        
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 240));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 60));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 6, 40));
		
		
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    12 => "Thruster",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    7 => "Sentinel Point Defense",
                    8 => "Light Particle Beam",
                    12 => "Particle Projector",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
                    9 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
        
        
        
    }

}



?>
