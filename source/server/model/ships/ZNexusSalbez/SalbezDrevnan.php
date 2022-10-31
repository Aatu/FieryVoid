<?php
class SalbezDrevnan extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 215;
        $this->faction = "ZNexus Sal-bez";
        $this->phpclass = "SalbezDrevnan";
        $this->imagePath = "img/ships/Nexus/salbez_drevnan.png";
        $this->shipClass = "Drev-nan Auxiliary Escort";
		$this->unofficial = true;
        $this->canvasSize = 65;
	    $this->isd = 2101;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
//        $this->rollcost = 2;
//        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(2, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(1, 6, 1, 3));
        $this->addPrimarySystem(new Engine(2, 9, 0, 6, 4));
		$this->addPrimarySystem(new LightLaser(0, 4, 3, 0, 360));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
        $this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));        
        $this->addPrimarySystem(new Hangar(0, 4));
        
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 180, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 240, 120));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 300, 180));
        $this->addFrontSystem(new Thruster(2, 6, 0, 4, 1));
	    
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 180, 60));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 300, 180));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));    
       
        $this->addPrimarySystem(new Structure(3, 36));

	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			7 => "Thruster",
			9 => "Light Laser",
			12 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			5 => "Thruster",
			9 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			9 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),

	);

        
        }
    }
?>
