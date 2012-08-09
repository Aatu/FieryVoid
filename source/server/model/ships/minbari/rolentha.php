<?php
class Rolentha extends HeavyCombatVessel{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 800;
        $this->faction = "Minbari";
        $this->phpclass = "Rolentha";
        $this->imagePath = "img/ships/rogata.png";
        $this->shipClass = "Rolentha";
        $this->gravitic = true;


        $this->forwardDefense = 17;
        $this->sideDefense = 17;

        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 25;

        $this->addPrimarySystem(new FusionCannon(3, 8, 1, 180, 0));
        $this->addPrimarySystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new CnC(5, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 8));
        $this->addPrimarySystem(new Engine(5, 28, 0, 14, 3));
        $this->addPrimarySystem(new Jammer(4, 8, 5));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 6, 4));
        $this->addPrimarySystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addPrimarySystem(new FusionCannon(3, 8, 1, 0, 180));

        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 240, 0));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 240, 0));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new Thruster(4, 20, 0, 8, 1));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 0, 120));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 0, 120));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new TractorBeam(4, 10, 0, 0));
        $this->addAftSystem(new Thruster(4, 35, 0, 10, 2));
        $this->addAftSystem(new TractorBeam(4, 10, 0, 0));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 36));
        $this->addAftSystem(new Structure( 5, 36));
        $this->addPrimarySystem(new Structure( 5, 45));
    }
}
?>
