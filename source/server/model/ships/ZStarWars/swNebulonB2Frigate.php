<?php
class swNebulonB2Frigate extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 550;
	$this->faction = "ZStarWars";
        $this->phpclass = "swNebulonB2Frigate";
        $this->imagePath = "img/starwars/Nebulon-B2.png";
	$this->canvasSize = 200;
        $this->shipClass = "Nebulon-B2 Frigate";
        
	    //$this->variantOf = "Corellian Corvette";
	    //$this->occurence = 'uncommon';
	    
	$this->fighters = array("Fighter Squadrons"=>2);
	    
	$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.75;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
	$this->iniativebonus = 6 *5; 
        
	$this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new SWScanner(3, 12, 6, 5));
        $this->addPrimarySystem(new Engine(3, 15, 0, 6, 3));
	$this->addPrimarySystem(new Thruster(2, 15, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(2, 15, 0, 5, 4));

        $this->addFrontSystem(new Thruster(2, 12, 0, 4, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 4, 1));
	$this->addFrontSystem(new Hangar(1, 14));        
	$this->addFrontSystem(new SWRayShield(2,12,4,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumTLaser(2, 240, 360, 3)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWHeavyTLaser(2, 270, 30, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumTLaser(2, 300, 60, 3)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWHeavyTLaser(2, 330, 90, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumTLaser(2, 0, 120, 3)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumLaser(2, 240, 60, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumLaser(2, 300, 120, 2)); //armor, arc and number of weapon in common housing!
	    
	$this->addAftSystem(new Engine(3, 14, 0, 5, 3));
	$hyperdrive = new JumpEngine(3, 16, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addAftSystem($hyperdrive);
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
	$this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
	$this->addAftSystem(new SWRayShield(2,12,4,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumLaser(2, 180, 360, 2)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(2, 120, 300, 2)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(2, 60, 240, 2)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(2, 0, 180, 2)); //armor, arc and number of weapon in common housing!
	    
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 36));
	    
	    
        $this->hitChart = array(
        		0=> array(
					10 => "Structure",
        				13 => "Thruster",
        				15 => "Scanner",
        				18 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
        		1=> array(
        				2 => "Thruster",
        				3 => "Ray Shield",
					7 => "Hangar",
        				9 => "Heavy Turbolaser",
        				12 => "Medium Turbolaser",
        				14 => "Medium Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
					6 => "Engine",		
		        		8 => "Hyperdrive",
					9 => "Ray Shield",
					13 => 'Medium Laser',
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
