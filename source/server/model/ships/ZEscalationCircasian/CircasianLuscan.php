<?php
class CircasianLuscan extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 130;
        $this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianLuscan";
        $this->imagePath = "img/ships/EscalationWars/CircasianLuscan.png";
        $this->shipClass = "Luscan Assault Frigate";
		$this->unofficial = true;
        $this->canvasSize = 75;
	    $this->isd = 1878;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(3, 11, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 9, 2, 3));
        $this->addPrimarySystem(new Engine(3, 9, 0, 5, 2));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(2, 11, 0, 5, 4));        
        
        $this->addFrontSystem(new EWParticleGun(2, 4, 2, 300, 60));
		$this->addFrontSystem(new EWLightParticleGun(1, 2, 1, 240, 120));
		$this->addFrontSystem(new EWLightParticleGun(1, 2, 1, 240, 120));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
	    
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(2, 48));


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
			6 => "Particle Gun",
			8 => "Light Particle Gun",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
