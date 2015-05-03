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
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
         
        $this->addPrimarySystem(new Reactor(6, 20, 0, 2));
        $this->addPrimarySystem(new CnC(6, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 32, 6, 12));
        $this->addPrimarySystem(new Engine(5, 18, 0, 10, 5));
        $this->addPrimarySystem(new Hangar(5, 3));
        
        $this->addFrontSystem(new Thruster(5, 21, 0, 6, 1));

        $this->addFrontSystem(new DestabilizerBeam(4, 10, 8, 300, 60));
        $this->addFrontSystem(new DestabilizerBeam(4, 10, 8, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new MolecularDisruptor(4, 8, 6, 300, 60));
        $this->addFrontSystem(new JumpEngine(6, 20, 6, 20));


        $this->addAftSystem(new Thruster(6, 28, 0, 10, 2));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 180, 300));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 120, 240));
        $this->addAftSystem(new MolecularDisruptor(4, 8, 6, 60, 180));
        
        $this->addLeftSystem(new GraviticThruster(5, 18, 0, 5, 3));
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 240, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));

        $this->addRightSystem(new GraviticThruster(5, 18, 0, 5, 4));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 56));
        $this->addAftSystem(new Structure( 6, 52));
        $this->addPrimarySystem(new Structure( 6, 60 ));
        $this->addLeftSystem(new Structure( 6, 62));
        $this->addRightSystem(new Structure( 6, 62 ));
        
        
    }

}



?>
