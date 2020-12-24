<?php
class swDreadnoughtRaider extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 625;
	$this->faction = "ZStarWars";
        $this->phpclass = "swDreadnoughtRaider";
        $this->imagePath = "img/starwars/dreadnaught.png";
	    //$this->canvasSize = 100;
        $this->shipClass = "Raider Dreadnought";	
	    
	$this->fighters = array("Fighter Squadrons"=>2);
	    
	$this->isd = "early Galactic Civil War";
	$this->notes = "Common (civilian/pirate).";

	$this->occurence = "uncommon";
	$this->variantOf = "Dreadnought";
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
        $this->addPrimarySystem(new SWScanner(4, 14, 5, 4));
        $this->addPrimarySystem(new Engine(4, 18, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(1, 12));   
	$this->addPrimarySystem(new Thruster(3, 15, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(3, 15, 0, 5, 4));
	$hyperdrive = new JumpEngine(4, 16, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);

        $this->addFrontSystem(new Thruster(3, 16, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 16, 0, 4, 1));
	$this->addFrontSystem(new SWRayShield(2,12,6,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumIon(3, 240, 30, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumIon(3, 330, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWHeavyLaser(3, 240, 60, 3));
	$this->addFrontSystem(new SWHeavyLaser(3, 300, 120, 3));	

    
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,12,6,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new Hangar(1, 12));
	$this->addAftSystem(new SWHeavyLaser(3, 180, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 180, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 120, 300, 3));
	$this->addAftSystem(new SWHeavyLaser(3, 60, 240, 3));
	$this->addAftSystem(new SWHeavyLaser(3, 0, 180, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWHeavyLaser(3, 0, 180, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumTLaser(3, 120, 240, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumTLaser(3, 120, 240, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!

    
	    
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
        				3 => "Thruster",
        				4 => "Ray Shield",
        				7 => "Medium Turbolaser",
					8 => "Medium Ion Cannon",
		        		11 => "Heavy Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				5 => "Ray Shield",
        				7 => "Medium Turbolaser",	
					9 => "Hangar",
		        		12 => "Heavy Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>