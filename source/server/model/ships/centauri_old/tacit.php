<?php
class Tacit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 325;
    $this->faction = "The Lion";
    $this->phpclass = "Tacit";
    $this->imagePath = "img/ships/tacit.png";
    $this->shipClass = "Tacit Police Cruiser";
    $this->canvasSize = 200;

    $this->forwardDefense = 11;
    $this->sideDefense = 13;

    $this->turncost = 0.5;
    $this->turndelaycost = 0.66;
    $this->accelcost = 2;
    $this->rollcost = 2;
    $this->pivotcost = 2;
    $this->iniativebonus = 30;
        
    $this->addPrimarySystem(new Reactor(5, 12, 0, 0));
    $this->addPrimarySystem(new CnC(5, 12, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
    $this->addPrimarySystem(new Engine(4, 20, 0, 8, 2));
    $this->addPrimarySystem(new Hangar(4, 3));
    $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
    $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));

    $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
    $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
    $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
    $this->addFrontSystem(new TacLaser(3, 5, 4, 300, 60));
    $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));

    $this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
    $this->addAftSystem(new Thruster(3, 8, 0, 4, 2));
    $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
    $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));

    $this->addFrontSystem(new Structure( 4, 56));
    $this->addPrimarySystem(new Structure( 5, 40));
    $this->addAftSystem(new Structure( 4, 38));
    }

}



?>
