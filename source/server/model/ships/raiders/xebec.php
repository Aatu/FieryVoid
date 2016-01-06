<?php
class Xebec extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
    	$this->pointCost = 300;
    	$this->faction = "Raiders";
        $this->phpclass = "Xebec";
        $this->imagePath = "img/ships/xebec.png";
        $this->shipClass = "Xebec";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
    	$this->iniativebonus = 30;
         
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 240, 60));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 120));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 180));

        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new Engine(3, 18, 0, 12, 2));
        $this->addPrimarySystem(new CnC(4, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 5));
        $this->addPrimarySystem(new Hangar(3, 2));
    	$this->addPrimarySystem(new Thruster(3, 15, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 15, 0, 5, 4));
		
        $temp1 = new CargoBay(2, 18);
        $temp2 = new CargoBay(2, 18);
        $temp1->displayName = "Cargo Bay A";
        $temp2->displayName = "Cargo Bay B";
        
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));

        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 360));
        $this->addAftSystem($temp1);
        $this->addAftSystem($temp2);
	
        $this->addPrimarySystem(new Structure( 4, 48));
        
        $this->hitChart = array(
        		0=> array( //Possible issue, Primary hit chart specifically calls for Port/Stb Thrust, this will select any thruster, if this is consistant among medium ships we might be able to do a check when thruster is damaged.
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
        				4 => "thruster",
        				5 => "thruster",
        				6 => "Medium Laser",
        				7 => "Medium Laser",
        				8 => "Medium Laser",
        				9 => "Standard Particle Beam",
        				10 => "Standard Particle Beam",
        				11 => "Standard Particle Beam",
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
        				6 => "cargo Bay A",
        				7 => "cargo Bay A",
        				8 => "cargo Bay A",
        				9 => "cargo Bay B",
        				10 => "cargo Bay B",
        				11 => "cargo Bay B",
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
