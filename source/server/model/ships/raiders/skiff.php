<?php
class Skiff extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 230;
	$this->faction = "Raiders";
        $this->phpclass = "Skiff";
        $this->imagePath = "img/ships/sloop.png"; //needs to be changed
        $this->shipClass = "Skiff";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 1;
         
        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 4));
        $this->addPrimarySystem(new CargoBay(3, 20));
        $this->addPrimarySystem(new CargoBay(3, 20));
        $this->addPrimarySystem(new Hangar(2, 4));
    	$this->addPrimarySystem(new Thruster(4, 13, 0, 4, 3));
    	$this->addPrimarySystem(new Thruster(4, 13, 0, 4, 4));
    	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
    	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
    	 
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 240, 360));
		$this->addFrontSystem(new LightParticleCannon(2, 6, 5, 0, 120));
        
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
	
        $this->addPrimarySystem(new Structure( 4, 48));
        
        $this->hitChart = array(
        		0=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "cargoBay",
        				8 => "cargoBay",
        				9 => "lightParticleBeam",
        				10 => "lightParticleBeam",
        				11 => "scanner",
        				12 => "scanner",
        				13 => "scanner",
        				14 => "hanger",
        				15 => "engine",
        				16 => "engine",
        				17 => "reactor",
        				18 => "reactor",
        				19 => "reactor",
        				20 => "CnC",
        		),
        		1=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "lightParticleCannon",
        				7 => "lightParticleCannon",
        				8 => "lightParticleCannon",
        				9 => "lightParticleCannon",
        				10 => "structure",
        				11 => "structure",
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
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "thruster",
        				8 => "lightParticleBeam",
        				9 => "lightParticleBeam",
        				10 => "lightParticleBeam",
        				11 => "structure",
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
