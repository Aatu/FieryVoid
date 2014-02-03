<?php
class Halos extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 800;
	$this->faction = "Brakiri";
        $this->phpclass = "Halos";
        $this->imagePath = "img/ships/halos.png";
        $this->shipClass = "Halos Heavy Scout";
        $this->shipSizeClass = 3;
        $this->occurence = "rare";
        
        $this->forwardDefense = 14;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 0.5;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(6, 22, 0, 9));
        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 16, 10, 10));
        $this->addPrimarySystem(new Engine(6, 16, 0, 15, 4));
        $this->addPrimarySystem(new JumpEngine(5, 12, 4, 28));
	$this->addPrimarySystem(new Hangar(5, 2));
   
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 240, 60));
        $this->addFrontSystem(new GravitonPulsar(3, 5, 2, 300, 120));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));
        $this->addFrontSystem(new GraviticThruster(5, 10, 0, 4, 1));

        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 120, 300));
        $this->addAftSystem(new GravitonPulsar(3, 5, 2, 60, 240));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));
        $this->addAftSystem(new GraviticThruster(5, 15, 0, 8, 2));

        $this->addLeftSystem(new GravitonPulsar(4, 5, 2, 240, 60));
        $this->addLeftSystem(new GraviticCannon(5, 6, 5, 300, 0));
        $this->addLeftSystem(new GraviticThruster(5, 15, 0, 6, 3));

        $this->addRightSystem(new GravitonPulsar(4, 5, 2, 300, 120));
        $this->addRightSystem(new GraviticCannon(5, 6, 5, 0, 60));
        $this->addRightSystem(new GraviticThruster(5, 15, 0, 6, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(6, 36));
        $this->addAftSystem(new Structure(6, 36));
        $this->addLeftSystem(new Structure(6, 48));
        $this->addRightSystem(new Structure(6, 48));
        $this->addPrimarySystem(new Structure(6, 44));
    }
}