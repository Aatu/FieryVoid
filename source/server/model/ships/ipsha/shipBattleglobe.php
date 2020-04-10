<?php
class ShipBattleglobe extends HeavyCombatVesselLeftRight{ //technically a Capital ship with HCV hull arrangement
    /*Ipsha general:
     - remember about EM hardening!
     - instead of -2 bonus to dropout/crit when caused by Ion weapon, just add -1 overall crit/dropout bonus
     - remind player of the above in comments in fleet selection phase!
     - Singularity Drive replaced by standard engine
     - add Sensor power demand to Reactor output (it's considered 'baseline' for tabletop!)
    */
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 700;
        $this->faction = "Ipsha";
        $this->phpclass = "ShipBattleglobe";
        //$this->imagePath = "img/ships/IpshaBattleglobe.png";
	$this->imagePath = "img/ships/IpshaBorgGlobe.png";
        $this->shipClass = "Battleglobe";    
	    	    
        $this->shipSizeClass = 3;
        //$this->fighters = array("heavy"=>6);
	        
	//$this->limited = 33;
	$this->isd = 2202;
	$this->notes = 'EM hardened';	  
	$this->EMHardened = true; //EM Hardening - some weapons would check for this value!
	$this->critRollMod = -1; //generalbonus to critical rolls!
		
        $this->forwardDefense = 18;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 1;
        $this->rollcost = 0;
        $this->pivotcost = 2;
	$this->gravitic = true;
        
	$this->iniativebonus = 0 *5; //THIS IS A CAPITAL SHIP, despite HCV arrangement!
	    
	    
		$this->enhancementOptionsEnabled[] = 'IPSH_EETH'; //can be refitted as Eethan Barony ship
		$this->enhancementOptionsEnabled[] = 'IPSH_ESSAN'; //can be refitted as Essan Barony ship
        
        
        //$this->addPrimarySystem(new MagGravReactor(4, 28, 0, 60));
	$this->addPrimarySystem(new MagGravReactor(4, 25, 0, 42+5));
	$this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 5, 7));
        $this->addPrimarySystem(new Engine(4, 30, 0, 4, 4));
        //$this->addPrimarySystem(new Hangar(4, 6));
	$this->addPrimarySystem(new SparkField(4, 0, 0, 0, 360));
        $this->addPrimarySystem(new MagGraviticThruster(4, 13, 0, 99, 1));
        $this->addPrimarySystem(new MagGraviticThruster(4, 13, 0, 99, 1));
        $this->addPrimarySystem(new MagGraviticThruster(4, 13, 0, 99, 2));
        $this->addPrimarySystem(new MagGraviticThruster(4, 13, 0, 99, 2));	    

	    
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240));	    
	$this->addLeftSystem(new MagGraviticThruster(4, 15, 0, 99, 3));
		
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240));
        $this->addRightSystem(new MagGraviticThruster(4, 15, 0, 99, 4));
		
		
        $this->addLeftSystem(new Structure(4, 60));
        $this->addRightSystem(new Structure(4, 60));
        $this->addPrimarySystem(new Structure(4, 52));
		
		
		$this->hitChart = array(
			0=> array(
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

