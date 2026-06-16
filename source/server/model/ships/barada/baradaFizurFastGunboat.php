<?php
class baradaFizurFastGunboat extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 200;
    $this->faction = "Barada Imperium";
	$this->phpclass = "baradaFizurFastGunboat";
	$this->shipClass = "Fizur Fast Gunboat";
	$this->imagePath = "img/ships/baradaFizurFastGunboat.png";
	$this->canvasSize = 120;
	$this->agile = true;

	$this->forwardDefense = 9;
	$this->sideDefense = 11;
	$this->isd = 2229;

	$this->turncost = 0.25;
	$this->turndelaycost = 0.25;
	$this->accelcost = 1;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 14 *5;
  
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
	$this->addPrimarySystem(new Reactor(4, 10, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	$sensors = new Scanner(4, 9, 2, 4);
	$sensors->markLCV();
	$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(4, 13, 0, 8, 1));


	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 270, 90));
    $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 270, 90));
    $this->addFrontSystem(new TwinArray(2, 4, 1, 0, 0, 360));

	$this->addPrimarySystem(new Structure(4, 30));
  
        $this->hitChart = array(
        		0=> array( //should never happen (...but actually sometimes does!)
        				10 => "Structure",
						12 => "1:Standard Particle Beam",
        				15 => "1:Twin Particle Gun",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				10 => "Structure",
						12 => "1:Standard Particle Beam",
        				15 => "1:Twin Particle Gun",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				10 => "Structure",
						12 => "1:Standard Particle Beam",
        				15 => "1:Twin Particle Gun",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
				),
        		
        ); //end of hit chart
    }
}
?>
