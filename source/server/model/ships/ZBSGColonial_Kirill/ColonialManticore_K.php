<?php
class ColonialManticore_K extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "ZPlaytest 12 Colonies of Kobol (Tier 1)";
        $this->phpclass = "ColonialManticore_K";
        $this->imagePath = "img/ships/BSG/ColonialManticore.png";
        $this->shipClass = "Manticore Corvette";
        $this->canvasSize = 100;
//	    $this->isd = 2007;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 5, 5));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 2));
        $this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 4, 4));        
		$this->addPrimarySystem(new Bulkhead(0, 4));
		$hyperdrive = new JumpEngine(4, 12, 6, 20);
		$hyperdrive->displayName = 'Phasing Drive';
		$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new MedBlastCannon(4, 5, 2, 300, 60));
        $this->addFrontSystem(new Thruster(4, 9, 0, 4, 1));
		$this->addFrontSystem(new Bulkhead(0, 4));

		$this->addAftSystem(new LtBlastCannon(4, 4, 1, 120, 240));
		$this->addAftSystem(new LtBlastCannon(4, 4, 1, 120, 240));
        $this->addAftSystem(new Thruster(4, 9, 0, 4, 2));    
        $this->addAftSystem(new Thruster(4, 9, 0, 4, 2));    
		$this->addAftSystem(new Bulkhead(0, 4));
       
        $this->addPrimarySystem(new Structure(4, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			7 => "Thruster",
			8 => "Class-S Missile Rack",
			9 => "Reload Rack",
			11 => "Scanner",
			14 => "Engine",
			15 => "Hangar",
			17 => "Reactor",
			19 => "Phasing Drive",
			20 => "C&C",
		),
		1=> array(
			4 => "Thruster",
			9 => "Medium Blast Cannon",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			10 => "Light Blast Cannon",
			18 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>
