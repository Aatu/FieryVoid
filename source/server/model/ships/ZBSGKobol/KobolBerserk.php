<?php
class KobolBerserk extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 850;
	$this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolBerserk";
        $this->imagePath = "img/ships/BSG/ColonialBerserk.png";
        $this->shipClass = "Berserk Carrier";
        $this->shipSizeClass = 3;
		$this->canvasSize = 140; //img has 140px per side
//		$this->unlimited 
		$this->unofficial = true;

	    $this->notes = 'May only boost sensors by 2.';

        $this->fighters = array("normal"=>12, "superheavy"=>2);

        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 5, 6));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 15));
		$hyperdrive = new JumpEngine(4, 12, 4, 20);
			$hyperdrive->displayName = 'FTL Drive';
			$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 300, 60));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new FlakCannon(3, 4, 2, 120, 240));

        $this->addLeftSystem(new Thruster(4, 9, 0, 3, 3));
        $this->addLeftSystem(new FlakCannon(3, 4, 2, 210, 330));
        $this->addLeftSystem(new FlakCannon(3, 4, 2, 210, 330));
        $this->addLeftSystem(new FlakCannon(3, 4, 2, 210, 330));
        $this->addLeftSystem(new FlakCannon(3, 4, 2, 210, 330));
        $this->addLeftSystem(new FlakCannon(3, 4, 2, 210, 330));
        $this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 330));
        $this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 330));
        $this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 330));

        $this->addRightSystem(new Thruster(4, 9, 0, 3, 4));
        $this->addRightSystem(new FlakCannon(3, 4, 2, 30, 150));
        $this->addRightSystem(new FlakCannon(3, 4, 2, 30, 150));
        $this->addRightSystem(new FlakCannon(3, 4, 2, 30, 150));
        $this->addRightSystem(new FlakCannon(3, 4, 2, 30, 150));
        $this->addRightSystem(new FlakCannon(3, 4, 2, 30, 150));
        $this->addRightSystem(new MedBlastCannon(4, 5, 2, 30, 150));
        $this->addRightSystem(new MedBlastCannon(4, 5, 2, 30, 150));
        $this->addRightSystem(new MedBlastCannon(4, 5, 2, 30, 150));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 30));
        $this->addLeftSystem(new Structure(4, 35));
        $this->addRightSystem(new Structure(4, 35));
        $this->addPrimarySystem(new Structure(3, 40));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "Scanner",
					12 => "Engine",
					14 => "Hangar",
					16 => "Reactor",
					18 => "FTL Drive",
					20 => "C&C",
			),
			1=> array(
					8 => "Thruster",
					10 => "Flak Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			2=> array(
                    8 => "Thruster",
					10 => "Flak Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					8 => "Flak Cannon",
                    11 => "Medium Blast Cannon",
					19 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					8 => "Flak Cannon",
                    11 => "Medium Blast Cannon",
					19 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>