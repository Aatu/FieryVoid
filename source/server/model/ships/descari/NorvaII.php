<?php
class NorvaII extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 200;
        $this->faction = "Descari";
	$this->phpclass = "NorvaII";
	$this->shipClass = "Norva II Gunboat";

	$this->imagePath = "img/ships/DescariNorva.png";
	$this->canvasSize = 200;

	$this->agile = true;

	$this->forwardDefense = 9;
	$this->sideDefense = 11;


	$this->variantOf = "Norva I Gunboat";
	$this->occurence = "common";
	$this->isd = 2247;


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
	    	$sensors = new Scanner(3, 12, 3, 4);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(3, 9, 0, 8, 2));

	$this->addPrimarySystem(new MediumPlasmaBolter(3, 0, 0, 300, 60));
	$this->addPrimarySystem(new LightPlasmaBolter(3, 0, 0, 300, 60));
	$this->addPrimarySystem(new LightPlasmaBolter(3, 0, 0, 300, 60));


	$this->addPrimarySystem(new Structure( 4, 36));



        $this->hitChart = array(
        		0=> array( 
        				9 => "Structure",
        				11 => "Medium Plasma Bolter",
        				14 => "Light Plasma Bolter",
        				17 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				9 => "Structure",
        				11 => "0:Medium Plasma Bolter",
        				14 => "0:Light Plasma Bolter",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				9 => "Structure",
        				11 => "0:Medium Plasma Bolter",
        				14 => "0:Light Plasma Bolter",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart


    }
}
?>
