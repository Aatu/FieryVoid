<?php
class wardsat extends OSAT{
    /*Deneth Ward OSAT, Raiders-2*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 210;
	$this->faction = "Deneth";
        $this->phpclass = "wardsat";
        $this->imagePath = "img/ships/DenethWardOSAT.png";
        $this->canvasSize = 80;
        $this->shipClass = 'Ward Satellite';
        
        //$this->limited = 10;
        //$this->occurence = "rare";
        $this->isd = 2228;
        //$this->unofficial = true;
        
        $this->forwardDefense = 9;
        $this->sideDefense = 9;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
        $this->addPrimarySystem(new Reactor(5, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 6, 3, 6)); 
        $this->addPrimarySystem(new Thruster(4, 6, 0, 0, 2)); 
        $this->addPrimarySystem(new AssaultLaser(4, 6, 4, 270, 90)); 
        $this->addPrimarySystem(new AssaultLaser(4, 6, 4, 270, 90)); 
        $this->addPrimarySystem(new TwinArray(3, 6, 2, 180, 360));
	$this->addPrimarySystem(new TwinArray(3, 6, 2, 0, 360));        
	$this->addPrimarySystem(new TwinArray(3, 6, 2, 0, 180));  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(5, 26));
        
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Thruster",
					14 => "Assault Laser",
          				16 => "Twin Array",
					18 => "Scanner",
					20 => "Reactor",
			)
		);
    
    
        
    }
}
?>
