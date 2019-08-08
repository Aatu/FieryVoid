<?php
class Hector extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 250;
		$this->faction = 'EA';//"EA defenses";
        $this->phpclass = "Hector";
        $this->imagePath = "img/ships/hector.png";
        $this->shipClass = 'Hector Satellite';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        $this->addPrimarySystem(new BMissileRack(3, 9, 0, 270, 90));
        $this->addPrimarySystem(new BMissileRack(3, 9, 0, 270, 90));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 180, 360));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 0, 180));
        $this->addPrimarySystem(new InterceptorMkI(2, 4, 1, 0, 360));
        //$this->addPrimarySystem(new InterceptorMkI(2, 4, 1, 0, 360));


        $this->addPrimarySystem(new Reactor(4, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 2, 4));   

        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 30));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Thruster",
				14 => "Class-B Missile Rack",
				16 => "Light Pulse Cannon",
				19 => "Scanner",
				19 => "Reactor",
				20 => "Interceptor I",
			),
			1=> array(
				20 => "Primary",
			),
			2=> array(
				20 => "Primary",
			),
        );
    }
}
?>
