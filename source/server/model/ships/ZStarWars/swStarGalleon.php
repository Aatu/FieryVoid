<?php
class swStarGalleon extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "StarWars Galactic Empire";
        $this->phpclass = "swStarGalleon";
        $this->imagePath = "img/starwars/StarGalleon.png";
	    //$this->canvasSize = 100;
        $this->shipClass = "Star Galleon";
	
		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Galactic Empire.";
    
	$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	$this->iniativebonus = 6 *5; 
        
	$this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 14, 0, 4));
        $this->addPrimarySystem(new SWScanner(4, 12, 5, 4));
        $this->addPrimarySystem(new Engine(4, 15, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(2, 2));   
	$this->addPrimarySystem(new Thruster(3, 12, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 12, 0, 4, 4));
	$hyperdrive = new JumpEngine(4, 14, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	$this->addPrimarySystem(new CargoBay(2, 80));
	$this->addPrimarySystem(new CargoBay(2, 80));

        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
	$this->addFrontSystem(new SWRayShield(2,12,6,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWCapitalConcussion(3, 240, 120, 2)); 
	$this->addFrontSystem(new SWMediumTLaser(3, 240, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 0, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!

        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,12,6,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumTLaser(3, 180, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumTLaser(3, 60, 300, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumTLaser(3, 0, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
    
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 40));
	    
	    
        $this->hitChart = array(
        		0=> array(
					8 => "Structure",
        				10 => "Thruster",
		        		11 => "Hyperdrive",
					12=> "Hangar",
        				16 => "Cargo Bay",
        				17 => "Scanner",
        				18 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
        		1=> array(
        				3 => "Thruster",
        				4 => "Ray Shield",
        				6 => "Capital Concussion Missile",	
        				8 => "Medium Turbolaser",
					11 => "0:Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				5 => "Ray Shield",
        				8 => "Medium Turbolaser",	
					11 => "0:Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
