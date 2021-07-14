<?php
class Maishan extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 710;
		$this->faction = "Yolu";
        $this->phpclass = "Maishan";
        $this->imagePath = "img/ships/maitau.png";
        $this->shipClass = "Maishan Strike Frigate";
        $this->gravitic = true;
        $this->canvasSize = 100;
        $this->occurence = "uncommon";
		$this->variantOf = "Maitau Pursuit Frigate";  

        $this->isd = 2251;
		        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 60;
        
        $this->addPrimarySystem(new Reactor(5, 18, 0, 2));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 20, 4, 10));
        $this->addPrimarySystem(new Engine(5, 16, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new FusionCannon(3, 8, 1, 180, 360));
        $this->addPrimarySystem(new FusionCannon(3, 8, 1, 0, 180));
        $this->addPrimarySystem(new GraviticThruster(5, 15, 0, 4, 3));
        $this->addPrimarySystem(new GraviticThruster(5, 15, 0, 4, 4));

        $this->addFrontSystem(new GraviticThruster(5, 14, 0, 6, 1));
        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 240, 360));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 60));
        $this->addFrontSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addFrontSystem(new HeavyFusionCannon(4, 10, 4, 0, 120));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new GraviticThruster(6, 24, 0, 10, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 240));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 64));
        
        $this->hitChart = array(
        		0=> array(
        				10 => "Thruster",
        				12 => "Fusion Cannon",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				7 => "Heavy Fusion Cannon",
        				10 => "Fusion Cannon",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Fusion Cannon",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
