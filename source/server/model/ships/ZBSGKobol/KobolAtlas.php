<?php
class KobolAtlas extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 750;
	$this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolAtlas";
        $this->imagePath = "img/ships/BSG/ColonialAtlas.png";
        $this->shipClass = "Atlas Carrier";
        $this->shipSizeClass = 3;
		$this->canvasSize = 170; //img has 200px per side
		$this->unofficial = true;

	    $this->notes = 'May only boost sensors by 2.';

        $this->fighters = array("normal"=>24, "superheavy"=>4);

        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 18, 0, 0));
        $this->addPrimarySystem(new SWScanner(5, 18, 5, 5));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 5));
		$this->addPrimarySystem(new Hangar(5, 32));
		$hyperdrive = new JumpEngine(4, 16, 6, 20);
			$hyperdrive->displayName = 'FTL Drive';
			$this->addPrimarySystem($hyperdrive);
		
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Railgun(4, 9, 6, 270, 30));
		$this->addFrontSystem(new Railgun(4, 9, 6, 330, 90));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 240, 120));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 240, 120));

        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 3, 2));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new Railgun(4, 9, 6, 150, 270));
		$this->addAftSystem(new Railgun(4, 9, 6, 90, 210));

        $this->addLeftSystem(new Thruster(4, 9, 0, 3, 3));
		$this->addLeftSystem(new Bulkhead(0, 4));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 180, 360));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 180, 360));
		$this->addLeftSystem(new FlakCannon(3, 4, 2, 150, 360));
		$this->addLeftSystem(new FlakCannon(3, 4, 2, 150, 360));

        $this->addRightSystem(new Thruster(4, 9, 0, 3, 4));
		$this->addRightSystem(new Bulkhead(0, 4));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 0, 180));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 0, 180));
		$this->addRightSystem(new FlakCannon(3, 4, 2, 0, 210));
		$this->addRightSystem(new FlakCannon(3, 4, 2, 0, 210));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 45));
        $this->addAftSystem(new Structure(4, 35));
        $this->addLeftSystem(new Structure(5, 30));
        $this->addRightSystem(new Structure(5, 30));
        $this->addPrimarySystem(new Structure(5, 50));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					10 => "Scanner",
					13 => "Engine",
					15 => "Hangar",
					17 => "Reactor",
					19 => "FTL Drive",
					20 => "C&C",
			),
			1=> array(
					6 => "Thruster",
					9 => "Railgun",
					11 => "Flak Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			2=> array(
					8 => "Thruster",
					11 => "Railgun",
					19 => "Structure",
					20 => "Primary",
			),
			3=> array(
					6 => "Thruster",
					8 => "Flak Cannon",
					11 => "Medium Blast Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			4=> array(
					6 => "Thruster",
					8 => "Flak Cannon",
					11 => "Medium Blast Cannon",
					19 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
