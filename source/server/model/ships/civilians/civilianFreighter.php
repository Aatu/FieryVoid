<?php
class CivilianFreighter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 120;
	$this->faction = "Civilians";
        $this->phpclass = "CivilianFreighter";
        $this->imagePath = "img/ships/civilianFreighter.png";
        $this->shipClass = "Civilian Freighter";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
	$this->iniativebonus = -20;
         
        $this->addPrimarySystem(new Reactor(3, 3, 0, 0));
        $this->addPrimarySystem(new CnC(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 2, 2));
        $this->addPrimarySystem(new Engine(3, 6, 0, 4, 3));
	$this->addPrimarySystem(new Hangar(3, 4));
	$this->addPrimarySystem(new StdParticleBeam(2, 4, 2, 0, 360));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));
		
        $this->addFrontSystem(new Thruster(2, 8, 0, 4, 1));
        $this->addFrontSystem(new CargoBay(2, 20));
        $this->addFrontSystem(new CargoBay(2, 20));
        $this->addFrontSystem(new CargoBay(2, 20));
        $this->addFrontSystem(new CargoBay(2, 20));
		
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem(new CargoBay(2, 20));
        $this->addAftSystem(new CargoBay(2, 20));
        $this->addAftSystem(new CargoBay(2, 20));
        $this->addAftSystem(new CargoBay(2, 20));
       
        $this->addPrimarySystem(new Structure( 3, 56));
    }
}
?>
