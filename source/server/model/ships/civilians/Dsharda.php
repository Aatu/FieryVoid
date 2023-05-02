<?php
class Dsharda extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 60;
	$this->faction = "Civilians";
	$this->phpclass = "Dsharda";
	$this->shipClass = "Narn D'Sharda Stock Freighter";
	$this->imagePath = "img/ships/NarnDsharda.png";
	$this->canvasSize = 70;

	$this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
	
	$this->forwardDefense = 10;
	$this->sideDefense = 12;
	$this->isd = 2216;

	$this->turncost = 0.33;
	$this->turndelaycost = 0.33;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 4 *5;
  
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
	$this->addPrimarySystem(new Reactor(2, 3, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(2, 6, 1, 2);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(2, 6, 0, 4, 2));

	$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 300, 360));
	$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 0, 60));
	
	$this->addPrimarySystem(new CargoBay(1, 16));		

	$this->addPrimarySystem(new Structure(2, 28));
  
        $this->hitChart = array(
        		0=> array( //should never happen (...but actually sometimes does!)
        				6 => "0:Structure",
        				12 => "Cargo Bay",
        				15 => "1:Light Particle Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				6 => "Structure",
        				12 => "0:Cargo Bay",
        				15 => "1:Light Particle Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				6 => "Structure",
        				12 => "0:Cargo Bay",
        				15 => "1:Light Particle Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
				),
        		
        ); //end of hit chart
    }
}
?>
