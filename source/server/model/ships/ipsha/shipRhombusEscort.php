<?php
class ShipRhombusEscort extends HeavyCombatVesselLeftRight{ 
	/*unofficial ship - from The Great Machine*/
    /*Ipsha general:
     - remember about EM hardening!
     - instead of -2 bonus to dropout/crit when caused by Ion weapon, just add -1 overall crit/dropout bonus
     - remind player of the above in comments in fleet selection phase!
     - Singularity Drive replaced by standard engine
     - add Sensor power demand to Reactor output (it's considered 'baseline' for tabletop!)
    */
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 385;
        $this->faction = "Ipsha";
        $this->phpclass = "ShipRhombusEscort";
        //$this->imagePath = "img/ships/IpshaRhombus.png";    
        $this->imagePath = "img/ships/IpshaBorgRhombus.png";
        $this->shipClass = "Rhombus Escort Cruiser";    
	    	    
        $this->unofficial = true;
        //$this->fighters = array("heavy"=>6);
	        
	//$this->limited = 33;
	$this->isd = 2209;
	$this->notes = 'EM hardened';	  
	$this->notes .= '<br>-1 critical roll bonus';
	$this->EMHardened = true; //EM Hardening - some weapons would check for this value!
	$this->critRollMod = -1; //generalbonus to critical rolls!
		
        $this->forwardDefense = 14;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 0;
        $this->pivotcost = 1;
	$this->gravitic = true;
        
	$this->iniativebonus = 6 *5; 
	    
	    
        
	$this->addPrimarySystem(new MagGravReactor(4, 20, 0, 20+4));
	$this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 5));
        $this->addPrimarySystem(new Engine(4, 26, 0, 6, 4));
        //$this->addPrimarySystem(new Hangar(4, 6));
	$this->addPrimarySystem(new SparkField(4, 0, 0, 0, 360));
        $this->addPrimarySystem(new MagGraviticThruster(3, 15, 0, 99, 1));
        $this->addPrimarySystem(new MagGraviticThruster(3, 15, 0, 99, 2));	    
	    
	    
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addLeftSystem(new BurstBeam(2, 6, 3, 180, 360));
	$this->addLeftSystem(new MagGraviticThruster(3, 13, 0, 99, 3));
	    
		
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addRightSystem(new BurstBeam(2, 6, 3, 0, 180));
        $this->addRightSystem(new MagGraviticThruster(3, 13, 0, 99, 4));
	    
		
		
        $this->addLeftSystem(new Structure(3, 40));
        $this->addRightSystem(new Structure(3, 40));
        $this->addPrimarySystem(new Structure(4, 40));
		
		
		$this->hitChart = array(
			0=> array( //kept in line with Ipsha standard instead of original SCS (more C&C, less weapon)
				8 => "Structure",
				10 => "Thruster",
				12 => "Spark Field",
				14 => "Scanner",
				16 => "Engine",
				18 => "Reactor",
				20 => "C&C",
			),
			3=> array(
				4 => "Thruster",
				8 => "Surge Cannon",
				10 => "Burst Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				8 => "Surge Cannon",
				10 => "Burst Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}
?>
