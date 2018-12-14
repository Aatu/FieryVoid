<?php
class DrakhTender extends BaseShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "Drakh";
        $this->phpclass = "DrakhTender";
        $this->imagePath = "img/ships/DrakhTanker.png";
        $this->shipClass = "Tender";
        $this->shipSizeClass = 3;

        $this->fighters = array("Shuttles" => 6, "Raiders" => 4);

        $this->gravitic = true;	    
	$this->unofficial = true;
	$this->advancedArmor = true;   

        //$this->occurence = "uncommon";
	//$this->variantOf = 'Tanker'; //very different role, and not considered a variant after all 
	$this->isd = 2208;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.75;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	$this->iniativebonus = -1 *5; 
		
	    
	    
	$this->addPrimarySystem(new CnC(5, 9, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 12, 0, 0));
		$sensors = new Scanner(5, 12, 4, 6);
		$sensors->markImproved();
		$this->addPrimarySystem($sensors);
        //$this->addPrimarySystem(new Scanner(5, 12, 4, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 8, 4));
        $this->addPrimarySystem(new CargoBay(4, 18));
        $this->addPrimarySystem(new JumpEngine(5, 15, 4, 24));
	$this->addPrimarySystem(new Hangar(4, 6, 2));
 		
        $this->addFrontSystem(new GraviticThruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(3, 8, 0, 4, 1));
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 240, 60)); 
	$this->addFrontSystem(new customLtPolarityPulsar(2, 0, 0, 300, 120)); 	    
	    
        $this->addAftSystem(new GraviticThruster(3, 12, 0, 5, 2));
	$this->addAftSystem(new GraviticThruster(3, 12, 0, 5, 2));
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 120, 300));  
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 60, 240));     
	    
	$this->addLeftSystem(new GraviticThruster(3, 10, 0, 5, 3));
	$this->addLeftSystem(new AbsorbtionShield(3,6,4,2,180,0)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addLeftSystem(new CargoBay(4, 20));
        $this->addLeftSystem(new CargoBay(4, 20));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));			
	    
	$this->addRightSystem(new GraviticThruster(3, 10, 0, 5, 4));
	$this->addRightSystem(new AbsorbtionShield(3,6,4,2,0,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
        $this->addRightSystem(new CargoBay(4, 20));
        $this->addRightSystem(new CargoBay(4, 20)); 
        $this->addRightSystem(new Catapult(4, 4));	
        $this->addRightSystem(new Catapult(4, 4));	  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 30));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 40));
	    
	    
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Cargo Bay",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
                    9 => "Light Polarity Pulsar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    8 => "Thruster",
		    11 => "Light Polarity Pulsar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
		    6 => "Absorbtion Shield",
                    10 => "Cargo Bay",
                    12 => "Catapult",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
		    6 => "Absorbtion Shield",
                    10 => "Cargo Bay",
                    12 => "Catapult",
                    18 => "Structure",
                    20 => "Primary",
            ),
       );
	    
	    
    }
}

?>
