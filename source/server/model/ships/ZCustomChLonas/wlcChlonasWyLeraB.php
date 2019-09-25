<?php
class wlcChlonasWyLeraB extends OSAT{
    /*Ch'Lonas Wy'Lera B OSAT*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 275;
        $this->faction = "Ch'Lonas";
        $this->variantOf = "Wy'Lera A OSAT";
	$this->unofficial = true;
        $this->phpclass = "wlcChlonasWyLeraB";
        $this->imagePath = "img/ships/legion.png";
        $this->shipClass = "Wy'Lera B OSAT";
        
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;
        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 2, 5)); 
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2)); 
        $this->addPrimarySystem(new AssaultLaser(3, 6, 4, 300, 60));
        $this->addPrimarySystem(new MatterCannon(3, 7, 4, 180, 360));
        $this->addPrimarySystem(new MatterCannon(3, 7, 4, 0, 180));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 24));
    }
}
