<?php
class NorvaI extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 150;
        $this->faction = "Descari";
	$this->phpclass = "NorvaI";
	$this->shipClass = "Norva I Gunboat";

	$this->imagePath = "img/ships/DescariNorva.png";
	$this->canvasSize = 80;

	$this->agile = true;

	$this->forwardDefense = 10;
	$this->sideDefense = 12;


	//$this->occurence = "rare";
	$this->isd = 2220;
	//$this->variantOf = "Liberator Plasma Gunboat";

	$this->turncost = 0.33;
	$this->turndelaycost = 0.33;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 2;
	$this->iniativebonus = 14 *5;


	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance


	$this->addPrimarySystem(new Reactor(3, 8, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(3, 10, 2, 3);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(3, 7, 0, 6, 2));

	$this->addPrimarySystem(new MediumPlasma(3, 5, 3, 300, 60));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));


	$this->addPrimarySystem(new Structure( 4, 30));



        $this->hitChart = array(
        		0=> array( 
        				9 => "Structure",
        				11 => "Medium Plasma Cannon",
        				15 => "Light Particle Beam",
        				17 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				9 => "Structure",
        				11 => "0:Medium Plasma Cannon",
        				15 => "0:Light Particle Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				9 => "Structure",
        				11 => "0:Medium Plasma Cannon",
        				15 => "0:Light Particle Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart


    }
}
?>
