<?php
class KobolValkyrie extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 900;
        $this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolValkyrie";
        $this->imagePath = "img/ships/BSG/ColonialOdin2.png";
        $this->shipClass = "Valkyrie Battlestar";
        $this->fighters = array("normal" => 12, "superheavy" => 2);
 //       $this->isd = 2238;
        $this->canvasSize = 145;

		$this->unofficial = true;
	    $this->notes = 'May only boost sensors by 2.';
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new SWScanner(5, 9, 5, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
        $this->addPrimarySystem(new ReloadRack(5, 9));
        $this->addPrimarySystem(new SMissileRack(5, 6, 0, 0, 360));
        $this->addPrimarySystem(new SMissileRack(5, 6, 0, 0, 360));
		$hyperdrive = new JumpEngine(4, 16, 6, 20);
			$hyperdrive->displayName = 'FTL Drive';
			$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Railgun(4, 9, 6, 330, 30));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
        $this->addFrontSystem(new LightRailGun(4, 6, 3, 300, 60));        
        $this->addFrontSystem(new LightRailGun(4, 6, 3, 300, 60));     
		$this->addFrontSystem(new FlakCannon(4, 4, 2, 210, 30));
		$this->addFrontSystem(new FlakCannon(4, 4, 2, 330, 150));
		$this->addFrontSystem(new Bulkhead(0, 4));
        $this->addFrontSystem(new Bulkhead(0, 4));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));

        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new MedBlastCannon(4, 5, 2, 180, 330));
		$this->addAftSystem(new MedBlastCannon(4, 5, 2, 30, 180));
		$this->addAftSystem(new FlakCannon(4, 4, 2, 60, 300));

		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 360));
		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 360));
		$this->addLeftSystem(new FlakCannon(4, 4, 2, 210, 330));
		$this->addLeftSystem(new FlakCannon(4, 4, 2, 210, 330));
		$this->addLeftSystem(new Bulkhead(0, 4));
        $this->addLeftSystem(new Bulkhead(0, 4));
        $this->addLeftSystem(new Thruster(4, 9, 0, 3, 3));
		$this->addLeftSystem(new Hangar(5, 8));

		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 0, 150));
		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 0, 150));
		$this->addRightSystem(new FlakCannon(4, 4, 2, 30, 150));
		$this->addRightSystem(new FlakCannon(4, 4, 2, 30, 150));
		$this->addRightSystem(new Bulkhead(0, 4));
        $this->addRightSystem(new Bulkhead(0, 4));
        $this->addRightSystem(new Thruster(4, 9, 0, 3, 4));
		$this->addRightSystem(new Hangar(5, 8));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 50));
        $this->addAftSystem(new Structure(5, 45));
        $this->addLeftSystem(new Structure( 5, 70));
        $this->addRightSystem(new Structure( 5, 70));
        $this->addPrimarySystem(new Structure( 5, 50));
    
            $this->hitChart = array(
        		0=> array(
        				6 => "Structure",
						8 => "Class-S Missile Rack",
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
						8 => "Light Railgun",
        				10 => "Medium Blast Cannon",
						12 => "Flak Cannon",
        				19 => "Structure",
        				20 => "Primary",
        		),
			2=> array(
					8 => "Thruster",
					9 => "Flak Cannon",
					12 => "Medium Blast Cannon",
					19 => "Structure",
					20 => "Primary",
				),
        		3=> array(
        				4 => "Thruster",
        				6 => "Medium Blast Cannon",
                        9 => "Flak Cannon",
						11 => "Hangar",
        				19 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Medium Blast Cannon",
                        9 => "Flak Cannon",
						11 => "Hangar",
        				19 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}

?>
