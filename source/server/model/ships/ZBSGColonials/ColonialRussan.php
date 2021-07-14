<?php
class ColonialRussanClass extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 450;
		$this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialRussanClass";
        $this->imagePath = "img/ships/BSG/ColonialRussan.png";
        $this->shipClass = "Russan Escortstar";
		$this->occurence = "Uncommon";
     
        $this->canvasSize = 65;
//	    $this->isd = 2011;

		$this->unofficial = true;

		$this->fighters = array("normal"=>12);
        
        $this->forwardDefense = 12;
        $this->sideDefense = 16;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
		$this->iniativebonus = 50;
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 9, 5, 5));
        $this->addPrimarySystem(new Engine(4, 16, 0, 10, 3));
       
		$this->addPrimarySystem(new Hangar(4, 14));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(4, 9, 0, 4, 4));        
//		$this->addPrimarySystem(new Bulkhead(0, 3));
		$this->addPrimarySystem(new BSGFlakBattery(5, 6, 2, 0, 360));
		$hyperdrive = new JumpEngine(4, 12, 6, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new RapidGatling(3, 4, 1, 270, 90));
		$this->addFrontSystem(new BSGFlakBattery(3, 6, 2, 270, 90));
		$this->addFrontSystem(new BSGFlakBattery(3, 6, 2, 270, 90));
		$this->addFrontSystem(new BSGFlakBattery(3, 6, 2, 270, 90));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 3));
        $this->addFrontSystem(new Bulkhead(0, 3));
	
		$this->addAftSystem(new RapidGatling(3, 4, 1, 90, 270));
		$this->addAftSystem(new BSGFlakBattery(3, 6, 2, 90, 270));
		$this->addAftSystem(new BSGFlakBattery(3, 6, 2, 90, 270));
		$this->addAftSystem(new BSGFlakBattery(3, 6, 2, 90, 270));
        $this->addAftSystem(new Thruster(3, 9, 0, 5, 2));    
        $this->addAftSystem(new Thruster(3, 9, 0, 5, 2));    
		$this->addAftSystem(new Bulkhead(0, 2));
       
        $this->addPrimarySystem(new Structure(4, 65));

		//d20 hit chart
		$this->hitChart = array(
		
		0=> array(
			6 => "Thruster",
            8 => "Flak Battery",
			10 => "Scanner",
			13 => "Engine",
			15 => "Hangar",
			17 => "Reactor",
			19 => "Hyperdrive",
			20 => "C&C",
		),
		1=> array(
			6 => "Thruster",
			7 => "Rapid Gatling Railgun",
            10 => "Flak Battery",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			7 => "Rapid Gatling Railgun",
            10 => "Flak Battery",
			18 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>

