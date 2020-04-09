<?php
class swCorellianCorvette extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 220;
	$this->faction = "ZStarWars";
        $this->phpclass = "swcorelliancorvette";
        $this->imagePath = "img/starwars/cr90.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Corellian Corvette";
	    
	$this->unofficial = true;
        // $this->agile = true;
		
		$this->isd = "late Galactic Republic";
		$this->notes = "Primary users: common (civilian)";

        
        $this->forwardDefense = 10;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 9 *5; //as semi-civilian ship, initiative isn't what it would be for a true warship
        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 3));
        $this->addPrimarySystem(new SWScanner(3, 8, 3, 3));
        $this->addPrimarySystem(new Engine(3, 13, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(1, 2));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
	$this->addPrimarySystem(new CargoBay(1, 35));
	$this->addPrimarySystem(new CargoBay(1, 35));
	    
	$hyperdrive = new JumpEngine(3, 8, 4, 10);
	$hyperdrive->displayName = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	    
	$this->addPrimarySystem(new SWMediumTLaser(2, 0, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
	$this->addPrimarySystem(new SWMediumTLaser(2, 0, 360, 2)); //armor, arc and number of weapon in common housing: structure and power data are calculated!
			    
        $this->addFrontSystem(new Thruster(1, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(1, 10, 0, 3, 1));
        $this->addFrontSystem(new CnC(3, 8, 0, 0));
	$this->addFrontSystem(new SWRayShield(2,6,3,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		
        $this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 11, 0, 4, 2));
	$this->addAftSystem(new Thruster(2, 9, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,6,3,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
       
        $this->addPrimarySystem(new Structure( 3, 50));
	    
	    
	    

        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
        				13 => "Cargo Bay",
        				15 => "Scanner",
        				17 => "Engine",
        				18 => "Hangar",
        				19 => "Hyperdrive",
        				20 => "Reactor",
        		),
        		1=> array(
        				2 => "Thruster",
        				3 => "Ray Shield",
        				7 => "0:Medium Turbolaser",
        				16 => "Structure",
        				17 => "C&C",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				6 => "Ray Shield",
        				9 => "0:Medium Turbolaser",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
