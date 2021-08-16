<?php
class KobolArtemis extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1100;
	$this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolArtemis";
        $this->imagePath = "img/ships/BSG/ColonialArtemis.png";
        $this->shipClass = "Artemis Battlestar (Alpha prototype)";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side
		$this->unofficial = true;

	    $this->notes = 'May only boost sensors by 2.';

        $this->fighters = array("normal"=>24, "superheavy"=>4);

//		$this->notes = "Primary users: Colonial Fleet";
//		$this->isd = 1948;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 19;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new SWScanner(6, 18, 6, 6));
        $this->addPrimarySystem(new Engine(6, 24, 0, 12, 4));
        $this->addPrimarySystem(new ReloadRack(6, 9));
        $this->addPrimarySystem(new SMissileRack(5, 6, 0, 0, 360));
		$hyperdrive = new JumpEngine(5, 16, 6, 20);
			$hyperdrive->displayName = 'FTL Drive';
			$this->addPrimarySystem($hyperdrive);
		
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 4));
        $this->addFrontSystem(new Railgun(4, 9, 6, 330, 30));
        $this->addFrontSystem(new Railgun(4, 9, 6, 330, 30));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 270, 30));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 330, 90));

        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 3, 2));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new MedBlastCannon(4, 5, 2, 60, 300));
		$this->addAftSystem(new MedBlastCannon(4, 5, 2, 60, 300));
		$this->addAftSystem(new MedBlastCannon(4, 5, 2, 60, 300));
		$this->addAftSystem(new FlakCannon(3, 4, 2, 150, 270));
		$this->addAftSystem(new FlakCannon(3, 4, 2, 90, 210));

        $this->addLeftSystem(new Thruster(5, 9, 0, 4, 3));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new Bulkhead(0, 4));
        $this->addLeftSystem(new FlakCannon(5, 4, 2, 210, 330));
        $this->addLeftSystem(new FlakCannon(5, 4, 2, 210, 330));
        $this->addLeftSystem(new FlakCannon(5, 4, 2, 210, 330));
        $this->addLeftSystem(new FlakCannon(5, 4, 2, 210, 330));
		$this->addLeftSystem(new Hangar(6, 14));

        $this->addRightSystem(new Thruster(5, 9, 0, 4, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
        $this->addRightSystem(new FlakCannon(5, 4, 2, 30, 150));
        $this->addRightSystem(new FlakCannon(5, 4, 2, 30, 150));
        $this->addRightSystem(new FlakCannon(5, 4, 2, 30, 150));
        $this->addRightSystem(new FlakCannon(5, 4, 2, 30, 150));
		$this->addRightSystem(new Hangar(6, 14));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(6, 50));
        $this->addAftSystem(new Structure(4, 50));
        $this->addLeftSystem(new Structure(6, 80));
        $this->addRightSystem(new Structure(6, 80));
        $this->addPrimarySystem(new Structure(6, 60));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					7 => "Class-S Missile Rack",
					9 => "Reload Rack",
					12 => "Scanner",
					15 => "Engine",
					17 => "Reactor",
					19 => "FTL Drive",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Railgun",
					10 => "Medium Blast Cannon",
					12 => "Flak Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					10 => "Medium Blast Cannon",
					12 => "Flak Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					8 => "Flak Cannon",
					12 => "Hangar",
					19 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					8 => "Flak Cannon",
					12 => "Hangar",
					19 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
