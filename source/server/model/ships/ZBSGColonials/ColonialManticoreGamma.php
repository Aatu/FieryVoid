<?php
class ColonialManticoreGamma extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "ZBSG Colonials";
        $this->phpclass = "ColonialManticoreGamma";
        $this->imagePath = "img/ships/BSG/ColonialManticore.png";
        $this->shipClass = "Manticore Corvette Gamma";
			$this->occurence = "rare";
			$this->variantOf = "Manticore Corvette";
        $this->canvasSize = 65;
//	    $this->isd = 2011;

		$this->notes = "Primary users: Colonial Fleet";

		$this->unofficial = true;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 3;
		$this->iniativebonus = 65;
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 5, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 4));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 4, 4));        
		$this->addPrimarySystem(new RapidGatling(5, 4, 1, 0, 360));
		$hyperdrive = new JumpEngine(4, 12, 6, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new RapidGatling(3, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(3, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(3, 4, 1, 270, 90));
		$this->addFrontSystem(new BSGFlakBattery(3, 6, 2, 270, 90));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 3));
        $this->addFrontSystem(new Bulkhead(0, 3));
	
		$this->addAftSystem(new RapidGatling(3, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(3, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(3, 4, 1, 90, 270));
		$this->addAftSystem(new BSGFlakBattery(3, 6, 5, 90, 270));
        $this->addAftSystem(new Thruster(3, 9, 0, 4, 2));    
        $this->addAftSystem(new Thruster(3, 9, 0, 4, 2));    
		$this->addAftSystem(new Bulkhead(0, 2));
       
        $this->addPrimarySystem(new Structure(4, 45));

		//d20 hit chart
		$this->hitChart = array(
		
		0=> array(
			7 => "Thruster",
            8 => "Rapid Gatling Railgun",
			11 => "Scanner",
			14 => "Engine",
			15 => "Hangar",
			17 => "Reactor",
			19 => "Hyperdrive",
			20 => "C&C",
		),
		1=> array(
			6 => "Thruster",
			9 => "Rapid Gatling Railgun",
            10 => "Flak Battery",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			9 => "Rapid Gatling Railgun",
            10 => "Flak Battery",
			18 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>

