<?php

class Athraskala extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 600;
        $this->faction = "Dilgar";
        $this->phpclass = "Athraskala";
        $this->imagePath = "img/ships/athraskala.png";
        $this->shipClass = "Athraskala Heavy Bomber";
        $this->shipSizeClass = 3;
        $this->isd = 2229;

        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->limited = 33;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        $this->addPrimarySystem(new Reactor(5, 13, 0, 4));
        $this->addPrimarySystem(new CnC(5, 13, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 7));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 4));
        $this->addPrimarySystem(new Hangar(4, 2));

        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new MassDriver(4, 18, 9, 330, 30));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $bombrack1 = new BombRack(1, 6, 0, 300, 60);
        //$bombrack1->addAmmo("B", 8);
        $this->addFrontSystem($bombrack1);
        $bombrack2 = new BombRack(1, 6, 0, 300, 60);
        //$bombrack2->addAmmo("B", 8);
        $this->addFrontSystem($bombrack2);

        $this->addAftSystem(new SMissileRack(1, 6, 0, 120, 240));
        $this->addAftSystem(new SMissileRack(1, 6, 0, 120, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 7, 0, 4, 4));
        $bombrack3 = new BombRack(1, 6, 0, 300, 60);
        //$bombrack3->addAmmo("B", 8);
        $this->addAftSystem($bombrack3);
        $bombrack4 = new BombRack(1, 6, 0, 300, 60);
        //$bombrack4->addAmmo("B", 8);
        $this->addAftSystem($bombrack4);

        $this->addLeftSystem(new Thruster(3, 13, 0, 5, 3));
        $this->addLeftSystem(new ScatterPulsar(1, 4, 2, 180, 360));

        $this->addRightSystem(new Thruster(3, 13, 0, 5, 4));
        $this->addRightSystem(new ScatterPulsar(1, 4, 2, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 42));
        $this->addRightSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 5, 40));
        
        $this->hitChart = array(
                0=> array(
                    12 => "Structure",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Class-S Missile Rack",
                    8 => "Bomb Rack",
                    11 => "Mass Driver",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Class-S Missile Rack",
                    10 => "Bomb Rack",
                    11 => "Engine",
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
