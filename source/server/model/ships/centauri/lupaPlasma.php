<?php
class lupaPlasma extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 170;
    $this->faction = "Centauri";
	$this->phpclass = "lupaPlasma";
	$this->shipClass = "Lupa Attack Boat (Plasma)";
        $this->variantOf = "Lupa Attack Boat (Escort)";
        $this->occurence = "common";
	$this->imagePath = "img/ships/lupa.png";
	$this->canvasSize = 90;
	$this->agile = true;

	$this->forwardDefense = 11;
	$this->sideDefense = 11;
	$this->isd = 2153;

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
  
	$this->addPrimarySystem(new Reactor(4, 10, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(4, 12, 4, 5);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(4, 8, 0, 6, 1));
	$this->addPrimarySystem(new TwinArray(3, 6, 2, 240, 60));
	$this->addPrimarySystem(new TwinArray(3, 6, 2, 300, 120));
	$this->addPrimarySystem(new MediumPlasma(3, 5, 3, 270, 90));
	$this->addPrimarySystem(new Structure(4, 30));
  
        $this->hitChart = array(
        		0=> array( //should never happen (...but actually sometimes does!)
        				10 => "Structure",
						12 => "Medium Plasma Cannon",
        				15 => "Twin Array",
        				17 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				10 => "0:Structure",
						12 => "0:Medium Plasma Cannon",
        				15 => "0:Twin Array",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				10 => "0:Structure",
						12 => "0:Medium Plasma Cannon",
        				15 => "0:Twin Array",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
				),
        		
        ); //end of hit chart
    }
}
?>
