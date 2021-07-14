<?php
class GreySharlin extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2100;
		$this->faction = "Minbari";
        $this->phpclass = "GreySharlin";
        $this->imagePath = "img/ships/sharlin.png";
        $this->shipClass = "Grey Sharlin";
        $this->shipSizeClass = 3;
        $this->gravitic = true;
		$this->canvasSize = 280;
        $this->fighters = array("normal"=>24);
        $this->forwardDefense = 15;
        $this->sideDefense = 19;
        $this->turncost = 1.33;
        $this->turndelaycost = 1.0;
        $this->accelcost = 5;
        $this->rollcost = 4;
        $this->pivotcost = 5;
        $this->iniativebonus = 5;
        $this->isd = 2058;
        $this->occurence = "unique";
        $this->variantOf = "Sharlin War Cruiser";

        // Ship system arguments: armor, health, power req, output
        $this->addPrimarySystem(new Reactor(6, 35, 0, 0));
        $this->addPrimarySystem(new CnC(7, 30, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 35, 4, 14));
        $this->addPrimarySystem(new Engine(6, 31, 0, 15, 4));
        $this->addPrimarySystem(new JumpEngine(6, 30, 4, 10));
		$this->addPrimarySystem(new Hangar(6, 28));
        $this->addPrimarySystem(new Jammer(6, 10, 5));
        $this->addPrimarySystem(new TractorBeam(6, 10, 0, 0));

        // weapons arguments: armor, health, power, start arc, end arc
		$this->addFrontSystem(new GraviticThruster(5, 12, 0, 5, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 300, 60));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new GraviticThruster(5, 12, 0, 5, 1));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new NeutronLaser(4, 10, 6, 120, 240));
        $this->addAftSystem(new GraviticThruster(5, 56, 0, 15, 2));
        $this->addAftSystem(new NeutronLaser(4, 10, 6, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));

        $this->addLeftSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new GraviticThruster(5, 16, 0, 7, 3));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 300));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 300));

        $this->addRightSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new GraviticThruster(5, 16, 0, 7, 4));
		$this->addRightSystem(new FusionCannon(3, 8, 1, 60, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 70));
        $this->addAftSystem(new Structure( 5, 70));
        $this->addLeftSystem(new Structure( 6, 96));
        $this->addRightSystem(new Structure( 6, 96));
        $this->addPrimarySystem(new Structure( 6, 80));
		
		$this->hitChart = array(
            0=> array(
                    6 => "Structure",
					8 => "Engine",
                    10 => "Jump Engine",
					11 => "Tractor Beam",
					13 => "Jammer",
                    15 => "Scanner",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
                    7 => "Neutron Laser",
                    11 => "Fusion Cannon",
					12 => "Electro-Pulse Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    9 => "Fusion Cannon",
                    12 => "Neutron Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    3 => "Thruster",
                    8 => "Fusion Cannon",
                    12 => "Neutron Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    3 => "Thruster",
                    8 => "Fusion Cannon",
                    12 => "Neutron Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
