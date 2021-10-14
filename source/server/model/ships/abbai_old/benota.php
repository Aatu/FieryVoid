<?php
class Benota extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 400;
		$this->faction = "Abbai (WotCR)";
        $this->phpclass = "Benota";
        $this->imagePath = "img/ships/AbbaiBenota.png";
        $this->shipClass = "Benota Fast Frigate";

		$this->isd = 2030;
        
        $this->forwardDefense = 12;
        $this->sideDefense = 15;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 35;
        
        $this->addPrimarySystem(new Reactor(5, 9, 0, 0));
        $this->addPrimarySystem(new CnC(5, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(4, 14, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(4, 1));
        $this->addPrimarySystem(new ShieldGenerator(4, 12, 4, 2));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 5, 4));
   
        $this->addFrontSystem(new Thruster(3, 12, 0, 6, 1));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 300, 0));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 2, 0, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));

        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 180, 300));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 60, 180));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));
        $this->addAftSystem(new Thruster(3, 12, 0, 5, 2));


        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 30));
        $this->addAftSystem(new Structure(3, 36));
        $this->addPrimarySystem(new Structure(4, 20));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					9 => "Thruster",
					11 => "Scanner",
					13 => "Shield Generator",
					14 => "Hangar",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Light Particle Beam",
					8 => "Assault Laser",	
					10 => "Gravitic Shield",
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
