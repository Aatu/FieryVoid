<?php
class SalbezShvrakRefit extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 200;
        $this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezShvrakRefit";
			$this->variantOf = "Shv'rak Mining Frigate";
			$this->occurence = "common";
        $this->imagePath = "img/ships/Nexus/salbez_mining_frigate.png";
        $this->shipClass = "Shv'rak Mining Frigate (2099 refit)";
		$this->unofficial = true;
        $this->canvasSize = 65;
	    $this->isd = 2099;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 9, 3, 4));
        $this->addPrimarySystem(new Engine(3, 9, 0, 4, 2));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 2, 4));        
        $this->addPrimarySystem(new Hangar(2, 1));
        
		$this->addFrontSystem(new LightLaser(1, 4, 3, 300, 60));
		$this->addFrontSystem(new LaserCutter(1, 6, 4, 300, 60));
		$this->addFrontSystem(new LaserCutter(1, 6, 4, 300, 60));
        $this->addFrontSystem(new Thruster(2, 10, 0, 4, 1));
	    
		$this->addAftSystem(new LightParticleBeamShip(0, 2, 1, 180, 360));
		$this->addAftSystem(new LightParticleBeamShip(0, 2, 1, 0, 180));
		$this->addAftSystem(new LightLaser(1, 4, 3, 120, 240));
        $this->addAftSystem(new Thruster(2, 15, 0, 4, 2));    
       
        $this->addPrimarySystem(new Structure(3, 36));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Scanner",
			12 => "Hangar",
			16 => "Engine",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			6 => "Thruster",
			9 => "Laser Cutter",
			10 => "Light Laser",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			6 => "Thruster",
			8 => "Light Laser",
			10 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
