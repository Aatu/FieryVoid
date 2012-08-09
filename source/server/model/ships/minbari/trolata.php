<?php
class Trolata extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 1100;
        $this->faction = "Minbari";
        $this->phpclass = "Trolata";
        $this->imagePath = "img/ships/troligan.png";
        $this->shipClass = "Trolata";
        $this->shipSizeClass = 3;
        $this->gravitic = true;

        $this->forwardDefense = 16;
        $this->sideDefense = 16;

        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 5;

        // Ship system arguments: armor, health, power req, output
        $this->addPrimarySystem(new Reactor(7, 23, 0, 0));
        $this->addPrimarySystem(new CnC(8, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 23, 4, 10));
        $this->addPrimarySystem(new Engine(7, 16, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(7, 2));
        $this->addPrimarySystem(new Jammer(8, 10, 5));

        // weapons arguments: armor, health, power, start arc, end arc
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addFrontSystem(new Thruster(6, 16, 0, 5, 1));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 180, 300));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new Thruster(6, 30, 0, 9, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 180));

        $this->addLeftSystem(new AntimatterConverter(5, 7, 5, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new Thruster(6, 14, 0, 4, 3));

        $this->addLeftSystem(new AntimatterConverter(5, 7, 5, 0, 120));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new Thruster(6, 14, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 44));
        $this->addAftSystem(new Structure( 7, 44));
        $this->addLeftSystem(new Structure( 7, 60));
        $this->addRightSystem(new Structure( 7, 60));
        $this->addPrimarySystem(new Structure( 8, 50));
    }
}
?>
