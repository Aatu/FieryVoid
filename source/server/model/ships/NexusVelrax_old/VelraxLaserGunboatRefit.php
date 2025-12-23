<?php
class VelraxLaserGunboatRefit extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 175;
        $this->faction = "Nexus Velrax Republic (early)";
        $this->phpclass = "VelraxLaserGunboatRefit";
        $this->imagePath = "img/ships/Nexus/velraxGunboat.png";
			$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Liviss Gunboat (2061)";
			$this->variantOf = "Nashran Gunboat";
			$this->occurence = "common";
		$this->unofficial = true;
			$this->isd = 2061;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'Atmospheric Capable.';
	    $this->notes .= '<br>May deploy independently.';
        
        $this->forwardDefense = 8;
        $this->sideDefense = 13;
        
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
  
		$this->addPrimarySystem(new Reactor(2, 9, 0, 0));
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
    	$sensors = new Scanner(2, 7, 2, 4);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
//        $this->addPrimarySystem(new AntiquatedScanner(2, 7, 2, 4));
		$this->addPrimarySystem(new Engine(3, 15, 0, 6, 2));

		$this->addFrontSystem(new NexusIonGun(2, 2, 2, 240, 60));
		$this->addFrontSystem(new NexusLaserSpear(2, 5, 3, 300, 60));
		$this->addFrontSystem(new NexusIonGun(2, 2, 2, 300, 120));
	    
        $this->addPrimarySystem(new Structure(3, 32));
	    
        $this->hitChart = array(
        		0=> array( 
        				10 => "Structure",
        				13 => "1:Laser Spear",
        				15 => "1:Ion Gun",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				10 => "Structure",
        				13 => "1:Laser Spear",
        				15 => "1:Ion Gun",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				10 => "Structure",
        				13 => "1:Laser Spear",
        				15 => "1:Ion Gun",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
