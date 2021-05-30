<?php
class ColonialLokiClass extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
		$this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialLokiClass";
        $this->imagePath = "img/ships/BSG/ColonialLoki.png";
        $this->shipClass = "Loki Gunboat Corvette";
        $this->canvasSize = 100;
//	    $this->isd = 2007;

		$this->unofficial = true;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.3;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(5, 10, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 8, 5, 6));
        $this->addPrimarySystem(new Engine(5, 12, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(2, 4));
		$this->addPrimarySystem(new Thruster(5, 9, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(5, 9, 0, 3, 4));        
		$this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
        $this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
		$hyperdrive = new JumpEngine(4, 10, 6, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);

		$this->addFrontSystem(new BSGMedBattery(4, 7, 4, 180, 360));
		$this->addFrontSystem(new BSGMedBattery(4, 7, 4, 0, 180));
        $this->addFrontSystem(new BSGMedBattery(4, 7, 4, 300, 60));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 3));

		$this->addAftSystem(new RapidGatling(4, 4, 1, 90, 270));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));    
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));    
		$this->addAftSystem(new Bulkhead(0, 2));
       
        $this->addPrimarySystem(new Structure(4, 50));

		//d20 hit chart
		$this->hitChart = array(
		
		0=> array(
			6 => "Thruster",
            8 => "Rapid Gatling Railgun",
			11 => "Scanner",
			14 => "Engine",
			15 => "Hangar",
			17 => "Reactor",
			19 => "Hyperdrive",
			20 => "C&C",
		),
		1=> array(
			5 => "Thruster",
			10 => "Battery",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			7 => "Thruster",
			8 => "Rapid Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>