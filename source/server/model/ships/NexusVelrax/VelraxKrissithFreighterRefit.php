<?php
class VelraxKrissithFreighterRefit extends MediumShipLeftRight{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 95;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "VelraxKrissithFreighterRefit";
        $this->imagePath = "img/ships/Nexus/velraxKrissith.png";
        $this->shipClass = "Velrax Krissith Freighter (2112)";
			$this->variantOf = "Velrax Krissith Freighter";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 100;
	    $this->isd = 2112;

        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = -15;

		$cA = new CargoBay(2, 30);
		$cB = new CargoBay(2, 30);
		$cC = new CargoBay(2, 30);
		$cD = new CargoBay(2, 30);
		$cE = new CargoBay(2, 30);
		$cF = new CargoBay(2, 30);
		
		$cA->displayName = "Cargo Bay A";
		$cB->displayName = "Cargo Bay B";
		$cC->displayName = "Cargo Bay C";
		$cD->displayName = "Cargo Bay D";
		$cE->displayName = "Cargo Bay E";
		$cF->displayName = "Cargo Bay F";
         
        $this->addPrimarySystem(new Reactor(2, 4, 0, 0));
        $this->addPrimarySystem(new CnC(2, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 4, 2, 2));
        $this->addPrimarySystem(new Engine(2, 6, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 12, 0, 4, 1));
        $this->addPrimarySystem(new Thruster(2, 12, 0, 4, 2));        
		$this->addPrimarySystem(new Hangar(0, 4, 2));
		
		$this->addLeftSystem(new NexusIonBolter(1, 2, 2, 180, 60));
        $this->addLeftSystem(new Thruster(2, 12, 0, 4, 3));
        $this->addLeftSystem($cA);
        $this->addLeftSystem($cB);
        $this->addLeftSystem($cC);
	    
		$this->addRightSystem(new NexusIonBolter(1, 2, 2, 300, 180));
        $this->addRightSystem(new Thruster(2, 12, 0, 4, 4));    
        $this->addRightSystem($cD);
        $this->addRightSystem($cE);
        $this->addRightSystem($cF);
       
        $this->addPrimarySystem(new Structure(3, 40));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

			3=> array(
				3 => "Thruster",
				5 => "Ion Bolter",
   				7 => "Cargo Bay A",
   				9 => "Cargo Bay B",
				11=> "Cargo Bay C",
				17 => "Structure",
				20 => "Primary",
			),

			4=> array(
				3 => "Thruster",
				5 => "Ion Bolter",
   				7 => "Cargo Bay D",
   				9 => "Cargo Bay E",
				11=> "Cargo Bay F",
				17 => "Structure",
				20 => "Primary",
		),

	);

        
        }
    }
?>
