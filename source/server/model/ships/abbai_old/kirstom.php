<?php
class Kirstom extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 625;
	$this->faction = "Abbai_old";
        $this->phpclass = "Kirstom";
        $this->imagePath = "img/ships/AbbaiBrova.png";
        $this->shipClass = "Kirstom Large Cruiser";
        $this->shipSizeClass = 3;

        $this->occurence = "rare";
        $this->variantOf = 'Brova Jump Cruiser';
        $this->isd = 2015;
        
        $this->forwardDefense = 18;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 4, 7));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 4));
 	$this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 4));
   
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 1, 0, 60));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 1, 300, 360));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 300, 60));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 300, 60));

        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 120, 180));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 240));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 240));

        $this->addLeftSystem(new AssaultLaser(4, 6, 4, 240, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 1, 240, 300));
        $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

        $this->addRightSystem(new AssaultLaser(4, 6, 4, 0, 120));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 1, 60, 120));
        $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 45));
        $this->addAftSystem(new Structure(4, 39));
        $this->addLeftSystem(new Structure(4, 52));
        $this->addRightSystem(new Structure(4, 52));
        $this->addPrimarySystem(new Structure(4, 36));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					11 => "Scanner",
					13 => "Shield Generator",
					14 => "Hangar",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",	
					7 => "Assault Laser",
					10 => "Light Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					8 => "Gravitic Shield",	
					10 => "Light Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Assault Laser",
					8 => "Light Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					5 => "Gravitic Shield",
					7 => "Assault Laser",
					8 => "Light Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
