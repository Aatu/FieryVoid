<?php
class LshonRefit extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 150;
        $this->faction = "Raiders";
	$this->phpclass = "LshonRefit";
	$this->shipClass = "Narn Privateer L'Shon Gunboat (2244 refit)";
			$this->occurence = "common";
			$this->variantOf = "Narn Privateer L'Shon Gunboat";
	$this->imagePath = "img/ships/dsharda.png";
	$this->canvasSize = 55;

	$this->notes = "Used only by Narn privateers";
	
	$this->forwardDefense = 10;
	$this->sideDefense = 12;
	$this->isd = 2244;

	$this->turncost = 0.33;
	$this->turndelaycost = 0.33;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 8 *5;
  
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
	$this->addPrimarySystem(new Reactor(2, 5, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(2, 12, 2, 3);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(2, 6, 0, 4, 1));

	$this->addFrontSystem(new LightPulse(1, 4, 2, 300, 360));
	$this->addFrontSystem(new MediumPulse(2, 6, 3, 300, 60));
	$this->addFrontSystem(new LightPulse(1, 4, 2, 0, 60));

	$this->addPrimarySystem(new Structure(2, 28));
  
        $this->hitChart = array(
        		0=> array( //should never happen (...but actually sometimes does!)
        				11 => "Structure",
        				13 => "1:Medium Pulse Cannon",
        				16 => "1:Light Pulse Cannon",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				11 => "Structure",
        				13 => "1:Medium Pulse Cannon",
        				16 => "1:Light Pulse Cannon",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				11 => "Structure",
        				13 => "1:Medium Pulse Cannon",
        				16 => "1:Light Pulse Cannon",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
				),
        		
        ); //end of hit chart
    }
}
?>
