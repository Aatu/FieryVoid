<?php
class Darkner extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement){
        parent::__construct($id, $userid, $name, $campaignX, $campaignY, $rolled, $rolling, $movement);
        
        $this->pointCost = 525;
        $this->faction = "Centauri";
        $this->phpclass = "Darkner";
        $this->imagePath = "ships/darkner.png";
        $this->shipClass = "Darkner";
        
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;
        
         
        $this->addSystem(new Reactor(6, 17, 0, 0, 2));
        $this->addSystem(new CnC(5, 12, 0, 0, 0));
        $this->addSystem(new Scanner(5, 14, 0, 5, 9));
        $this->addSystem(new Engine(5, 18, 0, 0, 12, 3));
        $this->addSystem(new Hangar(4, 1, 0));
        $this->addSystem(new Thruster(4, 10, 0, 0, 5, 3));
        $this->addSystem(new Thruster(4, 10, 0, 0, 5, 4));
        
        
        
        $this->addSystem(new Thruster(4, 10, 1, 0, 3, 1));
        $this->addSystem(new Thruster(4, 10, 1, 0, 3, 1));
        $this->addSystem(new BattleLaser(3, 6, 1, 2, 240, 0));
        $this->addSystem(new BattleLaser(3, 6, 1, 2, 0, 120));
        $this->addSystem(new MatterCannon(4, 7, 1, 4, 240, 0));
        $this->addSystem(new MatterCannon(4, 7, 1, 4, 0, 120));
        
        $this->addSystem(new Thruster(4, 19, 2, 0, 6, 2));
        $this->addSystem(new Thruster(4, 19, 2, 0, 6, 2));
        $this->addSystem(new JumpEngine(4, 15, 2, 4, 20));
        

        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addSystem(new Structure( 4, 42, 1));
        $this->addSystem(new Structure( 4, 40, 2));
        $this->addSystem(new Structure( 5, 32, 0));
        
        
    }

}



?>
