<?php
class PirateHunter extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 450;
		$this->faction = "Civilians";
        $this->phpclass = "PirateHunter";
        $this->imagePath = "img/ships/pirateHunter.png"; //Needs to change maybe?
        $this->shipClass = "Pirate Hunter";
        $this->canvasSize = 100;
        $this->isd = 2241;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
    	$this->iniativebonus = 70;
         
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 4, 5));
        $this->addPrimarySystem(new Hangar(3, 1));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 2));
    	$this->addPrimarySystem(new Thruster(4, 10, 0, 3, 3));
    	$this->addPrimarySystem(new Thruster(4, 10, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(4, 12, 0, 4, 1));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 240, 60));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 300, 120));		

        $this->addAftSystem(new LightPulse(3, 4, 2, 180, 360));
        $this->addAftSystem(new LightPulse(2, 4, 2, 0, 180));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
	
        $this->addPrimarySystem(new Structure( 4, 40));
        
        $this->hitChart = array(
        		0=> array(
        				9 => "Thruster",
        				12 => "Scanner",
        				15 => "Engine",
        				16 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				6 => "Medium Laser",
        				8 => "Medium Pulse Cannon",
        				17 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Light Pulse Cannon",
        				17 => "Structure",
        				20 => "Primary",
        		),
        );
    }

}



?>
