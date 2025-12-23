<?php
class Diamond extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 175;
	$this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Diamond";
        $this->imagePath = "img/starwars/CloneWars/diamond.png";
	    $this->canvasSize = 60;
        $this->shipClass = "Separatist Diamond Transport";
	    
	$this->unofficial = true;

	    $this->notes = 'Atmospheric capable';
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
		$this->iniativebonus = 12 *5; 
	
	$this->addPrimarySystem(new Reactor(4, 10, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
    $this->addPrimarySystem(new Engine(4, 12, 0, 8, 4));
    $this->addPrimarySystem(new CnC(4, 6, 0, 0));
	$this->addPrimarySystem(new Hangar(4, 2));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 3, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 3, 4));
	$this->addPrimarySystem(new JumpEngine(4, 10, 3, 36));
	$this->addPrimarySystem(new EMShield(4,6,0,2,0,360)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
	    
    $this->addFrontSystem(new Thruster(2, 8, 0, 4, 1));
    $this->addFrontSystem(new Thruster(2, 8, 0, 4, 1));
    $this->addFrontSystem(new CWQuadLaserCannon(2, 6, 3, 180, 60));
    $this->addFrontSystem(new CWQuadLaserCannon(2, 6, 3, 300, 180));
		
    $this->addAftSystem(new Thruster(3, 12, 0, 8, 2));
    $this->addAftSystem(new CargoBay(2, 15));
    $this->addAftSystem(new CargoBay(2, 15));
       
    $this->addPrimarySystem(new Structure( 3, 45));

        $this->hitChart = array(
        		0=> array(
        				8 => "Thruster",
						9 => "EM Shield",
						11 => "Jump Engine",
        				13 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",

        		),
        		1=> array(
        				8 => "Thruster",
        				10 => "Quad Laser Cannon",
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
