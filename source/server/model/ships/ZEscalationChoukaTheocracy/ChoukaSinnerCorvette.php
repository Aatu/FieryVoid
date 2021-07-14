<?php
class ChoukaSinnerCorvette extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 200;
        $this->faction = "ZEscalation Chouka Theocracy";
        $this->phpclass = "ChoukaSinnerCorvette";
        $this->imagePath = "img/ships/EscalationWars/ChoukaSinner.png";
        $this->shipClass = "Sinner Corvette";
		$this->unofficial = true;
        $this->agile = true;		
        $this->canvasSize = 75;
	    $this->isd = 1875;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 10;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
         
		$this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 7, 4, 4));
        $this->addPrimarySystem(new Engine(3, 11, 0, 6, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));        
        
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 240, 60));
        $this->addFrontSystem(new EWHeavyPlasmaGun(2, 5, 3, 240, 360));
        $this->addFrontSystem(new EWHeavyPlasmaGun(2, 5, 3, 0, 120));
		$this->addFrontSystem(new EWPointPlasmaGun(1, 3, 1, 300, 120));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
	    
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 1, 120, 300));
		$this->addAftSystem(new EWPointPlasmaGun(1, 3, 1, 60, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
       
        $this->addPrimarySystem(new Structure(3, 34));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Scanner",
			14 => "Engine",
			16 => "Hangar",
			18 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			7 => "Heavy Plasma Gun",
			9 => "Point Plasma Gun",
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
