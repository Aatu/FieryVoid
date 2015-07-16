<?php
class AltarianMagnus extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 600;
        $this->faction = "Centauri";
        $this->phpclass = "AltarianMagnus";
        $this->imagePath = "img/ships/altarian.png";
        $this->shipClass = "Altarian Magnus";
        $this->fighters = array("medium"=>6);
        $this->occurence = "rare";
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(6, 17, 0, -2));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 8));
        $this->addPrimarySystem(new Engine(5, 15, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(5, 7));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
        
        
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 180, 60));
        $this->addFrontSystem(new BattleLaser(3, 6, 6, 300, 60));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 300, 180));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 240, 0));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 0, 120));
        
        $this->addAftSystem(new MatterCannon(4, 7, 4, 120, 240));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new JumpEngine(3, 10, 3, 20));
        $this->addAftSystem(new TwinArray(3, 6, 2, 120, 0));
        $this->addAftSystem(new TwinArray(3, 6, 2, 0, 240));
        

        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 60));
        $this->addAftSystem(new Structure( 4, 60));
        $this->addPrimarySystem(new Structure( 6, 46));
        
        
    }

}



?>
