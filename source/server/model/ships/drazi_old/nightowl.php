<?php

class Nightowl extends BaseShipNoAft{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 525;
        $this->faction = "Drazi (WotCR)";
        $this->phpclass = "Nightowl";
        $this->imagePath = "img/ships/nightowl.png";
        $this->shipClass = "Nightowl Hyperspace Probe";
        $this->fighters = array("normal" => 6);

        $this->forwardDefense = 18;
        $this->sideDefense = 18;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        
        $this->addPrimarySystem(new Reactor(5, 15, 0, 4));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 20, 6, 8));
        $this->addPrimarySystem(new Engine(5, 15, 0, 8, 3));
        $this->addPrimarySystem(new JumpEngine(5, 15, 0, 4, 38));
        $this->addPrimarySystem(new Hangar(3, 8));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 4, 2));
        $this->addPrimarySystem(new Thruster(4, 12, 0, 4, 2));

        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 120));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 120));
        $this->addFrontSystem(new Thruster(3, 9, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 9, 0, 4, 1));

        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addLeftSystem(new RepeaterGun(3, 6, 4, 240, 60));
        $this->addLeftSystem(new CargoBay(3, 20));
        $this->addLeftSystem(new Thruster(4, 16, 0, 4, 3));

        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 300, 120));
        $this->addRightSystem(new RepeaterGun(3, 6, 4, 300, 120));
        $this->addRightSystem(new CargoBay(3, 20));
        $this->addRightSystem(new Thruster(4, 16, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 55));
        $this->addLeftSystem(new Structure( 4, 52));
        $this->addRightSystem(new Structure( 4, 52));
        $this->addPrimarySystem(new Structure( 5, 55));
    }
}
?>
