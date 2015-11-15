<?php
class Wolf extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 450;
    	$this->faction = "Raiders";
        $this->phpclass = "Wolf";
        $this->imagePath = "img/ships/xebec.png"; //need to change this
        $this->shipClass = "Wolf Raider";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
    	$this->iniativebonus = 0;
    	$this->fighters = array("light"=>6);
    	 
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 3));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Hangar(4, 4));
    	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 360));
		
        $this->addFrontSystem(new Thruster(2, 8, 0, 6, 1));
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 240, 60));
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 270, 120));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 60));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 180));
		$this->addFrontSystem(new CargoBay(3, 10));
		$this->addFrontSystem(new CargoBay(3, 10));
		$this->addFrontSystem(new Hangar(3, 2));
		$this->addFrontSystem(new Hangar(3, 2));
				
        $this->addAftSystem(new Thruster(2, 12, 0, 8, 2));
        $this->addAftSystem(new ParticleCannon(3, 8, 7, 120, 270));
        $this->addAftSystem(new ParticleCannon(3, 8, 7, 60, 240));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 240));
        $this->addAftSystem(new CargoBay(3, 20));
        $this->addAftSystem(new CargoBay(3, 20));
        
        $this->addPrimarySystem(new Structure( 4, 56));
        
        $this->hitChart = array(
        		0=> array( 
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "thruster",
        				8 => "Standard Particle Beam",
        				9 => "Standard Particle Beam",
        				10 => "scanner",
        				11 => "scanner",
        				12 => "scanner",
        				13 => "engine",
        				14 => "engine",
        				15 => "engine",
        				16 => "hangar",
        				17 => "hangar",
        				18 => "reactor",
        				19 => "reactor",
        				20 => "C&C",
        		),
        		1=> array( 
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "particle Cannon",
        				5 => "particle Cannon",
        				6 => "Standard Particle Beam",
        				7 => "Standard Particle Beam",
        				8 => "cargo Bay",
        				9 => "cargo Bay",
        				10 => "hangar",
        				11 => "hangar",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "primary",
        				19 => "primary",
        				20 => "primary",
        		),
        		2=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "particle Cannon",
        				5 => "particle Cannon",
        				6 => "Standard Particle Beam",
        				7 => "Standard Particle Beam",
        				8 => "cargo Bay",
        				9 => "cargo Bay",
        				10 => "cargo Bay",
        				11 => "cargo Bay",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "primary",
        				19 => "primary",
        				20 => "primary",
        		),
        );
        
    }

}



?>
