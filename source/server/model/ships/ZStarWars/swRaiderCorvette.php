<?php
class swRaiderCorvette extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 475;
	$this->faction = "ZStarWars";
        $this->phpclass = "swRaiderCorvette";
        $this->imagePath = "img/starwars/RaiderClassCorvette.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Raider Corvette";
	    
	    
		$this->isd = "early Galactic Empire";
		$this->notes = "Primary users: Galactic Empire";
	    
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
        
	$this->fighters = array("Fighter Squadrons"=>0.25);

        $this->addPrimarySystem(new Reactor(4, 16, 0, 3));
        $this->addPrimarySystem(new SWScanner(3, 8, 4, 4));
        $this->addPrimarySystem(new Engine(3, 12, 0, 8, 2));
        $this->addPrimarySystem(new CnC(4, 10, 0, 0));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 4));
	    
	$hyperdrive = new JumpEngine(3, 6, 4, 10);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	    
	    
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
	$this->addFrontSystem(new SWRayShield(3,8,3,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumTLaser(3, 240, 60, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumTLaser(3, 300, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumLaser(2, 240, 60, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumLaser(2, 300, 120, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumIon(3, 300, 60, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
		
        $this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
   	$this->addAftSystem(new Thruster(3, 10, 0, 5, 2));
   	$this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
	$this->addAftSystem(new SWRayShield(2,8,3,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumLaser(2, 180, 360, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(2, 0, 180, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new Hangar(2, 2));
	$this->addAftSystem(new SWAntifighterConcussion(2, 210, 360, 3)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWAntifighterConcussion(2, 0, 150, 3)); //armor, arc and number of weapon in common housing!       

        $this->addPrimarySystem(new Structure(4, 40));
	    
	    
	    
        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
        				11 => "Hyperdrive",
					13 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
       					19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				2 => "Thruster",
        				3 => "Ray Shield",
					5 => "Medium Laser", 
        				8 => "Medium Turbolaser",
                    			10 => "Medium Ion Cannon",
        				17 => "Structure",
           				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				6 => "Ray Shield",
					7 => "Hangar",	
					10 => "Medium Laser", 
					12 => 'Antifighter Missile',
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>