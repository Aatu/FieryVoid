<?php
class GODBeta extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 700;
		$this->faction = "EA";
        $this->phpclass = "GODBeta";
        $this->imagePath = "img/ships/god.png";
        $this->shipClass = "GOD Heavy Satellite (Beta)";
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;


        $this->addPrimarySystem(new LHMissileRack(3, 9, 0, 270, 90));
        $this->addPrimarySystem(new LHMissileRack(3, 9, 0, 270, 90));
        $this->addPrimarySystem(new LHMissileRack(3, 9, 0, 270, 90));
        $this->addPrimarySystem(new LHMissileRack(3, 9, 0, 270, 90));
        $this->addPrimarySystem(new HvyParticleCannon(5, 12, 9, 300, 60));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 180, 360));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 180, 360));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 0, 180));
        $this->addPrimarySystem(new LightPulse(2, 4, 2, 0, 180));
        $this->addPrimarySystem(new InterceptorMkII(2, 4, 2, 0, 360));
        $this->addPrimarySystem(new InterceptorMkII(2, 4, 2, 0, 360));


        $this->addPrimarySystem(new Reactor(4, 24, 0, 0));
     //   $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 6));   

        $this->addPrimarySystem(new Thruster(4, 20, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(4, 60));
    }
}