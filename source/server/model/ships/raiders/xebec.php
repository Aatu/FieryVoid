<?php
class Xebec extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
	$this->faction = "Raiders";
        $this->phpclass = "Xebec";
        $this->imagePath = "img/ships/xebec.png";
        $this->shipClass = "Xebec";
        $this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
    	$this->iniativebonus = 30;
         
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addPrimarySystem(new MediumLaser(3, 6, 3, 240, 60));
        $this->addPrimarySystem(new MediumLaser(3, 6, 3, 300, 120));
        $this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 0, 180));

        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 3));
        $this->addPrimarySystem(new CargoBay(2, 18));
        $this->addPrimarySystem(new CargoBay(2, 18));
        $this->addPrimarySystem(new Hangar(3, 2));
    	$this->addPrimarySystem(new Thruster(3, 15, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 15, 0, 5, 4));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));


        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 360));
	
        $this->addPrimarySystem(new Structure( 4, 48));
    }

}



?>
