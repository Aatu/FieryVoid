<?php
class swCorellianCorvette extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "StarWars Galactic Empire";
        $this->phpclass = "swcorelliancorvette";
        $this->imagePath = "img/starwars/cr90.png";
        $this->shipClass = "Corellian Corvette";
	    
	$this->unofficial = true;
        // $this->agile = true;
        //$this->canvasSize = 100;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 4;
	$this->iniativebonus = 12 *5;
        
        $this->addPrimarySystem(new Reactor(4, 10, 0, 6));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 4, 4));
        $this->addPrimarySystem(new Engine(4, 13, 0, 9, 3));
	$this->addPrimarySystem(new Hangar(2, 4));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
	    
	$hyperdrive = new JumpEngine(4, 8, 4, 10);
	$hyperdrive->name = 'Hyperdrive';
	$this->addPrimarySystem($hyperdrive);
	    
	$this->addPrimarySystem(new GuardianArray(0, 4, 2, 185, 175)); //_almost_ all-around! vast thrusters block firing arc directly aft...
		
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
	$this->addFrontSystem(new SWRayShield(2,6,3,2,270,90)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
		
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
	$this->addAftSystem(new SWRayShield(2,6,3,2,90,270)); //$armour, $maxhealth, $powerReq, $shieldFactor, $startArc, $endArc
       
        $this->addPrimarySystem(new Structure( 3, 40));
    }
}
?>
