<?php

class Nitratha extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 475;
        $this->faction = "Dilgar";
        $this->phpclass = "Nitratha";
        $this->imagePath = "img/ships/nitratha.png";
        $this->shipClass = "Ni'Tratha Jumpcruiser";
        $this->shipSizeClass = 3;

        $this->forwardDefense = 15;
        $this->sideDefense = 15;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        $this->addPrimarySystem(new Reactor(4, 18, 0, 0));
        $this->addPrimarySystem(new CnC(4, 21, 0, 1));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 7));
        $this->addPrimarySystem(new Engine(4, 9, 0, 5, 3));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new JumpEngine(4, 21, 4, 36));

        $this->addFrontSystem(new HeavyPlasma(2, 8, 5, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(2, 8, 5, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new LightBolter(1, 6, 2, 240, 120));
        $this->addFrontSystem(new LightBolter(1, 6, 2, 240, 120));

        $this->addAftSystem(new LightBolter(1, 6, 2, 180, 300));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 7, 0, 3, 3));
        $this->addAftSystem(new LightBolter(1, 6, 2, 60, 180));

        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
        $this->addLeftSystem(new LightBolter(1, 6, 2, 240, 360));
        $this->addLeftSystem(new LightBolter(1, 6, 2, 240, 360));
        $this->addLeftSystem(new MediumPlasma(2, 5, 3, 210, 330));

        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        $this->addRightSystem(new LightBolter(1, 6, 2, 0, 120));
        $this->addRightSystem(new LightBolter(1, 6, 2, 0, 120));
        $this->addRightSystem(new MediumPlasma(2, 5, 3, 30, 150));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addLeftSystem(new Structure( 4, 45));
        $this->addRightSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 4, 52));
        
        $this->hitChart = array(
                0=> array(
                    10 => "Structure",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Hvy Plasma Cannon",
                    8 => "Lt Bolter",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Lt Bolter",
                    10 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    4 => "Thruster",
                    6 => "Med Plasma Cannon",
                    9 => "Lt Bolter",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    4 => "Thruster",
                    6 => "Med Plasma Cannon",
                    9 => "Lt Bolter",
                    18 => "Structure",
                    20 => "Primary",
                ),
         );
    }
}
?>
