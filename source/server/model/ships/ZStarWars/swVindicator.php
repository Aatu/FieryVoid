<?php
class swVindicator extends BaseShipNoFwd{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 600;
	$this->faction = "ZStarWars";
        $this->phpclass = "swVindicator";
        $this->imagePath = "img/starwars/vindicator.png";
	    //$this->canvasSize = 100;
        $this->shipClass = "Vindicator Patrol Ship";
	    
	    
		$this->isd = "early Galactic Civil War";
		$this->notes = "Primary users: Galactic Empire";

	$this->fighters = array("Fighter Squadrons"=>2);
	
	$this->unofficial = true;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 2 *5; 

	$this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new SWScanner(4, 16, 6, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 10, 3));
	$this->addPrimarySystem(new Hangar(3, 24));   
	$hyperdrive = new JumpEngine(4, 16, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);

        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addLeftSystem(new Thruster(3, 15, 0, 6, 3));
	$this->addLeftSystem(new SWRayShield(3,15,9,3,210,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addLeftSystem(new SWLightLaser(2, 240, 60, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWLightTLaser(3, 240, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumTLaser(3, 240, 0, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!	
	$this->addLeftSystem(new SWMediumTLaser(3, 270, 30, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addLeftSystem(new SWMediumTLaser(3, 300, 60, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!

        $this->addRightSystem(new Thruster(3, 15, 0, 4, 1));     	    
	$this->addRightSystem(new Thruster(3, 15, 0, 6, 4));
	$this->addRightSystem(new SWRayShield(3,15,9,3,0,150)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addRightSystem(new SWLightLaser(2, 300, 120, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWLightTLaser(3, 0, 120, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumTLaser(3, 0, 120, 3)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumTLaser(3, 300, 60, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addRightSystem(new SWMediumTLaser(3, 330, 90, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!

        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
	$this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
	$this->addAftSystem(new SWRayShield(2,12,3,2,150,210)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWLightLaser(2, 180, 0, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightLaser(2, 120, 300, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightTLaser(3, 180, 300, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightTLaser(3, 60, 180, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
        $this->addAftSystem(new SWLightLaser(2, 60, 240, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addAftSystem(new SWLightLaser(2, 0, 180, 4)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	
	    
        $this->addRightSystem(new Structure( 4, 50));
        $this->addLeftSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 50));
	    
	    
        $this->hitChart = array(
        		0=> array(
					9 => "Structure",
		        		11 => "Hyperdrive",
					14=> "Hangar",
        				16 => "Scanner",
        				18 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
        		2=> array(
        				4 => "Thruster",
        				5 => "Ray Shield",
						7 => "Light Turbolaser",
		        		9 => "Light Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				2 => "Thruster",
        				3 => "Ray Shield",
        				5 => "Medium Turbolaser",
						7 => "Light Turbolaser",
						8 => "Light Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				2 => "Thruster",
        				3 => "Ray Shield",
        				5 => "Medium Turbolaser",
						7 => "Light Turbolaser",
						8 => "Light Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
