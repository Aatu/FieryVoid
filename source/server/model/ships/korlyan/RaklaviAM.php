<?php
class RaklaviAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
		$this->faction = "Kor-Lyan";
//	$this->faction = "Custom Ships";
        $this->phpclass = "RaklaviAM";
        $this->imagePath = "img/ships/korlyan_raklavi.png";
        $this->shipClass = "Raklavi Carrier";
        $this->shipSizeClass = 3;
		$this->canvasSize = 190; //img has 200px per side

		$this->fighters = array("assault shuttles"=>2, "normal"=>24);

		$this->isd = 2227;

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 5;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 0;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(80); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 80); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 1));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 5));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(5, 26));
   
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new AmmoMissileRackL(3, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new MultiDefenseLauncher(4, 'D', 240, 60, false));
        $this->addFrontSystem(new MultiDefenseLauncher(4, 'D', 300, 120, false));
        $this->addFrontSystem(new AmmoMissileRackL(3, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 12, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
        $this->addAftSystem(new AmmoMissileRackL(3, 0, 0, 120, 300, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new MultiDefenseLauncher(2, 'D', 120, 300, false));
        $this->addAftSystem(new MultiDefenseLauncher(2, 'D', 60, 240, false));
        $this->addAftSystem(new AmmoMissileRackL(3, 0, 0, 60, 240, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 360));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 360));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 360));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 180));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 44));
        $this->addAftSystem(new Structure(5, 39));
        $this->addLeftSystem(new Structure(5, 51));
        $this->addRightSystem(new Structure(5, 51));
        $this->addPrimarySystem(new Structure(5, 54));
		
		$this->hitChart = array(
			0=> array(
					11 => "Structure",
					13 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Class-D Missile Rack",
					10 => "Class-L Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Class-D Missile Rack",
					10 => "Class-L Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					10 => "Standard Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					10 => "Standard Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
