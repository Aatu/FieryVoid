<?php
class Pelta extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 250;
	$this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Pelta";
        $this->imagePath = "img/starwars/CloneWars/Pelta.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Republic Pelta Frigate";
	    
	$this->unofficial = true;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 12 *5; 
	
	$this->addPrimarySystem(new Reactor(5, 12, 0, 0));
    $this->addPrimarySystem(new Scanner(5, 10, 3, 5));
    $this->addPrimarySystem(new Engine(5, 14, 0, 10, 3));
    $this->addPrimarySystem(new CnC(5, 8, 0, 0));
	$this->addPrimarySystem(new Hangar(4, 2));
	$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
	$this->addPrimarySystem(new JumpEngine(5, 10, 3, 30));
	$this->addPrimarySystem(new EMShield(5,6,0,2,0,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	    
    $this->addFrontSystem(new Thruster(4, 7, 0, 4, 1));
    $this->addFrontSystem(new Thruster(4, 7, 0, 4, 1));
    $this->addFrontSystem(new CWTurbolaser(2, 4, 2, 240, 60));
    $this->addFrontSystem(new CWPointDefenseLaser(3, 4, 1, 240, 120));
    $this->addFrontSystem(new CWTurbolaser(2, 4, 2, 300, 120));
		
    $this->addAftSystem(new Thruster(3, 5, 0, 2, 2));
    $this->addAftSystem(new Thruster(3, 12, 0, 6, 2));
	$this->addAftSystem(new Thruster(3, 5, 0, 2, 2));
    $this->addAftSystem(new CWPointDefenseLaser(3, 4, 1, 180, 60));
    $this->addAftSystem(new CWPointDefenseLaser(3, 4, 1, 300, 180));
       
    $this->addPrimarySystem(new Structure( 5, 46));

        $this->hitChart = array(
        		0=> array(
        				7 => "Thruster",
						8 => "EM Shield",
						10 => "Jump Engine",
        				12 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				8 => "Thruster",
        				9 => "Point Defense Laser",
        				11 => "Turbolaser",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				8 => "Thruster",
        				10 => "Point Defense Laser",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
