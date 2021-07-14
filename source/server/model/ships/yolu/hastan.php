<?php
class Hastan extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 800;
        $this->faction = "Yolu";
        $this->phpclass = "Hastan";
        $this->imagePath = "img/ships/hastan.png";
        $this->shipClass = "Hastan Escort Frigate";
        $this->gravitic = true;

        $this->isd = 2243;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.50;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(6, 25, 0, 0));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 22, 4, 12));
        $this->addPrimarySystem(new Engine(6, 24, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new GraviticThruster(5, 15, 0, 6, 3));
        $this->addPrimarySystem(new GraviticThruster(5, 15, 0, 6, 4));

        $this->addFrontSystem(new GraviticThruster(5, 20, 0, 9, 1));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 180, 60));
        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 240, 360));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 0, 120));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 180));

        $this->addAftSystem(new GraviticThruster(5, 35, 0, 12, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 240));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 64));
        $this->addAftSystem(new Structure( 6, 70));
        $this->addPrimarySystem(new Structure( 6, 60 ));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				11 => "Thruster",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				8 => "Fusion Cannon",
        				10 => "Heavy Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				10 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }

}



?>
