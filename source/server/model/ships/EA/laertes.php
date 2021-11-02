<?php
class Laertes extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 325;
	$this->faction = 'EA';//"EA defenses";
        $this->phpclass = "Laertes";
        $this->imagePath = "img/ships/laertes.png";
        $this->shipClass = "Laertes Police Corvette (Gamma)";
        $this->canvasSize = 100;
        
        $this->isd = 2184;

        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 2;
	$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 5));
	$this->addPrimarySystem(new Hangar(3, 2));
	$this->addPrimarySystem(new Thruster(3, 8, 0, 2, 3));
	$this->addPrimarySystem(new Thruster(3, 8, 0, 2, 4));
		
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addFrontSystem(new InterceptorPrototype(2, 4, 1, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 11, 0, 4, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addAftSystem(new InterceptorPrototype(2, 4, 1, 90, 270));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
	
        $this->addPrimarySystem(new Structure( 4, 40));
    }

}



?>
