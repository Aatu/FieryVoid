<?php
class Tatros extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 225;
    $this->faction = "Centauri (WotCR)";
    $this->phpclass = "Tatros";
    $this->imagePath = "img/ships/jenas.png";
    $this->shipClass = "Tatros Escort Frigate";
    $this->agile = true;
    $this->canvasSize = 100;

    $this->forwardDefense = 11;
    $this->sideDefense = 12;

    $this->turncost = 0.5;
    $this->turndelaycost = 0.5;
    $this->accelcost = 2;
    $this->rollcost = 2;
    $this->pivotcost = 2;
    $this->iniativebonus = 60;
        
    $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
    $this->addPrimarySystem(new CnC(4, 9, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 12, 2, 5));
    $this->addPrimarySystem(new Engine(3, 9, 0, 8, 2));
    $this->addPrimarySystem(new Hangar(3, 1));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
    $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));

    $this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
    $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 60));
    $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 180));
    $this->addFrontSystem(new SentinelPD(1, 4, 2, 180, 60));
    $this->addFrontSystem(new SentinelPD(1, 4, 2, 300, 180));

    $this->addAftSystem(new Thruster(1, 10, 0, 4, 2));
    $this->addAftSystem(new Thruster(1, 10, 0, 4, 2));

    $this->addPrimarySystem(new Structure( 4, 40));
    }

}



?>
