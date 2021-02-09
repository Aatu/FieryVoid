<?php
class CircasianMoshestaBeta extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "ZEscalation Circasian Empire";
        $this->phpclass = "CircasianMoshestaBeta";
        $this->imagePath = "img/ships/EscalationWars/CircasianMoshesta.png";
        $this->shipClass = "Moshesta Frigate (1974 Refit)";
			$this->variantOf = "Moshesta Frigate";
			$this->occurence = "common";
		$this->unofficial = true;
        $this->canvasSize = 75;
	    $this->isd = 1974;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 10;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 65;
        
         
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 4, 5));
        $this->addPrimarySystem(new Engine(4, 11, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));        
        
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 240, 60));
		$this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 120));
		$this->addFrontSystem(new EWRocketLauncher(2, 4, 1, 270, 90));
        $this->addFrontSystem(new Thruster(2, 5, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 5, 0, 3, 1));
	    
        $this->addAftSystem(new Thruster(3, 8, 0, 5, 2));    
        $this->addAftSystem(new Thruster(3, 8, 0, 5, 2));    
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 240, 360));
		$this->addAftSystem(new LightParticleCannon(2, 6, 5, 240, 360));
		$this->addAftSystem(new LightParticleCannon(2, 6, 5, 0, 120));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 0, 120));
       
        $this->addPrimarySystem(new Structure(3, 36));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Thruster",
			12 => "Scanner",
			14 => "Hangar",
			17 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			4 => "Thruster",
			6 => "Rocket Launcher",
			9 => "Medium Plasma Cannon",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Light Particle Cannon",
			10 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
