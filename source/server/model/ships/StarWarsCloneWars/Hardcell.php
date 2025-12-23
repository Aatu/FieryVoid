<?php
class Hardcell extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
	$this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Hardcell";
        $this->imagePath = "img/starwars/CloneWars/hardcell.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Separatist Hardcell Transport";
	    
	$this->unofficial = true;

	    $this->notes = 'Atmospheric capable';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 10 *5; 
	
	$this->addPrimarySystem(new Reactor(4, 12, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 12, 3, 5));
    $this->addPrimarySystem(new Engine(4, 13, 0, 12, 4));
    $this->addPrimarySystem(new CnC(5, 8, 0, 0));
	$this->addPrimarySystem(new Hangar(4, 2));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
	$this->addPrimarySystem(new JumpEngine(4, 10, 3, 30));
	$this->addPrimarySystem(new EMShield(4,6,0,2,0,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
    $this->addPrimarySystem(new CWLaserCannon(2, 2, 1, 180, 360));
    $this->addPrimarySystem(new CWLaserCannon(2, 2, 1, 0, 180));
	    
    $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
    $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
    $this->addFrontSystem(new CWConcussionMissile(3, 6, 0, 240, 360));
    $this->addFrontSystem(new CWTurbolaser(2, 4, 2, 300, 60));
    $this->addFrontSystem(new CWTurbolaser(2, 4, 2, 300, 60));
    $this->addFrontSystem(new CWTurbolaser(2, 4, 2, 300, 60));
    $this->addFrontSystem(new CWConcussionMissile(3, 6, 0, 0, 120));
		
    $this->addAftSystem(new Thruster(2, 7, 0, 2, 2));
    $this->addAftSystem(new Thruster(2, 7, 0, 2, 2));
    $this->addAftSystem(new Thruster(2, 7, 0, 2, 2));
    $this->addAftSystem(new CargoBay(2, 15));
    $this->addAftSystem(new CargoBay(2, 15));
    $this->addAftSystem(new Thruster(2, 7, 0, 2, 2));
    $this->addAftSystem(new Thruster(2, 7, 0, 2, 2));
    $this->addAftSystem(new Thruster(2, 7, 0, 2, 2));
       
    $this->addPrimarySystem(new Structure( 3, 45));

        $this->hitChart = array(
        		0=> array(
        				6 => "Thruster",
						8 => "Laser Cannon",
						9 => "EM Shield",
						11 => "Jump Engine",
        				13 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",

        		),
        		1=> array(
        				6 => "Thruster",
        				8 => "Turbolaser",
        				10 => "Concussion Missile",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				8 => "Thruster",
        				12 => "Cargo Bay",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
