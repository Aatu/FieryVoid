<?php
class ColonialOdinClass extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1000;
	$this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialOdinClass";
        $this->imagePath = "img/ships/BSG/ColonialOdin2.png";
        $this->shipClass = "Odin Battlestar";
        $this->shipSizeClass = 3;
		$this->canvasSize = 140; //img has 140px per side
//		$this->unlimited 
		$this->unofficial = true;

        $this->fighters = array("medium"=>18, "superheavy"=>2);

		$this->notes = "Primary users: Colonial Fleet";
		$this->isd = 1948;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
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
        $this->addFrontSystem(new BSGMainBattery(5, 9, 6, 315, 45));
		$this->addFrontSystem(new LMissileRack(4, 6, 0, 270, 90));

        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Bulkhead(0, 5));
        $this->addAftSystem(new BSGMainBattery(5, 9, 6, 135, 225));
        $this->addAftSystem(new BSGFlakBattery(5, 6, 2, 120, 240));
		$this->addAftSystem(new BSGFlakBattery(5, 6, 2, 120, 240));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 120, 240));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 120, 240));
		$this->addAftSystem(new LMissileRack(4, 6, 0, 90, 270));
        $hyperdrive = new JumpEngine(6, 30, 8, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addAftSystem($hyperdrive);

        $this->addLeftSystem(new Thruster(5, 20, 0, 3, 3));
        $this->addLeftSystem(new Bulkhead(0, 6));
        $this->addLeftSystem(new BSGMainBattery(5, 9, 6, 235, 305));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 210, 330));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 210, 330));
		$this->addLeftSystem(new SMissileRack(5, 6, 0, 210, 330));
        $this->addLeftSystem(new SMissileRack(5, 6, 0, 210, 330));
        $this->addLeftSystem(new Hangar(5, 10));

        $this->addRightSystem(new Thruster(5, 20, 0, 3, 4));
        $this->addRightSystem(new Bulkhead(0, 6));
        $this->addRightSystem(new BSGMainBattery(5, 9, 6, 55, 125));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 30, 150));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 30, 150));
        $this->addRightSystem(new SMissileRack(5, 6, 0, 30, 150));
        $this->addRightSystem(new SMissileRack(5, 6, 0, 30, 150));
        $this->addRightSystem(new Hangar(5, 10));
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 50));
        $this->addAftSystem(new Structure(4, 42));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(5, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
                    12 => "Rapid Gatling Railgun",
					13 => "Flak Battery",
					14 => "Scanner",
					15 => "Engine",
					17 => "Reload Rack",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Main Battery",
					9 => "Class-L Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
                    5 => "Thruster",
					6 => "Rapid Gatling Railgun",
					7 => "Flak Battery",
                    8 => "Class-L Missile Rack",
					10 => "Main Battery",
                    12 => "HyperDrive",
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
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
