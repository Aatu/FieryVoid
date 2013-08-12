<?php
class CivilianTanker extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 130;
	$this->faction = "Civilians";
        $this->phpclass = "CivilianTanker";
        $this->imagePath = "img/ships/civilianTanker.png";
        $this->shipClass = "Civilian Tanker";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 999;
        $this->pivotcost = 999;
	$this->iniativebonus = -30;
         
        $this->addPrimarySystem(new Reactor(3, 3, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 2, 2));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
	$this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));
		
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new CnC(3, 5, 0, 0));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 2, 240, 120));
        $this->addFrontSystem(new CargoBay(3, 24));
        $this->addFrontSystem(new CargoBay(3, 24));
        $this->addFrontSystem(new CargoBay(3, 24));
		
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 8, 0, 2, 2));
        $this->addAftSystem(new Hangar(3, 4));
        $this->addAftSystem(new Engine(3, 6, 0, 4, 4));
        $this->addAftSystem(new StdParticleBeam(2, 4, 2, 60, 300));
        $this->addAftSystem(new CargoBay(3, 24));
        $this->addAftSystem(new CargoBay(3, 24));
        $this->addAftSystem(new CargoBay(3, 24));
       
        $this->addPrimarySystem(new Structure( 3, 52));
    }
}
?>
