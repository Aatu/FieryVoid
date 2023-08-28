<?php
class CircasianRotarraParticle extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 150;
        $this->faction = "ZEscalation Circasian Empire";
	$this->phpclass = "CircasianRotarraParticle";
	$this->shipClass = "Rotarra Police Corvette (Particle)";
			$this->variantOf = "Rotarra Police Corvette (Railgun)";
			$this->occurence = "common";
			$this->unofficial = true;
	$this->imagePath = "img/ships/EscalationWars/CircasianRotarra.png";
	$this->canvasSize = 75;

	$this->agile = true;

	$this->forwardDefense = 10;
	$this->sideDefense = 10;

	$this->isd = 1936;

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

	$this->addPrimarySystem(new Reactor(3, 8, 0, 0));
	$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(3, 8, 4, 3);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(3, 7, 0, 5, 1));

	$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
	$this->addFrontSystem(new EWRocketLauncher(1, 4, 1, 240, 60));
	$this->addFrontSystem(new EWRocketLauncher(1, 4, 1, 300, 120));

	$this->addPrimarySystem(new Structure( 3, 30));

        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "1:Light Particle Beam",
						15 => "1:Rocket Launcher",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				11 => "Structure",
        				13 => "1:Light Particle Beam",
						15 => "1:Rocket Launcher",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				11 => "Structure",
        				13 => "1:Light Particle Beam",
						15 => "1:Rocket Launcher",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart


    }
}
?>
