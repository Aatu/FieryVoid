<?php
class Hastan extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 800;
        $this->faction = "Yolu";
        $this->phpclass = "Hastan";
        $this->imagePath = "img/ships/hastan.png";
        $this->shipClass = "Hastan Escort Frigate";
        $this->gravitic = true;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 22, 4, 12));
        $this->addPrimarySystem(new Engine(6, 24, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(5, 15, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(5, 15, 0, 4, 4));

        $this->addFrontSystem(new Thruster(5, 20, 0, 9, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 180, 60));
        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 240, 360));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 0, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 180));

        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 5, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 64));
        $this->addAftSystem(new Structure( 6, 60));
        $this->addPrimarySystem(new Structure( 6, 70 ));
        
        
    }

}



?>
