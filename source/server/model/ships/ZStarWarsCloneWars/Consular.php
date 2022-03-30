<?php
class Consular extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 250;
	$this->faction = "ZStarWars Clone Wars";
        $this->phpclass = "Consular";
        $this->imagePath = "img/starwars/consular.png";
	    $this->canvasSize = 100;
        $this->shipClass = "Consular Corvette";
	    
	$this->unofficial = true;
        // $this->agile = true;
		
//		$this->isd = "late Galactic Republic";
		$this->notes = "Primary users: common (civilian)";

        
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
	$this->addFrontSystem(new CWShield(3,6,0,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		
    $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
    $this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
	$this->addAftSystem(new Thruster(2, 9, 0, 2, 2));
    $this->addAftSystem(new CWTwinTurbolaser(2, 6, 3, 90, 270));
	$this->addAftSystem(new CWShield(3,6,0,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
       
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
