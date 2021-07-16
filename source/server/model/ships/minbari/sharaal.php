<?php
class Sharaal extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1600;
		$this->faction = "Minbari";
        $this->phpclass = "Sharaal";
        $this->imagePath = "img/ships/sharlin.png";
        $this->shipClass = "Sharaal War Cruiser";
        $this->shipSizeClass = 3;
        $this->gravitic = true;
		$this->canvasSize = 280;
        $this->occurence = "uncommon";
        $this->fighters = array("normal"=>24);
        $this->forwardDefense = 15;
        $this->sideDefense = 19;
        $this->turncost = 1.33;
        $this->turndelaycost = 1.0;
        $this->accelcost = 5;
        $this->rollcost = 4;
        $this->pivotcost = 5;
        $this->iniativebonus = 5;
        $this->isd = 1999;
        $this->variantOf = "Sharlin War Cruiser";

        // Ship system arguments: armor, health, power req, output
        $this->addPrimarySystem(new Reactor(5, 33, 0, -2));
        $this->addPrimarySystem(new CnC(6, 26, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 28, 4, 11));
        $this->addPrimarySystem(new Engine(5, 22, 0, 10, 4));
        $this->addPrimarySystem(new JumpEngine(5, 25, 4, 12));
		$this->addPrimarySystem(new Hangar(5, 28));
        $this->addPrimarySystem(new Jammer(4, 10, 5));
        $this->addPrimarySystem(new TractorBeam(5, 10, 0, 0));

        // weapons arguments: armor, health, power, start arc, end arc
		$this->addFrontSystem(new GraviticThruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new AntimatterConverter(4, 11, 5, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new AntimatterConverter(4, 11, 5, 300, 60));
        $this->addFrontSystem(new GraviticThruster(4, 10, 0, 3, 1));

        $this->addAftSystem(new AntimatterConverter(4, 11, 5, 120, 240));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        $this->addAftSystem(new GraviticThruster(4, 48, 0, 12, 2));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        $this->addAftSystem(new AntimatterConverter(4, 11, 5, 120, 240));

        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new GraviticThruster(4, 14, 0, 5, 3));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 300));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 300));

        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new GraviticThruster(4, 14, 0, 5, 4));
		$this->addRightSystem(new FusionCannon(3, 8, 1, 60, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 66));
        $this->addAftSystem(new Structure( 4, 66));
        $this->addLeftSystem(new Structure( 5, 92));
        $this->addRightSystem(new Structure( 5, 92));
        $this->addPrimarySystem(new Structure( 5, 72));
		
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
                    5 => "Thruster",
                    8 => "Molecular Disruptor",
                    11 => "Antimatter Converter",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    9 => "Molecular Disruptor",
                    12 => "Antimatter Converter",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    3 => "Thruster",
                    8 => "Fusion Cannon",
                    11 => "Molecular Disruptor",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    3 => "Thruster",
                    8 => "Fusion Cannon",
                    11 => "Molecular Disruptor",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
