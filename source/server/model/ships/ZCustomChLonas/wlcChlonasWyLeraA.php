<?php
class wlcChlonasWyLeraA extends OSAT{
    /*Ch'Lonas Wy'Lera A OSAT*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	      $this->pointCost = 190;
        $this->faction = "Ch'Lonas Cooperative";
        $this->phpclass = "wlcChlonasWyLeraA";
        $this->imagePath = "img/ships/ChlonasWyLera.png";
        $this->canvasSize = 70;
        $this->shipClass = "Wy'Lera A OSAT";
        $this->isd = 2160;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
	      $this->unofficial = true;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));
        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 6, 2, 4)); 
		
        $this->addAftSystem(new Thruster(3, 6, 0, 0, 2)); 
		
        $this->addFrontSystem(new TacLaser(3, 5, 4, 300, 60));
        $this->addFrontSystem(new CustomLightMatterCannon(2, 0, 0, 180, 360));
		$this->addFrontSystem(new CustomLightMatterCannon(2, 0, 0, 0, 180));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 24));
		
        //d20 hit chart
        $this->hitChart = array(		
          0=> array( //PRIMARY - and only for an OSAT
            8 => "Structure",
            10 => "2:Thruster",
            12 => "1:Tactical Laser",
            14 => "1:Light Matter Cannon",
            16 => "1:Light Particle Beam",
            18 => "Scanner",
            20 => "Reactor",
          ),
        );
    }
}
