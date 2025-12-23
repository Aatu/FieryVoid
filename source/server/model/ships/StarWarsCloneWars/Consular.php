<?php
class Consular extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 250;
	$this->faction = "Star Wars Clone Wars";
        $this->phpclass = "Consular";
        $this->imagePath = "img/starwars/consular.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Republic Consular Corvette";
	    
	$this->unofficial = true;
        // $this->agile = true;
		
//		$this->isd = "late Galactic Republic";
//		$this->notes = "Primary users: common (civilian)";

        
        $this->forwardDefense = 10;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
	$this->iniativebonus = 12 *5; 
	
	$this->addPrimarySystem(new Reactor(3, 10, 0, 0));
    $this->addPrimarySystem(new Scanner(3, 10, 3, 5));
    $this->addPrimarySystem(new Engine(3, 13, 0, 6, 3));
    $this->addPrimarySystem(new CnC(3, 8, 0, 0));
	$this->addPrimarySystem(new Hangar(1, 2));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
	$this->addPrimarySystem(new JumpEngine(3, 10, 3, 30));
	    
    $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
    $this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
    $this->addFrontSystem(new CWPointDefenseLaser(2, 4, 1, 0, 360));
    $this->addFrontSystem(new CWTwinTurbolaser(2, 6, 3, 240, 360));
    $this->addFrontSystem(new CWTwinTurbolaser(2, 6, 3, 0, 120));
	$this->addFrontSystem(new EMShield(3,6,0,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		
    $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
    $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
	$this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
    $this->addAftSystem(new CWTwinTurbolaser(2, 6, 3, 90, 270));
	$this->addAftSystem(new EMShield(3,6,0,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
       
    $this->addPrimarySystem(new Structure( 3, 40));

        $this->hitChart = array(
        		0=> array(
        				8 => "Thruster",
						10 => "Jump Engine",
        				12 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				6 => "Thruster",
        				7 => "Point Defense Laser",
        				9 => "Twin Turbolaser",
        				10 => "EM Shield",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				8 => "Thruster",
        				9 => "Twin Turbolaser",
        				10 => "EM Shield",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
	    
	    
    }
}
?>
