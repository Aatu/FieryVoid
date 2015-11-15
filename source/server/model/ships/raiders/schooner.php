<?php
class Schooner extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 200;
	$this->faction = "Raiders";
        $this->phpclass = "Schooner";
        $this->imagePath = "img/ships/sloop.png"; //needs to be changed
        $this->shipClass = "Schooner";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
        $this->iniativebonus = 20;
        $this->fighters = array("light"=>12);        
         
        $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 8, 3, 4));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(2, 10, 0, 3, 4));
		
		$temp1 = new CargoBay(3, 16);
		$temp2 = new CargoBay(3, 16);
		$temp3 = new CargoBay(3, 16);
		$temp4 = new CargoBay(3, 16);
		$temp5 = new CargoBay(3, 16);
		$temp6 = new CargoBay(3, 16);
		$temp1->displayName = "Cargo Bay A";
		$temp2->displayName = "Cargo Bay B";
		$temp3->displayName = "Cargo Bay C";
		$temp4->displayName = "Cargo Bay D";
		$temp5->displayName = "Cargo Bay E";
		$temp6->displayName = "Cargo Bay F";
		$this->addFrontSystem(temp1);
		$this->addFrontSystem(temp2);
		$this->addFrontSystem(temp3);
		$this->addAftSystem(temp4);
		$this->addAftSystem(temp5);
		$this->addAftSystem(temp6);
		
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
		$this->addFrontSystem(new CnC(4, 9, 0, 0));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 120));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 180, 0));
		$this->addFrontSystem(new LightLaser(2, 4, 3, 0, 180));

		$this->addAftSystem(new Hangar(3, 4));
		$this->addAftSystem(new Engine(3, 9, 0, 6, 4));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 300));
		$this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 3, 2));
	
        $this->addPrimarySystem(new Structure( 4, 64));
        
        $this->hitChart = array(
        		0=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "thruster",
        				8 => "thruster",
        				9 => "thruster",
        				10 => "thruster",
        				11 => "thruster",
        				12 => "thruster",
        				13 => "thruster",
        				14 => "thruster",
        				15 => "scanner",
        				16 => "scanner",
        				17 => "scanner",
        				18 => "reactor",
        				19 => "reactor",
        				20 => "reactor",
        		),
        		1=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "Cargo Bay A",
        				6 => "Cargo Bay A",
        				7 => "Cargo Bay B",
        				8 => "Cargo Bay B",
        				9 => "Cargo Bay C",
        				10 => "Cargo Bay C",
        				11 => "Standard Particle Beam",
        				12 => "C&C",
        				13 => "Light Laser",
        				14 => "Light Laser",
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
        				5 => "Cargo Bay D",
        				6 => "Cargo Bay D",
        				7 => "Cargo Bay E",
        				8 => "Cargo Bay E",
        				9 => "Cargo Bay F",
        				10 => "Cargo Bay F",
        				11 => "Standard Particle Beam",
        				12 => "engine",
        				13 => "hangar",
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
