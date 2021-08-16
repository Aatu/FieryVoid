<?php
class KobolMercury extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 2400;
	$this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolMercury";
        $this->imagePath = "img/ships/BSG/ColonialMercury.png";
        $this->shipClass = "Mercury Battlestar (Beta prototype)";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 10;

	    $this->notes = 'May only boost sensors by 2.';

        $this->fighters = array("normal"=>72, "superheavy"=>12);

//		$this->notes = "Primary users: Colonial Fleet";
//		$this->isd = 1948;
        
        $this->forwardDefense = 20;
        $this->sideDefense = 25;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 6;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new CnC(6, 24, 0, 0));
        $this->addPrimarySystem(new SWScanner(6, 18, 6, 8));
        $this->addPrimarySystem(new Engine(5, 48, 0, 24, 5));
        $this->addPrimarySystem(new ReloadRack(5, 9));
        $this->addPrimarySystem(new SMissileRack(5, 6, 0, 0, 360));
        $this->addPrimarySystem(new SMissileRack(5, 6, 0, 0, 360));
        $this->addPrimarySystem(new EWNuclearTorpedo(5, 6, 3, 0, 360));
		$hyperdrive = new JumpEngine(5, 32, 6, 20);
			$hyperdrive->displayName = 'FTL Drive';
			$this->addPrimarySystem($hyperdrive);
		
        $this->addFrontSystem(new Thruster(6, 16, 0, 9, 1));
        $this->addFrontSystem(new Thruster(6, 16, 0, 9, 1));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new HeavyRailGun(5, 12, 9, 330, 30));
		$this->addFrontSystem(new HeavyRailGun(5, 12, 9, 330, 30));
//		$this->addFrontSystem(new HvyBlastCannon(5, 6, 4, 300, 30));
//		$this->addFrontSystem(new HvyBlastCannon(5, 6, 4, 330, 60));
        $this->addFrontSystem(new Railgun(5, 9, 6, 270, 30));
        $this->addFrontSystem(new Railgun(5, 9, 6, 270, 30));
        $this->addFrontSystem(new Railgun(5, 9, 6, 330, 90));
        $this->addFrontSystem(new Railgun(5, 9, 6, 330, 90));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new FlakArray(4, 10, 3, 210, 30));
		$this->addFrontSystem(new FlakArray(4, 10, 3, 210, 30));
		$this->addFrontSystem(new FlakArray(4, 10, 3, 330, 150));
		$this->addFrontSystem(new FlakArray(4, 10, 3, 330, 150));

        $this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new MedBlastCannon(4, 5, 2, 60, 300));
		$this->addAftSystem(new MedBlastCannon(4, 5, 2, 60, 300));
		$this->addAftSystem(new FlakArray(4, 10, 3, 150, 330));
		$this->addAftSystem(new FlakArray(4, 10, 3, 150, 330));
		$this->addAftSystem(new FlakArray(4, 10, 3, 30, 210));
		$this->addAftSystem(new FlakArray(4, 10, 3, 30, 210));

        $this->addLeftSystem(new Thruster(6, 12, 0, 5, 3));
        $this->addLeftSystem(new Thruster(6, 12, 0, 5, 3));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new FlakArray(4, 10, 3, 210, 330));
		$this->addLeftSystem(new FlakArray(4, 10, 3, 210, 330));
		$this->addLeftSystem(new FlakArray(4, 10, 3, 210, 330));
		$this->addLeftSystem(new FlakArray(4, 10, 3, 210, 330));
		$this->addLeftSystem(new HvyBlastCannon(5, 6, 4, 210, 360));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 360));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 360));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 180, 330));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 180, 330));
		$this->addLeftSystem(new HvyBlastCannon(5, 6, 4, 180, 330));
		$this->addLeftSystem(new Hangar(5, 21));
		$this->addLeftSystem(new Hangar(5, 21));

        $this->addRightSystem(new Thruster(6, 12, 0, 5, 4));
        $this->addRightSystem(new Thruster(6, 12, 0, 5, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new FlakArray(4, 10, 3, 30, 150));
		$this->addRightSystem(new FlakArray(4, 10, 3, 30, 150));
		$this->addRightSystem(new FlakArray(4, 10, 3, 30, 150));
		$this->addRightSystem(new FlakArray(4, 10, 3, 30, 150));
		$this->addRightSystem(new HvyBlastCannon(5, 6, 4, 0, 150));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 0, 150));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 0, 150));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 30, 180));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 30, 180));
		$this->addRightSystem(new HvyBlastCannon(5, 6, 4, 30, 180));
		$this->addRightSystem(new Hangar(5, 21));
		$this->addRightSystem(new Hangar(5, 21));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(7, 100));
        $this->addAftSystem(new Structure(6, 80));
        $this->addLeftSystem(new Structure(7, 120));
        $this->addRightSystem(new Structure(7, 120));
        $this->addPrimarySystem(new Structure(6, 100));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Class-S Missile Rack",
					9 => "Nuclear Torpedo",
					10 => "Reload Rack",
					12 => "Scanner",
					15 => "Engine",
					17 => "Reactor",
					19 => "FTL Drive",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Railgun",
					8 => "Heavy Railgun",
					11 => "Medium Blast Cannon",
					14 => "Flak Array",
					19 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Flak Array",
					11 => "Medium Blast Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					6 => "Flak Array",
					8 => "Heavy Blast Cannon",
					11 => "Medium Blast Cannon",
					14 => "Hangar",
					19 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					6 => "Flak Array",
					8 => "Heavy Blast Cannon",
					11 => "Medium Blast Cannon",
					14 => "Hangar",
					19 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
