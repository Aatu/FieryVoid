<?php
class Maltra extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 900;
        $this->faction = "Yolu";
        $this->phpclass = "Maltra";
        $this->imagePath = "img/ships/maltra.png";
        $this->shipClass = "Maltra Scout";
        $this->gravitic = true;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
         
        $this->addPrimarySystem(new Reactor(6, 20, 0, 0));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new ElintScanner(6, 32, 6, 14));
        $this->addPrimarySystem(new Engine(5, 18, 0, 10, 5));
        $this->addPrimarySystem(new Hangar(4, 2));
        
        $this->addFrontSystem(new Thruster(5, 15, 0, 5, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addFrontSystem(new JumpEngine(6, 20, 6, 20));

        $this->addAftSystem(new Thruster(6, 27, 0, 8, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 240));
        
        
        $this->addLeftSystem(new GraviticThruster(5, 14, 0, 4, 3)); 
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));

        $this->addRightSystem(new GraviticThruster(5, 14, 0, 4, 4));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 48));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addPrimarySystem(new Structure( 6, 52 ));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60 ));
        
        
    }

}



?>
