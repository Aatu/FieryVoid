<?php
class swDreadnought extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 625;
	$this->faction = "ZStarWars";
        $this->phpclass = "swDreadnought";
        $this->imagePath = "img/starwars/dreadnaught.png";
	    //$this->canvasSize = 100;
        $this->shipClass = "Dreadnought";	
	    
	$this->fighters = array("Fighter Squadrons"=>1);
	    
	    
		$this->isd = "100 BBY";
		$this->notes = "Primary users: common";
    
	$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 5 *5; 
        
	$this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new SWScanner(4, 12, 5, 4));
        $this->addPrimarySystem(new Engine(4, 15, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(1, 12));   
	$this->addPrimarySystem(new Thruster(3, 14, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 14, 0, 4, 4));
	$hyperdrive = new JumpEngine(4, 16, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
	$this->addFrontSystem(new SWRayShield(2,12,6,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWLightTLaser(3, 240, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!	
	$this->addFrontSystem(new SWLightTLaser(3, 240, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated! 
	$this->addFrontSystem(new SWMediumTLaserE(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaserE(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaserE(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWLightTLaser(3, 0, 120, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWLightTLaser(3, 0, 120, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,12,6,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWLightTLaser(3, 180, 300, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightTLaser(3, 60, 180, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 180, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 180, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 0, 180, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 0, 180, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumTLaserE(3, 120, 240, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumTLaserE(3, 120, 240, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumTLaserE(3, 120, 240, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!

    
	    
        $this->addFrontSystem(new Structure( 4, 55));
        $this->addAftSystem(new Structure( 4, 55));
        $this->addPrimarySystem(new Structure( 4, 55));
	    
	    
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
        				2 => "Thruster",
        				3 => "Ray Shield",
        				8 => "Medium Turbolaser (Early)",
 					12 => "Light Turbolaser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				3 => "Thruster",
        				4 => "Ray Shield",
        				7 => "Medium Turbolaser (Early)",	
					10 => "Light Turbolaser",
		        		13 => "Heavy Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
