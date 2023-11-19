<?php
class DtarnEscort extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 200;
    $this->faction = "Narn Regime";
	$this->phpclass = "DtarnEscort";
	$this->shipClass = "D'Tarn Light Gunboat (Escort)";
			$this->occurence = "common";
			$this->variantOf = "D'Tarn Light Gunboat (Plasma)";
	$this->imagePath = "img/ships/trakk.png";
	$this->canvasSize = 90;
	$this->agile = true;

	$this->forwardDefense = 9;
	$this->sideDefense = 11;
	$this->isd = 2236;

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
  
	$this->addPrimarySystem(new Reactor(3, 10, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(3, 12, 3, 5);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(3, 9, 0, 8, 2));

	$this->addFrontSystem(new BurstBeam(2, 6, 3, 240, 60));
	$this->addFrontSystem(new TwinArray(3, 6, 2, 240, 120));
	$this->addFrontSystem(new BurstBeam(2, 6, 3, 300, 120));

	$this->addPrimarySystem(new Structure(3, 36));
  
        $this->hitChart = array(
        		0=> array( //should never happen (...but actually sometimes does!)
        				10 => "Structure",
        				12 => "1:Twin Array",
        				15 => "1:Burst Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				10 => "Structure",
        				12 => "1:Twin Array",
        				15 => "1:Burst Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				10 => "Structure",
        				12 => "1:Twin Array",
        				15 => "1:Burst Beam",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
				),
        		
        ); //end of hit chart
    }
}
?>
