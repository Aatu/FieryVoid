<?php

class Garasoch extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 550;
        $this->faction = "Dilgar";
        $this->phpclass = "Garasoch";
        $this->imagePath = "img/ships/garasoch.png";
        $this->shipClass = "Garasoch Heavy Carrier";
        $this->shipSizeClass = 3;
        $this->isd = 2231;
                
        $this->limited = 33;
        $this->fighters = array("normal"=>48);

        $this->forwardDefense = 14;
        $this->sideDefense = 16;

        $this->turncost = 1;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        $this->addPrimarySystem(new Reactor(5, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 21, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 3, 8));
        $this->addPrimarySystem(new Engine(5, 11, 0, 6, 4));
        $this->addPrimarySystem(new Hangar(4, 52));
        $this->addPrimarySystem(new JumpEngine(5, 16, 4, 36));

        $this->addFrontSystem(new EnergyPulsar(3, 6, 3, 300, 60));
        $this->addFrontSystem(new EnergyPulsar(3, 6, 3, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 300, 60));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 300, 60));

        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 120, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 7, 0, 4, 4));
        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 120, 240));

        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 180, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));

        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 0, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 39));
        $this->addAftSystem(new Structure( 5, 39));
        $this->addLeftSystem(new Structure( 5, 45));
        $this->addRightSystem(new Structure( 5, 45));
        $this->addPrimarySystem(new Structure( 5, 48));
        
        $this->hitChart = array(
                0=> array(
                    9 => "Structure",
                    11 => "Jump Engine",
                    13 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Energy Pulsar",
                    8 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Scatter Pulsar",
                    9 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    5 => "Thruster",
                    7 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    5 => "Thruster",
                    7 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
        );
    }
}

?>
