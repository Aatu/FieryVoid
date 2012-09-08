<?php
class Kutai extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $movement){
        parent::__construct($id, $userid, $name,  $movement);
        
        $this->pointCost = 535;
        $this->faction = "Centauri";
        $this->phpclass = "Kutai";
        $this->imagePath = "img/ships/kutai.png";
        $this->shipClass = "Kutai";
        
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 33;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
         
        $this->addPrimarySystem(new Reactor(6, 17, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 15, 5, 8));
        $this->addPrimarySystem(new Engine(6, 15, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(6, 1));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
        
        
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new TwinArray(3, 6, 2, 240,120));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 240, 0));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 0, 120));
		$this->addFrontSystem(new MatterCannon(4, 7, 4, 240, 0));
        $this->addFrontSystem(new MatterCannon(4, 7, 4, 0, 120));
       
	    $this->addAftSystem(new TwinArray(3, 6, 2, 300, 60));  
        $this->addAftSystem(new MatterCannon(4, 7, 4, 300, 180));
		$this->addAftSystem(new MatterCannon(4, 7, 4, 60, 180));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
       
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 52));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 6, 36 ));
    }
}