<?php
class federationPatroller extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 525;
        $this->faction = "Terran Federation";
        $this->phpclass = "federationPatroller";
        $this->imagePath = "img/ships/federationPatroller.png";
        $this->shipClass = "Patroller";
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 100;
//	    $this->isd = 2108;

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(5, 15, 0, 2));
        $this->addPrimarySystem(new CnC(6, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 5, 7));
        $this->addPrimarySystem(new Engine(5, 12, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(3, 2, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));        
        
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 240, 60));
		$this->addFrontSystem(new HeavyLaser(5, 8, 6, 300, 60));
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 300, 120));
        $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
	    
		$this->addAftSystem(new PlasmaBolt(3, 5, 2, 120, 300));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));    
        $this->addAftSystem(new Thruster(3, 18, 0, 8, 2));    
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));    
		$this->addAftSystem(new PlasmaBolt(3, 5, 2, 60, 240));
       
        $this->addPrimarySystem(new Structure(5, 50));

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

		1=> array(
			4 => "Thruster",
			7 => "Heavy Laser",
			9 => "Plasma Bolt",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			10 => "Plasma Bolt",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
