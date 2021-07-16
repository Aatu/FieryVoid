<?php
class wlcTinshara extends HeavyCombatVessel{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 725;
        $this->faction = "Minbari";
        $this->phpclass = "wlcTinshara";
        $this->imagePath = "img/ships/tinashi.png";
        $this->shipClass = "Tinshara War Frigate";
        $this->gravitic = true;
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        $this->turncost = 1.0;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 35;
        $this->isd = 1869;
        $this->variantOf = "Tinashi War Frigate";
        $this->unofficial = true;
		
		$this->notes = 'ALTERNATE UNIVERSE - unit designed for "In ancient times" campaign';


        $this->addPrimarySystem(new Reactor(5, 21, 0, 0));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 20, 4, 11));
        $this->addPrimarySystem(new Engine(5, 16, 0, 9, 3));
        $this->addPrimarySystem(new JumpEngine(5, 20, 3, 14));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new GraviticThruster(4, 12, 0, 4, 3));
        $this->addPrimarySystem(new GraviticThruster(4, 12, 0, 4, 4));
        $this->addPrimarySystem(new Jammer(4, 8, 5));

        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 4, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new AntimatterConverter(4, 7, 5, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 0, 120));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 4, 1));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 240, 0));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 240, 0));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new GraviticThruster(4, 33, 0, 9, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 240));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 0, 120));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 0, 120));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 56));
        $this->addAftSystem(new Structure( 4, 56));
        $this->addPrimarySystem(new Structure( 5, 55));
		
		$this->hitChart = array(
            0=> array(
                    7 => "Structure",
					9 => "Thruster",
                    10 => "Jump Engine",
                    12 => "Jammer",
					14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    3 => "Thruster",
					5 => "Molecular Disruptor",
                    8 => "Fusion Cannon",
                    10 => "Antimatter Converter",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					9 => "Molecular Disruptor",
                    12 => "Fusion Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>
