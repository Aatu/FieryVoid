<?php
class ColonialJupiterClass extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1500;
	$this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialJupiterClass";
        $this->imagePath = "img/ships/BSG/ColonialBattlestar.png";
        $this->shipClass = "Jupiter Class Battlestar";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->limited = 33;
		$this->unofficial = true;

        $this->fighters = array("normal"=>60);

		$this->notes = "Primary users: Colonial Fleet";
		$this->isd = 1948;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 20;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 6;
        $this->iniativebonus = -5;
        
        $this->addPrimarySystem(new Reactor(6, 40, 0, 0));
        $this->addPrimarySystem(new CnC(6, 30, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 3, 6));
        $this->addPrimarySystem(new Engine(5, 28, 0, 12, 5));
        $this->addPrimarySystem(new ReloadRack(5, 9));
		$this->addPrimarySystem(new BSGFlakBattery(5, 6, 2, 0, 360));
		$this->addPrimarySystem(new BSGFlakBattery(5, 6, 2, 0, 360));
		$this->addPrimarySystem(new Bulkhead(0, 6));
        $this->addPrimarySystem(new Bulkhead(0, 6));
		$this->addPrimarySystem(new Thruster(3, 24, 0, 4, 2));
		$hyperdrive = new JumpEngine(6, 30, 8, 20);
		$hyperdrive->displayName = 'Hyperdrive';
		$this->addPrimarySystem($hyperdrive);
   
        $this->addFrontSystem(new Thruster(4, 30, 0, 4, 1));
		$this->addFrontSystem(new Bulkhead(0, 6));
		$this->addFrontSystem(new Bulkhead(0, 6));
        $this->addFrontSystem(new BSGRailgun(5, 9, 6, 315, 45));
		$this->addFrontSystem(new BSGRailgun(5, 9, 6, 315, 45));
        $this->addFrontSystem(new LMissileRack(4, 6, 0, 270, 90));
        $this->addFrontSystem(new LMissileRack(4, 6, 0, 270, 90));

        $this->addAftSystem(new Thruster(2, 18, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 18, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 18, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 18, 0, 3, 2));
		$this->addAftSystem(new Bulkhead(0, 6));
		$this->addAftSystem(new Bulkhead(0, 6));
        $this->addAftSystem(new BSGRailgun(5, 9, 6, 135, 225));
		$this->addAftSystem(new BSGRailgun(5, 9, 6, 135, 225));
        $this->addAftSystem(new LMissileRack(4, 6, 0, 90, 270));
        $this->addAftSystem(new LMissileRack(4, 6, 0, 90, 270));

        $this->addLeftSystem(new Thruster(2, 18, 0, 3, 3));
        $this->addLeftSystem(new Thruster(2, 18, 0, 3, 3));
		$this->addLeftSystem(new Bulkhead(0, 6));
		$this->addLeftSystem(new Bulkhead(0, 6));
        $this->addLeftSystem(new BSGRailgun(5, 9, 6, 225, 315));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
        $this->addLeftSystem(new BSGFlakBattery(5, 6, 2, 210, 330));
		$this->addLeftSystem(new Hangar(5, 30));

        $this->addRightSystem(new Thruster(2, 18, 0, 3, 4));
        $this->addRightSystem(new Thruster(2, 18, 0, 3, 4));
		$this->addRightSystem(new Bulkhead(0, 6));
		$this->addRightSystem(new Bulkhead(0, 6));
        $this->addRightSystem(new BSGRailgun(5, 9, 6, 45, 135));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
        $this->addRightSystem(new BSGFlakBattery(5, 6, 2, 30, 150));
		$this->addRightSystem(new Hangar(5, 30));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 70));
        $this->addAftSystem(new Structure(3, 70));
        $this->addLeftSystem(new Structure(4, 90));
        $this->addRightSystem(new Structure(4, 90));
        $this->addPrimarySystem(new Structure(5, 60));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					10 => "Thruster",
					12 => "Hyperdrive",
					14 => "Scanner",
					15 => "Engine",
					16 => "Flak Battery",
					17 => "Reload Rack",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					3 => "Thruster",
					7 => "Railgun",
					9 => "Class-L Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					9 => "Railgun",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					6 => "Hangar",
					7 => "Railgun",
					10 => "Flak Battery",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					6 => "Hangar",
					7 => "Railgun",
					10 => "Flak Battery",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
