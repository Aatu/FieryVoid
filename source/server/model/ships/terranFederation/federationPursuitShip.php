<?php
class federationPursuitShip extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 525;
        $this->faction = "Terran Federation";
        $this->phpclass = "federationPursuitShip";
        $this->imagePath = "img/ships/federationFrigate.png";
        $this->shipClass = "Pursuit Ship";
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 100;
//	    $this->isd = 2108;

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(5, 15, 0, 2));
        $this->addPrimarySystem(new CnC(6, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 5, 7));
        $this->addPrimarySystem(new Engine(5, 12, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(3, 2, 2));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 5, 4));        
        
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 240, 60));
		$this->addFrontSystem(new MediumLaser(4, 6, 5, 300, 60));
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 270, 90));
		$this->addFrontSystem(new MediumLaser(4, 6, 5, 300, 60));
		$this->addFrontSystem(new PlasmaBolt(3, 5, 2, 300, 120));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
	    
		$this->addAftSystem(new PlasmaBolt(3, 5, 2, 120, 300));
        $this->addAftSystem(new Thruster(3, 24, 0, 10, 2));    
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
			5 => "Thruster",
			7 => "Medium Laser",
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
