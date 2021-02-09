<?php
class GODAlpha extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 700;
		$this->faction = 'EA';//"EA defenses";
        $this->phpclass = "GODAlpha";
        $this->imagePath = "img/ships/god.png";
        $this->shipClass = "GOD Heavy Satellite (Alpha)";
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        $this->addPrimarySystem(new BMissileRack(3, 9, 0, 270, 90, true));
        $this->addPrimarySystem(new BMissileRack(3, 9, 0, 270, 90, true));
        $this->addPrimarySystem(new BMissileRack(3, 9, 0, 270, 90, true));
        $this->addPrimarySystem(new BMissileRack(3, 9, 0, 270, 90, true));
        $this->addPrimarySystem(new HeavyLaser(3, 8, 0, 300, 60));
        $this->addPrimarySystem(new HeavyLaser(3, 8, 0, 300, 60));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 180, 360));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 180, 360));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 0, 180));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 0, 180));
        $this->addPrimarySystem(new InterceptorMkII(2, 4, 2, 0, 360));
        $this->addPrimarySystem(new InterceptorMkII(2, 4, 2, 0, 360));

        $this->addPrimarySystem(new Reactor(4, 24, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 6));   
        $this->addPrimarySystem(new Thruster(4, 20, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 60));

		$this->hitChart = array(
                0=> array(
                        6 => "Structure",
                        8 => "Thruster",
                        10 => "Heavy Laser",
                        13 => "Class-B Missile Rack",
                        15 => "Light Pulse Cannon",
                        17 => "Scanner",
                        19 => "Reactor",
                        20 => "Interceptor II",
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
