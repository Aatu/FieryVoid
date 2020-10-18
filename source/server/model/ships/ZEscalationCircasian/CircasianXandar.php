<?php
class CircasianXandar extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 375;
        $this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianXandar";
        $this->imagePath = "img/ships/EscalationWars/CircasianXandar.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Xandar Lancer Frigate";
			$this->unofficial = true;
        $this->isd = 1952;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 6*5;
        
         
        $this->addPrimarySystem(new Reactor(2, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 9, 3, 5));
        $this->addPrimarySystem(new Engine(3, 9, 0, 8, 2));
        $this->addPrimarySystem(new LightLaser(0, 4, 3, 240, 30));
        $this->addPrimarySystem(new LightLaser(0, 4, 3, 330, 120));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
		$this->addFrontSystem(new Hangar(3, 2));
		$this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 240, 360));
		$this->addFrontSystem(new EWRocketLauncher(2, 2, 1, 0, 120));
		$this->addFrontSystem(new EWParticleLance(2, 10, 10, 330, 30));
                
        $this->addAftSystem(new Thruster(2, 9, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 4, 2));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 240, 60));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 60, 240));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 300, 120));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 30));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addPrimarySystem(new Structure( 3, 30));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
					13 => "Light Laser Cannon",
                    15 => "Scanner",
                    17 => "Engine",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
					7 => "Particle Lance",
                    9 => "Rocket Launcher",
					11 => "Hangar",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    10 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
