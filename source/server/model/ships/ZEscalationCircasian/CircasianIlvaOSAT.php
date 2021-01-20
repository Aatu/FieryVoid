<?php
class CircasianIlvaOSAT extends OSAT{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 100;
	$this->faction = "ZEscalation Circasian";
        $this->phpclass = "CircasianIlvaOSAT";
        $this->imagePath = "img/ships/EscalationWars/CircasianIlva.png";
        $this->canvasSize = 60;
        $this->shipClass = 'Ilva Torpedo Satellite';
        
        $this->isd = 1967;
        $this->unofficial = true;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 4)); 
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2)); 
        $this->addPrimarySystem(new EWRangedDualHeavyRocketLauncher(3, 8, 4, 270, 90)); 
        $this->addPrimarySystem(new EWRangedDualHeavyRocketLauncher(3, 8, 4, 270, 90)); 
        $this->addPrimarySystem(new LightLaser(1, 4, 3, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(3, 32));
        
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Thruster",
					15 => "Ranged Dual Heavy Rocket Launcher",
					17 => "Scanner",
					19 => "Reactor",
					20 => "Light Laser",
			)
		);
    
    
        
    }
}
?>
