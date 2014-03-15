<?php

class Leskrati extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 515;
        $this->faction = "Dilgar";
        $this->phpclass = "Leskrati";
        $this->imagePath = "img/ships/leskrati.png";
        $this->shipClass = "Leskrati Jumpcruiser";
        $this->shipSizeClass = 3;
        
        $this->limited = 33;

        $this->forwardDefense = 15;
        $this->sideDefense = 15;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 21, 0, 1));
        $this->addPrimarySystem(new Scanner(5, 14, 3, 8));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new JumpEngine(5, 16, 4, 36));

        $this->addFrontSystem(new MediumLaser(2, 6, 5, 300, 60));
        $this->addFrontSystem(new MediumLaser(2, 6, 5, 300, 60));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 240, 120));
        $this->addFrontSystem(new EnergyPulsar(2, 6, 3, 300, 60));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 240, 120));

        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 180, 300));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 9, 0, 4, 3));
        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 60, 180));

        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 240, 0));
        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 240, 0));
        $this->addLeftSystem(new EnergyPulsar(2, 6, 3, 240, 300));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 0, 120));
        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 0, 120));
        $this->addRightSystem(new EnergyPulsar(2, 6, 3, 60, 120));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 46));
        $this->addAftSystem(new Structure( 4, 45));
        $this->addLeftSystem(new Structure( 4, 51));
        $this->addRightSystem(new Structure( 4, 51));
        $this->addPrimarySystem(new Structure( 5, 52));


    }

}

?>
