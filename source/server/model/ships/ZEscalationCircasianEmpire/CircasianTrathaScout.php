<?php
class CircasianTrathaScout extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 350;
	$this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianTrathaScout";
        $this->imagePath = "img/ships/EscalationWars/CircasianTratha.png";
        $this->shipClass = "Tratha Scout Cruiser";
			$this->variantOf = "Eltesa Light Cruiser";
			$this->occurence = "rare";		
        $this->shipSizeClass = 3;
		$this->canvasSize = 150; //img has 200px per side
		$this->unofficial = true;

		$this->isd = 1966;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 12, 0, 4));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 15, 4, 6));
        $this->addPrimarySystem(new Engine(4, 11, 0, 10, 2));
		$this->addPrimarySystem(new Hangar(3, 4));
		$this->addPrimarySystem(new JumpEngine(4, 10, 3, 36));
   
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 240, 60));
        $this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 60));
        $this->addFrontSystem(new EWRocketLauncher(3, 4, 1, 300, 60));
        $this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 120));

        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 300));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 180));

		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 240, 120));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 60, 300));
        $this->addLeftSystem(new CargoBay(1, 12));
        $this->addLeftSystem(new Thruster(2, 11, 0, 4, 3));

		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 240, 120));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 60, 300));
        $this->addRightSystem(new CargoBay(1, 12));
        $this->addRightSystem(new Thruster(2, 11, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 30));
        $this->addAftSystem(new Structure(3, 32));
        $this->addLeftSystem(new Structure(3, 34));
        $this->addRightSystem(new Structure(3, 34));
        $this->addPrimarySystem(new Structure(4, 30));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Jump Engine",
					13 => "ELINT Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Rocket Launcher",
					9 => "Light Plasma Cannon",
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
					8 => "Cargo Bay",
					11 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Cargo Bay",
					11 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
