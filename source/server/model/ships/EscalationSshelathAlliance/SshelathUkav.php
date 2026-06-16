<?php
class SshelathUkav extends OSAT{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 130;
	$this->faction = "Escalation Wars Sshel'ath Alliance";
        $this->phpclass = "SshelathUkav";
        $this->imagePath = "img/ships/EscalationWars/SshelathUkav.png";
        $this->canvasSize = 60;
        $this->shipClass = 'Ukav Orbital Satellite';
        
        $this->isd = 1955;
        $this->unofficial = true;
        
        $this->forwardDefense = 7;
        $this->sideDefense = 7;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 24); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L

        $this->addPrimarySystem(new OSATCnC(0, 1, 0, 0));		
        $this->addPrimarySystem(new Reactor(3, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 6, 2, 4)); 
        $this->addAftSystem(new Thruster(2, 9, 0, 0, 2)); 

        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
		$this->addPrimarySystem(new AmmoMissileRackSO(2, 0, 0, 270, 90, $ammoMagazine, true));
		$this->addPrimarySystem(new AmmoMissileRackSO(2, 0, 0, 270, 90, $ammoMagazine, true));
		$this->addFrontSystem(new LightParticleBeamShip(0, 2, 1, 0, 360));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(2, 30));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					10 => "2:Thruster",
					12 => "1:Laser Cutter",
					14 => "Class-SO Missile Rack",
          			16 => "1:Light Particle Beam",
					18 => "Scanner",
					20 => "Reactor",
			)
		);
    
    
        
    }
}
?>
