<?php
class Terillon extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 425;
		$this->faction = "Llort";
        $this->phpclass = "Terillon";
        $this->imagePath = "img/ships/LlortTerillon.png";
        $this->shipClass = "Terillon Patrol Frigate";
        $this->agile = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 6));
        $this->addPrimarySystem(new Engine(4, 12, 0, 8, 2));
	$this->addPrimarySystem(new Hangar(4, 2,0));
	$this->addPrimarySystem(new Thruster(2, 5, 0, 2, 3));
	$this->addPrimarySystem(new Thruster(2, 5, 0, 2, 3));
	$this->addPrimarySystem(new Thruster(2, 5, 0, 2, 3));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 4));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 4));
		        		
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
	$this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new ScatterGun(3, 8, 3, 180, 60));
        $this->addFrontSystem(new ParticleCannon(4, 8, 7, 240, 60));
        $this->addFrontSystem(new MediumBolter(3, 8, 4, 300, 120));
        $this->addFrontSystem(new MediumBolter(3, 8, 4, 300, 120));

	$this->addAftSystem(new Thruster(2, 5, 0, 1, 2));
	$this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
	$this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new ScatterGun(3, 8, 3, 0, 240));        
               
        $this->addPrimarySystem(new Structure(4, 55));
		
        $this->hitChart = array (
        		0=> array (
        				9=>"Thruster",
        				12=>"Scanner",
        				15=>"Engine",
        				17=>"Hangar",
        				19=>"Reactor",
        				20=>"C&C",
        		),
        		1=> array (
        				5=>"Thruster",
        				6=>"Particle Cannon",
					8=>"Medium Bolter",
					9=>"Scattergun",
        				17=>"Structure",
        				20=>"Primary",
        		),
        		2=> array(
        				6=>"Thruster",
        				8=>"Scattergun",
        				17=>"Structure",
        				20=>"Primary",
        		),
        );
    }
}
?>
