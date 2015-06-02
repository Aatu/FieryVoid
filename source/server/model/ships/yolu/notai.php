<?php
class Notai extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 950;
        $this->faction = "Yolu";
        $this->phpclass = "Notai";
        $this->imagePath = "img/ships/notali.png";
        $this->shipClass = "Notali Assault Carrier";
        $this->gravitic = true;

        $this->forwardDefense = 15;
        $this->sideDefense = 17;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 28, 4, 12));
        $this->addPrimarySystem(new Engine(5, 23, 0, 10, 5));
        $this->addPrimarySystem(new Hangar(4, 2));

        $this->addFrontSystem(new GraviticThruster(5, 21, 0, 6, 1));

        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 10, 6, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 10, 6, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));

        $this->addAftSystem(new GraviticThruster(5, 28, 0, 10, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 240));

        $this->addLeftSystem(new GraviticThruster(5, 21, 0, 5, 3));
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 240, 60));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        $this->addLeftSystem(new Hangar(5, 12));

        $this->addRightSystem(new GraviticThruster(5, 21, 0, 5, 4));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 240, 60));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        $this->addRightSystem(new Hangar(5, 12));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 52));
        $this->addAftSystem(new Structure( 5, 52));
        $this->addPrimarySystem(new Structure( 6, 60 ));
        $this->addLeftSystem(new Structure( 6, 66));
        $this->addRightSystem(new Structure( 6, 66 ));


    }

}



?>
