<?php
class Allovan extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "Llort";
        $this->phpclass = "Allovan";
        $this->imagePath = "img/ships/LlortAllovan.png";
        $this->shipClass = "Allovan Attack Frigate";
 
        $this->isd = 2233;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 15;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 5, 7));
		$this->addPrimarySystem(new Engine(4, 14, 0, 8, 2));
		$this->addPrimarySystem(new Hangar(4, 3, 0));
        $this->addPrimarySystem(new Thruster(3, 11, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 11, 0, 4, 4));

	    
	    //new thrusters (correct)
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));

        $this->addFrontSystem(new MediumBolter(4, 8, 4, 300, 60));
        $this->addFrontSystem(new LightBolter(4, 6, 2, 120, 360));
        $this->addFrontSystem(new LightBolter(4, 6, 2, 120, 360));
        $this->addFrontSystem(new LightBolter(4, 6, 2, 0, 240));
        $this->addFrontSystem(new LightBolter(4, 6, 2, 0, 240));
        
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new LightBolter(4, 6, 2, 120, 360));
        $this->addAftSystem(new LightBolter(4, 6, 2, 120, 360));
        $this->addAftSystem(new LightBolter(4, 6, 2, 0, 240));
        $this->addAftSystem(new LightBolter(4, 6, 2, 0, 240));

        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 4, 42));
        $this->addAftSystem(new Structure( 4, 42));
        
        $this->hitChart = array(
        	0=> array(
        		9 => "Structure",
        		11 => "Thruster",
        		13 => "Scanner",
        		15 => "Engine",
        		17 => "Hangar",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		6 => "Thruster",
        		8 => "Medium Bolter",
        		11 => "Light Bolter",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		6 => "Thruster",
        		11 => "Light Bolter",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        );
    }
}
?>
