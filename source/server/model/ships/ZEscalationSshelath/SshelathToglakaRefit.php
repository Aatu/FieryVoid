<?php
class SshelathToglakaRefit extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "ZEscalation Sshelath";
        $this->phpclass = "SshelathToglakaRefit";
        $this->imagePath = "img/ships/EscalationWars/SshelathToglaka.png";
        $this->shipClass = "Toglaka Patrol Cutter (1960 Refit)";
			$this->variantOf = "Toglaka Patrol Cutter";
			$this->occurence = "common";		
		$this->unofficial = true;
        $this->agile = true;
        $this->canvasSize = 65;
	    $this->isd = 1960;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 10;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(2, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 4));
        $this->addPrimarySystem(new Engine(3, 9, 0, 6, 2));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));        
        

		$this->addFrontSystem(new EWLightGaussCannon(3, 6, 3, 300, 60));
		$this->addFrontSystem(new EWLightGaussCannon(3, 6, 3, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 270, 90));
        $this->addFrontSystem(new Thruster(2, 10, 0, 3, 1));
	    
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 60, 300));
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));    
       
        $this->addPrimarySystem(new Structure(3, 33));

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
			7 => "Light Gauss Cannon",
			9 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			7 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
