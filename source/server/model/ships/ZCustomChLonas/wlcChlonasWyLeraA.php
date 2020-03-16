<?php
class wlcChlonasWyLeraA extends OSAT{
    /*Ch'Lonas Wy'Lera A OSAT*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 190;
        $this->faction = "Ch'Lonas";
        $this->phpclass = "wlcChlonasWyLeraA";
        $this->imagePath = "img/ships/ChlonasWyLera.png";
        $this->canvasSize = 70;
        $this->shipClass = "Wy'Lera A OSAT";
        
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
	$this->unofficial = true;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 2, 4)); 
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2)); 
        $this->addPrimarySystem(new TacLaser(3, 5, 4, 300, 60));
        $this->addPrimarySystem(new CustomLightMatterCannon(2, 0, 0, 180, 360));
	$this->addPrimarySystem(new CustomLightMatterCannon(2, 0, 0, 0, 180));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 24));
    }
}
