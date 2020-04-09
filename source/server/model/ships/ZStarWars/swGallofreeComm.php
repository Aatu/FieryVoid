<?php
class swGallofreeComm extends LCV{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 200;
        $this->faction = "ZStarWars";
	$this->phpclass = "swGallofreeComm";
	$this->shipClass = "GR-75 Gallofree Communications Ship ";
        $this->imagePath = "img/starwars/GallofreeMediumTransport.png";
  
	$this->forwardDefense = 9;
	$this->sideDefense = 11;
  
	$this->unofficial = true;
	$this->occurence = "rare";
	$this->variantOf = "GR-75 Gallofree Medium Transport";
  
		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Rebel Alliance";
	    
	    
        $this->hangarRequired = ''; //StarWars unit independence is much larger than B5, LCV-sized units in general do not need hangars
        
	$this->turncost = 0.5;
	$this->turndelaycost = 0.5;
	$this->accelcost = 2;
	$this->rollcost = 1;
	$this->pivotcost = 1;
	$this->iniativebonus = 8 *5;
  
	$this->addFrontSystem(new InvulnerableThruster(99, 99, 0, 99, 1)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 3)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 2)); //unhitable and with unlimited thrust allowance
	$this->addAftSystem(new InvulnerableThruster(99, 99, 0, 99, 4)); //unhitable and with unlimited thrust allowance
  
	$this->addPrimarySystem(new Reactor(3, 9, 0, 0));
	$this->addPrimarySystem(new CnC(99, 99, 0, 0)); //C&C should be unhittable anyway
        $this->addPrimarySystem(new ElintScanner(3, 14, 4, 4)); //full ElInt Scanner, no typical SW limitation for ElInt!
	$this->addPrimarySystem(new Engine(3, 8, 0, 6, 1));
	$this->addPrimarySystem(new CargoBay(1, 40));
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
	        			9 => "Medium Laser",
        				14 => "Cargo Bay",
        				15 => "Hyperdrive",
        				17 => "Engine",
        				18 => "Reactor",
        				20 => "ELINT Scanner",
        		),
        		1=> array( //PRIMARY hit table, effectively
        				6 => "Structure",
        				7 => "0:Ray Shield",
	        			9 => "0:Medium Laser",
        				14 => "0:Cargo Bay",
        				15 => "0:Hyperdrive",
        				17 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:ELINT Scanner",
        		),
        		2=> array( //same as Fwd
        				6 => "Structure",
        				7 => "0:Ray Shield",
	        			9 => "0:Medium Laser",
        				14 => "0:Cargo Bay",
        				15 => "0:Hyperdrive",
        				17 => "0:Engine",
        				18 => "0:Reactor",
        				20 => "0:ELINT Scanner",
        		),
        		
        ); //end of hit chart
    }
}
?>
