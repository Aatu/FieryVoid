<?php
class swGallofreeMTransport extends LCV{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 110;
        $this->faction = "ZStarWars";
	$this->phpclass = "swGallofreeMTransport";
	$this->shipClass = "GR-75 Gallofree Medium Transport";
        $this->imagePath = "img/starwars/GallofreeMediumTransport.png";
  
	$this->forwardDefense = 9;
	$this->sideDefense = 11;
  
	$this->unofficial = true;
	    
	    
		$this->isd = "Galactic Republic";
		$this->notes = "Primary users: common (civilian)";
	    
	    
        $this->hangarRequired = ''; //StarWars unit independence is much larger than B5, LCV-sized units in general do not need hangars
  
	$this->turncost = 0.5;
	$this->turndelaycost = 1;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 4 *5;
  
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
	$this->addPrimarySystem(new Reactor(3, 9, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
        $this->addPrimarySystem(new SWScanner(2, 5, 1, 1));
	$this->addPrimarySystem(new Engine(3, 8, 0, 5, 1));
	$this->addPrimarySystem(new CargoBay(1, 60));
	$hyperdrive = new JumpEngine(2, 6, 3, 12);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	$this->addPrimarySystem(new SWRayShield(2,6,1,1,0,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addPrimarySystem(new SWMediumLaser(1, 240, 60, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addPrimarySystem(new SWMediumLaser(1, 300, 120, 2));
	$this->addPrimarySystem(new SWMediumLaser(1, 120, 300, 2));
	$this->addPrimarySystem(new SWMediumLaser(1, 60, 240, 2));
 
	$this->addPrimarySystem(new Structure( 2, 28));
  
        $this->hitChart = array(
        		0=> array( //should never be actually used, but must be present
        				6 => "Structure",
        				7 => "Ray Shield",
	        			10 => "Medium Laser",
        				15 => "Cargo Bay",
        				16 => "Hyperdrive",
        				18 => "Engine",
        				19 => "Reactor",
        				20 => "Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				6 => "Structure",
        				7 => "0:Ray Shield",
	        			10 => "0:Medium Laser",
        				15 => "0:Cargo Bay",
        				16 => "0:Hyperdrive",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		2=> array( //same as Fwd
        				6 => "Structure",
        				7 => "0:Ray Shield",
	        			9 => "0:Medium Laser",
        				15 => "0:Cargo Bay",
        				16 => "0:Hyperdrive",
        				18 => "0:Engine",
        				19 => "0:Reactor",
        				20 => "0:Scanner",
        		),
        		
        ); //end of hit chart
    }
}
?>
