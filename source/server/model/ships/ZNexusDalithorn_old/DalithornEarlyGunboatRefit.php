<?php
class DalithornEarlyGunboatRefit extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 155;
        $this->faction = "ZNexus Dalithorn Commonwealth (early)";
        $this->phpclass = "DalithornEarlyGunboatRefit";
        $this->imagePath = "img/ships/Nexus/Dalithorn_EarlyGunboat2.png";
		$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Early Gunboat (2038)";
			$this->variantOf = "Early Gunboat";
			$this->occurence = "common";
		$this->unofficial = true;
		$this->isd = 2038;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
		$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14*5;
 
		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
		$this->addPrimarySystem(new Reactor(3, 9, 0, 0));
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
//        $this->addPrimarySystem(new AntiquatedScanner(3, 10, 2, 4));
    	$sensors = new Scanner(3, 10, 2, 4);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(3, 9, 0, 6, 2));

		$this->addFrontSystem(new NexusShatterGun(1, 2, 1, 180, 360));
		$this->addFrontSystem(new NexusGasGun(1, 7, 2, 300, 60));
		$this->addFrontSystem(new NexusShatterGun(1, 2, 1, 0, 180));
	    
        $this->addPrimarySystem(new Structure(3, 30));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "1:Shatter Gun",
        				15 => "1:Gas Gun",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Shatter Gun",
        				15 => "1:Gas Gun",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Shatter Gun",
        				15 => "1:Gas Gun",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>