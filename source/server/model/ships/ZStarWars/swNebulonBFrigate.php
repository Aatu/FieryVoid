<?php
class swNebulonBFrigate extends HeavyCombatVessel{
	/*generic NebulonBFrigate*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 400;
	$this->faction = "ZStarWars";
        $this->phpclass = "swNebulonBFrigate";
        $this->imagePath = "img/starwars/nebulonb.png";
	    //$this->canvasSize = 100;
        $this->shipClass = "Nebulon-B Frigate";
	    //$this->variantOf = "Corellian Corvette";
	    //$this->occurence = 'uncommon';
	    
		$this->isd = "14 BBY";
		$this->notes = "Primary users: common";
	    
	$this->fighters = array("Fighter Squadrons"=>2);
	    
	$this->unofficial = true;
        // $this->agile = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.75;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;
	$this->iniativebonus = 6 *5; 
        
	$this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new SWScanner(3, 10, 5, 4));
        $this->addPrimarySystem(new Engine(3, 12, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));


        $this->addFrontSystem(new Thruster(2, 12, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 3, 1));
	$this->addFrontSystem(new Hangar(1, 14));        
	$this->addFrontSystem(new SWRayShield(2,8,4,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumTLaser(2, 240, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(2, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(2, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(2, 0, 120, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 240, 0, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 180, 300, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 60, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 0, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	 
	    
	$this->addAftSystem(new Engine(3, 10, 0, 4, 3));
	$hyperdrive = new JumpEngine(3, 16, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addAftSystem($hyperdrive);
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
	$this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,8,4,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumLaser(2, 180, 300, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumLaser(2, 60, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
	    

        $this->addFrontSystem(new Structure( 3, 50));
        $this->addAftSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 4, 30));
	    
	    
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
        				10 => "Medium Turbolaser",
        				12 => "Medium Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
					6 => "Engine",		
		        		8 => "Hyperdrive",
					9 => "Ray Shield",
					11 => 'Medium Laser',
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
