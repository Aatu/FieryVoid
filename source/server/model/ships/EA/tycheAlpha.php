<?php
class TycheAlpha extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "EA";
        $this->phpclass = "TycheAlpha";
        $this->imagePath = "img/ships/tyche.png";
        $this->shipClass = "Tyche Cruiser (Alpha)";
        $this->shipSizeClass = 3;
//			$this->canvasSize = 175; //img has 200px per side
 		$this->unofficial = true;
       
        $this->isd = 2148;

        $this->forwardDefense = 14;
        $this->sideDefense = 15;

        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 2, 4));
        $this->addPrimarySystem(new Engine(4, 11, 0, 4, 4));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new LtBlastCannon(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new LtBlastCannon(2, 4, 1, 0, 360));

        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
        $this->addFrontSystem(new LtBlastCannon(2, 4, 1, 240, 60));
        $this->addFrontSystem(new LtBlastCannon(2, 4, 1, 300, 120));

        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new LtBlastCannon(2, 4, 1, 60, 300));
        $this->addAftSystem(new MedBlastCannon(2, 5, 2, 60, 300));

        $this->addLeftSystem(new Thruster(3, 11, 0, 3, 3));
        $this->addLeftSystem(new MedBlastCannon(2, 5, 2, 180, 360));

        $this->addRightSystem(new Thruster(3, 11, 0, 3, 4));
        $this->addRightSystem(new MedBlastCannon(2, 5, 2, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 42));
        $this->addAftSystem(new Structure( 4, 28));
        $this->addLeftSystem(new Structure( 3, 45));
        $this->addRightSystem(new Structure( 3, 45));
        $this->addPrimarySystem(new Structure( 4, 36));
		
        $this->hitChart = array(
        	0=> array(
        		10 => "Structure",
        		12 => "Light Blast Cannon",
        		14 => "Scanner",
				16 => "Engine",
        		17 => "Hangar",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		5 => "Thruster",
        		8 => "Medium Plasma Cannon",
        		11 => "Light Blast Cannon",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		7 => "Thruster",
        		9 => "Medium Blast Cannon",
        		10 => "Light Blast Cannon",
        		18 => "Structure",
        		20 => "Primary",           			
        	),
        	3=> array(
        		6 => "Thruster",
        		10 => "Medium Blast Cannon",
        		18 => "Structure",
        		20 => "Primary",           			
        	),			
        	3=> array(
        		6 => "Thruster",
        		10 => "Medium Blast Cannon",
        		18 => "Structure",
        		20 => "Primary",           			
        	),			
		);		
		
    }
}

?>
