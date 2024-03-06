<?php
class MakarCorren extends OSAT{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 100;
	$this->faction = "ZNexus Makar Federation";
        $this->phpclass = "MakarCorren";
        $this->imagePath = "img/ships/Nexus/makarCorren.png";
        $this->canvasSize = 80;
        $this->shipClass = 'Corren OSAT';
        
        $this->isd = 1890;
        $this->unofficial = true;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 9;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
        $this->addPrimarySystem(new Reactor(3, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 7, 2, 4)); 
        $this->addPrimarySystem(new Thruster(3, 8, 0, 0, 2)); 
        $this->addPrimarySystem(new EWRangedHeavyRocketLauncher(3, 6, 2, 300, 60)); 
        $this->addPrimarySystem(new EWRangedHeavyRocketLauncher(3, 6, 2, 300, 60)); 
        $this->addPrimarySystem(new NexusDefenseGun(1, 4, 1, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(3, 25));
        
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Thruster",
					14 => "Ranged Heavy Rocket Launcher",
					16 => "Defense Gun",
					18 => "Scanner",
					20 => "Reactor",
			)
		);
    
    
        
    }
}
?>
