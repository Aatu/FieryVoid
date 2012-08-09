<?php
class Darkner extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 525;
        $this->faction = "Centauri";
        $this->phpclass = "Darkner";
        $this->imagePath = "img/ships/darkner.png";
        $this->shipClass = "Darkner";
        
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;
        
         
        $this->addPrimarySystem(new Reactor(6, 17, 0, 2));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 5, 9));
        $this->addPrimarySystem(new Engine(5, 18, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 5, 4));
        
        
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new BattleLaser(3, 6, 2, 240, 0));
        $this->addFrontSystem(new BattleLaser(3, 6, 2, 0, 120));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 240, 0));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 0, 120));
        
        $this->addAftSystem(new Thruster(4, 19, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 19, 0, 6, 2));
        $this->addAftSystem(new JumpEngine(4, 15, 4, 20));
        

        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 32));
        
        
    }

}



?>
