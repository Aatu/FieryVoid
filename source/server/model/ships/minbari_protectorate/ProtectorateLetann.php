<?php
class ProtectorateLetann extends HeavyCombatVessel{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 810;
        $this->faction = "Minbari Protectorate";
        $this->phpclass = "ProtectorateLetann";
        $this->imagePath = "img/ships/letann.png";
        $this->shipClass = "Letann Scout";
        $this->gravitic = true;
        $this->limited = 10;
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 35;
        $this->isd = 2020;
        $this->occurence = "rare";
        $this->variantOf = "Tinashi War Frigate";

        $this->addPrimarySystem(new Reactor(5, 25, 0, 6));
        $this->addPrimarySystem(new CnC(6, 24, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 24, 6, 13));
        $this->addPrimarySystem(new Engine(6, 20, 0, 10, 3));
        $this->addPrimarySystem(new JumpEngine(5, 20, 3, 12));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new GraviticThruster(4, 13, 0, 4, 3));
        $this->addPrimarySystem(new GraviticThruster(4, 13, 0, 4, 4));

        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 4, 1));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new GraviticThruster(4, 35, 0, 10, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 300, 60));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 60));
        $this->addAftSystem(new Structure( 4, 56));
        $this->addPrimarySystem(new Structure( 6, 60));
		
		$this->hitChart = array(
            0=> array(
                    9 => "Structure",
					11 => "Thruster",
                    12 => "Jump Engine",
					14 => "ELINT Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    8 => "Fusion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
                    10 => "Fusion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>

