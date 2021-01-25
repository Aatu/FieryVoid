<?php
class CircasianLotana extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 375;
	$this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianLotana";
        $this->imagePath = "img/ships/EscalationWars/CircasianKolanis.png";
        $this->shipClass = "Lotana Logistics Cruiser";
			$this->variantOf = "Kolanis Cruiser";
			$this->occurence = "common";
			$this->unofficial = true;
        $this->shipSizeClass = 3;
		$this->canvasSize = 175; //img has 200px per side

	$this->isd = 1965;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 4, 5));
        $this->addPrimarySystem(new Engine(4, 15, 0, 9, 3));
		$this->addPrimarySystem(new Hangar(4, 4));
   
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
        $this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 60));
        $this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 60));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 240, 60));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 240, 60));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 120));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 120));

        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));

		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 240, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 300));
		$this->addLeftSystem(new LightParticleCannon(3, 6, 5, 300, 360));
		$this->addLeftSystem(new LightParticleCannon(3, 6, 5, 300, 360));
        $this->addLeftSystem(new Thruster(2, 15, 0, 4, 3));
		$this->addLeftSystem(new CargoBay(2, 18));
		$this->addLeftSystem(new CargoBay(2, 18));

		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 120));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 60, 180));
		$this->addRightSystem(new LightParticleCannon(3, 6, 5, 0, 60));
		$this->addRightSystem(new LightParticleCannon(3, 6, 5, 0, 60));
        $this->addRightSystem(new Thruster(2, 15, 0, 4, 4));
		$this->addRightSystem(new CargoBay(2, 18));
		$this->addRightSystem(new CargoBay(2, 18));
        
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
					10 => "Light Plasma Cannon",
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
					12 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Light Particle Cannon",
					8 => "Light Particle Beam",
					12 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
