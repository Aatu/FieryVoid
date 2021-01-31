<?php
class CircasianIlustrisCommand extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 400;
        $this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianIlustrisCommand";
        $this->imagePath = "img/ships/EscalationWars/CircasianIlustris.png";
			$this->canvasSize = 125; //img has 200px per side
        $this->shipClass = "Ilustris Command Destroyer";
			$this->variantOf = "Ilustris Heavy Destroyer";
			$this->occurence = "rare";
			$this->unofficial = true;
        $this->isd = 1948;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 7*5;
        
         
        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(5, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 6, 6));
        $this->addPrimarySystem(new Engine(3, 13, 0, 10, 4));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
      
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
		$this->addFrontSystem(new EWRocketLauncher(1, 4, 1, 270, 90));
		$this->addFrontSystem(new EWRocketLauncher(1, 4, 1, 270, 90));
		$this->addFrontSystem(new LightParticleCannon(3, 6, 5, 300, 360));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 300, 60));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 300, 60));
		$this->addFrontSystem(new LightParticleCannon(3, 6, 5, 0, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));
                
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 3, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 300));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 180));
        $this->addAftSystem(new Thruster(3, 7, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 48));
        $this->addAftSystem(new Structure( 3, 42));
        $this->addPrimarySystem(new Structure( 3, 46));
		
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    11 => "Thruster",
                    13 => "Scanner",
                    15 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
					7 => "Light Particle Cannon",
					9 => "Rocket Launcher",
					12 => "Light Particle Beam",
					18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    9 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
