<?php
class DrakhMothership extends BaseShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 5000;
	$this->faction = "Drakh";
	$this->factionAge = 2; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->phpclass = "DrakhMothership";
        $this->imagePath = "img/ships/DrakhMothership.png";
        $this->shipClass = "Mothership";
        $this->shipSizeClass = 3;
        $this->fighters = array("Shuttles" => 24, "Raiders" => 72);
        $this->gravitic = true;	    
	$this->unofficial = true;
	$this->advancedArmor = true;   
        $this->Enormous = true;
        $this->limited = 10;
		
		$this->notes = "Not a military ship, not eligible for pickup battles."; //more akin to worldship
       
	$this->isd = 2200;
        
        $this->forwardDefense = 24;
        $this->sideDefense = 25;
        
        $this->turncost = 3;
        $this->turndelaycost = 3;
        $this->accelcost = 12;
        $this->rollcost = 12;
        $this->pivotcost = 12;
	$this->iniativebonus = -30; 
		
	    
	    
	$this->addPrimarySystem(new CnC(6, 35, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 40, 0, 12));
	$sensors = new ElintScanner(5, 24, 6, 10);
	$sensors->markImproved();
	$this->addPrimarySystem($sensors);
	$this->addPrimarySystem(new DrakhRaiderController(5, 10, 5, 1));
        $this->addPrimarySystem(new Engine(5, 33, 0, 16, 12));
        $this->addPrimarySystem(new SWTractorBeam(5, 0, 360, 1));
        $this->addPrimarySystem(new JumpEngine(6, 30, 8, 18));
	$this->addPrimarySystem(new Hangar(4, 24, 12));
        $this->addPrimarySystem(new CargoBay(3, 80));
 		
        $this->addFrontSystem(new GraviticThruster(3, 25, 0, 8, 1));
        $this->addFrontSystem(new GraviticThruster(3, 25, 0, 8, 1));
	$this->addFrontSystem(new AbsorbtionShield(3,8,8,3,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$sensors = new ElintScanner(4, 18, 5, 8);
	$sensors->markImproved();
	$this->addFrontSystem($sensors);
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 240, 120));
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 240, 120));
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 240, 120));
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 240, 120));
	$this->addFrontSystem(new customMphasedBeamAcc(5, 0, 0, 300, 60)); 
	$this->addFrontSystem(new customMphasedBeamAcc(5, 0, 0, 300, 60));   		    
	    
        $this->addAftSystem(new GraviticThruster(3, 38, 0, 12, 2));
	$this->addAftSystem(new GraviticThruster(3, 38, 0, 12, 2));
	$this->addAftSystem(new AbsorbtionShield(3,8,8,3,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addAftSystem(new CargoBay(3, 80));	
	$this->addAftSystem(new customHeavyPolarityPulsar(4, 0, 0, 120, 240));
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 120, 300)); 
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 120, 300)); 
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 60, 240)); 
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 60, 240)); 
  	    
	$this->addLeftSystem(new GraviticThruster(3, 30, 0, 8, 3));
	$this->addLeftSystem(new AbsorbtionShield(3,8,8,3,180,0)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addLeftSystem(new customMedPolarityPulsar(3, 0, 0, 240, 60)); 	
	$this->addLeftSystem(new customMedPolarityPulsar(3, 0, 0, 240, 60)); 		
	$this->addLeftSystem(new customLtPolarityPulsar(2, 0, 0, 180, 360)); 
	$this->addLeftSystem(new customLtPolarityPulsar(2, 0, 0, 180, 360));        
	$this->addLeftSystem(new Catapult(3, 36));
	$this->addLeftSystem(new Catapult(3, 36));
	$this->addLeftSystem(new Catapult(3, 36));
	$this->addLeftSystem(new Catapult(3, 36));
	$this->addLeftSystem(new Catapult(3, 36));
	$this->addLeftSystem(new Catapult(3, 36));
        $this->addLeftSystem(new CargoBay(3, 80));			
	    
	$this->addRightSystem(new GraviticThruster(3, 30, 0, 8, 4));
	$this->addRightSystem(new AbsorbtionShield(3,8,8,3,0,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addRightSystem(new customMedPolarityPulsar(3, 0, 0, 300, 120)); 	
	$this->addRightSystem(new customMedPolarityPulsar(3, 0, 0, 300, 120)); 		
	$this->addRightSystem(new customLtPolarityPulsar(2, 0, 0, 0, 180)); 
	$this->addRightSystem(new customLtPolarityPulsar(2, 0, 0, 0, 180));          
	$this->addRightSystem(new Catapult(4, 36));	
	$this->addRightSystem(new Catapult(4, 36));	
	$this->addRightSystem(new Catapult(4, 36));	
	$this->addRightSystem(new Catapult(4, 36));	
	$this->addRightSystem(new Catapult(4, 36));	
	$this->addRightSystem(new Catapult(4, 36));
        $this->addRightSystem(new CargoBay(3, 80));	
	        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 140));
        $this->addAftSystem(new Structure( 4, 140));
        $this->addLeftSystem(new Structure( 4, 160));
        $this->addRightSystem(new Structure( 4, 160));
        $this->addPrimarySystem(new Structure( 6, 200));
	    
	    
        $this->hitChart = array(
            0=> array(
                    5 => "Structure",
                    7 => "Cargo Bay",
                    8 => "Raider Controller",
                    9 => "Engine",
                    11 => "Jump Engine",
                    13 => "Tractor Beam",
                    15 => "ElintScanner",
                    17 => "Hangar",   
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Multiphased Beam Accelerator",
                    8 => "Light Polarity Pulsar",
					10 => "Absorption Shield",
                    12 => "ElintScanner",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    7 => "Heavy Polarity Pulsar",
                    9 => "Light Polarity Pulsar",
					11 => "Absorption Shield",
                    13 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
                    6 => "Medium Polarity Pulsar",
                    7 => "Light Polarity Pulsar",
					9 => "Absorption Shield",
                    12 => "Catapult",
                    14 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
                    6 => "Medium Polarity Pulsar",
                    7 => "Light Polarity Pulsar",
					9 => "Absorption Shield",
                    12 => "Catapult",
                    14 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
       );
	    
	    
    }
}
?>
