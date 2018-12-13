<?php
class DrakhSupportShip extends HeavyCombatVessel{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 950;
	$this->faction = "Drakh";
        $this->phpclass = "DrakhSupportShip";
        $this->imagePath = "img/ships/DrakhCruiser.png";
        $this->shipClass = "Support Ship";
        $this->shipSizeClass = 2;
        $this->fighters = array("Shuttles" => 6, "Raiders" => 4);
        $this->gravitic = true;	 
	$this->advancedArmor = true;   
	$this->unofficial = true;
        $this->occurence = "uncommon";
	$this->variantOf = "Attack Ship";
	$this->isd = 2259;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 6 *5; 
		
	    
	    
	$this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(6, 16, 0, 0));
		$sensors = new Scanner5, 16, 6, 10);
		$sensors->markImproved();
		$this->addPrimarySystem($sensors);
        //$this->addPrimarySystem(new Scanner(5, 16, 6, 10));
        $this->addPrimarySystem(new Engine(5, 14, 0, 8, 3));
        $this->addPrimarySystem(new JumpEngine(5, 15, 4, 36));
	$this->addPrimarySystem(new Hangar(4, 6, 1));
        $this->addPrimarySystem(new GraviticThruster(3, 13, 0, 5, 3));
        $this->addPrimarySystem(new GraviticThruster(3, 13, 0, 5, 4));
  
        $this->addFrontSystem(new GraviticThruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(3, 10, 0, 4, 1));
	$this->addFrontSystem(new AbsorbtionShield(2,6,4,2,240,60) ); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new AbsorbtionShield(2,6,4,2,300,120) );
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 240, 60));
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 270, 90)); 
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 300, 120));
	$this->addFrontSystem(new customMphasedBeamAcc(3, 0, 0, 270, 30)); 	   
	$this->addFrontSystem(new customMphasedBeamAcc(3, 0, 0, 330, 90)); 

	    
        $this->addAftSystem(new GraviticThruster(3, 18, 0, 5, 2));
	$this->addAftSystem(new GraviticThruster(3, 18, 0, 5, 2));
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 120, 300));  
	$this->addAftSystem(new customMedPolarityPulsar(3, 0, 0, 120, 240));
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 60, 240));
	$this->addAftSystem(new AbsorbtionShield(2,6,4,2,90,270) );
        $this->addAftSystem(new Catapult(4, 4));
        $this->addAftSystem(new Catapult(4, 4));
        $this->addAftSystem(new Catapult(4, 4));
        $this->addAftSystem(new Catapult(4, 4));
       
	    
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 65));
        $this->addAftSystem(new Structure( 4, 65));
        $this->addPrimarySystem(new Structure( 6, 60));
	    
	    
        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    10 => "Thruster",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    8 => "Multiphased Beam Accelerator",
                    11 => "Light Polarity Pulsar",
		    13 => "Absorbtion Shield",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    5 => "Thruster",
                    7 => "Medium Polarity Pulsar",
		    9 => "Light Polarity Pulsar",
		    11 => "Absorbtion Shield",
                    14 => "Catapult",
                    18 => "Structure",
                    20 => "Primary",
            ),
       );
	    
	    
    }
}

?>
