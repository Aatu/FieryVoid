<?php
class ShipScoutWheel extends BaseShip{
	/*Showdowns-7*/
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
        $this->phpclass = "ShipScoutWheel";
        //$this->imagePath = "img/ships/IpshaFancy.png"; //silhouette suggests ship is laid out horizontally, I have no fitting graphics
	$this->imagePath = "img/ships/IpshaBorgWheel.png";
	    //but I do have a fancy graphics for a scout ship, which I give in instead! at least it both fits the fleet and stands out from other designs, even if suggests vertial layout
        $this->shipClass = "Scout Wheel";    
	    	    
        //$this->shipSizeClass = 3;
        //$this->fighters = array("heavy"=>6);
	        
	    $this->limited = 10;
	    $this->isd = 2225;
	    $this->notes = 'EM hardened';	  
	    $this->EMHardened = true; //EM Hardening - some weapons would check for this value!
	    $this->critRollMod = -1; //generalbonus to critical rolls!
		
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->gravitic = true;
        
		$this->enhancementOptionsEnabled[] = 'IPSH_EETH'; //can be refitted as Eethan Barony ship
		$this->enhancementOptionsEnabled[] = 'IPSH_ESSAN'; //can be refitted as Essan Barony ship
        
	    
	    
	$this->addPrimarySystem(new MagGravReactor(5, 25, 0, 42+9+4));//+Sensor+Jump Drive power
	$this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 26, 9, 8));
        $this->addPrimarySystem(new Engine(5, 30, 0, 6, 4));
        $this->addPrimarySystem(new JumpEngine(5, 16, 4, 24));
	$this->addPrimarySystem(new SparkField(4, 0, 0, 0, 360));	
	$this->addPrimarySystem(new SparkField(4, 0, 0, 0, 360));
        
      
	$this->addFrontSystem(new SurgeCannon(3, 0, 0, 300, 60));
        $this->addFrontSystem(new EmPulsar(3, 0, 0, 270, 90));
	$this->addFrontSystem(new SurgeCannon(3, 0, 0, 300, 60));
        $this->addFrontSystem(new MagGraviticThruster(4, 13, 0, 99, 1));
        $this->addFrontSystem(new MagGraviticThruster(4, 13, 0, 99, 1));
		
	    
	$this->addAftSystem(new SurgeCannon(3, 0, 0, 120, 240, true));
        $this->addAftSystem(new EmPulsar(3, 0, 0, 90, 270));
	$this->addAftSystem(new SurgeCannon(3, 0, 0, 120, 240, true));
	$this->addAftSystem(new MagGraviticThruster(4, 13, 0, 99, 2));
        $this->addAftSystem(new MagGraviticThruster(4, 13, 0, 99, 2));
	    
	    


	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 210, 330));
	$this->addLeftSystem(new SurgeCannon(3, 0, 0, 210, 330));
        $this->addLeftSystem(new EmPulsar(3, 0, 0, 180, 360));
	$this->addLeftSystem(new MagGraviticThruster(4, 15, 0, 99, 3));
		

	$this->addRightSystem(new SurgeCannon(3, 0, 0, 30, 150));
	$this->addRightSystem(new SurgeCannon(3, 0, 0, 30, 150));
	$this->addRightSystem(new EmPulsar(3, 0, 0, 0, 180));
        $this->addRightSystem(new MagGraviticThruster(4, 15, 0, 99, 4));
		
		
        $this->addFrontSystem(new Structure(5, 45));
        $this->addAftSystem(new Structure(5, 45));
        $this->addLeftSystem(new Structure(5, 60));
        $this->addRightSystem(new Structure(5, 60));
        $this->addPrimarySystem(new Structure(5, 64));
		
		
		$this->hitChart = array(
			0=> array(
				8 => "Structure",
				10 => "Spark Field",
				12 => "Elint Scanner",
				14 => "Jump Engine",
				16 => "Engine",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				8 => "EM Pulsar",
				10 => "Surge Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "EM Pulsar",
				10 => "Surge Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				6 => "Thruster",
				8 => "EM Pulsar",
				10 => "Surge Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				6 => "Thruster",
				8 => "EM Pulsar",
				10 => "Surge Cannon",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}
?>
