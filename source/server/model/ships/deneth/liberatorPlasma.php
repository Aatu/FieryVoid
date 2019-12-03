<?php
class LiberatorPlasma extends LCV{
	/*Deneth Liberator LCV, from Raiders-2*/

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 195;
        $this->faction = "Deneth";
	$this->phpclass = "LiberatorPlasma";
	$this->shipClass = "Liberator Plasma Gunboat";

	$this->imagePath = "img/ships/jenas.png";
	$this->canvasSize = 100;

	$this->agile = true;


	$this->forwardDefense = 10;
	$this->sideDefense = 11;

	//$this->limited = 33;
	//$this->occurence = "rare";
	$this->isd = 2226;
	//$this->unofficial = true;

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
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(4, 12, 3, 4);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(4, 13, 0, 6, 1));

	$this->addPrimarySystem(new TwinArray(2, 6, 2, 180, 0));
	$this->addPrimarySystem(new MediumPlasma(3, 5, 3, 300, 60));
	$this->addPrimarySystem(new TwinArray(2, 6, 2, 0, 180));


	$this->addPrimarySystem(new Structure( 5, 31));



        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "Medium Plasma Cannon",
        				16 => "Twin Array",
        				18 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				11 => "Structure",
        				13 => "0:Medium Plasma Cannon",
        				16 => "0:Twin Array",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				11 => "Structure",
        				13 => "0:Medium Plasma Cannon",
        				16 => "0:Twin Array",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart


    }
}
?>
