<?php
class CottenGamma extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 550;
        $this->faction = "EA";
        $this->phpclass = "CottenGamma";
        $this->imagePath = "img/ships/cotton.png";
        $this->shipClass = "Cotten Long-Range Tender (Gamma)";
			$this->variantOf = "Cotten Long-Range Tender (Alpha)";
			$this->occurence = "common";
        $this->shipSizeClass = 3;
//			$this->canvasSize = 175; //img has 200px per side
 		$this->unofficial = true;
       
        $this->isd = 2246;

        $this->forwardDefense = 16;
        $this->sideDefense = 16;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 3, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(5, 12));

        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 0, 120));
        $this->addFrontSystem(new MediumPulse(4, 6, 3, 240, 120));
		$this->addFrontSystem(new CargoBay(2, 25));
		$this->addFrontSystem(new CargoBay(2, 25));

        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new JumpEngine(5, 20, 4, 24));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
        $this->addAftSystem(new CargoBay(2, 25));
        $this->addAftSystem(new CargoBay(2, 25));

        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
		$this->addLeftSystem(new MediumPulse(4, 6, 3, 180, 360));
        $this->addLeftSystem(new CargoBay(2, 25));
        $this->addLeftSystem(new CargoBay(2, 25));

        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
		$this->addRightSystem(new MediumPulse(4, 6, 3, 0, 180));
        $this->addRightSystem(new CargoBay(2, 25));
        $this->addRightSystem(new CargoBay(2, 25));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 54));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addLeftSystem(new Structure( 4, 50));
        $this->addRightSystem(new Structure( 4, 50));
        $this->addPrimarySystem(new Structure( 5, 50));
		
        $this->hitChart = array(
        	0=> array(
        		11 => "Structure",
        		13 => "Scanner",
				15 => "Engine",
        		17 => "Hangar",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		5 => "Thruster",
				7 => "Medium Pulse Cannon",
        		8 => "Interceptor Prototype",
				12 => "Cargo Bay",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		6 => "Thruster",
        		7 => "Interceptor Prototype",
				9 => "Jump Engine",
				12 => "Cargo Bay",
        		18 => "Structure",
        		20 => "Primary",           			
        	),
        	3=> array(
        		4 => "Thruster",
				6 => "Medium Pulse Cannon",
				12 => "Cargo Bay",
        		18 => "Structure",
        		20 => "Primary",           			
        	),			
        	3=> array(
        		4 => "Thruster",
				6 => "Medium Pulse Cannon",
				12 => "Cargo Bay",
        		18 => "Structure",
        		20 => "Primary",           		 	),			
		);		
		
    }
}

?>
