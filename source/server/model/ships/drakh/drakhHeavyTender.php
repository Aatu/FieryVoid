<?php
class DrakhHeavyTender extends BaseShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 1000;
	$this->faction = "Drakh";
        $this->phpclass = "DrakhHeavyTender";
        $this->imagePath = "img/ships/DrakhHeavyTender.png";
        $this->shipClass = "Heavy Tender";
        $this->shipSizeClass = 3;
        $this->limited = 33;
        $this->fighters = array("Shuttles" => 6, "Raiders" => 24);
        $this->gravitic = true;	    
	$this->unofficial = true;
	$this->advancedArmor = true;   
       
	$this->isd = 2234;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 20;
        
        $this->turncost = 2;
        $this->turndelaycost = 1.5;
        $this->accelcost = 6;
        $this->rollcost = 4;
        $this->pivotcost = 8;
	$this->iniativebonus = -2 *5; 
		
	    
	    
	$this->addPrimarySystem(new CnC(6, 28, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 35, 0, 12));
        $this->addPrimarySystem(new Scanner(5, 30, 6, 10));
        $this->addPrimarySystem(new Engine(5, 30, 0, 10, 3));
        $this->addPrimarySystem(new TractorBeam(5, 4, 0, 0));
        $this->addPrimarySystem(new JumpEngine(5, 25, 5, 36));
	$this->addPrimarySystem(new Hangar(4, 6, 2));
 		
        $this->addFrontSystem(new GraviticThruster(3, 25, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(3, 25, 0, 5, 1));
	$this->addFrontSystem(new AbsorbtionShield(3,8,8,3,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 240, 120)); 
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 240, 120));
	$this->addFrontSystem(new customMedPolarityPulsar(3, 0, 0, 240, 60)); 	
	$this->addFrontSystem(new customMedPolarityPulsar(3, 0, 0, 300, 120)); 		    
	    
        $this->addAftSystem(new GraviticThruster(3, 30, 0, 6, 2));
	$this->addAftSystem(new GraviticThruster(3, 30, 0, 6, 2));
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 120, 300));  
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 60, 240));  
        $this->addAftSystem(new Catapult(4, 4)); 
        $this->addAftSystem(new Catapult(4, 4)); 
        $this->addAftSystem(new Catapult(4, 4)); 
        $this->addAftSystem(new Catapult(4, 4)); 
        $this->addAftSystem(new Catapult(4, 4)); 
        $this->addAftSystem(new Catapult(4, 4)); 
        $this->addAftSystem(new Catapult(4, 4)); 
        $this->addAftSystem(new Catapult(4, 4));   
	    
	$this->addLeftSystem(new GraviticThruster(3, 28, 0, 7, 3));
	$this->addLeftSystem(new AbsorbtionShield(3,8,8,3,180,0)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addLeftSystem(new CargoBay(3, 30));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));			
	    
	$this->addRightSystem(new GraviticThruster(3, 28, 0, 7, 4));
	$this->addRightSystem(new AbsorbtionShield(3,8,8,3,0,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addRightSystem(new CargoBay(3, 30));
        $this->addRightSystem(new Catapult(4, 4));	
        $this->addRightSystem(new Catapult(4, 4));
        $this->addRightSystem(new Catapult(4, 4));	
        $this->addRightSystem(new Catapult(4, 4));
        $this->addRightSystem(new Catapult(4, 4));	
        $this->addRightSystem(new Catapult(4, 4));
        $this->addRightSystem(new Catapult(4, 4));	
        $this->addRightSystem(new Catapult(4, 4));	  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 120));
        $this->addAftSystem(new Structure( 4, 120));
        $this->addLeftSystem(new Structure( 4, 140));
        $this->addRightSystem(new Structure( 4, 140));
        $this->addPrimarySystem(new Structure( 6, 120));
	    
	    
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    10 => "Tractor Beam",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
		    6 => "Absorbtion Shield",
                    8 => "Light Polarity Pulsar",
                    10 => "Medium Polarity Pulsar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
		    8 => "Light Polarity Pulsar",
                    12 => "Catapult",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
		    5 => "Absorbtion Shield",
                    8 => "Cargo Bay",
                    12 => "Catapult",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
		    5 => "Absorbtion Shield",
                    8 => "Cargo Bay",
                    12 => "Catapult",
                    18 => "Structure",
                    20 => "Primary",
            ),
       );
	    
	    
    }
}
?>
