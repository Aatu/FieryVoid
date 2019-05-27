<?php
class SorithianSylusCutter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 100;
        $this->faction = "Small Races";
        $this->phpclass = "SorithianSylusCutter";
        $this->imagePath = "img/ships/BALightGunboat.png";
        $this->shipClass = "Sorithian Sylus Cutter";
        $this->occurence = "common";
        $this->isd = 2207;
	$this->canvasSize = 100;
	$this->agile = true;
	
	$this->unofficial = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        
        $this->turncost = 0.25;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 70;
    
 
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
	$this->addPrimarySystem(new Reactor(2, 6, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
	$this->addPrimarySystem(new Scanner(2, 6, 3, 2));
	$this->addPrimarySystem(new Engine(2, 6, 0, 4, 1));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 300, 360));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 330, 30));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 330, 30));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 60));  
        $this->hitChart = array(
        		0=> array( //should never happen
        				20 => "Structure",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				11 => "Structure",
        				14 => "0:Light Particle Beam",
        				16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				11 => "Structure",
        				14 => "0:Light Particle Beam",
        				16 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart
    }
}
?>
