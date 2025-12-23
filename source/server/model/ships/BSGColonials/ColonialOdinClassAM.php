<?php
class ColonialOdinClassAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 925;
	$this->faction = "BSG Colonials";
        $this->phpclass = "ColonialOdinClassAM";
        $this->imagePath = "img/ships/BSG/ColonialOdin2.png";
        $this->shipClass = "Odin Battlestar";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 160px per side
//		$this->unlimited 
		$this->unofficial = true;

        $this->fighters = array("medium"=>18);

		$this->notes = "Primary users: Colonial Fleet";
		$this->isd = 1948;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(80); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 80); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
        
        $this->addPrimarySystem(new Reactor(6, 27, 0, 0));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 9, 3, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 4));
        $this->addPrimarySystem(new ReloadRack(5, 9));
		$this->addPrimarySystem(new BSGFlakBattery(5, 6, 2, 0, 360));
        $this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
//		$this->addPrimarySystem(new Bulkhead(0, 6));
   
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
		$this->addFrontSystem(new Bulkhead(0, 5));
		$this->addFrontSystem(new Bulkhead(0, 5));
        $this->addFrontSystem(new BSGMainBattery(5, 9, 6, 330, 30));
		$this->addFrontSystem(new BSGMedBattery(5, 7, 4, 300, 360)); 
		$this->addFrontSystem(new BSGMedBattery(5, 7, 4, 0, 60)); 
        $this->addFrontSystem(new AmmoMissileRackS(4, 0, 0, 270, 90, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Bulkhead(0, 5));
        $this->addAftSystem(new BSGMainBattery(5, 9, 6, 150, 210));
		$this->addAftSystem(new BSGMedBattery(5, 7, 4, 150, 210)); 
        $this->addAftSystem(new BSGFlakBattery(5, 6, 2, 120, 240));
		$this->addAftSystem(new BSGFlakBattery(5, 6, 2, 120, 240));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 120, 240));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 120, 240));
        $this->addAftSystem(new AmmoMissileRackS(4, 0, 0, 90, 270, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $hyperdrive = new JumpEngine(6, 30, 8, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addAftSystem($hyperdrive);

        $this->addLeftSystem(new Thruster(4, 20, 0, 4, 3));
        $this->addLeftSystem(new Bulkhead(0, 6));
        $this->addLeftSystem(new BSGMainBattery(5, 9, 6, 210, 330));
        $this->addLeftSystem(new BSGMedBattery(5, 7, 4, 210, 330));
//        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 210, 330));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 210, 330));
        $this->addLeftSystem(new AmmoMissileRackS(5, 0, 0, 210, 330, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new Hangar(5, 10));

        $this->addRightSystem(new Thruster(4, 20, 0, 4, 4));
        $this->addRightSystem(new Bulkhead(0, 6));
        $this->addRightSystem(new BSGMainBattery(5, 9, 6, 30, 150));
        $this->addRightSystem(new BSGMedBattery(5, 7, 4, 30, 150));
//        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 30, 150));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 30, 150));
        $this->addRightSystem(new AmmoMissileRackS(5, 0, 0, 30, 150, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new Hangar(5, 10));
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(6, 50));
        $this->addAftSystem(new Structure(5, 42));
        $this->addLeftSystem(new Structure(5, 60));
        $this->addRightSystem(new Structure(5, 60));
        $this->addPrimarySystem(new Structure(5, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
                    11 => "Rapid Gatling Railgun",
					12 => "Flak Battery",
					14 => "Scanner",
					16 => "Engine",
					17 => "Reload Rack",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Main Battery",
					9 => "Battery",
					11 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
                    5 => "Thruster",
					6 => "Rapid Gatling Railgun",
					7 => "Flak Battery",
                    8 => "Class-S Missile Rack",
					10 => "Main Battery",
					12 => "Battery",
                    14 => "HyperDrive",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Hangar",
					8 => "Main Battery",
					9 => "Rapid Gatling Railgun",
					10 => "Flak Battery",
                    12 => "Class-S Missile Rack",
					13 => "Battery",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Hangar",
					8 => "Main Battery",
					9 => "Rapid Gatling Railgun",
					10 => "Flak Battery",
                    12 => "Class-S Missile Rack",
					13 => "Battery",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
