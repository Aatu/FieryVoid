<?php
class swStrikeCruiser extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 675;
	$this->faction = "ZStarWars";
        $this->phpclass = "swStrikeCruiser";
        $this->imagePath = "img/starwars/StrikeCruiser.png";
        $this->shipClass = "Strike Cruiser";
	    
		
		$this->isd = "late Galactic Civil War";
		$this->notes = "Primary users: Galactic Empire, New Republic";
		
	$this->fighters = array("fighter flights"=>1);
	    
	$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 3;
	$this->iniativebonus = 6 *5; 
        
	$this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new SWScanner(3, 12, 4, 6));
        $this->addPrimarySystem(new Engine(3, 28, 0, 12, 3));
	$this->addPrimarySystem(new Thruster(3, 15, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(3, 15, 0, 5, 4));
	$this->addPrimarySystem(new Hangar(3, 8));      

        $this->addFrontSystem(new Thruster(2, 12, 0, 4, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 4, 1));
	$this->addFrontSystem(new SWRayShield(3,16,7,3,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumTLaser(2, 240, 360, 4)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWHeavyTLaser(3, 270, 30, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumTLaser(2, 300, 60, 4)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWHeavyTLaser(3, 330, 90, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumTLaser(2, 0, 120, 4)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumIon(3, 240, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWMediumIon(3, 240, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWTractorBeam(2,240,360,3));
	$this->addFrontSystem(new SWTractorBeam(2,0,120,3));
	    
	$hyperdrive = new JumpEngine(3, 20, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addAftSystem($hyperdrive);
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
	$this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
	$this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
	$this->addAftSystem(new SWRayShield(3,16,7,3,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumTLaser(2, 180, 300, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumTLaser(2, 60, 180, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWHeavyTLaser(3, 210, 330, 3)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWHeavyTLaser(3, 30, 150, 3)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumIon(3, 180, 330, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWMediumIon(3, 30, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWAntifighterConcussion(2, 240, 120, 2)); //armor, arc and number of weapon in common housing!
	    
        $this->addFrontSystem(new Structure( 4, 60));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 4, 48));
	    
	    
        $this->hitChart = array(
        		0=> array(
					8 => "Structure",
					10 => "Hangar",
        				13 => "Thruster",
        				15 => "Scanner",
        				17 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
        		1=> array(
        				2 => "Thruster",
        				3 => "Ray Shield",
					5 => "Tractor Beam",
        				7 => "Heavy Turbolaser",
        				10 => "Medium Turbolaser",
                    			12 => "Medium Ion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
		        		6 => "Hyperdrive",
					7 => "Ray Shield",
        				9 => "Heavy Turbolaser",
					11 => 'Medium Ion Cannon',
					12 => 'Antifighter Missile',
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>