<?php

class Protra extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 500;
        $this->faction = "Dilgar";
        $this->phpclass = "Protra";
        $this->imagePath = "img/ships/protra.png";
        $this->shipClass = "Protra Scoutship";
        $this->shipSizeClass = 3;
        $this->iniativebonus = 5;
                $this->isd = 2218;
        
        $this->limited = 33;
        
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
        $this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new JumpEngine(4, 10, 3, 36));

        $this->addFrontSystem(new PlasmaTorch(1, 4, 2, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addFrontSystem(new PlasmaTorch(1, 4, 2, 300, 60));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 8, 0, 3, 1));

        $this->addAftSystem(new PlasmaTorch(1, 4, 2, 120, 240));
        $this->addAftSystem(new Thruster(2, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 10, 0, 4, 2));
        $this->addAftSystem(new Engine(3, 7, 0, 3, 4));
        $this->addAftSystem(new PlasmaTorch(1, 4, 2, 120, 240));

        $this->addLeftSystem(new LightLaser(1, 4, 3, 180, 360));
        $this->addLeftSystem(new Thruster(2, 11, 0, 4, 3));

        $this->addRightSystem(new LightLaser(1, 4, 3, 0, 180));
        $this->addRightSystem(new Thruster(2, 11, 0, 4, 4));

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
                    5 => "Heavy Plasma Cannon",
                    7 => "Plasma Torch",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Plasma Torch",
                    9 => "Engine",
                    18 => "Structure",
                    20 => "Primary",
                ),
                3=> array(
                    5 => "Thruster",
                    7 => "Light Laser",
                    18 => "Structure",
                    20 => "Primary",
                ),
                4=> array(
                    5 => "Thruster",
                    7 => "Light Laser",
                    18 => "Structure",
                    20 => "Primary",
                ),
         );
    }
}

?>
