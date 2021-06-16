<?php
class Skiff extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 230;
	$this->faction = "Raiders";
        $this->phpclass = "Skiff";
//        $this->imagePath = "img/ships/sloop.png"; //needs to be changed
        $this->imagePath = "img/ships/skiff.png"; 
        $this->shipClass = "Skiff";
        $this->canvasSize = 80;
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

		$this->isd = 2232;
        
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
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 4));
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
        				6 => "Thruster",
        				8 => "Cargo Bay",
        				10 => "Light Particle Beam",
        				13 => "Scanner",
        				14 => "Hangar",
        				16 => "Engine",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				9 => "Light Particle Cannon",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				7 => "Thruster",
        				10 => "Light Particle Beam",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
