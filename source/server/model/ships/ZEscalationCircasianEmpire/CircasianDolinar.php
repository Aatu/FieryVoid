<?php
class CircasianDolinar extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianDolinar";
        $this->imagePath = "img/ships/EscalationWars/CircasianDolinar.png";
        $this->shipClass = "Dolinar Technology Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side
		$this->unofficial = true;
		$this->limited = 10;

		$this->isd = 1958;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = -20;
        
        $this->addPrimarySystem(new Reactor(3, 25, 0, 0));
        $this->addPrimarySystem(new CnC(4, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 5));
        $this->addPrimarySystem(new Engine(3, 18, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
		$laboratory = new Quarters(4, 9);
			$laboratory->displayName = "Laboratory";
			$this->addPrimarySystem($laboratory);
   
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 240, 360));
        $this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 0, 120));
        $this->addFrontSystem(new EWParticleLance(3, 10, 10, 330, 30));
        $this->addFrontSystem(new EWParticleLance(3, 10, 10, 330, 30));

		$this->addAftSystem(new LightParticleCannon(2, 6, 5, 180, 240));
		$this->addAftSystem(new LightParticleCannon(2, 6, 5, 120, 180));
        $this->addAftSystem(new Thruster(2, 14, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 14, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 14, 0, 3, 2));

		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addLeftSystem(new LightLaser(3, 4, 3, 240, 360));
		$this->addLeftSystem(new LightLaser(3, 4, 3, 180, 300));
        $this->addLeftSystem(new Thruster(2, 13, 0, 5, 3));

		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
		$this->addRightSystem(new LightLaser(3, 4, 3, 0, 120));
		$this->addRightSystem(new LightLaser(3, 4, 3, 60, 180));
        $this->addRightSystem(new Thruster(2, 13, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 38));
        $this->addAftSystem(new Structure(3, 36));
        $this->addLeftSystem(new Structure(3, 40));
        $this->addRightSystem(new Structure(3, 40));
        $this->addPrimarySystem(new Structure(3, 40));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Scanner",
					13 => "Engine",
					15 => "Hangar",
					18 => "Reactor",
					19 => "Laboratory",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					7 => "Particle Lance",
					9 => "Rocket Launcher",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					9 => "Light Particle Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					5 => "Light Laser",
					8 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					5 => "Light Laser",
					8 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
