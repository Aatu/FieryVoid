<?php
class Katoc extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 575;
        $this->faction = "Narn";
        $this->phpclass = "Katoc";
        $this->imagePath = "img/ships/katoc.png";
        $this->shipClass = "Ka'Toc";
        
        
        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(5, 16, 0, 3));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 7));
        $this->addPrimarySystem(new Engine(5, 16, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(5, 6));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 15, 0, 4, 4));
        
        
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addFrontSystem(new LightPulse(2, 4, 2, 270, 90));
        $this->addFrontSystem(new MagGun(5, 9, 8, 300, 60));
        
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 240, 0));
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 0, 120));
        
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        $this->addAftSystem(new LightPulse(2, 4, 2, 90, 270));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 54));
        $this->addAftSystem(new Structure( 4, 54));
        $this->addPrimarySystem(new Structure( 5, 50));
        
        
    }

}



?>
