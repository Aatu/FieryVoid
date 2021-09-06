<?php
class ShipBattlehex extends HeavyCombatVesselLeftRight{ 
    /*Ipsha general:
     - remember about EM hardening!
     - instead of -2 bonus to dropout/crit when caused by Ion weapon, just add -1 overall crit/dropout bonus
     - remind player of the above in comments in fleet selection phase!
     - Singularity Drive replaced by standard engine
     - add Sensor power demand to Reactor output (it's considered 'baseline' for tabletop!)
    */
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "Ipsha";
        $this->phpclass = "ShipBattlehex";
        //$this->imagePath = "img/ships/IpshaHex.png";	    
        $this->imagePath = "img/ships/IpshaBorgHex.png";
        $this->shipClass = "Battlehex";    
	    	    
        //$this->shipSizeClass = 3;
        //$this->fighters = array("heavy"=>6);
	        
	//$this->limited = 33;
	$this->isd = 2200;
	$this->notes = 'EM hardened';	  
	$this->EMHardened = true; //EM Hardening - some weapons would check for this value!
	$this->critRollMod = -1; //generalbonus to critical rolls!
		
        $this->forwardDefense = 16;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 1;
        $this->rollcost = 0;
        $this->pivotcost = 1;
	$this->gravitic = true;
        
	$this->iniativebonus = 6 *5; 
	    
		$this->enhancementOptionsEnabled[] = 'IPSH_EETH'; //can be refitted as Eethan Barony ship
		$this->enhancementOptionsEnabled[] = 'IPSH_ESSAN'; //can be refitted as Essan Barony ship
        
	    
        
	$this->addPrimarySystem(new MagGravReactor(4, 19, 0, 30+4));
	$this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 4, 6));
        $this->addPrimarySystem(new Engine(4, 25, 0, 4, 3));
        //$this->addPrimarySystem(new Hangar(4, 6));
	$this->addPrimarySystem(new ResonanceGenerator(3, 0, 0, 270, 90));
        $this->addPrimarySystem(new MagGraviticThruster(4, 15, 0, 99, 1));
        $this->addPrimarySystem(new MagGraviticThruster(4, 11, 0, 99, 2));
        $this->addPrimarySystem(new MagGraviticThruster(4, 11, 0, 99, 2));	    

	    
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240, true));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240, true));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 120, 240, true));	
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 300, 60));    
	$this->addLeftSystem(new MagGraviticThruster(3, 13, 0, 99, 3));
		
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240, true));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240, true));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 120, 240, true));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 300, 60));
        $this->addRightSystem(new MagGraviticThruster(3, 13, 0, 99, 4));
		
		
        $this->addLeftSystem(new Structure(3, 45));
        $this->addRightSystem(new Structure(3, 45));
        $this->addPrimarySystem(new Structure(4, 50));
		
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Thruster",
				12 => "Resonance Generator",
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
