<?php
class ColonialOrion extends HeavyCombatVessel{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 700;
		$this->faction = "ZPlaytest BSG Colonials";
        $this->phpclass = "ColonialOrion";
        $this->imagePath = "img/ships/BSG/ColonialOrion.png";
        $this->shipClass = "Orion Reconisance Battlestar";
        $this->canvasSize = 120;
        $this->limited = 33;
//	    $this->isd = 2007;

        $this->fighters = array("normal"=>6 );

		$this->unofficial = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 4;
		$this->iniativebonus = 25;
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 5, 6));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
        $this->addPrimarySystem(new BSGFlakBattery(3, 6, 2, 0, 360));
        $this->addPrimarySystem(new BSGFlakBattery(3, 6, 2, 0, 360));
		$this->addPrimarySystem(new Thruster(3, 9, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 9, 0, 3, 4));        
        $hyperdrive = new JumpEngine(3, 12, 6, 20);
			$hyperdrive->displayName = 'Hyperdrive';
			$this->addPrimarySystem($hyperdrive);
			
		$this->addFrontSystem(new Hangar(3, 8));
        $this->addFrontSystem(new Thruster(3, 9, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 9, 0, 3, 1));
		$this->addFrontSystem(new BSGMedBattery(4, 7, 4, 300, 360));
		$this->addFrontSystem(new BSGMedBattery(4, 7, 4, 0, 60));
		$this->addFrontSystem(new RapidGatling(4, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(4, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(4, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(4, 4, 1, 270, 90));
		$this->addFrontSystem(new RapidGatling(4, 4, 1, 270, 90));
		$this->addFrontSystem(new Bulkhead(0, 5));

		$this->addAftSystem(new RapidGatling(4, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(4, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(4, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(4, 4, 1, 90, 270));
		$this->addAftSystem(new RapidGatling(4, 4, 1, 90, 270));
		$this->addAftSystem(new BSGMedBattery(4, 7, 4, 180, 240));
		$this->addAftSystem(new BSGMedBattery(4, 7, 4, 120, 180));
		$this->addAftSystem(new Bulkhead(0, 5));
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2));    
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));   
        $this->addAftSystem(new Thruster(3, 12, 0, 2, 2)); 
       
        $this->addPrimarySystem(new Structure(5, 60));
		$this->addAftSystem(new Structure(5, 60));
        $this->addPrimarySystem(new Structure(5, 60 ));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			7 => "Thruster",
			9 => "Flak Battery",
			12 => "Scanner",
			15 => "Engine",
			17 => "Reactor",
			19 => "Hyperdrive",
			20 => "C&C",
		),
		1=> array(
			5 => "Thruster",
			8 => "Battery",
			10 => "Rapid Gatling Railgun",
			12 => "Hangar",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			8 => "Battery",
			10 => "Rapid Gatling Railgun",
			18 => "Structure",
			20 => "Primary",
		),
	);

			
        }
    }
?>
