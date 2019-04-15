<?php

class Abrithi extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 400;
        $this->faction = "Dilgar";
        $this->phpclass = "Abrithi";
        $this->imagePath = "img/ships/abrithi.png";
        $this->shipClass = "Abrithi Assault Cruiser";
        $this->shipSizeClass = 3;
        
        $this->fighters = array("assault shuttles"=>14);
        $this->isd = 2226;

        $this->forwardDefense = 14;
        $this->sideDefense = 15;

        $this->turncost = 0.5;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;

        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(5, 11, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 7));
        $this->addPrimarySystem(new Engine(4, 10, 0, 5, 2));
        $this->addPrimarySystem(new Hangar(4, 16));

        $this->addFrontSystem(new MediumLaser(2, 6, 5, 300, 60));
        $this->addFrontSystem(new MediumLaser(2, 6, 5, 300, 60));
        $this->addFrontSystem(new Thruster(2, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(2, 7, 0, 3, 1));
        $this->addFrontSystem(new LightLaser(1, 4, 3, 300, 60));
        $this->addFrontSystem(new LightLaser(1, 4, 3, 300, 60));

        $this->addAftSystem(new PlasmaTorch(1, 4, 2, 180, 360));
        $this->addAftSystem(new Thruster(2, 4, 0, 1, 2));
        $this->addAftSystem(new Thruster(2, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(2, 4, 0, 1, 2));
        $this->addAftSystem(new Engine(3, 7, 0, 3, 2));
        $this->addAftSystem(new PlasmaTorch(1, 4, 2, 0, 180));

        $this->addLeftSystem(new Thruster(2, 11, 0, 4, 3));
        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 180, 360));

        $this->addRightSystem(new Thruster(2, 11, 0, 4, 4));
        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 27));
        $this->addAftSystem(new Structure( 4, 27));
        $this->addLeftSystem(new Structure( 4, 39));
        $this->addRightSystem(new Structure( 4, 39));
        $this->addPrimarySystem(new Structure( 5, 36));
/*        
        $this->hitChart = array(
                0=> array(
                    10 => "Structure",
                    12 => "Scanner",
                    14 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Medium Laser",
                    8 => "Light Laser",
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
		*/
    }

}
?>
