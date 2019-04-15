<?php

class Tratharti extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 750;
        $this->faction = "Dilgar";
        $this->phpclass = "Tratharti";
        $this->imagePath = "img/ships/tratharti.png";
        $this->shipClass = "Tratharti Gunship";
        $this->shipSizeClass = 3;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 2;

        $this->addPrimarySystem(new Reactor(4, 23, 0, -4));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 8));
        $this->addPrimarySystem(new Engine(4, 13, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(3, 2));

        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addFrontSystem(new MassDriver(5, 18, 9, 330, 30));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));

        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 120, 300));
        $this->addAftSystem(new HeavyBolter(3, 10, 6, 120, 240));
        $this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 4, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 9, 0, 4, 2));
        $this->addAftSystem(new HeavyBolter(3, 10, 6, 120, 240));
        $this->addAftSystem(new ScatterPulsar(1, 4, 2, 60, 240));

        $this->addLeftSystem(new HeavyBolter(2, 10, 6, 240, 0));
        $this->addLeftSystem(new HeavyBolter(3, 10, 6, 240, 0));
        $this->addLeftSystem(new Thruster(3, 11, 0, 4, 3));

        $this->addRightSystem(new HeavyBolter(2, 10, 6, 0, 120));
        $this->addRightSystem(new HeavyBolter(3, 10, 6, 0, 120));
        $this->addRightSystem(new Thruster(3, 11, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 40));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 48));
        $this->addRightSystem(new Structure( 5, 48));
        $this->addPrimarySystem(new Structure( 5, 52));
        
        $this->hitChart = array(
                0=> array(
                    13 => "Structure",
                    15 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Medium Laser",
                    9 => "Mass Driver",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Heavy Bolter",
                    10 => "Scatter Pulsar",
                    11 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    6 => "Thruster",
                    10 => "Heavy Bolter",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    6 => "Thruster",
                    10 => "Heavy Bolter",
                    18 => "Structure",
                    20 => "Primary",
                ),
         );
    }
}
?>
