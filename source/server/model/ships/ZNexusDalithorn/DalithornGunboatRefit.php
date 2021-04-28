<?php
class DalithornGunboatRefit extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 195;
        $this->faction = "ZNexus Dalithorn";
        $this->phpclass = "DalithornGunboatRefit";
        $this->imagePath = "img/ships/Nexus/DalithornGunboat.png";
		$this->canvasSize = 55; //img has 200px per side
        $this->shipClass = "Gunboat (2150 Refit)";
			$this->variantOf = "Gunboat";
			$this->occurence = "common";
		$this->unofficial = true;
			$this->isd = 2150;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
	    $this->notes .= '<br>Antiquated Sensors.';
        
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
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
        $this->addPrimarySystem(new AntiquatedScanner(3, 10, 2, 5));
		$this->addPrimarySystem(new Engine(4, 13, 0, 7, 2));
		$this->addPrimarySystem(new NexusMinigun(2, 4, 1, 180, 60));
		$this->addPrimarySystem(new NexusGasGun(4, 7, 2, 300, 60));
		$this->addPrimarySystem(new NexusMinigun(2, 4, 1, 300, 180));
	    
	    
        $this->addPrimarySystem(new Structure(4, 34));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				13 => "Minigun",
        				16 => "Gas Gun",
						18 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "0:Structure",
        				13 => "0:Minigun",
        				16 => "0:Gas Gun",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "0:Structure",
        				13 => "0:Minigun",
        				16 => "0:Gas Gun",
						18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
