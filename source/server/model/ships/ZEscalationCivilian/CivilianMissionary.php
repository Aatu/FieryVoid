<?php
class CivilianMissionary extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 150;
        $this->faction = "ZEscalation Civilian";
        $this->phpclass = "CivilianMissionary";
        $this->imagePath = "img/ships/EscalationWars/ChoukaSinner.png";
        $this->shipClass = "Missionary Fast Transport";
		$this->unofficial = true;
        $this->agile = true;		
        $this->canvasSize = 75;
	    $this->isd = 1900;

 		$this->notes = 'Chouka Theocracy';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 10;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
         
		$this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 7, 4, 4));
        $this->addPrimarySystem(new Engine(3, 11, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
		$this->addPrimarySystem(new Quarters(3, 9));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));        
        
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 240, 60));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 300, 120));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
	    
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 1, 120, 300));
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 1, 60, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));    
       
        $this->addPrimarySystem(new Structure(3, 34));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			9 => "Quarters",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Point Plasma Gun",
			16 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Point Plasma Gun",
			16 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
