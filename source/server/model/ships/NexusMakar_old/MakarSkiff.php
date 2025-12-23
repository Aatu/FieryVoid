<?php
class MakarSkiff extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 40;
        $this->faction = "Nexus Support Units";
        $this->phpclass = "MakarSkiff";
        $this->imagePath = "img/ships/Nexus/makar_skiff2.png";
		$this->canvasSize = 70; //img has 200px per side
        $this->shipClass = "Makar Civilian Skiff";
		$this->unofficial = true;
		$this->isd = 1852;

        $this->hangarRequired = ''; //Nexus LCVs are more independent than their B5 counterparts
	    $this->notes = 'May deploy independently.';
	    $this->notes .= '<br>Atmospheric capable';
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14*5;
 
		$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
		$this->addPrimarySystem(new Reactor(2, 6, 0, 0));
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
//        $this->addPrimarySystem(new AntiquatedScanner(3, 10, 2, 4));
    	$sensors = new Scanner(2, 7, 2, 2);
			$sensors->markLCV();
			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(1, 7, 0, 4, 3));

		$this->addFrontSystem(new CargoBay(1, 6));
		$this->addFrontSystem(new CargoBay(1, 6));
	    
        $this->addPrimarySystem(new Structure(2, 24));
	    
        $this->hitChart = array(
        		0=> array( 
        				11 => "Structure",
        				14 => "1:Cargo Bay",
						16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				11 => "Structure",
        				14 => "1:Cargo Bay",
						16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				11 => "Structure",
        				14 => "1:Cargo Bay",
						16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
