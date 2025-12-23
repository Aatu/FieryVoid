<?php
class CircasianMorketOSAT extends OSAT{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 70;
	$this->faction = "Escalation Wars Circasian Empire";
        $this->phpclass = "CircasianMorketOSAT";
        $this->imagePath = "img/ships/EscalationWars/CircasianMorket.png";
        $this->canvasSize = 60;
        $this->shipClass = 'Morket Defense Satellite';
        
        $this->isd = 1952;
        $this->unofficial = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));        
        $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 4)); 
        $this->addAftSystem(new Thruster(3, 6, 0, 0, 2)); 
        $this->addFrontSystem(new EWRangedRocketLauncher(2, 4, 1, 180, 360)); 
        $this->addFrontSystem(new EWRangedRocketLauncher(2, 4, 1, 0, 180)); 
        $this->addFrontSystem(new EWRangedRocketLauncher(2, 4, 1, 270, 90)); 
        $this->addFrontSystem(new LightLaser(1, 4, 3, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(3, 24));
        
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "2:Thruster",
					15 => "1:Ranged Rocket Launcher",
					17 => "Scanner",
					19 => "Reactor",
					20 => "1:Light Laser",
			)
		);
    
    
        
    }
}
?>
