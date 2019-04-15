<?php

class TargathC extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 700;
        $this->faction = "Dilgar";
        $this->phpclass = "TargathC";
        $this->imagePath = "img/ships/targath.png";
        $this->shipClass = "Targath-C Attack Cruiser";
        $this->shipSizeClass = 3;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 4;
        
        $this->occurence = "rare";
        $this->variantOf = "Targath Strike Cruiser";

        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 21, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 9));
        $this->addPrimarySystem(new Engine(5, 11, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(4, 2));

        $this->addFrontSystem(new HeavyBolter(3, 10, 6, 300, 60));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new ScatterPulsar(1, 4, 2, 300, 60));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new HeavyBolter(3, 10, 6, 300, 60));

        $this->addAftSystem(new EnergyPulsar(2, 6, 3, 180, 300));
        $this->addAftSystem(new MediumBolter(2, 8, 4, 120, 240));
        $this->addAftSystem(new Thruster(2, 4, 0, 1, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 4, 0, 1, 2));
        $this->addAftSystem(new Engine(4, 7, 0, 4, 3));
        $this->addAftSystem(new MediumBolter(2, 8, 4, 120, 240));
        $this->addAftSystem(new EnergyPulsar(2, 6, 3, 60, 180));

        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 240, 360));
        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 180, 300));
        $this->addLeftSystem(new QuadPulsar(3, 10, 4, 240, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));

        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 0, 120));
        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 60, 180));
        $this->addRightSystem(new QuadPulsar(3, 10, 4, 0, 120));
        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 33));
        $this->addLeftSystem(new Structure( 4, 39));
        $this->addRightSystem(new Structure( 4, 39));
        $this->addPrimarySystem(new Structure( 5, 40));
        
        $this->hitChart = array(
                0=> array(
                    10 => "Structure",
                    13 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Heavy Bolter",
                    8 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Energy Pulsar",
                    10 => "Medium Bolter",
                    11 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    5 => "Thruster",
                    7 => "Quad Pulsar",
                    10 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    5 => "Thruster",
                    7 => "Quad Pulsar",
                    10 => "Scatter Pulsar",
                    18 => "Structure",
                    20 => "Primary",
                ),
         );
    }

}

?>
