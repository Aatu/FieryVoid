<?php
class ShipTetraship extends MediumShipLeftRight{
    /*Ipsha general:
     - remember about EM hardening!
     - instead of -2 bonus to dropout/crit when caused by Ion weapon, just add -1 overall crit/dropout bonus
     - remind player of the above in comments in fleet selection phase!
     - Singularity Drive replaced by standard engine
     - add Sensor power demand to Reactor output (it's considered 'baseline' for tabletop!)
    */
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "Ipsha";
        $this->phpclass = "ShipTetraship";
        //$this->imagePath = "img/ships/IpshaTetra.png";    
        $this->imagePath = "img/ships/IpshaBorgTetra.png";
        	$this->canvasSize = 100;
        $this->shipClass = "Tetraship";    
	    	    
        //$this->shipSizeClass = 1;
        //$this->fighters = array("heavy"=>6);
	        
	//$this->limited = 33;
	$this->isd = 2230;
	$this->notes = 'EM hardened';	  
	$this->EMHardened = true; //EM Hardening - some weapons would check for this value!
	$this->critRollMod = -1; //generalbonus to critical rolls!
		
        $this->forwardDefense = 14;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1; // 1/2 by original SCS; I gave it just 1 but +2 base thrust
        $this->rollcost = 0;
        $this->pivotcost = 1;
	$this->gravitic = true;
        
	$this->iniativebonus = 12 *5;
	    
	    
        

	$this->addPrimarySystem(new MagGravReactor(3, 15, 0, 12+4));
	$this->addPrimarySystem(new CnC(3, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 5));
        $this->addPrimarySystem(new Engine(3, 16, 0, 5, 2));
        //$this->addPrimarySystem(new Hangar(4, 6));	
	$this->addPrimarySystem(new SurgeBlaster(3, 0, 0, 300, 60));
        $this->addPrimarySystem(new MagGraviticThruster(4, 15, 0, 99, 1)); 
        $this->addPrimarySystem(new MagGraviticThruster(3, 10, 0, 99, 2));
        $this->addPrimarySystem(new MagGraviticThruster(3, 10, 0, 99, 2));	    
	    
	$this->addLeftSystem(new EmPulsar(3, 0, 0, 180, 360));
	$this->addLeftSystem(new EmPulsar(3, 0, 0, 180, 360));
	$this->addLeftSystem(new MagGraviticThruster(3, 13, 0, 99, 3));
		
	$this->addRightSystem(new EmPulsar(3, 0, 0, 0, 180));
	$this->addRightSystem(new EmPulsar(3, 0, 0, 0, 180));
        $this->addRightSystem(new MagGraviticThruster(3, 13, 0, 99, 4));
		
		
        $this->addPrimarySystem(new Structure(3, 42));
		
		
		$this->hitChart = array(
			0=> array(
				6 => "Thruster",
				9 => "Surge Blaster",
				12 => "Scanner",
				15 => "Engine",
				18 => "Reactor",
				20 => "C&C",
			),
			3=> array(
				4 => "Thruster",
				7 => "EM Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				7 => "EM Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}

?>
