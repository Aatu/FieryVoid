<?php
class CircasianFeilah extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 175;
        $this->faction = "ZEscalation Circasian Empire";
	$this->phpclass = "CircasianFeilah";
	$this->shipClass = "Feilah Gunboat";
			$this->unofficial = true;
	$this->imagePath = "img/ships/EscalationWars/CircasianFeilah.png";
	$this->canvasSize = 75;

	$this->agile = true;

	$this->forwardDefense = 10;
	$this->sideDefense = 10;


	$this->isd = 1944;

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


	$this->addPrimarySystem(new Reactor(3, 9, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(3, 8, 4, 3);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(3, 9, 0, 5, 1));

	$this->addPrimarySystem(new LightPlasma(2, 4, 2, 270, 90));
	$this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 240, 360));
	$this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 0, 120));


	$this->addPrimarySystem(new Structure( 3, 21));



        $this->hitChart = array(
        		0=> array( 
        				10 => "Structure",
        				12 => "Light Plasma Cannon",
        				15 => "Light Particle Beam",
        				17 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				10 => "Structure",
        				12 => "0:Light Plasma Cannon",
        				15 => "0:Light Particle Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				10 => "Structure",
        				12 => "0:Light Plasma Cannon",
        				15 => "0:Light Particle Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart


    }
}
?>
