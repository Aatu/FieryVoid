<?php
class swLancer extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
	$this->faction = "ZStarWars";
        $this->phpclass = "swLancer";
        $this->imagePath = "img/starwars/lancer.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Lancer Frigate";
	    
	    
		$this->isd = "2 ABY";
		$this->notes = "Primary users: Galactic Empire, New Republic";
	    
	$this->unofficial = true;
        // $this->agile = true;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 2;
	$this->iniativebonus = 12 *5; //true warship
        
        $this->addPrimarySystem(new Reactor(3, 15, 0, 3));
        $this->addPrimarySystem(new SWScanner(3, 8, 4, 4));
        $this->addPrimarySystem(new Engine(3, 12, 0, 7, 2));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 4));
	$this->addPrimarySystem(new SWMediumLaserAF(1, 180, 360, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addPrimarySystem(new SWMediumLaserAF(1, 0, 180, 4));
	    
	$hyperdrive = new JumpEngine(3, 6, 4, 10);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	    
	    
        $this->addFrontSystem(new Thruster(3, 6, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 3, 1));
	$this->addFrontSystem(new SWRayShield(2,6,3,1,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumLaserAF(1, 180, 360, 4));
	$this->addFrontSystem(new SWMediumLaserAF(1, 240, 60, 4));
	$this->addFrontSystem(new SWMediumLaserAF(1, 300, 120, 4));
	$this->addFrontSystem(new SWMediumLaserAF(1, 0, 180, 4));

        $this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
   	$this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
	$this->addAftSystem(new SWRayShield(2,6,3,1,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumLaserAF(1, 150, 330, 4));
	$this->addAftSystem(new SWMediumLaserAF(1, 180, 360, 4));   
	$this->addAftSystem(new SWMediumLaserAF(1, 0, 180, 4));   
	$this->addAftSystem(new SWMediumLaserAF(1, 30, 210, 4));       
        $this->addPrimarySystem(new Structure(4, 35));
	    
	    
	    
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
						10 => "AF Medium Laser", 
        				12 => "Hyperdrive",
						14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
       					19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				3 => "Thruster",
        				4 => "Ray Shield",
        				9 => "AF Medium Laser",
        				17 => "Structure",
           				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				5 => "Ray Shield",
        				10 => "AF Medium Laser",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
