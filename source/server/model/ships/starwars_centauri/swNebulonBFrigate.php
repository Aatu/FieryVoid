<?php
class swNebulonBFrigate extends HeavyCombatVessel{
	/*generic NebulonBFrigate*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 550;
	$this->faction = "StarWars Galactic Empire";
        $this->phpclass = "swNebulonBFrigate";
        $this->imagePath = "img/starwars/nebulonb.png";
	    //$this->canvasSize = 100;
        $this->shipClass = "Nebulon-B Frigate";
	    //$this->variantOf = "Corellian Corvette";
	    //$this->occurence = 'uncommon';
	    
	$this->fighters = array("fighter flights"=>4);
	    
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
        $this->addPrimarySystem(new Reactor(4, 15, 0, 6));
        $this->addPrimarySystem(new Scanner(3, 10, 5, 4));
        $this->addPrimarySystem(new Engine(3, 12, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 13, 0, 4, 4));


        $this->addFrontSystem(new Thruster(2, 12, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 3, 1));
	$this->addFrontSystem(new Hangar(1, 15));        
	$this->addFrontSystem(new SWRayShield(2,8,4,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumTLaser(2, 240, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(2, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(2, 300, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(2, 0, 120, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 240, 0, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 0, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 180, 300, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 60, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	 
	    
	$this->addAftSystem(new Engine(3, 10, 0, 4, 3));
	$hyperdrive = new JumpEngine(3, 16, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addAftSystem($hyperdrive);
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
	$this->addAftSystem(new Thruster(2, 12, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,8,4,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumLaser(2, 180, 60, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumLaser(2, 300, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
	    

        $this->addFrontSystem(new Structure( 3, 50));
        $this->addAftSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 4, 30));
	    
	    
        $this->hitChart = array(
        		0=> array(
					8 => "Structure",
        				13 => "Thruster",
        				16 => "Scanner",
        				18 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
        		1=> array(
        				3 => "Thruster",
        				4 => "Ray Shield",
					7 => "Hangar",
        				10 => "Medium Turbolaser",
        				12 => "Medium Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				9 => "Thruster",
					11 => "Engine",		
		        		12 => "Hyperdrive",
					13 => 'Medium Laser',
        				14 => "Ray Shield",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
