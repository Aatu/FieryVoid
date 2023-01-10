<?php
class zzunoffTdirk extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 175;
		$this->faction = "Narn";
        $this->phpclass = "zzunoffTdirk";
        $this->imagePath = "img/ships/tgan.png";
        $this->shipClass = "T'Dirk Early Satellite";
		$this->isd = 2213;
 		$this->unofficial = 'S'; //design released after AoG demise

	    $this->notes = 'Only fires Class-D light missiles.';
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 3)); 
        $this->addPrimarySystem(new Thruster(3, 6, 0, 0, 2));
        
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
        $this->addPrimarySystem(new CustomLightSoMissileRack(2, 6, 0, 240, 360, true));
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
        $this->addPrimarySystem(new CustomLightSoMissileRack(2, 6, 0, 0, 120, true));
        $this->addPrimarySystem(new LightParticleBeamShip(1, 2, 1, 270, 90));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(3, 21));
	    
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Thruster",
						13 => "Light Particle Beam",
                        16 => "Light SO-Missile Rack",
						18 => "Scanner",
                        20 => "Reactor",
                ),
        );
    }
}
