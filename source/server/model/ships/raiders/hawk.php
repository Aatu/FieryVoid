<?php
class Hawk extends MediumShipLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 210;
		$this->faction = "Raiders";
        $this->phpclass = "Hawk";
        $this->imagePath = "img/ships/drazi/stareagle.png"; 
        $this->shipClass = "Hawk Frigate";
        $this->canvasSize = 128;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 30;

		$this->addPrimarySystem(new CnC(4, 6, 0, 0));
		$this->addPrimarySystem(new LightParticleCannon(2, 6, 5, 300, 60));
		$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 1));
		$this->addPrimarySystem(new Thruster(2, 11, 0, 6, 2));
		$this->addPrimarySystem(new Scanner(4, 8, 3, 4));
		$this->addPrimarySystem(new Engine(4, 10, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(2, 1));
		$this->addPrimarySystem(new Reactor(4, 8, 0, 0));
		
		$this->addLeftSystem(new Thruster(3, 8, 0, 2, 3));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 240, 360));
		$this->addLeftSystem(new CargoBay(3, 10));
		
		$this->addRightSystem(new Thruster(3, 8, 0, 2, 4));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 120));
		$this->addRightSystem(new CargoBay(3, 10));
		
        $this->addPrimarySystem(new Structure( 3, 32));
        
        $this->hitChart = array (
        		0=> array (
        				8=>"Thruster",
        				10=>"Light Particle Cannon",
        				13=>"Scanner",
        				15=>"Engine",
        				17=>"Hangar",
        				19=>"Reactor",
        				20=>"C&C",
        		),
        		3=> array (
        				5=>"Thruster",
        				7=>"Standard Particle Beam",
        				11=>"Cargo Bay",
        				17=>"Structure",
        				20=>"Primary",
        		),
        		4=> array(
        				5=>"Thruster",
        				7=>"Standard Particle Beam",
        				11=>"Cargo Bay",
        				17=>"Structure",
        				20=>"Primary",
        		),
        );
    }
}

?>
