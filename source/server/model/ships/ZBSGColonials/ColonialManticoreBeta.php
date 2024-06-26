<?php
class ColonialManticoreBeta extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 550;
		$this->faction = "ZBSG Colonials";
        $this->phpclass = "ColonialManticoreBeta";
        $this->imagePath = "img/ships/BSG/ColonialManticore.png";
        $this->shipClass = "Manticore Corvette Beta";
			$this->occurence = "uncommon";
			$this->variantOf = "Manticore Corvette";
        $this->canvasSize = 65;
//	    $this->isd = 2009;

		$this->notes = "Primary users: Colonial Fleet";

		$this->unofficial = true;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 3;
		$this->iniativebonus = 60;
         
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

		$this->addFrontSystem(new BSGMedBattery(4, 7, 4, 270, 360));
        $this->addFrontSystem(new BSGMedBattery(4, 7, 4, 300, 60));
        $this->addFrontSystem(new BSGMedBattery(4, 7, 4, 0, 90));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
		$this->addFrontSystem(new Bulkhead(0, 3));
        $this->addFrontSystem(new Bulkhead(0, 3));

		$this->addAftSystem(new RapidGatling(3, 4, 1, 180, 30));
		$this->addAftSystem(new RapidGatling(3, 4, 1, 180, 30));
		$this->addAftSystem(new RapidGatling(3, 4, 1, 330, 180));
		$this->addAftSystem(new RapidGatling(3, 4, 1, 330, 180));
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
			10 => "Battery",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			8 => "Rapid Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>

