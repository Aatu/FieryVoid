<?php
class ColonialRiverEscortAM extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "BSG Colonials";
        $this->phpclass = "ColonialRiverEscortAM";
        $this->imagePath = "img/ships/BSG/ColonialRiver.png";
        $this->shipClass = "River Patrol Craft";
        $this->canvasSize = 100;
//	    $this->isd = 2007;
        
		$this->unofficial = true;

        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
         
        $this->addPrimarySystem(new Reactor(4, 10, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 8, 5, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 6, 3));
        $this->addPrimarySystem(new ReloadRack(4, 9));
		$this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 3, 4));        
		$this->addPrimarySystem(new BSGFlakBattery(4, 6, 2, 0, 360));
		$hyperdrive = new JumpEngine(4, 10, 6, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 3));
        $this->addFrontSystem(new Bulkhead(0, 3));
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 270, 90, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new BSGMedBattery(4, 7, 4, 300, 60));
		$this->addFrontSystem(new RapidGatling(3, 4, 1, 270, 90));

		$this->addAftSystem(new RapidGatling(3, 4, 1, 90, 270));
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 90, 270, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));    
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));    
		$this->addAftSystem(new Bulkhead(0, 2));
       
        $this->addPrimarySystem(new Structure(4, 45));

		//d20 hit chart
		$this->hitChart = array(
		
		0=> array(
			6 => "Thruster",
			8 => "Reload Rack",
            9 => "Flak Battery",
			11 => "Scanner",
			14 => "Engine",
			15 => "Hangar",
			17 => "Reactor",
			19 => "Hyperdrive",
			20 => "C&C",
		),
		1=> array(
			6 => "Thruster",
			9 => "Battery",
			10 => "Rapid Gatling Railgun",
            11 => "Class-S Missile Rack",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			7 => "Thruster",
			8 => "Rapid Gatling Railgun",
			10 => "Class-S Missile Rack",
			18 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>