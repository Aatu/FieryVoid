<?php

class ProtraI extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 575;
        $this->faction = "Dilgar";
        $this->phpclass = "ProtraI";
        $this->imagePath = "img/ships/protrai.png";
        $this->shipClass = "Protra-I Improved Scoutship";
        $this->shipSizeClass = 3;
        $this->iniativebonus = 5;
        
        $this->limited = 33;
        $this->occurence = "uncommon";
        $this->variantOf = "Protra Scoutship";

        $this->forwardDefense = 14;
        $this->sideDefense = 15;

        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;

        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 11, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 20, 6, 11));
        $this->addPrimarySystem(new Engine(4, 9, 0, 5, 4));
        $this->addPrimarySystem(new Hangar(2, 2));
        $this->addPrimarySystem(new JumpEngine(4, 10, 3, 36));

        $this->addFrontSystem(new MediumBolter(2, 8, 4, 240, 60));
        $this->addFrontSystem(new EnergyPulsar(3, 6, 3, 300, 60));
        $this->addFrontSystem(new MediumBolter(2, 8, 4, 300, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));

        $this->addAftSystem(new LightBolter(2, 6, 2, 120, 300));
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 5, 2));
        $this->addAftSystem(new Engine(3, 9, 0, 4, 4));
        $this->addAftSystem(new LightBolter(2, 6, 2, 60, 240));

        $this->addLeftSystem(new ScatterPulsar(2, 4, 2, 180, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));

        $this->addRightSystem(new ScatterPulsar(2, 4, 2, 0, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 30));
        $this->addAftSystem(new Structure( 4, 30));
        $this->addLeftSystem(new Structure( 4, 36));
        $this->addRightSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 32));
        
        $this->hitChart = array(
                0=> array(
                    9 => "Structure",
                    11 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    5 => "Energy Pulsar",
                    7 => "Medium Bolter",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Light Bolter",
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
