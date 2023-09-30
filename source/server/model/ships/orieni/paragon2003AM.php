<?php
class Paragon2003AM extends BaseShip{
    /*Paragon Strike Force Command Ship, variant ISD 1782; WoCR*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 1050;
		$this->faction = "Orieni";
        $this->phpclass = "Paragon2003AM";
        $this->imagePath = "img/ships/prophet.png";
        $this->canvasSize = 280;
        $this->shipClass = "Paragon Strike Force Command Ship (2003)";
        $this->variantOf = "Prophet Command Ship";
	    $this->isd = 2003;
        $this->occurence = "rare";
        $this->limited = 33;
	    
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
        $ammoMagazine = new AmmoMagazine(72); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 72); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H       
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C
		//KK missiles are not present in FV however
		
		
        $this->addPrimarySystem(new Reactor(5, 42, 0, 0));
        $this->addPrimarySystem(new CnC(6, 30, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 30, 5, 8));
        $this->addPrimarySystem(new Engine(5, 30, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(5, 38, 36));
        $this->addPrimarySystem(new JumpEngine(5, 40, 6, 25));
        $this->addPrimarySystem(new HKControlNode(5, 24, 3, 3));
        $this->addPrimarySystem(new HeavyLaserLance(4, 6, 4, 0, 360));
        $this->addPrimarySystem(new HeavyLaserLance(4, 6, 4, 0, 360));
		$this->addPrimarySystem(new AmmoMissileRackSO(5, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
      
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new OrieniGatlingRG(1, 4, 1, 240, 120));
        $this->addFrontSystem(new HeavyGausscannon(3, 10, 4, 270, 90));
        $this->addFrontSystem(new HeavyGausscannon(3, 10, 4, 270, 90));
		$this->addFrontSystem(new AmmoMissileRackSO(5, 0, 0, 270, 90, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(5, 0, 0, 270, 90, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 2, 2));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new OrieniGatlingRG(1, 4, 1, 60, 300));
        $this->addAftSystem(new HeavyGausscannon(2, 10, 4, 90, 270));
        $this->addAftSystem(new HeavyGausscannon(2, 10, 4, 90, 270));
		$this->addAftSystem(new AmmoMissileRackSO(5, 0, 0, 90, 270, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addLeftSystem(new Thruster(4, 25, 0, 6, 3));
        $this->addLeftSystem(new HeavyLaserLance(3, 6, 4, 180, 360));
        $this->addLeftSystem(new HeavyLaserLance(3, 6, 4, 180, 360));
        $this->addLeftSystem(new HeavyGausscannon(3, 10, 4, 180, 360));
        $this->addLeftSystem(new HeavyGausscannon(3, 10, 4, 180, 360));
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
        $this->addLeftSystem(new OrieniGatlingRG(1, 4, 1, 180, 360));
		$this->addLeftSystem(new AmmoMissileRackSO(5, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addRightSystem(new Thruster(4, 25, 0, 6, 4));
        $this->addRightSystem(new HeavyLaserLance(3, 6, 4, 0, 180));
        $this->addRightSystem(new HeavyLaserLance(3, 6, 4, 0, 180));
        $this->addRightSystem(new HeavyGausscannon(3, 10, 4, 0, 180));
        $this->addRightSystem(new HeavyGausscannon(3, 10, 4, 0, 180));
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
        $this->addRightSystem(new OrieniGatlingRG(1, 4, 1, 0, 180));
		$this->addRightSystem(new AmmoMissileRackSO(5, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base


		//structures
        $this->addFrontSystem(new Structure(4, 60));
        $this->addAftSystem(new Structure(4, 60));
        $this->addLeftSystem(new Structure(4, 68));
        $this->addRightSystem(new Structure(4, 68));
        $this->addPrimarySystem(new Structure(5, 60));

	//d20 hit chart
	$this->hitChart = array(
		
		//PRIMARY
		0=> array( 
			6 => "Structure",
			7 => "Heavy Laser Lance",
			8 => "Class-SO Missile Rack",
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
			6 => "Class-SO Missile Rack",
			9 => "Heavy Gauss Cannon",
			11 => "Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Aft
		2=> array(
			5 => "Thruster",
			6 => "Class-SO Missile Rack",
			9 => "Heavy Gauss Cannon",
			11 => "Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Port
		3=> array(
			3 => "Thruster",
			5 => "Heavy Laser Lance",
			8 => "Heavy Gauss Cannon",
			9 => "Class-SO Missile Rack",
			11 => "Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
		//Starboard
		4=> array(
			3 => "Thruster",
			5 => "Heavy Laser Lance",
			8 => "Heavy Gauss Cannon",
			9 => "Class-SO Missile Rack",
			11 => "Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
	);


    }
}
?>
        