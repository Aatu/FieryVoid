<?php
class ShipCarrierCube extends BaseShip{
    /*Ipsha general:
     - remember about EM hardening!
     - instead of -2 bonus to dropout/crit when caused by Ion weapon, just add -1 overall crit/dropout bonus
     - remind player of the above in comments in fleet selection phase!
     - Singularity Drive replaced by standard engine
     - add Sensor power demand to Reactor output (it's considered 'baseline' for tabletop!)
    */
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 800;
	$this->faction = "Ipsha";
        $this->phpclass = "ShipCarrierCube";
        //$this->imagePath = "img/ships/IpshaCube.png";    
        $this->imagePath = "img/ships/IpshaBorgCube.png";
        $this->shipClass = "Carrier Cube";    
	    	    
        $this->shipSizeClass = 3;
        $this->fighters = array("heavy"=>12+12);
	        
	    //$this->limited = 33;
	    $this->isd = 2230;
	    $this->notes = 'EM hardened';	  
	    $this->EMHardened = true; //EM Hardening - some weapons would check for this value!
	    $this->critRollMod = -1; //generalbonus to critical rolls!
		
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->gravitic = true;
        
	$this->iniativebonus = 0 *5;
	    
	    
       
	$this->addPrimarySystem(new MagGravReactor(4, 23, 0, 30+4));
	$this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 4, 6));
        $this->addPrimarySystem(new Engine(4, 30, 0, 6, 4));
        //$this->addPrimarySystem(new Hangar(4, 6));
	$this->addPrimarySystem(new SparkField(4, 0, 0, 0, 360));	
	$this->addPrimarySystem(new SparkField(4, 0, 0, 0, 360));
        
      

	$this->addFrontSystem(new ResonanceGenerator(4, 0, 0, 270, 360));
        $this->addFrontSystem(new EmPulsar(3, 0, 0, 270, 90));
	$this->addFrontSystem(new EmPulsar(3, 0, 0, 270, 90));
	$this->addFrontSystem(new ResonanceGenerator(4, 0, 0, 0, 90));
        $this->addFrontSystem(new MagGraviticThruster(4, 13, 0, 99, 1));
        $this->addFrontSystem(new MagGraviticThruster(4, 13, 0, 99, 1));
		
	    
	$this->addAftSystem(new ResonanceGenerator(4, 0, 0, 180, 270));
        $this->addAftSystem(new EmPulsar(3, 0, 0, 90, 270));
	$this->addAftSystem(new EmPulsar(3, 0, 0, 90, 270));
	$this->addAftSystem(new ResonanceGenerator(4, 0, 0, 90, 180));
        $this->addAftSystem(new MagGraviticThruster(4, 15, 0, 99, 2));
        $this->addAftSystem(new MagGraviticThruster(4, 15, 0, 99, 2)); 
	    
	    
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 210, 330));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 210, 330));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 210, 330));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 210, 330));
	$this->addLeftSystem(new Hangar(4, 12));
	$this->addLeftSystem(new MagGraviticThruster(4, 15, 0, 99, 3));
	    

	$this->addRightSystem(new SurgeCannon(3, 0, 0, 30, 150));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 30, 150));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 30, 150));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 30, 150));	
	$this->addRightSystem(new Hangar(4, 12));    
        $this->addRightSystem(new MagGraviticThruster(4, 15, 0, 99, 4));
	    
		
		
        $this->addFrontSystem(new Structure(4, 45));
        $this->addAftSystem(new Structure(4, 45));
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(4, 60));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				12 => "Spark Field",
				14 => "Scanner",
				16 => "Engine",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				8 => "Resonance Generator",
				12 => "EM Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				4 => "Thruster",
				8 => "Resonance Generator",
				12 => "EM Pulsar",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				8 => "Surge Cannon",
				10 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				8 => "Surge Cannon",
				10 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}
?>
