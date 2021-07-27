<?php
class SalbezShvrez extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 325;
        $this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezShvrez";
        $this->imagePath = "img/ships/Nexus/salbez_shvrez.png";
        $this->shipClass = "Shv'rez Frigate";
		$this->unofficial = true;
        $this->canvasSize = 75;
	    $this->isd = 2121;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(3, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 4, 6));
        $this->addPrimarySystem(new Engine(3, 12, 0, 10, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));        
        $this->addPrimarySystem(new Hangar(2, 2));
        
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 240, 360));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 120));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 120));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 0, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	    
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 300));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 300));
		$this->addAftSystem(new LightLaser(2, 4, 3, 90, 270));
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));    
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));    
       
        $this->addPrimarySystem(new Structure(4, 49));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			8 => "Thruster",
			11 => "Scanner",
			14 => "Engine",
			16 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			7 => "Medium Laser",
			9 => "Light Laser",
			10 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			7 => "Thruster",
			9 => "Light Laser",
			10 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
