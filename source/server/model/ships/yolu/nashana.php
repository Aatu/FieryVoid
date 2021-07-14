<?php
class Nashana extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 950;
	$this->faction = "Yolu";
        $this->phpclass = "Nashana";
        $this->imagePath = "img/ships/nashana.png";
        $this->shipClass = "Nashana Light Cruiser";

        $this->isd = 2244;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 5;
        $this->fighters=array("normal"=>6);
        
        $this->gravitic = true;

        $this->addPrimarySystem(new Reactor(6, 25, 0, 4));
        $this->addPrimarySystem(new CnC(7, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 24, 4, 12));
        $this->addPrimarySystem(new Engine(7, 23, 0, 12, 5));
		$this->addPrimarySystem(new Hangar(5, 8));
		$this->addPrimarySystem(new JumpEngine(5, 15, 4, 20));

        $this->addFrontSystem(new GraviticThruster(4, 12, 0, 3, 1));
        $this->addFrontSystem(new GraviticThruster(4, 12, 0, 3, 1));
        $this->addFrontSystem(new FusionAgitator(4, 10, 4, 300, 60));
        $this->addFrontSystem(new DestabilizerBeam(4, 10, 8, 300, 60));
        $this->addFrontSystem(new FusionAgitator(5, 10, 4, 300, 60));

        $this->addAftSystem(new FusionCannon(3, 8, 1, 120, 300));
        $this->addAftSystem(new GraviticThruster(6, 28, 0, 12, 2));
        $this->addAftSystem(new FusionCannon(3, 8, 1, 60, 240));

        $this->addLeftSystem(new GraviticThruster(5, 18, 0, 6, 3));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 240, 60));
        $this->addLeftSystem(new HeavyFusionCannon(4, 10, 4, 240, 0));
        $this->addLeftSystem(new FusionCannon(3, 8, 1, 120, 300));

        $this->addRightSystem(new GraviticThruster(5, 18, 0, 6, 4));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 300, 120));
        $this->addRightSystem(new HeavyFusionCannon(4, 10, 4, 0, 120));
        $this->addRightSystem(new FusionCannon(3, 8, 1, 60, 240));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 49));
        $this->addAftSystem(new Structure( 6, 52));
        $this->addPrimarySystem(new Structure( 7, 60 ));
        $this->addLeftSystem(new Structure( 6, 65));
        $this->addRightSystem(new Structure( 6, 65 ));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				10 => "Jump Engine",
        				13 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Destabilizer Beam",
        				10 => "Fusion Agitator",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				8 => "Thruster",
        				10 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				6 => "Heavy Fusion Cannon",
        				10 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				6 => "Heavy Fusion Cannon",
        				10 => "Fusion Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
