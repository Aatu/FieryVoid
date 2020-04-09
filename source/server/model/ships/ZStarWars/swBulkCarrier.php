<?php
class swBulkCarrier extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 400;
	$this->faction = "ZStarWars";
        $this->phpclass = "swBulkCarrier";
        $this->imagePath = "img/starwars/BulkCarrier.png";
	    //$this->canvasSize = 100;
        $this->shipClass = "Neutron Star Bulk Carrier";	
	$this->variantOf = "Neutron Star Bulk Cruiser";
	$this->fighters = array("Fighter Squadrons"=>3);
  
  
		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Galactic Empire, Rebel Alliance.";
	    
    
	$this->unofficial = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	$this->iniativebonus = 4 *5; 
        
	$this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 14, 0, 4));
        $this->addPrimarySystem(new SWScanner(4, 16, 8, 5));
        $this->addPrimarySystem(new Engine(4, 15, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(2, 38));   
	$this->addPrimarySystem(new Thruster(3, 12, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 12, 0, 4, 4));
	$hyperdrive = new JumpEngine(4, 14, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Thruster(2, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 4, 1));
	$this->addFrontSystem(new SWRayShield(2,10,6,1,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new CargoBay(2, 80));
	$this->addFrontSystem(new SWMediumLaser(1, 240, 360, 4)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumLaser(1, 300, 60, 4)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumLaser(1, 300, 60, 4)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumLaser(1, 0, 120, 4)); //armor, arc and number of weapon in common housing!


        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,10,6,1,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new CargoBay(2, 80));
	$this->addAftSystem(new SWMediumLaser(1, 180, 300, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(1, 120, 240, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(1, 120, 240, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(1, 60, 180, 4)); //armor, arc and number of weapon in common housing!
    
        $this->addFrontSystem(new Structure( 3, 40));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 3, 44));
	    
	    
        $this->hitChart = array(
        		0=> array(
					7 => "Structure",
        				9 => "Thruster",
		        		10 => "Hyperdrive",
					14=> "Hangar",
        				16 => "Scanner",
        				18 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Ray Shield",
        				9 => "Medium Laser",
					13 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				7 => "Ray Shield",
        				10 => "Medium Laser",	
					14 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
