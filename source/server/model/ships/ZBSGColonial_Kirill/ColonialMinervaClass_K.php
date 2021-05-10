<?php
class ColonialMinervaClass_K extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1500;
	$this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "ColonialMinervaClass_K";
        $this->imagePath = "img/ships/BSG/ColonialMinerva.png";
        $this->shipClass = "Minerva Battlestar";
        $this->shipSizeClass = 3;
		$this->canvasSize = 170; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 33;

        $this->fighters = array("normal"=>24, "superheavy"=>4);

//		$this->notes = "Primary users: Colonial Fleet";
//		$this->isd = 1948;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 21;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 6, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 12, 5));
        $this->addPrimarySystem(new ReloadRack(5, 9));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
        $this->addPrimarySystem(new EWNuclearTorpedo(4, 6, 3, 0, 360));
		$this->addPrimarySystem(new Bulkhead(0, 4));
        $this->addPrimarySystem(new Bulkhead(0, 4));
		$this->addPrimarySystem(new Bulkhead(0, 4));
		$this->addPrimarySystem(new Bulkhead(0, 4));
		$hyperdrive = new JumpEngine(4, 16, 6, 20);
		$hyperdrive->displayName = 'Phasing Drive';
		$this->addPrimarySystem($hyperdrive);
		
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new FlakArray(4, 10, 3, 210, 30));
		$this->addFrontSystem(new FlakArray(4, 10, 3, 330, 150));

        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new FlakArray(4, 10, 3, 150, 330));
		$this->addAftSystem(new FlakArray(4, 10, 3, 30, 210));
        $this->addAftSystem(new Railgun(4, 9, 6, 180, 30));
        $this->addAftSystem(new Railgun(4, 9, 6, 180, 30));
        $this->addAftSystem(new Railgun(4, 9, 6, 330, 180));
        $this->addAftSystem(new Railgun(4, 9, 6, 330, 180));
		$this->addAftSystem(new MedBlastCannon(3, 5, 2, 60, 300));
		$this->addAftSystem(new MedBlastCannon(3, 5, 2, 60, 300));
		$this->addAftSystem(new MedBlastCannon(3, 5, 2, 60, 300));
		$this->addAftSystem(new MedBlastCannon(3, 5, 2, 60, 300));

        $this->addLeftSystem(new Thruster(4, 9, 0, 3, 3));
        $this->addLeftSystem(new Thruster(4, 9, 0, 3, 3));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new FlakArray(3, 10, 3, 210, 330));
		$this->addLeftSystem(new FlakArray(3, 10, 3, 210, 330));
		$this->addLeftSystem(new FlakArray(3, 10, 3, 210, 330));
		$this->addLeftSystem(new FlakArray(3, 10, 3, 210, 330));
		$this->addLeftSystem(new Railgun(4, 9, 6, 300, 30));
		$this->addLeftSystem(new HvyBlastCannon(3, 6, 4, 300, 30));
		$this->addLeftSystem(new Hangar(4, 14));

        $this->addRightSystem(new Thruster(4, 9, 0, 3, 4));
        $this->addRightSystem(new Thruster(4, 9, 0, 3, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new FlakArray(3, 10, 3, 30, 150));
		$this->addRightSystem(new FlakArray(3, 10, 3, 30, 150));
		$this->addRightSystem(new FlakArray(3, 10, 3, 30, 150));
		$this->addRightSystem(new FlakArray(3, 10, 3, 30, 150));
		$this->addRightSystem(new Railgun(4, 9, 6, 330, 60));
		$this->addRightSystem(new HvyBlastCannon(3, 6, 4, 330, 60));
		$this->addRightSystem(new Hangar(4, 14));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 65));
        $this->addAftSystem(new Structure(3, 55));
        $this->addLeftSystem(new Structure(4, 85));
        $this->addRightSystem(new Structure(4, 85));
        $this->addPrimarySystem(new Structure(4, 60));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Class-S Missile Rack",
					9 => "Nuclear Torpedo",
					10 => "Reload Rack",
					12 => "Scanner",
					15 => "Engine",
					17 => "Reactor",
					19 => "Phasing Drive",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					8 => "Medium Blast Cannon",
					12 => "Flak Array",
					19 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					7 => "Flak Array",
					10 => "Medium Blast Cannon",
					13 => "Railgun",
					19 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Flak Array",
					8 => "Railgun",
					10 => "Heavy Blast Cannon",
					12 => "Hangar",
					19 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Flak Array",
					8 => "Railgun",
					10 => "Heavy Blast Cannon",
					12 => "Hangar",
					19 => "Structure",
					20 => "Primary",			),
		);
    }
}

?>
