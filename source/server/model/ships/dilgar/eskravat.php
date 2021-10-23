<?php

class Eskravat extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 460;
        $this->faction = "Dilgar";
        $this->phpclass = "Eskravat";
        $this->imagePath = "img/ships/athraskala.png";
        $this->shipClass = "Eskravat Refueling Barge";
        $this->shipSizeClass = 3;
        $this->isd = 2229;

        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 4;

        $this->addPrimarySystem(new Reactor(5, 13, 0, 0));
        $this->addPrimarySystem(new CnC(5, 13, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 7));
        $this->addPrimarySystem(new Engine(4, 11, 0, 6, 4));
        $this->addPrimarySystem(new Hangar(4, 3));
        $this->addPrimarySystem(new ReloadRack(5, 9));

        $this->addFrontSystem(new SMissileRack(3, 6, 0, 240, 60));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 120));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
		$this->addFrontSystem(new CargoBay(4, 25));
		$this->addFrontSystem(new CargoBay(4, 25));

        $this->addAftSystem(new SMissileRack(1, 6, 0, 120, 240));
        $this->addAftSystem(new SMissileRack(1, 6, 0, 120, 240));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 2, 2));
        $this->addAftSystem(new Engine(3, 7, 0, 4, 4));
		$this->addAftSystem(new CargoBay(4, 25));
		$this->addAftSystem(new CargoBay(4, 25));

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
                    11 => "Structure",
					12 => "Reload Rack",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
                ),
                1=> array(
                    4 => "Thruster",
                    6 => "Class-S Missile Rack",
                    11 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
                ),
                2=> array(
                    6 => "Thruster",
                    8 => "Class-S Missile Rack",
                    12 => "Cargo Bay",
                    13 => "Engine",
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
