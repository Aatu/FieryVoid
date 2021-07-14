<?php
class Aluin extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 1100;
        $this->faction = "Yolu";
        $this->phpclass = "Aluin";
        $this->imagePath = "img/ships/aluin.png";
        $this->shipClass = "Aluin Gunship";
        $this->gravitic = true;

        $this->isd = 2110;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
         
        $this->addPrimarySystem(new Reactor(6, 25, 0, 2));
        $this->addPrimarySystem(new CnC(6, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 28, 4, 12));
        $this->addPrimarySystem(new Engine(6, 23, 0, 10, 5));
        $this->addPrimarySystem(new Hangar(5, 3));
        
        $this->addFrontSystem(new GraviticThruster(5, 21, 0, 6, 1));
        $this->addFrontSystem(new DestabilizerBeam(4, 10, 8, 300, 60));
        $this->addFrontSystem(new DestabilizerBeam(4, 10, 8, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new JumpEngine(6, 20, 6, 18));

        $this->addAftSystem(new GraviticThruster(6, 28, 0, 10, 2));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        
        $this->addLeftSystem(new GraviticThruster(5, 18, 0, 5, 3));
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 240, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 180, 300));
        
        $this->addRightSystem(new GraviticThruster(5, 18, 0, 5, 4));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 60, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 56));
        $this->addAftSystem(new Structure( 6, 56));
        $this->addPrimarySystem(new Structure( 6, 64 ));
        $this->addLeftSystem(new Structure( 6, 70));
        $this->addRightSystem(new Structure( 6, 70));
        
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
        				4 => "Thruster",
        				6 => "Destabilizer Beam",
        				9 => "Molecular Disruptor",
        				11 => "Jump Engine",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Molecular Disruptor",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				8 => "Molecular Disruptor",
        				12 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				8 => "Molecular Disruptor",
        				12 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
?>
