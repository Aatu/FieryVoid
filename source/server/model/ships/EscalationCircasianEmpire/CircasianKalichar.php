<?php
class CircasianKalichar extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 625;
		$this->faction = "Escalation Wars Circasian Empire";
        $this->phpclass = "CircasianKalichar";
        $this->imagePath = "img/ships/EscalationWars/CircasianKalichar.png";
        $this->shipClass = "Kalichar Lancer Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;
		$this->limited = 33;

		$this->isd = 1974;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 25, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(4, 2, 1));
        $this->addPrimarySystem(new JumpEngine(3, 12, 3, 36));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 240, 60));
		$this->addFrontSystem(new EWParticleLance(4, 10, 10, 330, 30));
		$this->addFrontSystem(new EWParticleLance(4, 10, 10, 330, 30));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 300, 120));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 300, 120));

        $this->addAftSystem(new Thruster(3, 9, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 4, 2));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 120, 300));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 120, 300));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 60, 240));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 60, 240));

        $this->addLeftSystem(new ParticleCannon(3, 8, 7, 300, 360));
		$this->addLeftSystem(new EWHeavyRocketLauncher(3, 6, 2, 240, 360));
		$this->addLeftSystem(new EWHeavyRocketLauncher(3, 6, 2, 180, 300));
        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));

        $this->addRightSystem(new ParticleCannon(3, 8, 7, 0, 60));
		$this->addRightSystem(new EWHeavyRocketLauncher(3, 6, 2, 0, 120));
		$this->addRightSystem(new EWHeavyRocketLauncher(3, 6, 2, 60, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(4, 36));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Jump Engine",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Particle Lance",
					11 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					10 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Particle Cannon",
					10 => "Heavy Rocket Launcher",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Particle Cannon",
					10 => "Heavy Rocket Launcher",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
