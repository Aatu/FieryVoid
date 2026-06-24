<?php
class penitentGCAM extends SmallStarBaseFourSections{
		function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2800;
		$this->base = true;
		$this->smallBase = true;
        $this->faction = "Great Crusade Orieni Imperium";		
		$this->phpclass = "penitentGCAM";
		$this->shipClass = "Penitent Station (2249)";
		$this->imagePath = "img/ships/GCpenitent.png";
		$this->canvasSize = 280; 
		$this->fighters = array("light"=>24, "medium"=>24); 
		$this->shipSizeClass = 3; 
		$this->Enormous = true; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 22;
		$this->sideDefense = 22;
		$this->isd = 2249;

		$this->unofficial = true;

		/*replaced by TAGed versions!		
		$this->addPrimarySystem(new Structure(5, 150)); //needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(new Structure(5, 132)); 
		$this->addAftSystem(new Structure(5, 132));
		$this->addLeftSystem(new Structure(5, 132));
		$this->addRightSystem(new Structure(5, 132));
		*/
		$this->addPrimarySystem(new Structure( 5, 150));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(5, 132, 270,90));
		$this->addAftSystem(Structure::createAsOuter(5, 132, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(5, 132, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(5, 132, 0, 180));		
		
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(480); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 480); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_KK';//add enhancement options for other missiles - Class-KK               
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//By the Book Orieni should have access to missie types: KK, B, A, H, L, C

		/* replaced with proper two C&Cs!
        $this->addPrimarySystem(new ProtectedCnC(6, 42, 0, 0));
		*/
		$cnc = new CnC(5, 21, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 21, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);
		
		$this->addPrimarySystem(new Reactor(5, 44, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 28, 4, 8));
		$this->addPrimarySystem(new Scanner(5, 28, 4, 8));
		$this->addPrimarySystem(new Hangar(5, 14, 6));
		$this->addPrimarySystem(new Hangar(5, 14, 6));
        $this->addPrimarySystem(new HKControlNodeOrieni(5, 30, 4, 4));
    	$this->addPrimarySystem(new WarLance(5, 9, 6, 0, 360));
       	$this->addPrimarySystem(new WarLance(5, 9, 6, 0, 360));
       	$this->addPrimarySystem(new WarLance(5, 9, 6, 0, 360));
       	$this->addPrimarySystem(new WarLance(5, 9, 6, 0, 360));

   		$this->addFrontSystem(new HeavyGaussRifle(5, 12, 5, 270, 90));
   		$this->addFrontSystem(new HeavyGaussRifle(5, 12, 5, 270, 90));
		$this->addFrontSystem(new AmmoMissileRackB(5, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackB(5, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new ImpRapidGatling(5, 4, 2, 270, 90));
		$this->addFrontSystem(new ImpRapidGatling(5, 4, 2, 270, 90));
		$this->addFrontSystem(new ImpRapidGatling(5, 4, 2, 270, 90));
		$this->addFrontSystem(new ImpRapidGatling(5, 4, 2, 270, 90));
		$this->addFrontSystem(new ImpRapidGatling(5, 4, 2, 270, 90));
		$this->addFrontSystem(new ImpRapidGatling(5, 4, 2, 270, 90));
		//$this->addFrontSystem(new Hangar(5, 6));
		//$this->addFrontSystem(new CargoBay(5, 25));
		//$this->addFrontSystem(new SubReactorUniversal(5, 30, 0, 0));

			$hangar = new Hangar(5, 6, 6);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
			$cargoBay = new CargoBay(5, 25);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 30, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);

        $this->addAftSystem(new HeavyGaussRifle(5, 12, 5, 90, 270));
        $this->addAftSystem(new HeavyGaussRifle(5, 12, 5, 90, 270));
		$this->addAftSystem(new AmmoMissileRackB(5, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackB(5, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new ImpRapidGatling(5, 4, 2, 90, 270));
		$this->addAftSystem(new ImpRapidGatling(5, 4, 2, 90, 270));
		$this->addAftSystem(new ImpRapidGatling(5, 4, 2, 90, 270));
		$this->addAftSystem(new ImpRapidGatling(5, 4, 2, 90, 270));
		$this->addAftSystem(new ImpRapidGatling(5, 4, 2, 90, 270));
		$this->addAftSystem(new ImpRapidGatling(5, 4, 2, 90, 270));
		//$this->addAftSystem(new Hangar(5, 6));
		//$this->addAftSystem(new CargoBay(5, 25));
		//$this->addAftSystem(new SubReactorUniversal(5, 30, 0, 0));

			$hangar = new Hangar(5, 6, 6);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
			$cargoBay = new CargoBay(5, 25);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 30, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);

        $this->addLeftSystem(new HeavyGaussRifle(5, 12, 5, 180, 360));
        $this->addLeftSystem(new HeavyGaussRifle(5, 12, 5, 180, 360));
		$this->addLeftSystem(new AmmoMissileRackB(5, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new AmmoMissileRackB(5, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new ImpRapidGatling(5, 4, 2, 180, 360));
		$this->addLeftSystem(new ImpRapidGatling(5, 4, 2, 180, 360));
		$this->addLeftSystem(new ImpRapidGatling(5, 4, 2, 180, 360));
		$this->addLeftSystem(new ImpRapidGatling(5, 4, 2, 180, 360));
		$this->addLeftSystem(new ImpRapidGatling(5, 4, 2, 180, 360));
		$this->addLeftSystem(new ImpRapidGatling(5, 4, 2, 180, 360));
		//$this->addLeftSystem(new Hangar(5, 6));
		//$this->addLeftSystem(new CargoBay(5, 25));
		//$this->addLeftSystem(new SubReactorUniversal(5, 30, 0, 0));
			
			$hangar = new Hangar(5, 6, 6);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
			$cargoBay = new CargoBay(5, 25);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 30, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);

        $this->addRightSystem(new HeavyGaussRifle(5, 12, 5, 0, 180));
        $this->addRightSystem(new HeavyGaussRifle(5, 12, 5, 0, 180));
		$this->addRightSystem(new AmmoMissileRackB(5, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new AmmoMissileRackB(5, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new ImpRapidGatling(5, 4, 2, 0, 180));
		$this->addRightSystem(new ImpRapidGatling(5, 4, 2, 0, 180));
		$this->addRightSystem(new ImpRapidGatling(5, 4, 2, 0, 180));
		$this->addRightSystem(new ImpRapidGatling(5, 4, 2, 0, 180));
		$this->addRightSystem(new ImpRapidGatling(5, 4, 2, 0, 180));
		$this->addRightSystem(new ImpRapidGatling(5, 4, 2, 0, 180));
		//$this->addRightSystem(new Hangar(5, 6));
		//$this->addRightSystem(new CargoBay(5, 25));
		//$this->addRightSystem(new SubReactorUniversal(5, 30, 0, 0));
			
			$hangar = new Hangar(5, 6, 6);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
			$cargoBay = new CargoBay(5, 25);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(5, 30, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);

		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				11 => "War Lance",
				13 => "HK Control Node",
				16 => "Scanner",
				17 => "Hangar",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
			1=> array(
				2 => "TAG:Class-B Missile Rack",
				4 => "TAG:Heavy Gauss Rifle",
				7 => "TAG:Improved Gatling Railgun",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				11 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				2 => "TAG:Class-B Missile Rack",
				4 => "TAG:Heavy Gauss Rifle",
				7 => "TAG:Improved Gatling Railgun",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				11 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				2 => "TAG:Class-B Missile Rack",
				4 => "TAG:Heavy Gauss Rifle",
				7 => "TAG:Improved Gatling Railgun",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				11 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				2 => "TAG:Class-B Missile Rack",
				4 => "TAG:Heavy Gauss Rifle",
				7 => "TAG:Improved Gatling Railgun",
				9 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				11 => "TAG:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}
?>