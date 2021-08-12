<?php
class ColonialBerserkClassCarrier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 750;
	$this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialBerserkClassCarrier";
        $this->imagePath = "img/ships/BSG/ColonialBerserk.png";
        $this->shipClass = "Berserk Carrier";
        $this->shipSizeClass = 3;
		$this->canvasSize = 140; //img has 140px per side
//		$this->unlimited 
		$this->unofficial = true;

        $this->fighters = array("medium"=>30, "heavy"=>6, "superheavy"=>6);
		$this->customFighter = array("Python"=>6);

		$this->notes = "Primary users: Colonial Fleet";
		$this->isd = 1948;

        $this->notes = 'Python capable';
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 22, 0, 0));
        $this->addPrimarySystem(new CnC(5, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 9, 3, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 4));
        $this->addPrimarySystem(new ReloadRack(5, 9));
		$this->addPrimarySystem(new BSGFlakBattery(5, 6, 2, 0, 360));
//		$this->addPrimarySystem(new Bulkhead(0, 5));

        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
		$this->addFrontSystem(new Bulkhead(0, 5));
		$this->addFrontSystem(new Bulkhead(0, 5));
		$this->addFrontSystem(new RapidGatling(5, 4, 1, 300, 60));

        $this->addAftSystem(new Thruster(4, 15, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 15, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 15, 0, 4, 2));
        $this->addAftSystem(new Bulkhead(0, 5));
        $this->addAftSystem(new Bulkhead(0, 5));
        $this->addAftSystem(new RapidGatling(5, 4, 1, 120, 240));
        $hyperdrive = new JumpEngine(6, 30, 8, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addAftSystem($hyperdrive);

        $this->addLeftSystem(new Thruster(3, 20, 0, 3, 3));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
		$this->addLeftSystem(new RapidGatling(4, 4, 1, 180, 360));
		$this->addLeftSystem(new RapidGatling(4, 4, 1, 180, 360));
        $this->addLeftSystem(new RapidGatling(4, 4, 1, 180, 360));
		$this->addLeftSystem(new SMissileRack(4, 6, 0, 210, 330));
        $this->addLeftSystem(new SMissileRack(4, 6, 0, 210, 330));
        $this->addLeftSystem(new Hangar(4, 21));

        $this->addRightSystem(new Thruster(5, 20, 0, 3, 4));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
		$this->addRightSystem(new RapidGatling(4, 4, 1, 0, 180));
        $this->addRightSystem(new RapidGatling(4, 4, 1, 0, 180));
		$this->addRightSystem(new RapidGatling(4, 4, 1, 0, 180));
        $this->addRightSystem(new SMissileRack(4, 6, 0, 30, 150));
        $this->addRightSystem(new SMissileRack(4, 6, 0, 30, 150));
        $this->addRightSystem(new Hangar(4, 21));
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 70));
        $this->addAftSystem(new Structure(4, 65));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(5, 50));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Flak Battery",
					14 => "Scanner",
					15 => "Engine",
					17 => "Reload Rack",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Rapid Gatling Railgun",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
                    5 => "Thruster",
					7 => "Rapid Gatling Railgun",
                    10 => "HyperDrive",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Hangar",
					8 => "Rapid Gatling Railgun",
					10 => "Flak Battery",
                    12 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Hangar",
					8 => "Rapid Gatling Railgun",
					10 => "Flak Battery",
                    12 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>