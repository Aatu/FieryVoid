<?php
class Sellac extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 105;
	$this->faction = "Civilians";
        $this->phpclass = "Sellac";
        $this->imagePath = "img/ships/AbbaiSellac.png";
        $this->shipClass = "Abbai Sellac Freighter";
        $this->canvasSize = 200;
	    
	$this->isd = 2000;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 6;
	$this->iniativebonus = -20;
		
        $this->addPrimarySystem(new Reactor(3, 7, 0, 0));
        $this->addPrimarySystem(new CnC(4, 4, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 3));
        $this->addPrimarySystem(new Engine(4, 9, 0, 4, 4));
	$this->addPrimarySystem(new Hangar(3, 2));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 2, 3));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 2, 4));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new CargoBay(2, 32));
        $this->addFrontSystem(new CargoBay(2, 32));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));
        $this->addAftSystem(new CargoBay(2, 32));
        $this->addAftSystem(new CargoBay(2, 32));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
       
        $this->addPrimarySystem(new Structure( 3, 30));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Thruster",
        				11 => "Scanner",
        				13 => "Hangar",
        				16 => "Engine",
        				18 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				3 => "Thruster",
        				4 => "Light Particle Beam",
        				8 => "Cargo Bay",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				5 => "Light Particle Beam",
        				9 => "Cargo Bay",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
