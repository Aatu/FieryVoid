<?php
class ColonialManticore_K extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
		$this->faction = "ZPlaytest 12 Colonies of Cobol";
        $this->phpclass = "ColonialManticore_K";
        $this->imagePath = "img/ships/BSG/ColonialManticore.png";
        $this->shipClass = "Manticore Class Corvette";
        $this->canvasSize = 100;
//	    $this->isd = 2007;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 50;
        
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 5, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
        $this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new SMissileRack(4, 6, 0, 0, 360));
		$this->addPrimarySystem(new Hangar(4, 4));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 3, 4));        
		$this->addPrimarySystem(new Bulkhead(0, 3));
		$hyperdrive = new JumpEngine(4, 12, 6, 20);
		$hyperdrive->displayName = 'Phasing Drive';
		$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Railgun(4, 9, 6, 300, 60));
        $this->addFrontSystem(new Thruster(4, 9, 0, 4, 1));
		$this->addFrontSystem(new Bulkhead(0, 3));

		$this->addAftSystem(new FlakCannon(3, 4, 2, 180, 30));
		$this->addAftSystem(new FlakCannon(3, 4, 2, 180, 30));
		$this->addAftSystem(new FlakCannon(3, 4, 2, 330, 180));
		$this->addAftSystem(new FlakCannon(3, 4, 2, 330, 180));
        $this->addAftSystem(new Thruster(3, 9, 0, 4, 2));    
        $this->addAftSystem(new Thruster(3, 9, 0, 4, 2));    
		$this->addAftSystem(new Bulkhead(0, 2));
       
        $this->addPrimarySystem(new Structure(4, 45));

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
			9 => "Railgun",
			19 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			10 => "Flak Cannon",
			19 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>
