<?php
class Notali extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 1000;
        $this->faction = "Yolu";
        $this->phpclass = "Notali";
        $this->imagePath = "img/ships/notali.png";
        $this->shipClass = "Notali Carrier";
        $this->gravitic = true;

        $this->forwardDefense = 15;
        $this->sideDefense = 17;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        $this->limited = 33;
        $this->fighters = array("normal"=>24);

        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 28, 4, 12));
        $this->addPrimarySystem(new Engine(5, 23, 0, 10, 5));
        $this->addPrimarySystem(new Hangar(4, 2));

        $this->addFrontSystem(new GraviticThruster(5, 21, 0, 6, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionAgitator(4, 10, 4, 300, 60));
        $this->addFrontSystem(new MolecularFlayer(4, 8, 4, 330, 30));
        $this->addFrontSystem(new FusionAgitator(4, 10, 4, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));

        $this->addAftSystem(new GraviticThruster(5, 28, 0, 10, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 240));

        $this->addLeftSystem(new GraviticThruster(5, 21, 0, 5, 3));
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        $this->addLeftSystem(new Hangar(5, 12));

        $this->addRightSystem(new GraviticThruster(5, 21, 0, 5, 4));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        $this->addRightSystem(new Hangar(5, 12));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 52));
        $this->addAftSystem(new Structure( 5, 52));
        $this->addPrimarySystem(new Structure( 6, 60 ));
        $this->addLeftSystem(new Structure( 6, 66));
        $this->addRightSystem(new Structure( 6, 66 ));

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
        				6 => "Fusion Agitator",
        				7 => "Molecular Flayer",
        				9 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				8 => "Molecular Disruptor",
        				10 => "Fusion Cannon",
        				12 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				8 => "Molecular Disruptor",
        				10 => "Fusion Cannon",
        				12 => "Hangar",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }

}



?>
