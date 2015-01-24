<?php
class Malau extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 625;
		$this->faction = "Yolu";
        $this->phpclass = "Malau";
        $this->imagePath = "img/ships/maitau.png";
        $this->shipClass = "Malau Attack Frigate";
        $this->shipSizeClass = 1;
        $this->occurence = "common";
        $this->gravitic = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 60;
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(5, 18, 0, 2));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 20, 4, 10));
        $this->addPrimarySystem(new Engine(5, 16, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(4, 1));
   
   
        $this->addFrontSystem(new GraviticThruster(5, 14, 0, 6, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new MolecularDisruptor(3, 8, 6, 300, 360));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));

        
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new GraviticThruster(6, 24, 0, 10, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 240));

        
        $this->addLeftSystem(new GraviticThruster(5, 15, 0, 4, 3)); 
        $this->addLeftSystem(new MolecularDisruptor(4, 8, 6, 240, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));

        
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addRightSystem(new MolecularDisruptor(4, 8, 6, 0, 360));
        $this->addRightSystem(new GraviticThruster(5, 15, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 44));
    }
}
