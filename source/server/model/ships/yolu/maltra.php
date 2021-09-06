<?php
class Maltra extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 900;
        $this->faction = "Yolu";
        $this->phpclass = "Maltra";
        $this->imagePath = "img/ships/maltra.png";
        $this->shipClass = "Maltra Scout";
        $this->gravitic = true;

        $this->isd = 2050;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 4;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
         
        $this->addPrimarySystem(new Reactor(6, 20, 0, 0));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new ElintScanner(6, 32, 6, 14));
        $this->addPrimarySystem(new Engine(5, 18, 0, 8, 5));
        $this->addPrimarySystem(new Hangar(4, 2));
        
        $this->addFrontSystem(new GraviticThruster(5, 15, 0, 4, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addFrontSystem(new JumpEngine(6, 20, 6, 18));

        $this->addAftSystem(new GraviticThruster(6, 24, 0, 8, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 240)); 
        
        $this->addLeftSystem(new GraviticThruster(5, 14, 0, 4, 3)); 
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 180, 360));

        $this->addRightSystem(new GraviticThruster(5, 14, 0, 4, 4));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 48));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addPrimarySystem(new Structure( 6, 56 ));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60 ));
        
        $this->hitChart = array(
        		0=> array(
        				12 => "Structure",
        				14 => "ELINT Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				9 => "Fusion Cannon",
        				11 => "Jump Engine",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				8 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				8 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }

}



?>
