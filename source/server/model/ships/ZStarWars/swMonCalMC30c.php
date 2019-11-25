<?php
class swMonCalMC30c extends HeavyCombatVesselLeftRight{
	/*eggshell torpedo frigate*/
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 650; //tenative barring destruction tests
	$this->faction = "ZStarWars";
        $this->phpclass = "swMonCalMC30c";
        $this->imagePath = "img/starwars/mc30.png";
	    //$this->canvasSize = 100;
        $this->shipClass = "MonCal MC 30c Torpedo Frigate";
        $this->limited = 33; //limited deployment

		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Rebel Alliance, New Republic";
	
	$this->unofficial = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 8 *5; 
        
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 1));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 1));
	$this->addPrimarySystem(new SWRayShield(3,8,4,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(3, 14, 0, 0));
        $this->addPrimarySystem(new SWScanner(3, 12, 6, 6));
        $this->addPrimarySystem(new Engine(3, 8, 0, 5, 3));
        $this->addPrimarySystem(new Engine(3, 8, 0, 5, 3));
	$hyperdrive = new JumpEngine(3, 14, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	$this->addPrimarySystem(new SWRayShield(3,8,4,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 2));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 2));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 4, 2));

        $this->addLeftSystem(new Thruster(2, 6, 0, 3, 3));
        $this->addLeftSystem(new Thruster(2, 6, 0, 3, 3));
	$this->addLeftSystem(new SWRayShield(2,12,8,3,180,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addLeftSystem(new SWMediumTLaser(2, 210, 360, 2));
	$this->addLeftSystem(new SWMediumTLaser(2, 210, 360, 2));
	$this->addLeftSystem(new SWCapitalProton(2, 210, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWCapitalProton(2, 210, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWCapitalProton(2, 180, 330, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumLaser(2, 180, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumLaser(2, 180, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
        $this->addRightSystem(new Thruster(2, 6, 0, 3, 4));
	$this->addRightSystem(new Thruster(2, 6, 0, 3, 4));
	$this->addRightSystem(new SWRayShield(2,12,8,3,0,180)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addRightSystem(new SWMediumTLaser(2, 0, 150, 2));
	$this->addRightSystem(new SWMediumTLaser(2, 0, 150, 2));
	$this->addRightSystem(new SWCapitalProton(2, 0, 150, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWCapitalProton(2, 0, 150, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWCapitalProton(2, 30, 180, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumLaser(2, 0, 180, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumLaser(2, 0, 180, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	    
    
	    
        $this->addLeftSystem(new Structure( 2, 40));
        $this->addRightSystem(new Structure( 2, 40));
        $this->addPrimarySystem(new Structure( 3, 40));
	    
	    
        $this->hitChart = array(
        		0=> array(
					8 => "Structure",
        				10 => "Ray Shield",
        				13 => "Thruster",
		        		14 => "Hyperdrive",
        				16 => "Scanner",
        				18 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
            
        		3=> array(
        				2 => "Thruster",
        				3 => "Ray Shield",
        				7 => "Capital Proton Torpedo",	
					10 => "Medium Turbolaser",
		        		13 => "Medium Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
            
        		4=> array(
        				2 => "Thruster",
        				3 => "Ray Shield",
        				7 => "Capital Proton Torpedo",	
					10 => "Medium Turbolaser",
		        		13 => "Medium Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
            
        );
	    
	    
    }
}
?>
