<?php
class ShipJumpsphere extends BaseShip{
  /*custom ship, providing Ipsha with jumpship other than the scout*/
    /*Ipsha general:
     - remember about EM hardening!
     - instead of -2 bonus to dropout/crit when caused by Ion weapon, just add -1 overall crit/dropout bonus
     - remind player of the above in comments in fleet selection phase!
     - Singularity Drive replaced by standard engine
     - add Sensor power demand to Reactor output (it's considered 'baseline' for tabletop!)
    */
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1000;
	$this->faction = "Ipsha";
        $this->phpclass = "ShipJumpsphere";
        //$this->imagePath = "img/ships/IpshaWarsphere.png";    
        $this->imagePath = "img/ships/IpshaBorgSphere.png";
        $this->shipClass = "Jumpsphere";   
		$this->variantOf = "Warsphere";     
		$this->occurence = 'rare'; 
	    
	    $this->unofficial = true;
	    	    
        $this->shipSizeClass = 3;
        //$this->fighters = array("heavy"=>6);
	        
	    $this->limited = 33;
	    $this->isd = 2265;
	    $this->notes = 'EM hardened';	  
	    $this->EMHardened = true; //EM Hardening - some weapons would check for this value!
	    $this->critRollMod = -1; //generalbonus to critical rolls!
		
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 2;
        $this->rollcost = 0;
        $this->pivotcost = 0;
	$this->gravitic = true;
        
		$this->enhancementOptionsEnabled[] = 'IPSH_EETH'; //can be refitted as Eethan Barony ship
		$this->enhancementOptionsEnabled[] = 'IPSH_ESSAN'; //can be refitted as Essan Barony ship
        
	    

	$this->addPrimarySystem(new MagGravReactor(4, 28, 0, 60+6+4));
	$this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 20, 6, 8));
        $this->addPrimarySystem(new Engine(4, 33, 0, 8, 6));
        $this->addPrimarySystem(new JumpEngine(4, 16, 4, 24));
	$this->addPrimarySystem(new SparkField(4, 0, 0, 0, 360));	
	$this->addPrimarySystem(new SparkField(4, 0, 0, 0, 360));
        
      
        $this->addFrontSystem(new EmBolter(4, 0, 0, 300, 60));
	$this->addFrontSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addFrontSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addFrontSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addFrontSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addFrontSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addFrontSystem(new SurgeCannon(3, 0, 0, 300, 60));
        $this->addFrontSystem(new MagGraviticThruster(4, 15, 0, 99, 1));
        $this->addFrontSystem(new MagGraviticThruster(4, 15, 0, 99, 1));
		
	    
        $this->addAftSystem(new EmBolter(4, 0, 0, 120, 240));
	$this->addAftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addAftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addAftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addAftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addAftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addAftSystem(new SurgeCannon(3, 0, 0, 120, 240));
        $this->addAftSystem(new MagGraviticThruster(4, 15, 0, 99, 2));
        $this->addAftSystem(new MagGraviticThruster(4, 15, 0, 99, 2));
	    
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 180, 300));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 180, 300));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 180, 300));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 240, 360));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 240, 360));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 240, 360));	    
	$this->addLeftSystem(new MagGraviticThruster(4, 20, 0, 99, 3));
		
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 60, 180));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 60, 180));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 60, 180));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 0, 120));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 0, 120));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 0, 120));
        $this->addRightSystem(new MagGraviticThruster(4, 20, 0, 99, 4));
		
		
        $this->addFrontSystem(new Structure(4, 60));
        $this->addAftSystem(new Structure(4, 60));
        $this->addLeftSystem(new Structure(4, 72));
        $this->addRightSystem(new Structure(4, 72));
        $this->addPrimarySystem(new Structure(5, 56));
		
		
		$this->hitChart = array(
			0=> array(
				7 => "Structure",
				10 => "Spark Field",
				12 => "Jump Engine",
				14 => "Scanner",
				16 => "Engine",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				5 => "EM Bolter",
				12 => "Surge Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				4 => "Thruster",
				5 => "EM Bolter",
				12 => "Surge Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				11 => "Surge Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				11 => "Surge Cannon",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}

?>
