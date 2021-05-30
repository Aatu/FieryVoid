<?php

class KobolValkyrie extends BaseShipNoAft{

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
        $this->sideDefense = 14;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new SWScanner(5, 9, 5, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 12, 3));
        $this->addPrimarySystem(new ReloadRack(5, 9));
        $this->addPrimarySystem(new SMissileRack(5, 6, 0, 0, 360));
        $this->addPrimarySystem(new SMissileRack(5, 6, 0, 0, 360));
//		$this->addPrimarySystem(new Bulkhead(0, 4));
//      $this->addPrimarySystem(new Bulkhead(0, 4));
 //     $this->addPrimarySystem(new Bulkhead(0, 4));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 2));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 2));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 2));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 2));
		$hyperdrive = new JumpEngine(4, 16, 6, 20);
			$hyperdrive->displayName = 'Phasing Drive';
			$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Railgun(4, 9, 6, 330, 30));
        $this->addFrontSystem(new Railgun(4, 9, 6, 330, 30));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
		$this->addFrontSystem(new MedBlastCannon(4, 5, 2, 240, 120));
        $this->addFrontSystem(new LtBlastCannon(4, 4, 1, 210, 360));        
        $this->addFrontSystem(new LtBlastCannon(4, 4, 1, 0, 150));        
        $this->addFrontSystem(new SMissileRack(4, 6, 0, 270, 90));
		$this->addFrontSystem(new FlakArray(4, 10, 3, 210, 30));
		$this->addFrontSystem(new FlakArray(4, 10, 3, 330, 150));
		$this->addFrontSystem(new Bulkhead(0, 4));
        $this->addFrontSystem(new Bulkhead(0, 4));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));

		$this->addLeftSystem(new MedBlastCannon(4, 5, 2, 210, 360));
		$this->addLeftSystem(new FlakArray(4, 10, 3, 210, 330));
		$this->addLeftSystem(new FlakArray(4, 10, 3, 210, 330));
        $this->addLeftSystem(new LtBlastCannon(4, 4, 1, 180, 360));        
        $this->addLeftSystem(new LtBlastCannon(4, 4, 1, 180, 360));        
		$this->addLeftSystem(new Bulkhead(0, 4));
        $this->addLeftSystem(new Bulkhead(0, 4));
        $this->addLeftSystem(new Thruster(4, 9, 0, 5, 3));
		$this->addLeftSystem(new Hangar(5, 7));

		$this->addRightSystem(new MedBlastCannon(4, 5, 2, 0, 150));
		$this->addRightSystem(new FlakArray(4, 10, 3, 30, 150));
		$this->addRightSystem(new FlakArray(4, 10, 3, 30, 150));
        $this->addRightSystem(new LtBlastCannon(4, 4, 1, 0, 180));        
        $this->addRightSystem(new LtBlastCannon(4, 4, 1, 0, 180));        
		$this->addRightSystem(new Bulkhead(0, 4));
        $this->addRightSystem(new Bulkhead(0, 4));
        $this->addRightSystem(new Thruster(4, 9, 0, 5, 4));
		$this->addRightSystem(new Hangar(5, 7));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 50));
        $this->addLeftSystem(new Structure( 5, 70));
        $this->addRightSystem(new Structure( 5, 70));
        $this->addPrimarySystem(new Structure( 5, 45));
    
            $this->hitChart = array(
        		0=> array(
        				6 => "Structure",
						8 => "Class-S Missile Rack",
						9 => "Reload Rack",
						11 => "Thruster",
						13 => "Phasing Drive",
        				15 => "Scanner",
        				17 => "Engine",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				5 => "Class-S Missile Rack",
        				7 => "Medium Blast Cannon",
        				8 => "Light Blast Cannon",
						10 => "Flak Array",
						12 => "Railgun",
        				19 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Medium Blast Cannon",
        				7 => "Light Blast Cannon",
                        9 => "Flak Array",
						11 => "Hangar",
        				19 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Medium Blast Cannon",
        				7 => "Light Blast Cannon",
                        9 => "Flak Array",
						11 => "Hangar",
        				19 => "Structure",
        				20 => "Primary",
        		),
        );
    
    }
}
?>
