<?php
class SshelathTolsa extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 125;
        $this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathTolsa";
        $this->imagePath = "img/ships/EscalationWars/SshelathTolsa.png";
			$this->canvasSize = 55; //img has 200px per side
        $this->shipClass = "Tolsa Corvette";
		$this->unofficial = true;
			$this->isd = 1910;

	    $this->notes = 'Mst-as Faction';
	    $this->notes .= '<br>Light missiles only';
	    $this->notes .= '<br>Atmospheric capable';

        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
		$this->agile = true;
		
        $this->turncost = 0.25;
        $this->turndelaycost = 0.25;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 14*5;
 
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
		$this->addPrimarySystem(new Reactor(3, 8, 0, 0));
		$this->addPrimarySystem(new CnC(99, 1, 0, 0)); //C&C should be unhittable anyway
        $this->addPrimarySystem(new AntiquatedScanner(3, 8, 4, 3));
//    	$sensors = new Scanner(3, 10, 2, 4);
//			$sensors->markLCV();
//			$this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(3, 7, 0, 4, 3));

		$this->addFrontSystem(new NexusLightLaserCutter(2, 4, 3, 300, 60));
		$this->addFrontSystem(new EWDefenseLaser(1, 2, 1, 0, 360));
		$this->addFrontSystem(new NexusLightLaserCutter(2, 4, 3, 300, 60));
	    
        $this->addPrimarySystem(new Structure(2, 26));
	    
        $this->hitChart = array(
        		0=> array( 
        				10 => "Structure",
        				13 => "1:Light Laser Cutter",
        				15 => "1:Defense Laser",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //redirect to PRIMARY
        				10 => "Structure",
        				13 => "1:Light Laser Cutter",
        				15 => "1:Defense Laser",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //redirect to PRIMARY
        				10 => "Structure",
        				13 => "1:Light Laser Cutter",
        				15 => "1:Defense Laser",
						17 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),        		
        ); //end of hit chart
    }
}
?>
