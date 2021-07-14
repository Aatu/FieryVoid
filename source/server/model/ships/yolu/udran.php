<?php
class Udran extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 1375;
        $this->faction = "Yolu";
        $this->phpclass = "Udran";
        $this->imagePath = "img/ships/ulana.png";
        $this->shipClass = "Udran Command Cruiser";
        $this->gravitic = true;
        $this->occurence = "rare";
		$this->variantOf = "Ulana Patrol Cruiser";       
        $this->fighters=array("normal"=>6);

        $this->isd = 2241;

        $this->forwardDefense = 16;
        $this->sideDefense = 17;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;


        $this->addPrimarySystem(new Reactor(7, 25, 0, 4));
        $this->addPrimarySystem(new CnC(7, 24, 0, 1));
        $this->addPrimarySystem(new Scanner(6, 28, 5, 13));
        $this->addPrimarySystem(new Engine(7, 25, 0, 12, 6));
        $this->addPrimarySystem(new Hangar(5, 2, 6));

        $this->addFrontSystem(new GraviticThruster(5, 21, 0, 6, 1));
        $this->addFrontSystem(new DestabilizerBeam(4, 8, 6, 300, 60));
        $this->addFrontSystem(new FusionAgitator(4, 10, 4, 300, 60));
        $this->addFrontSystem(new JumpEngine(6, 20, 6, 18));
        $this->addFrontSystem(new FusionAgitator(4, 10, 4, 300, 60));
        $this->addFrontSystem(new DestabilizerBeam(4, 8, 6, 300, 60));

        $this->addAftSystem(new GraviticThruster(6, 30, 0, 12, 2));
        $this->addAftSystem(new HeavyFusionCannon(4, 8, 6, 120, 240));
        $this->addAftSystem(new HeavyFusionCannon(4, 8, 6, 120, 240));

        $this->addLeftSystem(new GraviticThruster(5, 20, 0, 6, 3));
        $this->addLeftSystem(new HeavyFusionCannon(4, 8, 6, 240, 0));
        $this->addLeftSystem(new MolecularFlayer(5, 8, 4, 300, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 120, 300));

        $this->addRightSystem(new GraviticThruster(5, 20, 0, 6, 4));
        $this->addRightSystem(new HeavyFusionCannon(4, 8, 6, 0, 120));
        $this->addRightSystem(new MolecularFlayer(5, 8, 4, 0, 60));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 240));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 240));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 72));
        $this->addAftSystem(new Structure( 6, 72));
        $this->addPrimarySystem(new Structure( 7, 80 ));
        $this->addLeftSystem(new Structure( 6, 85));
        $this->addRightSystem(new Structure( 6, 85 ));

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
        				6 => "Fusion Agitator",
        				9 => "Destabilizer Beam",
        				11 => "Jump Engine",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				9 => "Heavy Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Molecular Flayer",
        				8 => "Heavy Fusion Cannon",
        				12 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Molecular Flayer",
        				8 => "Heavy Fusion Cannon",
        				12 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }

}



?>
