<?php
class federationDreadnought extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
         
	$this->pointCost = 1150;
	$this->faction = "Terran Federation";
        $this->phpclass = "federationDreadnought";
        $this->imagePath = "img/ships/federationDreadnought.png";
        $this->shipClass = "Dreadnought";
        $this->shipSizeClass = 3;
//		$this->canvasSize = 160; 
        $this->limited = 10;
		$this->unofficial = true;

        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
		$this->enhancementOptionsEnabled[] = 'AMMO_I';//add enhancement options for other missiles - Class-I	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		$this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X
		$this->enhancementOptionsEnabled[] = 'AMMO_Z';//add enhancement options for other missiles - Class-Z                   				
        
        $this->addPrimarySystem(new Reactor(7, 30, 0, 0));
        $this->addPrimarySystem(new CnC(7, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 24, 6, 8));
        $this->addPrimarySystem(new Engine(4, 28, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(4, 4, 4));
        $this->addPrimarySystem(new JumpEngine(5, 15, 4, 30));
		
        $this->addFrontSystem(new Thruster(5, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(5, 15, 0, 4, 1));
		$this->addFrontSystem(new HeavyLaser(5, 8, 6, 300, 60));
		$this->addFrontSystem(new HeavyLaser(5, 8, 6, 300, 60));
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 240, 60));
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 270, 90));
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 300, 120));

        $this->addAftSystem(new Thruster(4, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 35, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 15, 0, 3, 2));
		$this->addAftSystem(new PlasmaBolt(3, 5, 2, 120, 300));
		$this->addAftSystem(new PlasmaBolt(3, 5, 2, 60, 240));

		$this->addLeftSystem(new HeavyLaser(5, 8, 6, 240, 360));
		$this->addLeftSystem(new HeavyLaser(5, 8, 6, 180, 240));
		$this->addLeftSystem(new PlasmaBolt(3, 5, 2, 180, 360));
		$this->addLeftSystem(new PlasmaBolt(3, 5, 2, 180, 360));
		$this->addLeftSystem(new PlasmaBolt(3, 5, 2, 180, 360));
        $this->addLeftSystem(new AmmoMissileRackS(3, 0, 0, 180, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

		$this->addRightSystem(new HeavyLaser(5, 8, 6, 0, 120));
		$this->addRightSystem(new HeavyLaser(5, 8, 6, 120, 180));
		$this->addRightSystem(new PlasmaBolt(3, 5, 2, 0, 180));
		$this->addRightSystem(new PlasmaBolt(3, 5, 2, 0, 180));
		$this->addRightSystem(new PlasmaBolt(3, 5, 2, 0, 180));
        $this->addRightSystem(new AmmoMissileRackS(3, 0, 0, 0, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(6, 100));
        $this->addAftSystem(new Structure(5, 70));
        $this->addLeftSystem(new Structure(5, 90));
        $this->addRightSystem(new Structure(5, 90));
        $this->addPrimarySystem(new Structure(6, 65));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					11 => "Jump Engine",
					13 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Heavy Laser",
					10 => "Plasma Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					9 => "Plasma Bolt",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Heavy Laser",
					9 => "Plasma Bolt",
					11 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Heavy Laser",
					9 => "Plasma Bolt",
					11 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
