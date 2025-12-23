<?php
class SshelathNimraAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathNimraAM";
        $this->imagePath = "img/ships/EscalationWars/SshelathNirte.png";
        $this->shipClass = "Nimra Command Cruiser";
			$this->variantOf = "Nirte Medium Cruiser";
			$this->occurence = "rare";		
        $this->shipSizeClass = 3;
		$this->canvasSize = 150; //img has 200px per side
		$this->unofficial = true;

		$this->isd = 1957;
	
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1.0;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - class-SO launchers hold 12 rounds per mount
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 24); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Engine(4, 13, 0, 7, 4));
		$this->addPrimarySystem(new Hangar(4, 4));
   
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 360));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 0, 60));
        $this->addFrontSystem(new EWLightGaussCannon(2, 6, 3, 240, 120));

        $this->addAftSystem(new Thruster(3, 20, 0, 7, 2));
		$this->addAftSystem(new EWLightGaussCannon(2, 6, 3, 180, 300));
		$this->addAftSystem(new JumpEngine(3, 10, 3, 40));
		$this->addAftSystem(new EWLightGaussCannon(2, 6, 3, 60, 180));

		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new AmmoMissileRackSO(3, 0, 0, 240, 60, $ammoMagazine, false));
		$this->addLeftSystem(new LaserCutter(3, 6, 4, 300, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 3, 3));

		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new AmmoMissileRackSO(3, 0, 0, 300, 120, $ammoMagazine, false));
		$this->addRightSystem(new LaserCutter(3, 6, 4, 0, 60));
        $this->addRightSystem(new Thruster(3, 13, 0, 3, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 35));
        $this->addAftSystem(new Structure(3, 36));
        $this->addLeftSystem(new Structure(3, 40));
        $this->addRightSystem(new Structure(3, 40));
        $this->addPrimarySystem(new Structure(4, 36));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					12 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Laser Cutter",
					8 => "Light Gauss Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					8 => "Light Gauss Cannon",
					10 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Class-SO Missile Rack",
					8 => "Laser Cutter",
					10 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Class-SO Missile Rack",
					8 => "Laser Cutter",
					10 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
