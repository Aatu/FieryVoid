<?php
class ProtectorateRetlata extends HeavyCombatVessel{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 540;
        $this->faction = "Minbari Protectorate";
        $this->phpclass = "ProtectorateRetlata";
        $this->imagePath = "img/ships/rogata.png";
        $this->shipClass = "Retlata Transport";
        $this->gravitic = true;
        $this->occurence = "common";
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 15;
        $this->isd = 2006;

        $this->addPrimarySystem(new FusionCannon(3, 8, 1, 180, 0));
        $this->addPrimarySystem(new CargoBay(3, 35));
        $this->addPrimarySystem(new Reactor(5, 30, 0, 2));
        $this->addPrimarySystem(new CnC(5, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 8));
        $this->addPrimarySystem(new Engine(5, 28, 0, 14, 3));
        $this->addPrimarySystem(new TractorBeam(4, 10, 0, 0));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new GraviticThruster(4, 15, 0, 6, 3));
        $this->addPrimarySystem(new GraviticThruster(4, 15, 0, 6, 4));
        $this->addPrimarySystem(new CargoBay(3, 35));
        $this->addPrimarySystem(new FusionCannon(3, 8, 1, 0, 180));

        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new GraviticThruster(4, 20, 0, 8, 1));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 120));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new CargoBay(3, 20));
        $this->addAftSystem(new GraviticThruster(4, 35, 0, 10, 2));
        $this->addAftSystem(new CargoBay(3, 20));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 300, 60));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 36));
        $this->addAftSystem(new Structure( 5, 36));
        $this->addPrimarySystem(new Structure( 5, 45));
		
		$this->hitChart = array(
            0=> array(
                    7 => "Structure",
					8 => "Tractor Beam",
					9 => "Fusion Cannon",
					11 => "Cargo Bay",
					13 => "Thruster",
					15 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    8 => "Fusion Cannon",
                    10 => "Electro-Pulse Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Fusion Cannon",
					12 => "Cargo Bay",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
