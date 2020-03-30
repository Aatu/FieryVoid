<?php
class swCorellianCorvetteEscort extends MediumShip{
	/*escort variant: not so heavy shielding, but lots of light guns instead of some cargo space*/
	/*also, fighter capacity*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 250;
	$this->faction = "ZStarWars";
        $this->phpclass = "swcorelliancorvetteescort";
        $this->imagePath = "img/starwars/cr90.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Corellian Escort Corvette";
	    $this->variantOf = "Corellian Corvette";
	    $this->occurence = 'uncommon';
	    
	$this->fighters = array("Fighter Squadrons"=>0.5);
	    
		$this->isd = "early Galactic Empire";
		$this->notes = "Primary users: common";
	    
	$this->unofficial = true;
        // $this->agile = true;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 12 *5; 
        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new SWScanner(3, 8, 4, 4));
        $this->addPrimarySystem(new Engine(3, 13, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(1, 6));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
	$this->addPrimarySystem(new CargoBay(1, 10));
	$this->addPrimarySystem(new CargoBay(1, 10));
	    
	$hyperdrive = new JumpEngine(3, 8, 4, 10);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	    
	$this->addPrimarySystem(new SWMediumTLaser(2, 0, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addPrimarySystem(new SWMediumTLaser(2, 0, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
			    
	    
	$this->addFrontSystem(new SWLightLaser(0, 180, 0, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWLightLaser(0, 270, 90, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWLightLaser(0, 0, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
        $this->addFrontSystem(new Thruster(1, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 10, 0, 3, 1));
        $this->addFrontSystem(new CnC(3, 8, 0, 0));
	$this->addFrontSystem(new SWRayShield(1,4,2,1,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 11, 0, 4, 2));
	$this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(1,4,2,1,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWLightLaser(0, 180, 0, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightLaser(0, 0, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
	    
        $this->addPrimarySystem(new Structure( 3, 50));
	    
	    
	    
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
        				10 => "Cargo Bay",
        				12 => "Scanner",
        				14 => "Engine",
        				18 => "Hangar",
        				19 => "Hyperdrive",
        				20 => "Reactor",
        		),
        		1=> array(
        				2 => "Thruster",
						3 => "Ray Shield",
						5 => 'Light Laser',
        				8 => "0:Medium Turbolaser",
        				16 => "Structure",
        				17 => "C&C",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
						5 => "Ray Shield",
						6 => 'Light Laser',
        				9 => "0:Medium Turbolaser",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
