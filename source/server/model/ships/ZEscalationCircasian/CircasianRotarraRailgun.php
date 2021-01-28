<?php
class CircasianRotarraRailgun extends LCV{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 140;
        $this->faction = "ZEscalation Circasian";
	$this->phpclass = "CircasianRotarraRailgun";
	$this->shipClass = "Rotarra Police Corvette (Railgun)";
			$this->unofficial = true;
	$this->imagePath = "img/ships/EscalationWars/CircasianRotarra.png";
	$this->canvasSize = 75;

	$this->agile = true;

	$this->forwardDefense = 10;
	$this->sideDefense = 10;

	$this->isd = 1890;

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
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(3, 8, 4, 3);
		$sensors->markLCV();
		$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new Engine(3, 7, 0, 4, 1));

	$this->addPrimarySystem(new NexusUltralightRailgun(1, 3, 2, 270, 90));
	$this->addPrimarySystem(new EWRocketLauncher(1, 4, 1, 240, 60));
	$this->addPrimarySystem(new EWRocketLauncher(1, 4, 1, 300, 120));


	$this->addPrimarySystem(new Structure( 3, 30));



        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "Ultralight Railgun",
						15 => "Rocket Launcher",
        				17 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				11 => "Structure",
        				13 => "0:Ultralight Railgun",
						15 => "0:Rocket Launcher",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				11 => "Structure",
        				13 => "0:Ultralight Railgun",
						15 => "0:Rocket Launcher",
        				17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart


    }
}
?>
