<?php
class ColonialMercuryClass extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 2300;
	$this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialMercuryClass";
        $this->imagePath = "img/ships/BSG/ColonialMercury.png";
        $this->shipClass = "Mercury Battlestar";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->limited = 10;
		$this->unofficial = true;

	    $this->notes = 'May only boost sensors by 2.';
		$this->notes .= '<br>Primary users: Colonial Fleet';
        $this->notes .= '<br>Python capable';

        $this->fighters = array("medium"=>36, "heavy"=>12, "superheavy"=>6);
		$this->customFighter = array("Python"=>12);

		$this->isd = 1948;

//        $this->notes .= '<br>Provides +5 Initiative for all friendly Colonial units';
        
        $this->forwardDefense = 20;
        $this->sideDefense = 22;
        
        $this->turncost = 1.75;
        $this->turndelaycost = 1.75;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 6;
        $this->iniativebonus = -5;
        
        $this->addPrimarySystem(new Reactor(6, 45, 0, 0));
        $this->addPrimarySystem(new CnC(6, 35, 0, 0));
        $this->addPrimarySystem(new SWScanner(6, 18, 3, 6));
        $this->addPrimarySystem(new Engine(6, 32, 0, 12, 5));
        $this->addPrimarySystem(new ReloadRack(5, 9));
		$this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
		$this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
		$this->addPrimarySystem(new BSGMedBattery(5, 7, 4, 0, 360));
		$hyperdrive = new JumpEngine(6, 30, 8, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Thruster(6, 30, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 30, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 6));
		$this->addFrontSystem(new Bulkhead(0, 6));
		$this->addFrontSystem(new BSGMainBattery(6, 9, 6, 315, 45));
		$this->addFrontSystem(new BSGMainBattery(6, 9, 6, 270, 360));
        $this->addFrontSystem(new BSGMainBattery(6, 9, 6, 0, 90));
        $this->addFrontSystem(new BSGMedBattery(6, 7, 4, 270, 360));
        $this->addFrontSystem(new BSGMedBattery(6, 7, 4, 0, 90));
        $this->addFrontSystem(new LMissileRack(6, 6, 0, 315, 45));
 //       $this->addPrimarySystem(new EWNuclearTorpedo(5, 6, 3, 315, 45));

        $this->addAftSystem(new Thruster(6, 18, 0, 3, 2));
        $this->addAftSystem(new Thruster(6, 18, 0, 3, 2));
        $this->addAftSystem(new Thruster(6, 18, 0, 3, 2));
        $this->addAftSystem(new Thruster(6, 18, 0, 3, 2));
		$this->addAftSystem(new Bulkhead(0, 6));
		$this->addAftSystem(new Bulkhead(0, 6));
		$this->addAftSystem(new BSGMainBattery(6, 9, 6, 180, 270));
        $this->addAftSystem(new BSGMainBattery(6, 9, 6, 90, 180));
        $this->addAftSystem(new LMissileRack(6, 6, 0, 135, 225));
		$this->addAftSystem(new BSGMedBattery(6, 7, 4, 135, 225));
		$this->addAftSystem(new BSGMedBattery(6, 7, 4, 135, 225));

        $this->addLeftSystem(new Thruster(6, 18, 0, 3, 3));
        $this->addLeftSystem(new Thruster(6, 18, 0, 3, 3));
		$this->addLeftSystem(new Bulkhead(0, 6));
		$this->addLeftSystem(new Bulkhead(0, 6));
        $this->addLeftSystem(new BSGMainBattery(6, 9, 6, 205, 335));
		$this->addLeftSystem(new BSGMedBattery(6, 7, 4, 190, 350));
		$this->addLeftSystem(new BSGMedBattery(6, 7, 4, 290, 350));
        $this->addLeftSystem(new BSGFlakBattery(6, 6, 2, 190, 350));
        $this->addLeftSystem(new BSGFlakBattery(6, 6, 2, 190, 350));
        $this->addLeftSystem(new BSGFlakBattery(6, 6, 2, 190, 350));
		$this->addLeftSystem(new RapidGatling(6, 4, 1, 190, 350));
		$this->addLeftSystem(new RapidGatling(6, 4, 1, 190, 350));
		$this->addLeftSystem(new RapidGatling(6, 4, 1, 190, 350));
		$this->addLeftSystem(new Hangar(6, 18));

        $this->addRightSystem(new Thruster(6, 18, 0, 3, 4));
        $this->addRightSystem(new Thruster(6, 18, 0, 3, 4));
		$this->addRightSystem(new Bulkhead(0, 6));
		$this->addRightSystem(new Bulkhead(0, 6));
        $this->addRightSystem(new BSGMainBattery(6, 9, 6, 25, 155));
		$this->addRightSystem(new BSGMedBattery(6, 7, 4, 10, 170));
		$this->addRightSystem(new BSGMedBattery(6, 7, 4, 10, 170));
        $this->addRightSystem(new BSGFlakBattery(6, 6, 2, 10, 170));
        $this->addRightSystem(new BSGFlakBattery(6, 6, 2, 10, 170));
        $this->addRightSystem(new BSGFlakBattery(6, 6, 2, 10, 170));
		$this->addRightSystem(new RapidGatling(6, 4, 1, 10, 170));
		$this->addRightSystem(new RapidGatling(6, 4, 1, 10, 170));
		$this->addRightSystem(new RapidGatling(6, 4, 1, 10, 170));
		$this->addRightSystem(new Hangar(6, 18));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 100));
        $this->addAftSystem(new Structure(3, 80));
        $this->addLeftSystem(new Structure(4, 100));
        $this->addRightSystem(new Structure(4, 100));
        $this->addPrimarySystem(new Structure(5, 80));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					9 => "Rapid Gatling Railgun",
					11 => "Hyperdrive",
					13 => "Scanner",
					15 => "Engine",
					16 => "Battery",
					17 => "Reload Rack",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Main Battery",
					9 => "Class-L Missile Rack",
                    12 => "Battery",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Main Battery",
                    9 => "Class-L Missile Rack",
                    11 => "Battery",
					18 => "Structure",
					20 => "Primary",
			),

			3=> array(
					3 => "Thruster",
					6 => "Hangar",
					8 => "Main Battery",
					10 => "Battery",
                    12 => "Rapid Gatling Railgun",
					14 => "Flak Battery",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					6 => "Hangar",
					8 => "Main Battery",
					10 => "Battery",
                    12 => "Rapid Gatling Railgun",
					14 => "Flak Battery",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>