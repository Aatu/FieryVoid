<?php
class swCorellianGunship extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "ZStarWars";
        $this->phpclass = "swcorelliangunship";
        $this->imagePath = "img/starwars/dp20.png";
	    $this->canvasSize = 100;
        $this->shipClass = "DP20 Corellian Gunship";
	    
	    
		$this->isd = "early Galactic Empire";
		$this->notes = "Primary users: common";
	    
	$this->unofficial = true;
        // $this->agile = true;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
	$this->iniativebonus = 12 *5; //true warship
        
        $this->addPrimarySystem(new Reactor(4, 16, 0, 3));
        $this->addPrimarySystem(new SWScanner(3, 8, 4, 4));
        $this->addPrimarySystem(new Engine(3, 12, 0, 8, 2));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 4));
	$this->addPrimarySystem(new SWLightLaser(1, 0, 360, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
	$hyperdrive = new JumpEngine(3, 6, 4, 10);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	    
	    
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
	$this->addFrontSystem(new SWCapitalConcussion(3, 300, 60, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWRayShield(3,8,3,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumTLaser(3, 240, 60, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 240, 60, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 300, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 300, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
		
        $this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
   	$this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
	$this->addAftSystem(new SWRayShield(1,4,2,1,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWLightLaser(1, 180, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightLaser(1, 0, 180, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!       

        $this->addPrimarySystem(new Structure(4, 38));
	    
	    
	    
        $this->hitChart = array(
        		0=> array(
        				2 => "Thruster",
					7 => "Light Laser", 
        				9 => "Hyperdrive",
					11 => "Scanner",
        				13 => "Engine",
        				14 => "Hangar",
					16 => "Light Laser", 
       					19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				2 => "Thruster",
        				3 => "Ray Shield",
					6 => "Capital Concussion Missile",
        				11 => "Medium Turbolaser",
        				17 => "Structure",
           				20 => "Primary",
        		),
        		2=> array(
        				3 => "Thruster",
        				4 => "Ray Shield",
					10 => "Light Laser", 
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
