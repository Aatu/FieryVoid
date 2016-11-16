<?php
class swCorellianCorvetteEscortH extends MediumShip{
	/*anti-ship escort variant: additional turbolasers*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 340;
	$this->faction = "StarWars Galactic Empire";
        $this->phpclass = "swcorelliancorvetteescorth";
        $this->imagePath = "img/starwars/cr90.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Corellian Heavy Escort Corvette";
	    $this->variantOf = "Corellian Corvette";
	    $this->occurence = 'uncommon';
	    
	    
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
        
        $this->addPrimarySystem(new Reactor(4, 10, 0, 5));
        $this->addPrimarySystem(new Scanner(3, 8, 4, 4));
        $this->addPrimarySystem(new Engine(4, 13, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(2, 4));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
	$this->addPrimarySystem(new CargoBay(1, 10));
	$this->addPrimarySystem(new CargoBay(1, 10));
	    
	$hyperdrive = new JumpEngine(4, 8, 4, 10);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	    
	$this->addPrimarySystem(new SWMediumTLaser(2, 0, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addPrimarySystem(new SWMediumTLaser(2, 0, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
			    
	    

        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new CnC(4, 8, 0, 0));
	$this->addFrontSystem(new SWRayShield(2,6,3,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWLightTLaser(0, 180, 0, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWLightTLaser(0, 0, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
	    
	    
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,6,3,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWLightTLaser(0, 180, 0, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightTLaser(0, 0, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
	    
        $this->addPrimarySystem(new Structure( 3, 50));
	    
	    
	    
        $this->hitChart = array(
        		0=> array(
        				6 => "Thruster",
        				10 => "Cargo Bay",
        				13 => "Medium Turbolaser",
        				15 => "Scanner",
        				17 => "Engine",
        				18 => "Hangar",
        				19 => "Hyperdrive",
        				20 => "Reactor",
        		),
        		1=> array(
        				4 => "Thruster",
					7 => 'Light Turbolaser',
        				8 => "Ray Shield",
        				16 => "Structure",
        				17 => "C&C",
        				20 => "Primary",
        		),
        		2=> array(
        				8 => "Thruster",
					10 => 'Light Turbolaser',
        				11 => "Ray Shield",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
