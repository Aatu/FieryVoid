<?php
class wlcTigata extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 1100;
        $this->faction = "Minbari";
        $this->phpclass = "wlcTigata";
        $this->imagePath = "img/ships/tigara.png";
        $this->shipClass = "Tigata";
        $this->shipSizeClass = 3;
        $this->gravitic = true;
        $this->fighters = array("normal"=>6, "medium"=>6);
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 5;
        $this->unofficial = true;
        $this->isd = 1870;
        $this->variantOf ="Tigara";
		
		$this->notes = 'ALTERNATE UNIVERSE - unit designed for "In ancient times" campaign';


        // Ship system arguments: armor, health, power req, output
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 25, 4, 10));
        $this->addPrimarySystem(new Engine(6, 20, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 8));
        $this->addPrimarySystem(new Jammer(5, 10, 5));

        // weapons arguments: armor, health, power, start arc, end arc
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new AntimatterConverter(4, 7, 5, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new GraviticThruster(4, 25, 0, 6, 1));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new AntimatterConverter(4, 7, 5, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new Hangar(4, 6));
        $this->addAftSystem(new GraviticThruster(4, 38, 0, 10, 2));
        $this->addAftSystem(new JumpEngine(4, 12, 3, 36));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));

        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 300));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addLeftSystem(new GraviticThruster(4, 16, 0, 5, 3));

        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addRightSystem(new GraviticThruster(4, 16, 0, 5, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addLeftSystem(new Structure( 5, 70));
        $this->addRightSystem(new Structure( 5, 70));
        $this->addPrimarySystem(new Structure( 6, 60));
		
		$this->hitChart = array(
            0=> array(
                    9 => "Structure",
					11 => "Engine",
					13 => "Jammer",
                    15 => "Scanner",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
					5 => "Antimatter Converter",
					7 => "Molecular Disruptor",
					9 => "Fusion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Fusion Cannon",
            		9 => "Jump Engine",
                    11 => "Hangar",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    3 => "Thruster",
                    8 => "Fusion Cannon",
                    10 => "Molecular Disruptor",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    3 => "Thruster",
                    8 => "Fusion Cannon",
                    10 => "Molecular Disruptor",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
