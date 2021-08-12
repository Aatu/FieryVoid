<?php
class KobolJanus extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
         $this->pointCost = 900;
        $this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "KobolJanus";
        $this->imagePath = "img/ships/BSG/KobolJanus.png";
        $this->shipClass = "Janus Heavy Cruiser";
 //       $this->isd = 2160;
        $this->canvasSize = 145;

		$this->unofficial = true;
	    $this->notes = 'May only boost sensors by 2.';
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        
        $this->iniativebonus = 30;

        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 6, 5));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(4, 9, 0, 3, 4));
        $this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
        
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 9, 0, 3, 1));
		$this->addFrontSystem(new HvyBlastCannon(4, 6, 4, 270, 60)); 
        $this->addFrontSystem(new HeavyRailGun(4, 12, 9, 300, 60)); 
		$this->addFrontSystem(new HvyBlastCannon(4, 6, 4, 300, 90)); 
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 180, 30));
		$this->addFrontSystem(new FlakCannon(3, 4, 2, 330, 180));
		$this->addFrontSystem(new Bulkhead(0, 4));
		$this->addFrontSystem(new Bulkhead(0, 4));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Bulkhead(0, 4));
        $this->addAftSystem(new Bulkhead(0, 4));
		$this->addAftSystem(new MedBlastCannon(4, 5, 2, 180, 360)); 
		$this->addAftSystem(new MedBlastCannon(4, 5, 2, 0, 180)); 
		$this->addAftSystem(new FlakCannon(3, 4, 2, 120, 240)); 
		$this->addAftSystem(new FlakCannon(3, 4, 2, 120, 240)); 
        $hyperdrive = new JumpEngine(4, 12, 6, 20);
			$hyperdrive->displayName = 'FTL Drive';
			$this->addAftSystem($hyperdrive);

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 45));
        $this->addAftSystem(new Structure( 4, 35));
        $this->addPrimarySystem(new Structure( 5, 40));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Thruster",
					11 => "Class-S Missile Rack",
					13 => "Scanner",
					15 => "Engine",
					16 => "Hangar",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Heavy Railgun",
					9 => "Heavy Blast Cannon",
					11 => "Flak Cannon",
                    19 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Medium Blast Cannon",
					10 => "Flak Cannon",
					12 => "FTL Drive",
                    19 => "Structure",
					20 => "Primary",
			),
		);
		

    }
}
?>
