<?php
class swArquitensCommandCruiser extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 430;
	$this->faction = "ZStarWars";
        $this->phpclass = "swArquitensCommandCruiser";
        $this->imagePath = "img/starwars/ArquitensLightCruiser.png";
	$this->canvasSize = 200;
        $this->shipClass = "Arquitens Command Cruiser";
        

		$this->isd = "Galactic Civil War";
		$this->notes = "Primary users: Galactic Empire";
	    
	$this->fighters = array("Fighter Squadrons"=>0.5);
	    
	$this->unofficial = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 4;
	$this->iniativebonus = 6 *5; 
        
	$this->addPrimarySystem(new CnC(4, 14, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 22, 0, 0));
        $this->addPrimarySystem(new SWScanner(3, 12, 6, 5));
        $this->addPrimarySystem(new Engine(3, 18, 0, 8, 3));
	$this->addPrimarySystem(new Thruster(2, 15, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(2, 15, 0, 5, 4));

        $this->addFrontSystem(new Thruster(2, 12, 0, 4, 1));
        $this->addFrontSystem(new Thruster(2, 12, 0, 4, 1));
     
	$this->addFrontSystem(new SWRayShield(2,12,6,3,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addFrontSystem(new SWMediumTLaser(3, 240, 360, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumTLaser(3, 270, 60, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumTLaser(3, 300, 90, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWMediumTLaser(3, 0, 120, 2)); //armor, arc and number of weapon in common housing!
	$this->addFrontSystem(new SWCapitalConcussion(2, 240, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addFrontSystem(new SWCapitalConcussion(2, 0, 120, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!

	    
	$hyperdrive = new JumpEngine(3, 16, 5, 15);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addAftSystem($hyperdrive);
	$this->addAftSystem(new Hangar(2, 4));  
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 12, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,12,4,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	$this->addAftSystem(new SWMediumLaser(2, 180, 360, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(2, 180, 360, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(2, 0, 180, 4)); //armor, arc and number of weapon in common housing!
	$this->addAftSystem(new SWMediumLaser(2, 0, 180, 4)); //armor, arc and number of weapon in common housing!
	//$this->addAftSystem(new SWAntifighterConcussion(2, 240, 120, 2)); //armor, arc and number of weapon in common housing!
	    
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 36));
	    
	    
        $this->hitChart = array(
        		0=> array(
					9 => "Structure",
        				13 => "Thruster",
        				15 => "Scanner",
        				17 => "Engine",
        				19 => "Reactor",
					20 => "C&C",
        		),
        		1=> array(
        				3 => "Thruster",
        				4 => "Ray Shield",
        				8 => "Medium Turbolaser",
					10 => "Capital Concussion Missile",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				4 => "Thruster",
        				5 => "Ray Shield",
					6 => "Hangar",	
		        		8 => "Hyperdrive",
					11 => "Medium Laser",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>