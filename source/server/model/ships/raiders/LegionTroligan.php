<?php
class LegionTroligan extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 830;
        $this->faction = "Raiders";
        $this->phpclass = "LegionTroligan";
        $this->imagePath = "img/ships/troligan.png";
        $this->shipClass = "Legion Captured Troligan";
        $this->shipSizeClass = 3;
        $this->gravitic = true;
        $this->limited = 10;
		$this->occurence = "unique";

		$this->notes = "Used only by the Imperial Star Legion";
		$this->notes .= "<br>This is a unique vessel as only one exists.";

        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 5;
        $this->isd = 2201;

        // Ship system arguments: armor, health, power req, output
        $this->addPrimarySystem(new Reactor(7, 23, 0, 0));
        $this->addPrimarySystem(new CnC(8, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 23, 4, 10));
        $this->addPrimarySystem(new Engine(7, 16, 0, 9, 3));
        $this->addPrimarySystem(new Hangar(7, 2));

        // weapons arguments: armor, health, power, start arc, end arc
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new MediumPulse(5, 6, 3, 300, 60));
        $this->addFrontSystem(new GraviticThruster(6, 16, 0, 5, 1));
        $this->addFrontSystem(new MediumPulse(5, 6, 3, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new MediumPulse(6, 6, 3, 120, 240));
        $this->addAftSystem(new GraviticThruster(6, 30, 0, 9, 2));
        $this->addAftSystem(new MediumPulse(6, 6, 3, 120, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));

        $this->addLeftSystem(new MediumPulse(5, 6, 3, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new GraviticThruster(6, 14, 0, 4, 3));

        $this->addRightSystem(new MediumPulse(5, 6, 3, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new GraviticThruster(6, 14, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 44));
        $this->addAftSystem(new Structure( 7, 44));
        $this->addLeftSystem(new Structure( 7, 60));
        $this->addRightSystem(new Structure( 7, 60));
        $this->addPrimarySystem(new Structure( 8, 50));
		
		$this->hitChart = array(
            0=> array(
                    12 => "Structure",
					14 => "Engine",
                    16 => "Scanner",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    6 => "Medium Pulse Cannon",
                    8 => "Fusion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Medium Pulse Cannon",
                    10 => "Fusion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
					6 => "Medium Pulse Cannon",
                    9 => "Fusion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
					6 => "Medium Pulse Cannon",
                    9 => "Fusion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
		
    }
}
?>