<?php
class DalithornGunboatRefit extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 195;
        $this->faction = "Nexus Dalithorn Commonwealth";
        $this->phpclass = "DalithornGunboatRefit";
        $this->imagePath = "img/ships/Nexus/Dalithorn_Gunboat2.png";
		$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Gunboat (2150)";
			$this->variantOf = "Gunboat";
			$this->occurence = "common";
		$this->unofficial = true;
			$this->isd = 2150;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
		$this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.25;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14*5;
 
		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
		$this->addPrimarySystem(new Reactor(4, 9, 0, 0));
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
//        $this->addPrimarySystem(new AntiquatedScanner(3, 10, 2, 5));
    	$sensors = new Scanner(3, 10, 2, 5);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(4, 13, 0, 7, 2));

		$this->addFrontSystem(new NexusMinigun(2, 4, 1, 180, 60));
		$this->addFrontSystem(new NexusGasGun(2, 7, 2, 300, 60));
		$this->addFrontSystem(new NexusMinigun(2, 4, 1, 300, 180));
	    
        $this->addPrimarySystem(new Structure(4, 32));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "1:Minigun",
        				15 => "1:Gas Gun",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Minigun",
        				15 => "1:Gas Gun",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "Structure",
        				13 => "1:Minigun",
        				15 => "1:Gas Gun",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
