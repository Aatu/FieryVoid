<?php
class swBulkTransport extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 80;
	$this->faction = "ZStarWars";
        $this->phpclass = "swBulkTransport";
        $this->imagePath = "img/starwars/BulkTransport.png";
        $this->shipClass = "Action VI Bulk Transport";
	    
      
		$this->isd = "Galactic Republic";
		$this->notes = "Primary users: Common (civilian).";
      
	$this->unofficial = true;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 2 *5; //as semi-civilian ship, initiative isn't what it would be for a true warship
        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 3));
        $this->addPrimarySystem(new SWScanner(3, 8, 3, 2));
        $this->addPrimarySystem(new Engine(3, 13, 0, 8, 3));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
	$this->addPrimarySystem(new CargoBay(1, 80));
	$this->addPrimarySystem(new CargoBay(1, 80));
	    
	$hyperdrive = new JumpEngine(3, 8, 4, 10);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	    
	//$this->addPrimarySystem(new SWMediumTLaser(2, 0, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	//$this->addPrimarySystem(new SWMediumTLaser(2, 0, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
			    
        $this->addFrontSystem(new Thruster(1, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 10, 0, 3, 1));
	$this->addFrontSystem(new Hangar(1, 6));
	$this->addFrontSystem(new SWRayShield(2,6,3,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		
        $this->addAftSystem(new Thruster(2, 10, 0, 4, 2));
	$this->addAftSystem(new Thruster(2, 10, 0, 4, 2));
	$this->addAftSystem(new SWRayShield(2,6,1,1,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
       
        $this->addPrimarySystem(new Structure( 2, 40));
	    
	    
	    
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
        				13 => "Cargo Bay",
        				15 => "Scanner",
        				17 => "Engine",
        				18 => "Hyperdrive",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				3 => "Thruster",
        				4 => "Ray Shield",
        				5 => "Hangar",
        				10 => "0:Cargo Bay",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				5 => "Ray Shield",
        				10 => "0:Cargo Bay",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
