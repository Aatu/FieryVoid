<?php
class DrakhDreadnought extends BaseShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 2200;
	$this->faction = "Drakh";
	$this->factionAge = 2; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial
        $this->phpclass = "DrakhDreadnought";
        $this->imagePath = "img/ships/DrakhHeavyTender.png";
        $this->shipClass = "Dreadnought";
        $this->shipSizeClass = 3;
        $this->fighters = array("Shuttles" => 6, "Raiders" => 12);
        $this->gravitic = true;	    
	$this->unofficial = true;
	$this->advancedArmor = true;   
       
	$this->isd = 2234;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 19;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.33;
        $this->accelcost = 6;
        $this->rollcost = 4;
        $this->pivotcost = 6;
	$this->iniativebonus = 0; 
		
	    
	    
	$this->addPrimarySystem(new CnC(7, 25, 0, 0));
        $this->addPrimarySystem(new Reactor(6, 30, 0, 12));
		$sensors = new Scanner(6, 24, 7, 11);
		$sensors->markImproved();
		$this->addPrimarySystem($sensors);
        $this->addPrimarySystem(new Engine(6, 25, 0, 12, 3));
        $this->addPrimarySystem(new SWTractorBeam(5, 0, 360, 1));
        $this->addPrimarySystem(new JumpEngine(6, 20, 5, 36));
	$this->addPrimarySystem(new Hangar(5, 6, 2));
 		
        $this->addFrontSystem(new GraviticThruster(4, 16, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(4, 16, 0, 4, 1));
	$this->addFrontSystem(new AbsorbtionShield(3,8,8,3,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new customHeavyPolarityPulsar(4, 0, 0, 300, 60));
	$this->addFrontSystem(new customHeavyPolarityPulsar(4, 0, 0, 300, 60));
	$this->addFrontSystem(new customMphasedBeamAcc(5, 0, 0, 300, 60)); 
	$this->addFrontSystem(new customMphasedBeamAcc(5, 0, 0, 300, 60));   		    
	    
        $this->addAftSystem(new GraviticThruster(5, 35, 0, 7, 2));
	$this->addAftSystem(new GraviticThruster(5, 35, 0, 7, 2));
	$this->addAftSystem(new AbsorbtionShield(3,8,8,3,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new customHeavyPolarityPulsar(4, 0, 0, 120, 240));
	$this->addAftSystem(new customHeavyPolarityPulsar(4, 0, 0, 120, 240)); 
  
	    
	$this->addLeftSystem(new GraviticThruster(4, 25, 0, 6, 3));
	$this->addLeftSystem(new AbsorbtionShield(3,8,8,3,180,0)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addLeftSystem(new customLtPolarityPulsar(3, 0, 0, 240, 60)); 
	$this->addLeftSystem(new customLtPolarityPulsar(3, 0, 0, 120, 300));        
	$this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));
        $this->addLeftSystem(new Catapult(4, 4));			
	    
	$this->addRightSystem(new GraviticThruster(4, 25, 0, 6, 4));
	$this->addRightSystem(new AbsorbtionShield(3,8,8,3,0,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addRightSystem(new customLtPolarityPulsar(3, 0, 0, 300, 120)); 
	$this->addRightSystem(new customLtPolarityPulsar(3, 0, 0, 60, 240));          
	$this->addRightSystem(new Catapult(4, 4));	
        $this->addRightSystem(new Catapult(4, 4));
        $this->addRightSystem(new Catapult(4, 4));	
        $this->addRightSystem(new Catapult(4, 4));
        $this->addRightSystem(new Catapult(4, 4));	
        $this->addRightSystem(new Catapult(4, 4));	  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 80));
        $this->addAftSystem(new Structure( 6, 80));
        $this->addLeftSystem(new Structure( 6, 108));
        $this->addRightSystem(new Structure( 6, 108));
        $this->addPrimarySystem(new Structure( 7, 96));
	    
	    
        $this->hitChart = array(
            0=> array(
                    7 => "Structure",
                    9 => "Engine",
                    11 => "Jump Engine",
                    13 => "Tractor Beam",
                    15 => "Scanner",
                    17 => "Hangar",   
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Multiphased Beam Accelerator",
                    9 => "Heavy Polarity Pulsar",
					11 => "Absorbtion Shield",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Heavy Polarity Pulsar",
					10 => "Absorbtion Shield",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    5 => "Thruster",
                    6 => "Light Polarity Pulsar",
					8 => "Absorbtion Shield",
                    12 => "Catapult",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    5 => "Thruster",
                    6 => "Light Polarity Pulsar",
					8 => "Absorbtion Shield",
                    12 => "Catapult",
                    18 => "Structure",
                    20 => "Primary",
            ),
       );
	    
	    
    }
}

?>
