<?php
class DalithornTugRefit extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 85;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "DalithornTugRefit";
        $this->imagePath = "img/ships/Nexus/dalithorn_tug.png";
        $this->shipClass = "Dalithorn Drenthim Tug (2113)";
			$this->variantOf = "Dalithorn Drenthim Tug";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 2113;

 		$this->fighters = array("superheavy"=>1);

		$this->notes = "Usually uses transport super-heavy unit.";	
       
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 2;
        $this->turndelaycost = 2;
        $this->accelcost = 6;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = -4*5;

		$cA = new CargoBay(1, 25);
		$cB = new CargoBay(1, 25);
		$cC = new CargoBay(1, 25);
		$cD = new CargoBay(1, 25);
		$cE = new CargoBay(1, 25);
		$cF = new CargoBay(1, 25);
		$cG = new CargoBay(1, 25);
		$cH = new CargoBay(1, 25);

		$cA->displayName = "Cargo Bay A";
		$cB->displayName = "Cargo Bay B";
		$cC->displayName = "Cargo Bay C";
		$cD->displayName = "Cargo Bay D";
		$cE->displayName = "Cargo Bay E";
		$cF->displayName = "Cargo Bay F";
		$cG->displayName = "Cargo Bay G";
		$cH->displayName = "Cargo Bay H";
		
        $this->addPrimarySystem(new Reactor(2, 6, 0, 0));
        $this->addPrimarySystem(new CnC(3, 4, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 6, 2, 2));
        $this->addPrimarySystem(new Engine(2, 16, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(1, 1, 1));
		$this->addPrimarySystem(new NexusAutocannon(1, 4, 1, 0, 360));
		$this->addPrimarySystem(new Catapult(1, 6));
        $this->addPrimarySystem(new Thruster(1, 15, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(1, 15, 0, 6, 4));        
        
        $this->addFrontSystem(new Thruster(2, 13, 0, 4, 1));
        $this->addFrontSystem(new Thruster(2, 13, 0, 4, 1));
		$this->addFrontSystem($cA);
		$this->addFrontSystem($cB);
		$this->addFrontSystem($cC);
		$this->addFrontSystem($cD);
	    
        $this->addAftSystem(new Thruster(1, 10, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 13, 0, 4, 2));    
        $this->addAftSystem(new Thruster(1, 10, 0, 2, 2));    
		$this->addAftSystem($cE);
		$this->addAftSystem($cF);
		$this->addAftSystem($cG);
		$this->addAftSystem($cH);
       
        $this->addPrimarySystem(new Structure(2, 37));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			6 => "Thruster",
			8 => "Autocannon",
			9 => "Catapult",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			3 => "Thruster",
			5 => "Cargo Bay A",
			7 => "Cargo Bay B",
			9 => "Cargo Bay C",
			11 => "Cargo Bay D",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			3 => "Thruster",
			5 => "Cargo Bay A",
			7 => "Cargo Bay B",
			9 => "Cargo Bay C",
			11 => "Cargo Bay D",
			17 => "Structure",
			20 => "Primary",
		),

	);
        
        }
    }
?>
