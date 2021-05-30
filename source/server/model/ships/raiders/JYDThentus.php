<?php
class JYDThentus extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 425;
		$this->faction = "Raiders";
        $this->phpclass = "JYDThentus";
        $this->imagePath = "img/ships/thentus.png";
        $this->shipClass = "JYD Thentus Frigate";
        $this->agile = true;
	    
		$this->occurence = "unique";

		$this->notes = 'Used only by the Junkyard Dogs';
		$this->notes .= '<br>Only one exists';

	    $this->isd = 2260;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        
        $this->iniativebonus = 65;
        
        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 10, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 7));
        $this->addPrimarySystem(new Engine(4, 14, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4));        
        $this->addPrimarySystem(new StdParticleBeam(4, 4, 1, 180, 0));
        $this->addPrimarySystem(new StdParticleBeam(4, 4, 1, 0, 180));
		
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 270, 90));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 270, 90));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240, 60));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 300, 120));        
        $this->addFrontSystem(new Thruster(4, 13, 0, 6, 1));
		          
        $this->addAftSystem(new TwinArray(3, 6, 2, 120, 300));
        $this->addAftSystem(new TwinArray(3, 6, 2, 60, 240));		
        $this->addAftSystem(new Thruster(4, 20, 0, 12, 2));        
		
		//structures
        $this->addPrimarySystem(new Structure(4, 60));
		
		
		
		$this->hitChart = array(
			0=> array(
				7 => "Thruster",
				9 => "Standard Particle Beam",
				12 => "Scanner",
				15 => "Engine",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				8 => "Medium Laser",
				11 => "Twin Array",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				9 => "Twin Array",
				17 => "Structure",
				20 => "Primary",
			),
		);
    }
}



?>
