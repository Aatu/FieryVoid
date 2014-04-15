<?php
class Antoph extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "Brakiri";
        $this->phpclass = "Antoph";
        $this->imagePath = "img/ships/antoph.png";
        $this->shipClass = "Antoph Light Cruiser";
                
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 6, 7));
        $this->addPrimarySystem(new Engine(5, 14, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new GraviticThruster(3, 13, 0, 5, 3));
        $this->addPrimarySystem(new GraviticThruster(3, 13, 0, 5, 4));
        $this->addPrimarySystem(new GraviticCannon(4, 6, 5, 240, 0));
        $this->addPrimarySystem(new GraviticCannon(4, 6, 5, 0, 120));
        
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GravitonBeam(5, 8, 8, 300, 60));
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 300, 120));
        
        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 120, 240));
        $this->addAftSystem(new GraviticThruster(4, 12, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(4, 12, 0, 6, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 48));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addPrimarySystem(new Structure( 5, 33 ));
    }
}
?>
