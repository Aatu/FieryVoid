<?php
class VindicatorCA extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 745;
        $this->faction = "Deneth";
        $this->phpclass = "vindicatorca";
        $this->imagePath = "img/ships/DenethVindicator.png";
	$this->canvasSize = 200;
        $this->shipClass = "Vindicator Heavy Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
	    $this->isd = 2243;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
         
        $this->addPrimarySystem(new Reactor(5, 27, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 17, 5, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(5, 14));
        $this->addPrimarySystem(new JumpEngine(5, 15, 4, 24));
  
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
	$this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 240, 60));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 300, 60));
	$this->addFrontSystem(new AssaultLaser(4, 6, 4, 300, 60));
        $this->addFrontSystem(new TwinArray(2, 6, 2, 300, 120));
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
	    
        $this->addAftSystem(new TwinArray(2, 6, 2, 120, 300));
        $this->addAftSystem(new TwinArray(2, 6, 2, 60, 240));
        $this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
                
        $this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));
        $this->addLeftSystem(new MediumPulse(3, 6, 3, 180, 360));
        $this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
  
        $this->addRightSystem(new Thruster(4, 15, 0, 6, 4));
        $this->addRightSystem(new MediumPulse(3, 6, 3, 0, 180));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 4, 52));
        $this->addLeftSystem(new Structure( 5, 52));
        $this->addRightSystem(new Structure( 5, 52));
        $this->addPrimarySystem(new Structure( 5, 55));
        
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
        				6 => "Heavy Laser",
        				10 => "Assault Laser",
        				12 => "Twin Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				8 => "Thruster",
        				10 => "Twin Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				5 => "Twin Array",
					7 => "Medium Pulse Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				5 => "Twin Array",
					7 => "Medium Pulse Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
