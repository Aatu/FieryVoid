<?php
class penitent2003AM extends SmallStarBaseFourSections{
		function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2060;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Orieni Imperium (defenses)";
		$this->phpclass = "penitent2003AM";
		$this->shipClass = "Penitent Station (2003)";
			$this->variantOf = "Penitent Station";
			$this->occurence = "common";
		$this->imagePath = "img/ships/penitent.png";
		$this->canvasSize = 280; 
		$this->fighters = array("light"=>24, "medium"=>24); 
		$this->shipSizeClass = 3; 
		$this->Enormous = true; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 22;
		$this->sideDefense = 22;
		$this->isd = 2003;
		
		$this->addFrontSystem(new Structure(5, 132));
		$this->addAftSystem(new Structure(5, 132));
		$this->addLeftSystem(new Structure(5, 132));
		$this->addRightSystem(new Structure(5, 132));
		$this->addPrimarySystem(new Structure(5, 150));
		
		
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(96); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 96); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_KK';//add enhancement options for other missiles - Class-KK               
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C
		
		
		$this->addPrimarySystem(new Reactor(5, 44, 0, 0));
        $this->addPrimarySystem(new ProtectedCnC(6, 42, 0, 0));
        //$this->addPrimarySystem(new CnC(5, 21, 0, 0)); //2 C&Cs do not work in FV - merging into one larger and better protected system!
		$this->addPrimarySystem(new Scanner(5, 28, 4, 7));
		$this->addPrimarySystem(new Scanner(5, 28, 4, 7));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new Hangar(5, 14));
        $this->addPrimarySystem(new HKControlNode(5, 30, 4, 4));
    	$this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));
       	$this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));
       	$this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));
       	$this->addPrimarySystem(new HeavyLaserLance(5, 6, 4, 0, 360));

   		$this->addFrontSystem(new HeavyGausscannon(5, 10, 4, 270, 90));
   		$this->addFrontSystem(new HeavyGausscannon(5, 10, 4, 270, 90));
		$this->addFrontSystem(new AmmoMissileRackSO(5, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackSO(5, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new OrieniGatlingRG(5, 4, 1, 270, 90));
		$this->addFrontSystem(new OrieniGatlingRG(5, 4, 1, 270, 90));
		$this->addFrontSystem(new OrieniGatlingRG(5, 4, 1, 270, 90));
		$this->addFrontSystem(new OrieniGatlingRG(5, 4, 1, 270, 90));
		$this->addFrontSystem(new OrieniGatlingRG(5, 4, 1, 270, 90));
		$this->addFrontSystem(new OrieniGatlingRG(5, 4, 1, 270, 90));
		$this->addFrontSystem(new Hangar(5, 6));
		$this->addFrontSystem(new CargoBay(5, 25));
		$this->addFrontSystem(new SubReactorUniversal(5, 30, 0, 0));

        $this->addAftSystem(new HeavyGausscannon(5, 10, 4, 90, 270));
        $this->addAftSystem(new HeavyGausscannon(5, 10, 4, 90, 270));
		$this->addAftSystem(new AmmoMissileRackSO(5, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackSO(5, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new OrieniGatlingRG(5, 4, 1, 90, 270));
		$this->addAftSystem(new OrieniGatlingRG(5, 4, 1, 90, 270));
		$this->addAftSystem(new OrieniGatlingRG(5, 4, 1, 90, 270));
		$this->addAftSystem(new OrieniGatlingRG(5, 4, 1, 90, 270));
		$this->addAftSystem(new OrieniGatlingRG(5, 4, 1, 90, 270));
		$this->addAftSystem(new OrieniGatlingRG(5, 4, 1, 90, 270));
		$this->addAftSystem(new Hangar(5, 6));
		$this->addAftSystem(new CargoBay(5, 25));
		$this->addAftSystem(new SubReactorUniversal(5, 30, 0, 0));

        $this->addLeftSystem(new HeavyGausscannon(5, 10, 4, 180, 360));
        $this->addLeftSystem(new HeavyGausscannon(5, 10, 4, 180, 360));
		$this->addLeftSystem(new AmmoMissileRackSO(5, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new AmmoMissileRackSO(5, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new OrieniGatlingRG(5, 4, 1, 180, 360));
		$this->addLeftSystem(new OrieniGatlingRG(5, 4, 1, 180, 360));
		$this->addLeftSystem(new OrieniGatlingRG(5, 4, 1, 180, 360));
		$this->addLeftSystem(new OrieniGatlingRG(5, 4, 1, 180, 360));
		$this->addLeftSystem(new OrieniGatlingRG(5, 4, 1, 180, 360));
		$this->addLeftSystem(new OrieniGatlingRG(5, 4, 1, 180, 360));
		$this->addLeftSystem(new Hangar(5, 6));
		$this->addLeftSystem(new CargoBay(5, 25));
		$this->addLeftSystem(new SubReactorUniversal(5, 30, 0, 0));

        $this->addRightSystem(new HeavyGausscannon(5, 10, 4, 0, 180));
        $this->addRightSystem(new HeavyGausscannon(5, 10, 4, 0, 180));
		$this->addRightSystem(new AmmoMissileRackSO(5, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new AmmoMissileRackSO(5, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new OrieniGatlingRG(5, 4, 1, 0, 180));
		$this->addRightSystem(new OrieniGatlingRG(5, 4, 1, 0, 180));
		$this->addRightSystem(new OrieniGatlingRG(5, 4, 1, 0, 180));
		$this->addRightSystem(new OrieniGatlingRG(5, 4, 1, 0, 180));
		$this->addRightSystem(new OrieniGatlingRG(5, 4, 1, 0, 180));
		$this->addRightSystem(new OrieniGatlingRG(5, 4, 1, 0, 180));
		$this->addRightSystem(new Hangar(5, 6));
		$this->addRightSystem(new CargoBay(5, 25));
		$this->addRightSystem(new SubReactorUniversal(5, 30, 0, 0));

		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				11 => "Heavy Laser Lance",
				13 => "HK Control Node",
				16 => "Scanner",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				2 => "Class-SO Missile Rack",
				4 => "Heavy Gauss Cannon",
				7 => "Gatling Railgun",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "Class-SO Missile Rack",
				4 => "Heavy Gauss Cannon",
				7 => "Gatling Railgun",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "Class-SO Missile Rack",
				4 => "Heavy Gauss Cannon",
				7 => "Gatling Railgun",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "Class-SO Missile Rack",
				4 => "Heavy Gauss Cannon",
				7 => "Gatling Railgun",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}
?>