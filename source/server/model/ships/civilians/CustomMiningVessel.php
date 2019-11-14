<?php
class CustomMiningVessel extends MediumShip{
    /*LCV, approximated as MCV*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 80;
		$this->faction = "Civilians";
        $this->phpclass = "CustomMiningVessel";
        $this->imagePath = "img/ships/sloop.png";
        $this->shipClass = "Mining Vessel";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.25;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 4;
    	$this->iniativebonus = 5 *5; //much reduced from regular LCV due to civilian crew and control systems
      
		$this->unofficial = true;
		$this->isd = 2000;
         
       
      $this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
      $this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
      $this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
      $this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance

      $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
      $this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
      $this->addPrimarySystem(new Scanner(3, 6, 1, 1));
      $this->addPrimarySystem(new Engine(2, 11, 0, 4, 4));
	$this->addPrimarySystem(new CargoBay(0, 5));
	$this->addPrimarySystem(new CargoBay(0, 5));
      $this->addPrimarySystem(new CustomMiningCutter(1, 0, 0, 240, 120));
      $this->addPrimarySystem(new CustomIndustrialGrappler(2, 0, 0, 330, 30));
      $this->addPrimarySystem(new CustomIndustrialGrappler(2, 0, 0, 330, 30));
	    
      $this->addPrimarySystem(new Structure( 2, 25));
        
        $this->hitChart = array(
        		0=> array( //should never happen
        				10 => "Structure",
        				11 => "Mining Cutter",
        				13 => "Industrial Grappler",
        				16 => "Cargo Bay",
        				18 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				10 => "Structure",
        				11 => "0:Mining Cutter",
        				13 => "0:Industrial Grappler",
        				16 => "0:Cargo Bay",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				10 => "Structure",
        				11 => "0:Mining Cutter",
        				13 => "0:Industrial Grappler",
        				16 => "0:Cargo Bay",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart
    }
}
?>
