<?php
class Demos extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 575;
        $this->faction = "Centauri";
        $this->phpclass = "Demos";
        $this->imagePath = "img/ships/demos.png";
        $this->shipClass = "Demos";
        
        
        $this->forwardDefense = 12;
        $this->sideDefense = 14;
        
        $this->turncost = 0.50;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(7, 15, 0, 2));
        $this->addPrimarySystem(new CnC(6, 14, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 20, 4, 9));
        $this->addPrimarySystem(new Engine(7, 13, 0, 10, 2));
        $this->addPrimarySystem(new Hangar(6, 2));
        $this->addPrimarySystem(new Thruster(5, 15, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(5, 15, 0, 5, 4));
        
        
        
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
        $this->addFrontSystem(new HeavyArray(3, 8, 4, 240, 120));
        $this->addFrontSystem(new HeavyArray(3, 8, 4, 240, 120));
        $this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
        $this->addFrontSystem(new BallisticTorpedo(4, 5, 6, 300, 60));
        
        
        $this->addAftSystem(new Thruster(4, 8, 0, 5, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 5, 2));
        $this->addAftSystem(new JumpEngine(6, 16, 3, 16));
    
        

        
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 50));
        $this->addAftSystem(new Structure( 4, 46));
        $this->addPrimarySystem(new Structure( 6, 30));
        
        
    }

}



?>
