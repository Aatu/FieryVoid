<?php
class Seta extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 450;
	$this->faction = "Abbai (WotCR)";
        $this->phpclass = "Seta";
        $this->imagePath = "img/ships/AbbaiBenota.png";
        $this->shipClass = "Seta Group Scout";

        $this->occurence = "uncommon";
        $this->variantOf = 'Benota Fast Frigate';
        $this->isd = 2030;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(5, 12, 0, 0));
        $this->addPrimarySystem(new CnC(5, 6, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 12, 5, 8));
        $this->addPrimarySystem(new Engine(4, 11, 0, 8, 3));
 	$this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new ShieldGenerator(4, 12, 4, 2));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 5, 1));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 300, 0));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 0, 60));
        $this->addFrontSystem(new SensorSpike(2, 0, 0, 240, 60));
        $this->addFrontSystem(new ImpCommJammer(3, 0, 0, 300, 60));
        $this->addFrontSystem(new SensorSpike(2, 0, 0, 300, 120));

        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 180, 300));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 60, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));


        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 30));
        $this->addAftSystem(new Structure(3, 36));
        $this->addPrimarySystem(new Structure(4, 20));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					9 => "Thruster",
					11 => "Elint Scanner",
					13 => "Shield Generator",
					14 => "Hangar",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Improved Comm Jammer",
					9 => "Sensor Spike",	
					11 => "Gravitic Shield",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					9 => "Gravitic Shield",	
					11 => "Light Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
