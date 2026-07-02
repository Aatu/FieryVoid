<?php
class paragonGCAM extends BaseShip{
    /*Uniqe Paragon Strike Force Command Ship*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1500;
		$this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "paragonGCAM";
        $this->imagePath = "img/ships/GCparagon.png";
        $this->canvasSize = 240;
        $this->shipClass = "Paragon Command Ship (2242)";
			$this->variantOf = "Prophet Command Ship (2240)";
			$this->occurence = "uncommon";
        $this->limited = 33;
	    $this->isd = 2242;

		$this->unofficial = true;
	    
        $this->shipSizeClass = 3;
        $this->fighters = array("light"=>12, "medium"=>18, "assault shuttles"=>6);
		
        $this->forwardDefense = 19;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 6;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(120); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 120); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_KK';//add enhancement options for other missiles - Class-KK               
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C
		
        $this->addPrimarySystem(new Reactor(5, 42, 0, 0));
		$this->addPrimarySystem(new FlagBridge(6, 30, 0, 1, 'Orieni Command Bonus', 10,  true, true, true, false, array("Great Crusade Orieni Imperium", "Orieni Imperium")));
        $this->addPrimarySystem(new Scanner(5, 30, 5, 8));
        $this->addPrimarySystem(new Engine(5, 30, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(5, 38, 12));
        $this->addPrimarySystem(new JumpEngine(5, 40, 6, 25));
        $this->addPrimarySystem(new HKControlNodeOrieni(5, 24, 3, 3));
        $this->addPrimarySystem(new LightLaserLance(3, 6, 5, 0, 360));
        $this->addPrimarySystem(new LightLaserLance(3, 6, 5, 0, 360));
		$this->addPrimarySystem(new AmmoMissileRackL(5, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new HeavyGaussRifle(4, 12, 5, 270, 90));
        $this->addFrontSystem(new HeavyGaussRifle(4, 12, 5, 270, 90));
		$this->addFrontSystem(new AmmoMissileRackL(5, 0, 0, 270, 90, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackL(5, 0, 0, 270, 90, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new HeavyGaussRifle(4, 12, 5, 90, 270));
        $this->addAftSystem(new HeavyGaussRifle(4, 12, 5, 90, 270));
		$this->addAftSystem(new AmmoMissileRackL(5, 0, 0, 90, 270, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addLeftSystem(new Thruster(4, 25, 0, 6, 3));
        $this->addLeftSystem(new WarLance(3, 9, 6, 180, 360));
        $this->addLeftSystem(new WarLance(3, 9, 6, 180, 360));
        $this->addLeftSystem(new HeavyGaussRifle(4, 12, 5, 180, 360));
        $this->addLeftSystem(new HeavyGaussRifle(4, 12, 5, 180, 360));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
		$this->addLeftSystem(new AmmoMissileRackL(5, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addRightSystem(new Thruster(4, 25, 0, 6, 4));
        $this->addRightSystem(new WarLance(3, 9, 6, 0, 180));
        $this->addRightSystem(new WarLance(3, 9, 6, 0, 180));
        $this->addRightSystem(new HeavyGaussRifle(4, 12, 5, 0, 180));
        $this->addRightSystem(new HeavyGaussRifle(4, 12, 5, 0, 180));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));
		$this->addRightSystem(new AmmoMissileRackL(5, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

		//structures
        $this->addFrontSystem(new Structure(6, 60));
        $this->addAftSystem(new Structure(5, 60));
        $this->addLeftSystem(new Structure(5, 68));
        $this->addRightSystem(new Structure(5, 68));
        $this->addPrimarySystem(new Structure(6, 60));

	//d20 hit chart
	$this->hitChart = array(
		
		//PRIMARY
		0=> array( 
			6 => "Structure",
			7 => "Light Laser Lance",
			8 => "Class-L Missile Rack",
			10 => "Jump Engine",
			12 => "Scanner",
			14 => "Engine",
			16 => "Hangar",
			18 => "HK Control Node",
			19 => "Reactor",
			20 => "C&C",
		),
		//Forward
		1=> array(
			4 => "Thruster",
			6 => "Class-L Missile Rack",
			9 => "Heavy Gauss Rifle",
			11 => "Improved Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Aft
		2=> array(
			5 => "Thruster",
			6 => "Class-L Missile Rack",
			9 => "Heavy Gauss Rifle",
			11 => "Improved Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Port
		3=> array(
			3 => "Thruster",
			5 => "War Lance",
			8 => "Heavy Gauss Cannon",
			9 => "Class-L Missile Rack",
			11 => "Improved Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Starboard
		4=> array(
			3 => "Thruster",
			5 => "War Lance",
			8 => "Heavy Gauss Cannon",
			9 => "Class-L Missile Rack",
			11 => "Improved Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
	);


    }
}
?>
