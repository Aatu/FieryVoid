<?php
class CivilianFelenia extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100;
        $this->faction = "ZEscalation Civilian";
        $this->phpclass = "CivilianFelenia";
        $this->imagePath = "img/ships/EscalationWars/CivilianFelenia.png";
        $this->shipClass = "Felenia Small Transport";
		$this->unofficial = true;
        $this->canvasSize = 75;

		$this->notes = 'Circasian Empire';
	    $this->isd = 1926;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        $this->iniativebonus = -10;

		$cA = new CargoBay(2, 18);
		$cB = new CargoBay(2, 30);
		$cC = new CargoBay(2, 30);
		
		$cA->displayName = "Cargo Bay A";
		$cB->displayName = "Cargo Bay B";
		$cC->displayName = "Cargo Bay C";
         
        $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(2, 8, 2, 2));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 4));
        $this->addPrimarySystem(new Hangar(3, 4));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 3, 4));        
        
        $this->addFrontSystem(new NexusUltralightRailgun(1, 3, 2, 240, 120));
        $this->addFrontSystem($cA);
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
	    
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));    
        $this->addAftSystem(new NexusUltralightRailgun(1, 3, 2, 60, 300));
        $this->addAftSystem($cB);
        $this->addAftSystem($cC);
       
        $this->addPrimarySystem(new Structure(3, 54));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			10 => "Scanner",
			13 => "Hangar",
			16 => "Engine",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Ultralight Railgun",
			10 => "Cargo Bay A",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Ultralight Railgun",
			10 => "Cargo Bay B",
			12 => "Cargo Bay C",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
