<?php
class ProtectorateTigarin extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

            $this->pointCost = 1350;
            $this->faction = "Minbari Protectorate";
            $this->phpclass = "ProtectorateTigarin";
            $this->imagePath = "img/ships/tigara.png";
            $this->shipClass = "Protectorate Tigarin Patrol Cruiser";
            $this->shipSizeClass = 3;
            $this->gravitic = true;
            $this->fighters = array("normal"=>6);
            $this->forwardDefense = 15;
            $this->sideDefense = 17;
            $this->turncost = 1.0;
            $this->turndelaycost = 1.0;
            $this->accelcost = 4;
            $this->rollcost = 3;
            $this->pivotcost = 4;
            $this->iniativebonus = 5;
            $this->variantOf ="Protectorate Tigara Attack Cruiser";
			$this->occurence = "rare";
            $this->isd = 2059;

            // Ship system arguments: armor, health, power req, output
            $this->addPrimarySystem(new Reactor(6, 20, 0, 0));
            $this->addPrimarySystem(new CnC(6, 24, 0, 0));
            $this->addPrimarySystem(new Scanner(6, 28, 4, 11));
            $this->addPrimarySystem(new Engine(6, 20, 0, 10, 3));
            $this->addPrimarySystem(new Hangar(5, 8));

                    // weapons arguments: armor, health, power, start arc, end arc
            $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
            $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
            $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
            $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
            $this->addFrontSystem(new GraviticThruster(4, 25, 0, 6, 1));
            $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
            $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
            $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 0));
            $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 120));

            $this->addAftSystem(new FusionCannon(3, 8, 1, 180, 300));
            $this->addAftSystem(new NeutronLaser(4, 10, 6, 120, 240));
            $this->addAftSystem(new GraviticThruster(4, 38, 0, 10, 2));
            $this->addAftSystem(new NeutronLaser(4, 10, 6, 120, 240));
            $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 180));

            $this->addLeftSystem(new NeutronLaser(4, 10, 6, 240, 0));
            $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
            $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
            $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 300));
            $this->addLeftSystem(new GraviticThruster(4, 16, 0, 5, 3));

            $this->addRightSystem(new NeutronLaser(4, 10, 6, 0, 120));
            $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
            $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
            $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 180));
            $this->addRightSystem(new GraviticThruster(4, 16, 0, 5, 4));

            //0:primary, 1:front, 2:rear, 3:left, 4:right;
            $this->addFrontSystem(new Structure( 6, 50));
            $this->addAftSystem(new Structure( 5, 50));
            $this->addLeftSystem(new Structure( 5, 72));
            $this->addRightSystem(new Structure( 5, 72));
            $this->addPrimarySystem(new Structure( 6, 62));
			
			$this->hitChart = array(
            0=> array(
                    11 => "Structure",
					13 => "Engine",
                    15 => "Scanner",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
					5 => "Neutron Laser",
					9 => "Fusion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Fusion Cannon",
                    10 => "Neutron Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    3 => "Thruster",
                    8 => "Fusion Cannon",
                    10 => "Neutron Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    3 => "Thruster",
                    8 => "Fusion Cannon",
                    10 => "Neutron Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
