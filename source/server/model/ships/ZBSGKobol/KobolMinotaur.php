<?php
class KobolMinotaur extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1300;
	$this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolMinotaur";
        $this->imagePath = "img/ships/BSG/ColonialMinotaur.png";
        $this->shipClass = "Minotaur Gunship";
        $this->shipSizeClass = 3;
		$this->canvasSize = 200; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 33;

	    $this->notes = 'May only boost sensors by 2.';

        $this->forwardDefense = 15;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(6, 20, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new SWScanner(6, 18, 6, 6));
        $this->addPrimarySystem(new Engine(6, 24, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(6, 2));
		
        $this->addFrontSystem(new Thruster(6, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 9, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new HeavyRailGun(5, 12, 9, 330, 30));
		$this->addFrontSystem(new HeavyRailGun(5, 12, 9, 330, 30));
		$this->addFrontSystem(new HvyBlastCannon(5, 6, 4, 270, 30));
		$this->addFrontSystem(new HvyBlastCannon(5, 6, 4, 330, 90));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 210, 360));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 0, 150));

        $this->addAftSystem(new Thruster(5, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(5, 10, 0, 2, 2));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new FlakCannon(3, 4, 2, 180, 330));
		$this->addAftSystem(new FlakCannon(3, 4, 2, 30, 180));
		$hyperdrive = new JumpEngine(5, 32, 6, 20);
			$hyperdrive->displayName = 'FTL Drive';
			$this->addAftSystem($hyperdrive);

        $this->addLeftSystem(new Thruster(6, 9, 0, 3, 3));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new FlakCannon(3, 4, 2, 210, 330));
		$this->addLeftSystem(new FlakCannon(3, 4, 2, 210, 330));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 330));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 330));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 330));
		$this->addLeftSystem(new LtBlastCannon(4, 4, 1, 210, 330));
		$this->addLeftSystem(new LtBlastCannon(4, 4, 1, 210, 330));
		$this->addLeftSystem(new LtBlastCannon(4, 4, 1, 210, 330));
		$this->addLeftSystem(new LtBlastCannon(4, 4, 1, 210, 330));
		$this->addLeftSystem(new LtBlastCannon(4, 4, 1, 210, 330));
		$this->addLeftSystem(new LtBlastCannon(4, 4, 1, 210, 330));

        $this->addRightSystem(new Thruster(6, 9, 0, 3, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new FlakCannon(3, 4, 2, 30, 150));
		$this->addRightSystem(new FlakCannon(3, 4, 2, 30, 150));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 30, 150));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 30, 150));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 30, 150));
		$this->addRightSystem(new LtBlastCannon(4, 4, 1, 30, 150));
		$this->addRightSystem(new LtBlastCannon(4, 4, 1, 30, 150));
		$this->addRightSystem(new LtBlastCannon(4, 4, 1, 30, 150));
		$this->addRightSystem(new LtBlastCannon(4, 4, 1, 30, 150));
		$this->addRightSystem(new LtBlastCannon(4, 4, 1, 30, 150));
		$this->addRightSystem(new LtBlastCannon(4, 4, 1, 30, 150));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(7, 80));
        $this->addAftSystem(new Structure(5, 45));
        $this->addLeftSystem(new Structure(7, 60));
        $this->addRightSystem(new Structure(7, 60));
        $this->addPrimarySystem(new Structure(6, 45));
		
		$this->hitChart = array(
			0=> array(
					11 => "Structure",
					13 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Heavy Railgun",
					8 => "Heavy Blast Cannon",
					10 => "Flak Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			2=> array(
					8 => "Thruster",
					11 => "Flak Cannon",
					13 => "FTL Drive",
					19 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					6 => "Light Blast Cannon",
					9 => "Medium Blast Cannon",
					11 => "Flak Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					6 => "Light Blast Cannon",
					9 => "Medium Blast Cannon",
					11 => "Flak Cannon",
					19 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
