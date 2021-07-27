<?php
class CottenAlpha extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "EA (early)";
        $this->phpclass = "CottenAlpha";
        $this->imagePath = "img/ships/cotton.png";
        $this->shipClass = "Cotten Long-Range Tender (Alpha)";
        $this->shipSizeClass = 3;
//			$this->canvasSize = 175; //img has 200px per side
 		$this->unofficial = true;
       
        $this->isd = 2176;

        $this->forwardDefense = 16;
        $this->sideDefense = 16;

        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 16, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(4, 12));

        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new InterceptorPrototype(2, 4, 1, 270, 90));
		$this->addFrontSystem(new CargoBay(2, 25));
		$this->addFrontSystem(new CargoBay(2, 25));

        $this->addAftSystem(new Thruster(3, 6, 0, 1, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 6, 0, 1, 2));
        $this->addAftSystem(new JumpEngine(4, 20, 3, 24));
        $this->addAftSystem(new InterceptorPrototype(2, 4, 1, 90, 270));
        $this->addAftSystem(new CargoBay(2, 25));
        $this->addAftSystem(new CargoBay(2, 25));

        $this->addLeftSystem(new Thruster(3, 13, 0, 3, 3));
        $this->addLeftSystem(new CargoBay(2, 25));
        $this->addLeftSystem(new CargoBay(2, 25));

        $this->addRightSystem(new Thruster(3, 13, 0, 3, 4));
        $this->addRightSystem(new CargoBay(2, 25));
        $this->addRightSystem(new CargoBay(2, 25));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 54));
        $this->addAftSystem(new Structure( 3, 48));
        $this->addLeftSystem(new Structure( 3, 50));
        $this->addRightSystem(new Structure( 3, 50));
        $this->addPrimarySystem(new Structure( 4, 50));
		
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
        		6 => "Thruster",
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
        		6 => "Thruster",
				12 => "Cargo Bay",
        		18 => "Structure",
        		20 => "Primary",           			
        	),			
        	3=> array(
        		6 => "Thruster",
				12 => "Cargo Bay",
        		18 => "Structure",
        		20 => "Primary",              	),			
		);		
		
    }
}

?>
