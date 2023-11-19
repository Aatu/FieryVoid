<?php
class gaimRafaPulse extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 225;
        $this->faction = "Gaim Intelligence";
	$this->phpclass = "gaimRafaPulse";
	$this->shipClass = "Rafa Gunboat (Pulse)";
        $this->variantOf = "Rafa Gunboat (Scattergun)";		
		$this->occurence = "common";	
	$this->imagePath = "img/ships/GaimRafa.png";
	$this->canvasSize = 75;

	$this->agile = true;

	$this->forwardDefense = 10;
	$this->sideDefense = 11;

	$this->isd = 2250;

	$this->turncost = 0.25;
	$this->turndelaycost = 0.25;
	$this->accelcost = 1;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 14 *5;

	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

	$this->addPrimarySystem(new Reactor(4, 9, 0, 0));
	$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(4, 12, 2, 4);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(4, 11, 0, 6, 1));
	$this->addPrimarySystem(new Bulkhead(0, 1));
	$this->addPrimarySystem(new Bulkhead(0, 1));

	//NOT moving to front due to Bulkhead interaction!
	$this->addPrimarySystem(new MediumPulse(3, 6, 3, 300, 60));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 180, 360));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 180));

	$this->addPrimarySystem(new Structure( 5, 30));

        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "0:Medium Pulse Cannon",
        				16 => "0:Standard Particle Beam",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				11 => "Structure",
        				13 => "0:Medium Pulse Cannon",
        				16 => "0:Standard Particle Beam",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				11 => "Structure",
        				13 => "0:Medium Pulse Cannon",
        				16 => "0:Standard Particle Beam",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart


    }
}
?>
