<?php
class Shargoti extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2500;
		$this->faction = "Minbari";
        $this->phpclass = "Shargoti";
        $this->imagePath = "img/ships/sharlin.png";
        $this->shipClass = "Shargoti Heavy Battlecruiser";
        $this->shipSizeClass = 3;
        $this->gravitic = true;
        $this->canvasSize = 310;
        $this->fighters = array("normal"=>24);
        $this->limited = 10;

		$this->notes .= "<br>official Shargoti Battlecruiser with Gravity Nets replaced by Fusion Cannons"; 
		$this->unofficial = true;

		
        $this->forwardDefense = 16;
        $this->sideDefense = 20;

        $this->turncost = 1.5;
        $this->turndelaycost = 1.33;
        $this->accelcost = 6;
        $this->rollcost = 5;
        $this->pivotcost = 5;
        $this->iniativebonus = 5;
		$this->isd = 2112;
        
        // Ship system arguments: armor, health, power req, output
        $this->addPrimarySystem(new Reactor(6, 40, 0, 0));
        $this->addPrimarySystem(new CnC(7, 36, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 33, 4, 13));
        $this->addPrimarySystem(new Engine(6, 33, 0, 16, 5));
        $this->addPrimarySystem(new JumpEngine(6, 30, 4, 10));
		$this->addPrimarySystem(new Hangar(6, 30));
        $this->addPrimarySystem(new Jammer(6, 10, 5));
        $this->addPrimarySystem(new TractorBeam(6, 10, 0, 0));

        // weapons arguments: armor, health, power, start arc, end arc
		$this->addFrontSystem(new GraviticThruster(4, 14, 0, 5, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 360));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new ElectroPulseGun(2, 6, 3, 300, 60));
        $this->addFrontSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new GraviticThruster(4, 14, 0, 5, 1));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new NeutronLaser(4, 10, 6, 120, 240));
        $this->addAftSystem(new GraviticThruster(4, 56, 0, 16, 2));
        $this->addAftSystem(new NeutronLaser(4, 10, 6, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));

        $this->addLeftSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 300));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 300));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 300));
        $this->addLeftSystem(new GraviticThruster(4, 20, 0, 7, 3));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 360));

        $this->addRightSystem(new NeutronLaser(4, 10, 6, 300, 60));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 180));
        $this->addRightSystem(new GraviticThruster(4, 20, 0, 7, 4));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 80));
        $this->addAftSystem(new Structure( 6, 80));
        $this->addLeftSystem(new Structure( 6, 108));
        $this->addRightSystem(new Structure( 6, 108));
        $this->addPrimarySystem(new Structure( 7, 96));
		
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
					13 => "Electro-Pulse Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    10 => "Fusion Cannon",
                    12 => "Neutron Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    3 => "Thruster",
                    9 => "Fusion Cannon",
                    12 => "Neutron Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    3 => "Thruster",
                    9 => "Fusion Cannon",
                    12 => "Neutron Laser",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
