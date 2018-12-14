<?php
class DrakhScoutShip extends MediumShip{
	
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 750;
	$this->faction = "Drakh";
        $this->phpclass = "DrakhScoutShip";
        $this->imagePath = "img/ships/DrakhPatrolShip.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Scout Ship";
        $this->gravitic = true;	 
	$this->advancedArmor = true;   
	$this->unofficial = true;
        $this->occurence = "uncommon";
	$this->variantOf = "Patrol Ship";
	$this->isd = 2227;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
	$this->iniativebonus = 12 *5; 
		
	    
	    
	$this->addPrimarySystem(new CnC(5, 9, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 15, 0, 4));
		$sensors = new ElintScanner(5, 18, 4, 12);
		$sensors->markImproved();
		$this->addPrimarySystem($sensors);
        //$this->addPrimarySystem(new ElintScanner(5, 18, 4, 12));
        $this->addPrimarySystem(new Engine(4, 12, 0, 9, 2));
	$this->addPrimarySystem(new Hangar(3, 1, 1));
        $this->addPrimarySystem(new GraviticThruster(3, 10, 0, 5, 3));
        $this->addPrimarySystem(new GraviticThruster(3, 10, 0, 5, 4));
  
        $this->addFrontSystem(new GraviticThruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(3, 8, 0, 4, 1));
	$this->addFrontSystem(new AbsorbtionShield(2,4,3,1,270,90) ); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new customLtPhaseDisruptorShip(3, 0, 0, 240, 0)); 
	$this->addFrontSystem(new customMedPolarityPulsar(3, 0, 0, 300, 60)); 	 	   
	$this->addFrontSystem(new customLtPhaseDisruptorShip(3, 0, 0, 0, 120));  
	    
        $this->addAftSystem(new GraviticThruster(3, 12, 0, 6, 2));
	$this->addAftSystem(new GraviticThruster(3, 12, 0, 6, 2));
	$this->addAftSystem(new AbsorbtionShield(2,4,3,1,90,270));
	$this->addAftSystem(new customLtPolarityPulsar(2, 0, 0, 90,270));  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure( 5, 50));
	    
		
	    
        $this->hitChart = array(
            0=> array(
                    10 => "Thruster",
                    13 => "ELINT Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Medium Polarity Pulsar",
                    10 => "Light Phase Disruptor",
		    11 => "Absorbtion Shield",
                    17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
		    9 => "Light Polarity Pulsar",
		    10 => "Absorbtion Shield",
                    17 => "Structure",
                    20 => "Primary",
            ),
       );
	    
    }
	
	
	
}
?>
