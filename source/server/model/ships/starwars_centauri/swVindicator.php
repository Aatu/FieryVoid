<?php
class swVindicator extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 725;
	$this->faction = "StarWars Galactic Empire";
        $this->phpclass = "swVindicator";
        $this->imagePath = "img/starwars/vindicator.png";
	    //$this->canvasSize = 100;
        $this->shipClass = "Vindicator Patrol Ship";

	$this->fighters = array("fighter flights"=>4);
	
	$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.75;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 6 *5; 
        
	$this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new SWScanner(4, 12, 6, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 10, 3));
	$this->addPrimarySystem(new Hangar(3, 12));   
	$this->addPrimarySystem(new Thruster(3, 14, 0, 6, 3));
	$this->addPrimarySystem(new Thruster(3, 14, 0, 6, 4));
	$hyperdrive = new JumpEngine(4, 16, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
	$this->addFrontSystem(new SWRayShield(3,15,9,3,300,60)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWLightLaser(2, 240, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWLightTLaser(3, 240, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!	
	$this->addFrontSystem(new SWMediumTLaser(3, 270, 30, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 330, 90, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWLightTLaser(3, 0, 120, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightLaser(2, 300, 120, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
	$this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
	$this->addAftSystem(new SWRayShield(2,12,6,2,180,300)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWRayShield(2,12,6,2,60,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWLightTLaser(3, 180, 300, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightTLaser(3, 60, 180, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightLaser(2, 180, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightLaser(2, 120, 300, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightLaser(2, 240, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightLaser(2, 0, 180, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 240, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 0, 120, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
    
	    
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 4, 50));
	    
	    
        $this->hitChart = array(
        		0=> array(
					8 => "Structure",
        				12 => "Thruster",
		        		13 => "Hyperdrive",
					14=> "Hangar",
        				16 => "Scanner",
        				18 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				5 => "Ray Shield",
        				9 => "Medium Turbolaser",
 					11 => "Light Turbolaser",
					12 => "Light Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				5 => "Ray Shield",
        				8 => "Medium Turbolaser",	
					10 => "Light Turbolaser",
		        		13 => "Light Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
