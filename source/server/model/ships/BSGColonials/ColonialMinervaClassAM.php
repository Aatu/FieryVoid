<?php
class ColonialMinervaClassAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1100;
	$this->faction = "BSG Colonials";
        $this->phpclass = "ColonialMinervaClassAM";
        $this->imagePath = "img/ships/BSG/ColonialMinerva.png";
        $this->shipClass = "Minerva Battlestar";
        $this->shipSizeClass = 3;
		$this->canvasSize = 170; //img has 170px per side
//		$this->limited
		$this->unofficial = true;

        $this->fighters = array("medium"=>12, "superheavy"=>2);

		$this->notes = "Primary users: Colonial Fleet";
		$this->isd = 1935;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(60); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 60); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
        
        $this->addPrimarySystem(new Reactor(5, 27, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 9, 3, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 4));
        $this->addPrimarySystem(new ReloadRack(5, 9));
		$hyperdrive = new JumpEngine(5, 30, 8, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);
   
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
		$this->addFrontSystem(new Bulkhead(0, 5));
        $this->addFrontSystem(new BSGMainBattery(5, 9, 6, 300, 360));
        $this->addFrontSystem(new BSGMainBattery(5, 9, 6, 300, 360));
        $this->addFrontSystem(new BSGMainBattery(5, 9, 6, 0, 60));
        $this->addFrontSystem(new BSGMainBattery(5, 9, 6, 0, 60));
        $this->addFrontSystem(new AmmoMissileRackL(5, 0, 0, 300, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
//		$this->addFrontSystem(new LMissileRack(5, 6, 0, 270, 360));
//        $this->addFrontSystem(new LMissileRack(5, 6, 0, 0, 90));

        $this->addAftSystem(new Thruster(4, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 15, 0, 3, 2));
        $this->addAftSystem(new Bulkhead(0, 5));
        $this->addAftSystem(new BSGFlakBattery(5, 6, 2, 120, 240));
		$this->addAftSystem(new BSGFlakBattery(5, 6, 2, 120, 240));
    
        $this->addLeftSystem(new Thruster(5, 20, 0, 4, 3));
        $this->addLeftSystem(new BSGMainBattery(5, 9, 6, 240, 300));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
        $this->addLeftSystem(new RapidGatling(5, 4, 1, 210, 330));
        $this->addLeftSystem(new RapidGatling(5, 4, 1, 210, 330));
//        $this->addLeftSystem(new SMissileRack(5, 6, 0, 210, 330));
        $this->addLeftSystem(new AmmoMissileRackS(5, 0, 0, 210, 330, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new Hangar(5, 7));

        $this->addRightSystem(new Thruster(5, 20, 0, 4, 4));
        $this->addRightSystem(new BSGMainBattery(5, 9, 6, 60, 120));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
        $this->addRightSystem(new RapidGatling(5, 4, 1, 30, 150));
        $this->addRightSystem(new RapidGatling(5, 4, 1, 30, 150));
//        $this->addRightSystem(new SMissileRack(5, 6, 0, 30, 150));
        $this->addRightSystem(new AmmoMissileRackS(5, 0, 0, 30, 150, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new Hangar(5, 7));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 50));
        $this->addRightSystem(new Structure(4, 50));
        $this->addPrimarySystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Hyperdrive",
					14 => "Scanner",
					16 => "Engine",
					18 => "Reload Rack",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Main Battery",
					9 => "Class-L Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
                    4 => "Thruster",
					7 => "Flak Battery",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					6 => "Hangar",
					7 => "Main Battery",
                    9 => "Rapid Gatling Railgun",
					10 => "Flak Battery",
                    12 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					6 => "Hangar",
					7 => "Main Battery",
                    9 => "Rapid Gatling Railgun",
					10 => "Flak Battery",
					12 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>