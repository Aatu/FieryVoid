<?php
class CircasianYollana extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianYollana";
        $this->imagePath = "img/ships/EscalationWars/CircasianYollana.png";
        $this->shipClass = "Yollana Patrol Frigate";
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 60;
	    $this->isd = 1916;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 9;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
        $this->addPrimarySystem(new Engine(3, 9, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 5, 4));        
        
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 60));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 60));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 60));
		$this->addFrontSystem(new LightPlasma(2, 4, 2, 300, 60));
		$this->addFrontSystem(new LightRailGun(1, 6, 3, 240, 120));
        $this->addFrontSystem(new Thruster(2, 6, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 3, 1));
	    
		$this->addAftSystem(new LightRailGun(1, 6, 3, 60, 300));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));    
       
        $this->addPrimarySystem(new Structure(2, 36));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Thruster",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			7 => "Light Plasma Cannon",
			9 => "Light Railgun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Light Railgun",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
