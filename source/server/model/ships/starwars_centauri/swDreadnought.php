<?php
class swDreadnought extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "StarWars Galactic Empire";
        $this->phpclass = "swDreadnought";
        $this->imagePath = "img/starwars/dp20.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Dreadnought";
	
	$this->fighters = array("fighter flights"=>2);
    
	$this->unofficial = true;
        // $this->agile = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 5 *5; 
        
	$this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 5, 4));
        $this->addPrimarySystem(new Engine(4, 15, 0, 9, 3));
	$this->addFrontSystem(new Hangar(1, 12));   
	$this->addPrimarySystem(new Thruster(3, 14, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 14, 0, 4, 4));
	$hyperdrive = new JumpEngine(4, 16, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Thruster(4, 12, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 12, 0, 3, 1));
	$this->addFrontSystem(new SWRayShield(2,12,6,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWLightTLaser(3, 240, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!	
	$this->addFrontSystem(new SWLightTLaser(3, 240, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated! 
	$this->addFrontSystem(new SWEarlyMediumTLaser(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWEarlyMediumTLaser(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWEarlyMediumTLaser(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWLightTLaser(3, 0, 120, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWLightTLaser(3, 0, 120, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,12,6,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWLightTLaser(3, 240, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightTLaser(3, 0, 120, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 0, 180, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 0, 180, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 180, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 180, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWEarlyMediumTLaser(3, 120, 240, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWEarlyMediumTLaser(3, 120, 240, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWEarlyMediumTLaser(3, 120, 240, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!

    
	    
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 4, 50));
	    
	    
        $this->hitChart = array(
        		0=> array(
					8 => "Structure",
        				10 => "Thruster",
		        		12 => "Hyperdrive",
					14=> "Hangar",
        				16 => "Scanner",
        				18 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
        		1=> array(
        				3 => "Thruster",
        				5 => "Ray Shield",
        				8 => "Early Medium Turbolaser",
 					12 => "Light Turbolaser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				7 => "Early Medium Turbolaser",	
					9 => "Light Turbolaser",
		        		12 => "Heavy Laser",
        				14 => "Ray Shield",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
