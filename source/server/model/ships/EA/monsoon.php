<?php
class Monsoon extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 500;
        $this->faction = "Earth Alliance (Custom)";
        $this->phpclass = "Monsoon";
        $this->imagePath = "img/ships/monsoon.png";
        $this->shipClass = "Monsoon Advanced Gunboat (Alpha)";
        $this->canvasSize = 100;
	    
	    $this->isd = 2261;
    	$this->unofficial = true;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->agile = true;
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(4, 13, 0, -3));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 3));
        $this->addPrimarySystem(new InterceptorMkII(3, 4, 2, 180, 360));
        $this->addPrimarySystem(new InterceptorMkII(3, 4, 2, 0, 180));
		$this->addPrimarySystem(new Thruster(4, 13, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(4, 13, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 4, 1));
        $this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 60));
        $this->addFrontSystem(new HeavyLaser(4, 8, 6, 330, 30));
        $this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 60));
		
        $this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
        $this->addAftSystem(new LightPulse(3, 4, 2, 180, 360));
		$this->addAftSystem(new Hangar(4, 2));
        $this->addAftSystem(new LightPulse(3, 4, 2, 0, 180));
	
        $this->addPrimarySystem(new Structure( 5, 50));
        
		$this->hitChart = array(
                0=> array(
                        7 => "Thruster",
						10 => "Interceptor II",
                        13 => "Scanner",
                        16 => "Engine",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
						5 => "Heavy Laser",
                        8 => "Medium Pulse Cannon",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        4 => "Thruster",
                        7 => "Light Pulse Cannon",
                        8 => "Hangar",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
