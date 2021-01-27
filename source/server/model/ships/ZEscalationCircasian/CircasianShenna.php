<?php
class CircasianShenna extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianShenna";
        $this->imagePath = "img/ships/EscalationWars/CircasianShenna.png";
			$this->canvasSize = 150; //img has 200px per side
        $this->shipClass = "Shenna Strike Carrier";
			$this->limited = 33;
			$this->unofficial = true;
        $this->isd = 1953;
		$this->fighters = array("normal"=>18);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 6*5;
        
         
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 9, 3, 5));
        $this->addPrimarySystem(new Engine(3, 11, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(3, 20));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
		$this->addFrontSystem(new EWParticleLance(3, 12, 14, 300, 360));
		$this->addFrontSystem(new EWParticleLance(3, 12, 14, 0, 60));
                
        $this->addAftSystem(new Thruster(2, 9, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 4, 2));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
        $this->addAftSystem(new EWRocketLauncher(2, 4, 1, 240, 360));
        $this->addAftSystem(new EWRocketLauncher(2, 4, 1, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 40));
        $this->addAftSystem(new Structure( 3, 40));
        $this->addPrimarySystem(new Structure( 3, 40));
		
        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    10 => "Thruster",
                    12 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
					7 => "Particle Lance",
                    10 => "Light Particle Beam",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
					7 => "Rocket Launcher",
                    10 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
