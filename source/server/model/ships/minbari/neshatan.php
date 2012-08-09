<?php
class Neshatan extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

	$this->pointCost = 2300;
	$this->faction = "Minbari";
        $this->phpclass = "Neshatan";
        $this->imagePath = "img/ships/neshatan.png";
        $this->shipClass = "Neshatan Gunship";
        $this->shipSizeClass = 3;
        $this->gravitic = true;

        $this->forwardDefense = 16;
        $this->sideDefense = 19;

        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 5;
        $this->rollcost = 5;
        $this->pivotcost = 5;
        $this->iniativebonus = 5;

        // Ship system arguments: armor, health, power req, output
        $this->addPrimarySystem(new Reactor(6, 35, 0, -6));
        $this->addPrimarySystem(new CnC(6, 26, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 28, 4, 12));
        $this->addPrimarySystem(new Engine(6, 28, 0, 12, 4));
        $this->addPrimarySystem(new JumpEngine(5, 30, 4, 10));
        $this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new Jammer(4, 10, 5));

        // weapons arguments: armor, health, power, start arc, end arc
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new Thruster(5, 20, 0, 5, 1));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 180, 300));
        $this->addAftSystem(new NeutronLaser(4, 10, 6, 180, 300));
        $this->addAftSystem(new NeutronLaser(4, 10, 6, 180, 300));
        $this->addAftSystem(new Thruster(4, 48, 0, 12, 2));
        $this->addAftSystem(new NeutronLaser(4, 10, 6, 60, 180));
        $this->addAftSystem(new NeutronLaser(4, 10, 6, 60, 180));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 180));

        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new NeutronLaser(4, 10, 6, 240, 0));
        $this->addLeftSystem(new Thruster(4, 16, 0, 5, 3));

        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new NeutronLaser(4, 10, 6, 0, 120));
        $this->addRightSystem(new Thruster(4, 16, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 60));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addLeftSystem(new Structure( 5, 80));
        $this->addRightSystem(new Structure( 5, 80));
        $this->addPrimarySystem(new Structure( 6, 66));
    }
}
?>
