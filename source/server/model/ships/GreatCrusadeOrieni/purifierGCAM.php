<?php
class purifierGCAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 650;
		$this->faction = "Great Crusade Orieni Imperium";
        $this->phpclass = "purifierGCAM";
        $this->imagePath = "img/ships/GCenlightenment.png";
        $this->shipClass = "Purifier Bomber";
			$this->variantOf = "Enlightenment Invader (2237)";
			$this->occurence = "uncommon";
        $this->shipSizeClass = 3;
	    $this->isd = 2245;
        $this->canvasSize = 215;

		$this->unofficial = true;
		
        $this->forwardDefense = 19;
        $this->sideDefense = 18;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 3;
        $this->pivotcost = 3;
		
		//ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(112); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 112); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_KK';//add enhancement options for other missiles - Class-KK               
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C
		//Bomb Rack has only Basic missiles available for bomb racks! BUT Purifier has missile racks as well :) - but ammo magazine limit is for total missiles
        
        $this->addPrimarySystem(new Reactor(5, 30, 0, -2));
        $this->addPrimarySystem(new CnC(5, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 25, 4, 7));
        $this->addPrimarySystem(new Engine(5, 25, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(4, 2, 2));
		$this->addPrimarySystem(new AmmoMissileRackL(5, 6, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
              
		$this->addFrontSystem(new AmmoMissileRackL(5, 6, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackL(5, 6, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackL(5, 6, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new ImpRapidGatling(2, 4, 2, 240, 120));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));  

        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new ImpRapidGatling(2, 4, 2, 60, 300));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));

		$this->addLeftSystem(new AmmoMissileRackS(3, 0, 0, 180, 300, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new AmmoMissileRackS(3, 0, 0, 240, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
        $this->addLeftSystem(new ImpRapidGatling(2, 4, 2, 180, 360));
		$this->addLeftSystem(new AmmoBombRack(2, 6, 0, 180, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new AmmoBombRack(2, 6, 0, 180, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
        $this->addLeftSystem(new MassDriver(4, 18, 9, 330, 30));

		$this->addRightSystem(new AmmoMissileRackS(3, 0, 0, 60, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new AmmoMissileRackS(3, 0, 0, 0, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));
        $this->addRightSystem(new ImpRapidGatling(2, 4, 2, 0, 180));
		$this->addRightSystem(new AmmoBombRack(2, 6, 0, 0, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new AmmoBombRack(2, 6, 0, 0, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new Thruster(4, 20, 0, 6, 4));
        $this->addRightSystem(new MassDriver(4, 18, 9, 330, 30));

		//structures
        $this->addFrontSystem(new Structure(4, 51));
        $this->addAftSystem(new Structure(4, 54));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(5, 48));

	//d20 hit chart
	$this->hitChart = array(
		
		//PRIMARY
		0=> array( 
			9 => "Structure",
			11 => "Class-L Missile Rack",
			14 => "Scanner",
			17 => "Engine",
			18 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		//Forward
		1=> array(
			6 => "Thruster",
			8 => "Class-L Missile Rack",
			11 => "Improved Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Aft
		2=> array(
			8 => "Thruster",
			10 => "Improved Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Port
		3=> array(
			4 => "Thruster",
			6 => "Bomb Rack",
			8 => "Improved Gatling Railgun",
			11 => "Mass Driver",
			18 => "Structure",
			20 => "Primary",
		),
		//Starboard
		4=> array(
			4 => "Thruster",
			6 => "Bomb Rack",
			8 => "Improved Gatling Railgun",
			11 => "Mass Driver",
			18 => "Structure",
			20 => "Primary",
		),
	);

	    
	    
    }
}



?>
