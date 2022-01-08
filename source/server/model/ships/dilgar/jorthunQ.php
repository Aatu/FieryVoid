<?php
class JorthunQ extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 250;
    $this->faction = "Dilgar";
	$this->phpclass = "JorthunQ";
	$this->shipClass = "Jorthun-Q Patrol Cutter (Bolter)";
			$this->occurence = "common";
			$this->variantOf = "Jorthun-A Patrol Cutter (Plasma)";
	$this->imagePath = "img/ships/jorthun.png";
	$this->canvasSize = 90;
	$this->agile = true;

	$this->unofficial = true;

	$this->forwardDefense = 10;
	$this->sideDefense = 10;
	$this->isd = 2227;

	$this->turncost = 0.25;
	$this->turndelaycost = 0.25;
	$this->accelcost = 1;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 14 *5;
  
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
	$this->addPrimarySystem(new Reactor(2, 9, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(2, 9, 3, 4);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(1, 11, 0, 6, 2));
	
	$this->addFrontSystem(new LightBolter(1, 6, 2, 240, 60));
	$this->addFrontSystem(new LightBolter(1, 6, 2, 270, 90));
	$this->addFrontSystem(new LightBolter(1, 6, 2, 300, 120));
	
	$this->addPrimarySystem(new Structure(3, 26));
  
        $this->hitChart = array(
        		0=> array( //should never happen (...but actually sometimes does!)
        				12 => "Structure",
        				16 => "1:Light Bolter",
        				18 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				12 => "0:Structure",
        				16 => "1:Light Bolter",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				12 => "0:Structure",
        				16 => "1:Light Bolter",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
				),
        		
        ); //end of hit chart
    }
}
?>
