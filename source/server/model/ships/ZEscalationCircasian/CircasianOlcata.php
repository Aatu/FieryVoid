<?php
class CircasianOlcata extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 225;
        $this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianOlcata";
        $this->imagePath = "img/ships/EscalationWars/CircasianOlcata.png";
        $this->shipClass = "Olcata Frigate";
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 75;
	    $this->isd = 1942;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 10;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
         
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
        $this->addPrimarySystem(new Engine(3, 7, 0, 5, 2));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 2, 4));        
        
        $this->addFrontSystem(new LightLaser(3, 4, 3, 240, 360));
        $this->addFrontSystem(new LightLaser(3, 4, 3, 0, 120));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 300, 120));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
	    
		$this->addAftSystem(new LightLaser(1, 4, 3, 180, 300));
		$this->addAftSystem(new LightLaser(1, 4, 3, 60, 180));
        $this->addAftSystem(new Thruster(2, 13, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(3, 30));


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
			7 => "Light Laser",
			10 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			9 => "Light Laser",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
