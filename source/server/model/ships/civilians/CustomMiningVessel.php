<?php
/*NOT FINISHED YET*/

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

      $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
      $this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
      $this->addPrimarySystem(new Scanner(4, 12, 3, 4));
      $this->addPrimarySystem(new Engine(4, 13, 0, 6, 1));
      $this->addPrimarySystem(new TwinArray(2, 6, 2, 180, 0));
      $this->addPrimarySystem(new ParticleCannon(3, 8, 7, 300, 60));
      $this->addPrimarySystem(new TwinArray(2, 6, 2, 0, 180));
      $this->addPrimarySystem(new Structure( 5, 31));
        
        $this->hitChart = array(
        		0=> array( //should never happen
        				20 => "Structure",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				11 => "Structure",
        				13 => "0:Particle Cannon",
        				16 => "0:Twin Array",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				11 => "Structure",
        				13 => "0:Particle Cannon",
        				16 => "0:Twin Array",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart
    }
}
?>
