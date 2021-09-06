<?php
class ColonialFreya extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 975;
	$this->faction = "ZBSG Colonials";
        $this->phpclass = "ColonialFreya";
        $this->imagePath = "img/ships/BSG/ColonialOdin2.png";
        $this->shipClass = "Freya Missile Battlestar";
			$this->occurence = "rare";
			$this->variantOf = "Odin Battlestar";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 140px per side
//		$this->unlimited 
		$this->unofficial = true;

        $this->fighters = array("medium"=>18);

		$this->notes = "Primary users: Colonial Fleet";
		$this->isd = 1965;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
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
   
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
		$this->addFrontSystem(new Bulkhead(0, 5));
		$this->addFrontSystem(new RapidGatling(5, 4, 1, 300, 60));
		$this->addFrontSystem(new RapidGatling(5, 4, 1, 300, 60));
		$this->addFrontSystem(new LMissileRack(5, 6, 0, 330, 30));
		$this->addFrontSystem(new SMissileRack(5, 6, 0, 300, 360));
		$this->addFrontSystem(new SMissileRack(5, 6, 0, 330, 30));
		$this->addFrontSystem(new SMissileRack(5, 6, 0, 0, 60));

        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 3, 2));
        $this->addAftSystem(new Bulkhead(0, 5));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 120, 240));
		$this->addAftSystem(new RapidGatling(5, 4, 1, 120, 240));
		$this->addAftSystem(new LMissileRack(4, 6, 0, 120, 240));
		$this->addAftSystem(new SMissileRack(4, 6, 0, 120, 240));
		$this->addAftSystem(new SMissileRack(4, 6, 0, 90, 270));
        $hyperdrive = new JumpEngine(6, 30, 8, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addAftSystem($hyperdrive);

        $this->addLeftSystem(new Thruster(4, 20, 0, 4, 3));
        $this->addLeftSystem(new Bulkhead(0, 6));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 210, 330));
		$this->addLeftSystem(new RapidGatling(5, 4, 1, 210, 330));
		$this->addLeftSystem(new LMissileRack(5, 6, 0, 210, 330));
		$this->addLeftSystem(new SMissileRack(5, 6, 0, 210, 330));
		$this->addLeftSystem(new SMissileRack(5, 6, 0, 210, 330));
        $this->addLeftSystem(new Hangar(5, 10));

        $this->addRightSystem(new Thruster(4, 20, 0, 4, 4));
        $this->addRightSystem(new Bulkhead(0, 6));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 30, 150));
		$this->addRightSystem(new RapidGatling(5, 4, 1, 30, 150));
        $this->addRightSystem(new LMissileRack(5, 6, 0, 30, 150));
        $this->addRightSystem(new SMissileRack(5, 6, 0, 30, 150));
        $this->addRightSystem(new SMissileRack(5, 6, 0, 30, 150));
        $this->addRightSystem(new Hangar(5, 10));
       
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(6, 50));
        $this->addAftSystem(new Structure(5, 42));
        $this->addLeftSystem(new Structure(5, 50));
        $this->addRightSystem(new Structure(5, 50));
        $this->addPrimarySystem(new Structure(5, 40));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Flak Battery",
					14 => "Scanner",
					16 => "Engine",
					17 => "Reload Rack",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					7 => "Rapid Gatling Railgun",
					9 => "Class-L Missile Rack",
					11 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
                    5 => "Thruster",
					7 => "Rapid Gatling Railgun",
                    9 => "Class-S Missile Rack",
					11 => "Class-L Missile Rack",
                    13 => "HyperDrive",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Hangar",
					8 => "Class-L Missile Rack",
					9 => "Rapid Gatling Railgun",
                    13 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Hangar",
					8 => "Class-L Missile Rack",
					9 => "Rapid Gatling Railgun",
                    13 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
