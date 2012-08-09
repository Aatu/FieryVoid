<?php
class Letann extends HeavyCombatVessel{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 900;
        $this->faction = "Minbari";
        $this->phpclass = "Letann";
        $this->imagePath = "img/ships/tinashi.png";
        $this->shipClass = "Letann Scout";
        $this->gravitic = true;


        $this->forwardDefense = 14;
        $this->sideDefense = 17;

        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 35;

        $this->addPrimarySystem(new Reactor(5, 25, 0, 6));
        $this->addPrimarySystem(new CnC(6, 24, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 24, 6, 13));
        $this->addPrimarySystem(new Engine(6, 20, 0, 10, 3));
        $this->addPrimarySystem(new JumpEngine(5, 20, 3, 12));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Jammer(4, 8, 5));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 4));

        $this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addFrontSystem(new Thruster(4, 8, 0, 4, 1));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new Thruster(4, 35, 0, 10, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 300, 60));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 60));
        $this->addAftSystem(new Structure( 4, 56));
        $this->addPrimarySystem(new Structure( 6, 60));
    }
}
?>

