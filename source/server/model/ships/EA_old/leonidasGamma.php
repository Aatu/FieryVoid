<?php
class LeonidasGamma extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 150;
		$this->faction = 'EA (early)';//"EA defenses";
        $this->phpclass = "LeonidasGamma";
        $this->imagePath = "img/ships/hector.png";
        $this->shipClass = 'Leonidas Satellite (Gamma)';
			$this->variantOf = "Leonidas Satellite (Alpha)";
			$this->occurence = "common";
 		$this->unofficial = true;

	    $this->isd = 2198;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new SoMissileRack(3, 6, 0, 270, 90, true));
        $this->addPrimarySystem(new SoMissileRack(3, 6, 0, 270, 90, true));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addPrimarySystem(new InterceptorPrototype(2, 4, 1, 0, 360));
        $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        //$this->addPrimarySystem(new InterceptorMkI(2, 4, 1, 0, 360));

        $this->addPrimarySystem(new Reactor(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 14, 2, 4));   

        $this->addPrimarySystem(new Thruster(2, 6, 0, 0, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(3, 30));
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Thruster",
				14 => "Class-SO Missile Rack",
				16 => "Light Particle Beam",
				18 => "Scanner",
				19 => "Reactor",
				20 => "Interceptor Prototype",
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
