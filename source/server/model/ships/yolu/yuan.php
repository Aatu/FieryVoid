<?php
class Yuan extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 2100;
        $this->faction = "Yolu";
        $this->phpclass = "Yuan";
        $this->imagePath = "img/ships/yuan.png";
        $this->shipClass = "Yuan Dreadnought";
        $this->gravitic = true;
        $this->fighters = array("normal"=>12);
        $this->canvasSize = 280;

        $this->isd = 2100;

        $this->forwardDefense = 18;
        $this->sideDefense = 19;
		$this->limited = 10;
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 4;
        $this->pivotcost = 6;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(7, 35, 0, 6));
        $this->addPrimarySystem(new CnC(7, 28, 0, 0));
        $this->addPrimarySystem(new Scanner(7, 32, 5, 13));
        $this->addPrimarySystem(new Engine(7, 30, 0, 16, 6));
        $this->addPrimarySystem(new Hangar(6, 16));

    //    $this->addFrontSystem(new JumpEngine(6, 25, 6, 18));
        $this->addFrontSystem(new GraviticThruster(5, 28, 0, 10, 1));
        $this->addFrontSystem(new FusionCannon(4, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(4, 8, 1, 240, 60));
        $this->addFrontSystem(new MolecularFlayer(5, 8, 4, 300, 0));
        $this->addFrontSystem(new DestabilizerBeam(5, 10, 8, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(5, 8, 6, 300, 60));
        $this->addFrontSystem(new FusionAgitator(5, 10, 4, 300, 60));
        $this->addFrontSystem(new FusionAgitator(5, 10, 4, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(5, 8, 6, 300, 60));
        $this->addFrontSystem(new DestabilizerBeam(5, 10, 8, 300, 60));
        $this->addFrontSystem(new MolecularFlayer(5, 8, 4, 0, 60));
        $this->addFrontSystem(new FusionCannon(4, 8, 1, 300, 120));
        $this->addFrontSystem(new FusionCannon(4, 8, 1, 300, 120));

        $this->addAftSystem(new GraviticThruster(6, 40, 0, 16, 2));
        $this->addAftSystem(new FusionCannon(4, 8, 1, 120, 300));
        $this->addAftSystem(new FusionCannon(4, 8, 1, 120, 300));
        $this->addAftSystem(new MolecularDisruptor(5, 8, 6, 120, 240));
        $this->addAftSystem(new MolecularDisruptor(5, 8, 6, 120, 240));
        $this->addAftSystem(new FusionCannon(4, 8, 1, 60, 240));
        $this->addAftSystem(new FusionCannon(4, 8, 1, 60, 240));

        $this->addLeftSystem(new GraviticThruster(5, 25, 0, 8, 3));
        $this->addLeftSystem(new FusionCannon(4, 8, 1, 180, 0));
        $this->addLeftSystem(new FusionCannon(4, 8, 1, 180, 0));
        $this->addLeftSystem(new MolecularFlayer(5, 8, 4, 240, 300));
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 240, 360));
        $this->addLeftSystem(new FusionAgitator(5, 10, 4, 240, 0));
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 180, 300));
        $this->addLeftSystem(new FusionAgitator(5, 10, 4, 180, 300));

        $this->addRightSystem(new GraviticThruster(5, 25, 0, 8, 4));
        $this->addRightSystem(new FusionCannon(4, 8, 1, 0, 180));
        $this->addRightSystem(new FusionCannon(4, 8, 1, 0, 180));
        $this->addRightSystem(new MolecularFlayer(5, 8, 4, 60, 120));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 0, 120));
        $this->addRightSystem(new FusionAgitator(5, 10, 4, 0, 120));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 60, 180));
        $this->addRightSystem(new FusionAgitator(5, 10, 4, 60, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 80));
        $this->addAftSystem(new Structure( 6, 80));
        $this->addPrimarySystem(new Structure( 7, 90 ));
        $this->addLeftSystem(new Structure( 6, 96));
        $this->addRightSystem(new Structure( 6, 96 ));

        $this->hitChart = array(
        		0=> array(
        				12 => "Structure",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				3 => "Thruster",
        				5 => "Fusion Agitator",
        				6 => "Molecular Flayer",
        				7 => "Fusion Cannon",
        				9 => "Destabilizer Beam",
        				11 => "Molecular Disruptor",
        				13 => "Jump Engine",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Molecular Disruptor",
        				11 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Molecular Flayer",
        				8 => "Molecular Disruptor",
        				10 => "Fusion Agitator",
        				12 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Molecular Flayer",
        				8 => "Molecular Disruptor",
        				10 => "Fusion Agitator",
        				12 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }

}



?>
