<?php
class Massan extends MediumShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 180;
        $this->faction = "Civilians";
        $this->phpclass = "Massan";
        $this->imagePath = "img/ships/YoluMassan.png";
        $this->shipClass = "Yolu Massan Freighter";
        $this->gravitic = true;
        $this->canvasSize = 100;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

        $this->isd = 2006;

        $this->forwardDefense = 13;
        $this->sideDefense = 14;

        $this->turncost = 1.5;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(5, 11, 0, 4));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 3, 5));
        $this->addPrimarySystem(new Engine(5, 14, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(4, 2, 1));
        $this->addPrimarySystem(new CargoBay(4, 100));
        $this->addPrimarySystem(new GraviticThruster(4, 13, 0, 4, 3));
        $this->addPrimarySystem(new GraviticThruster(4, 13, 0, 4, 4));

        $this->addFrontSystem(new GraviticThruster(4, 12, 0, 5, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 180, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 180));

        $this->addAftSystem(new GraviticThruster(4, 15, 0, 6, 2));
        $this->addAftSystem(new CargoBay(4, 24));
        $this->addAftSystem(new CargoBay(4, 24));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 40));
        
        $this->hitChart = array(
        		0=> array(
        				6 => "Thruster",
        				10 => "Cargo Bay",
        				13 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				6 => "Thruster",
        				9 => "Fusion Cannon",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				13 => "Cargo Bay",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
