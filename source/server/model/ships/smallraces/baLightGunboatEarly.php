<?php
class BALightGunboatEarly extends LCV{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 195;
        $this->faction = "Small Races";
        $this->phpclass = "BALightGunboatEarly";
        $this->imagePath = "img/ships/BALightGunboat.png";
        $this->shipClass = "BA Light Gunboat (early)";
			$this->occurence = "common";
			$this->variantOf = 'BA Light Gunboat';

		$this->canvasSize = 100;
		$this->agile = true;
        $this->isd = 2192;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.25;
        $this->turndelaycost = 0.25;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 70;
 
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
		$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
		$this->addPrimarySystem(new Reactor(4, 11, 0, 0));
		$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	    	$sensors = new Scanner(4, 12, 3, 5);
		    $sensors->markLCV();
		    $this->addPrimarySystem($sensors);
		$this->addPrimarySystem(new Engine(4, 11, 0, 4, 1));

		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 60));
		$this->addFrontSystem(new MedBlastCannon(3, 0, 0, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 180));
		$this->addFrontSystem(new BAInterceptorPrototype(2, 0, 0, 0, 360));

		$this->addPrimarySystem(new Structure( 5, 30));
  
        $this->hitChart = array(
        		0=> array( //should never happen
        				10 => "Structure",
        				12 => "1:Medium Blast Cannon",
        				15 => "1:Standard Particle Beam",
        				17 => "1:BA Interceptor Prototype",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				10 => "Structure",
        				12 => "1:Medium Blast Cannon",
        				15 => "1:Standard Particle Beam",
        				17 => "1:BA Interceptor Prototype",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				10 => "Structure",
        				12 => "1:Medium Blast Cannon",
        				15 => "1:Standard Particle Beam",
        				17 => "1:BA Interceptor Prototype",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart
    }
}
?>
