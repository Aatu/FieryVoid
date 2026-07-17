<?php
class ShipCargoHauler extends MediumShip{
    /*Ipsha general:
     - remember about EM hardening!
     - instead of -2 bonus to dropout/crit when caused by Ion weapon, just add -1 overall crit/dropout bonus
     - remind player of the above in comments in fleet selection phase!
     - Singularity Drive replaced by standard engine
     - add Sensor power demand to Reactor output (it's considered 'baseline' for tabletop!)
    */
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 110;
        $this->faction = "Civilians";
        $this->phpclass = "ShipCargoHauler";
        //$this->imagePath = "img/ships/IpshaTetra.png";    
        $this->imagePath = "img/ships/ipshaShipCargoHauler.png";
        	$this->canvasSize = 100;
        $this->shipClass = "Ipsha Cargo Hauler";    
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

		$this->isd = 2210;
		$this->notes = 'EM hardened';	  
		$this->EMHardened = true; //EM Hardening - some weapons would check for this value!
		$this->critRollMod = -1; //generalbonus to critical rolls!
		
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2; // 1/2 by original SCS; I gave it just 1 but +2 base thrust
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->gravitic = true;
        
		$this->iniativebonus = 2 *5;
	    
	    
		$this->enhancementOptionsEnabled[] = 'IPSH_EETH'; //can be refitted as Eethan Barony ship
		$this->enhancementOptionsEnabled[] = 'IPSH_ESSAN'; //can be refitted as Essan Barony ship
        
        

		$this->addPrimarySystem(new MagGravReactor(3, 10, 0, 12+4));
		$this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 2));
        $this->addPrimarySystem(new Engine(3, 12, 0, 2, 2));
        $this->addPrimarySystem(new CargoBay(3,75));
        $this->addPrimarySystem(new MagGraviticThruster(3, 9, 0, 99, 3)); 
        $this->addPrimarySystem(new MagGraviticThruster(3, 9, 0, 99, 4));
	    
		$this->addFrontSystem(new SurgeCannon(3, 0, 0, 300, 60));
		$this->addFrontSystem(new MagGraviticThruster(3, 9, 0, 99, 1));
		$this->addFrontSystem(new MagGraviticThruster(3, 9, 0, 99, 1));

        $this->addAftSystem(new MagGraviticThruster(3, 17, 0, 99, 2));
		
		
        $this->addPrimarySystem(new Structure(4, 40));
		
		
		$this->hitChart = array(
			0=> array(
				6 => "Thruster",
				9 => "Cargo Bay",
				12 => "Scanner",
				15 => "Engine",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				8 => "Surge Cannon",
				11 => "Cargo Bay",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				11 => "Cargo Bay",
				18 => "Structure",
				20 => "Primary",
			),
		);
    }
}

?>
