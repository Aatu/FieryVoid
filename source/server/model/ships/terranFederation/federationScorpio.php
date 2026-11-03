<?php
class federationScorpio extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 625;
        $this->faction = "Terran Federation";
        $this->phpclass = "federationScorpio";
        $this->imagePath = "img/ships/federationScorpio.png";
        $this->shipClass = "Scorpio Prototype";
        $this->occurence = "unique"; 
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 100;
//	    $this->isd = 2108;

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(5, 12, 0, 2));
        $this->addPrimarySystem(new CnC(6, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 5, 7));
        $this->addPrimarySystem(new Engine(5, 12, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(3, 1, 1));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 7, 3));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 7, 4));        
        
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 240, 60));
		$this->addFrontSystem(new LaserCannonLance(4, 16, 12, 300, 60));
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 300, 120));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
	    
        $this->addAftSystem(new Thruster(3, 28, 0, 12, 2));    
       
        $this->addPrimarySystem(new Structure(5, 60));

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
			5 => "Thruster",
			7 => "Laser Cannon Lance",
			9 => "Plasma Bolt",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
