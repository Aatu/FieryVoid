<?php
class CircasianVestas extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 475;
	$this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianVestas";
        $this->imagePath = "img/ships/EscalationWars/CircasianKolanis.png";
        $this->shipClass = "Vestas Command Cruiser";
			$this->variantOf = "Kolanis Cruiser";
			$this->occurence = "rare";
			$this->unofficial = true;
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side

	$this->isd = 1970;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 4, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 9, 3));
		$this->addPrimarySystem(new Hangar(4, 4));
   
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
		$this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 60));
		$this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 240, 360));
        $this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 0, 120));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 240, 60));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 300, 120));

        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));

		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 240, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 300));
		$this->addLeftSystem(new ParticleCannon(3, 8, 7, 300, 360));
		$this->addLeftSystem(new ParticleCannon(3, 8, 7, 300, 360));
		$this->addLeftSystem(new EWRocketLauncher(3, 4, 1, 240, 360));
		$this->addLeftSystem(new EWRocketLauncher(3, 4, 1, 180, 300));
        $this->addLeftSystem(new Thruster(2, 15, 0, 4, 3));

		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 120));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 60, 180));
		$this->addRightSystem(new ParticleCannon(3, 8, 7, 0, 60));
		$this->addRightSystem(new ParticleCannon(3, 8, 7, 0, 60));
		$this->addRightSystem(new EWRocketLauncher(3, 4, 1, 0, 120));
		$this->addRightSystem(new EWRocketLauncher(3, 4, 1, 60, 180));
        $this->addRightSystem(new Thruster(2, 15, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 36));
        $this->addAftSystem(new Structure(3, 38));
        $this->addLeftSystem(new Structure(3, 42));
        $this->addRightSystem(new Structure(3, 42));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					13 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Rocket Launcher",
					9 => "Light Particle Cannon",
					11 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					8 => "Thruster",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Light Particle Cannon",
					8 => "Light Particle Beam",
					11 => "Rocket Launcher",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Light Particle Cannon",
					8 => "Light Particle Beam",
					11 => "Rocket Launcher",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
