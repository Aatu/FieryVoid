<?php
class Nota1856 extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 475;
	$this->faction = "Abbai (WotCR)";
        $this->phpclass = "Nota1856";
        $this->imagePath = "img/ships/AbbaiNota.png";
        $this->shipClass = "Nota Deep Scout (1856)";
			$this->occurence = "common";
			$this->variantOf = 'Nota Deep Scout';
        $this->shipSizeClass = 3;

        $this->limited = 10;
        $this->isd = 1856;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 16, 6, 8));
        $this->addPrimarySystem(new Engine(5, 14, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(5, 2));
        $this->addPrimarySystem(new ShieldGenerator(5, 12, 4, 3));
        $this->addPrimarySystem(new CommJammer(3, 0, 0, 0, 360));
        $this->addPrimarySystem(new JumpEngine(5, 12, 4, 32));
   
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 1, 300, 360));
        $this->addFrontSystem(new GraviticShield(0, 6, 0, 1, 0, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));

        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 180, 240));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 120, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));

        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new SensorSpear(3, 0, 0, 240, 360));
        $this->addLeftSystem(new GraviticShield(0, 6, 0, 1, 240, 300));
        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));

        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addRightSystem(new SensorSpear(3, 0, 0, 0, 120));
        $this->addRightSystem(new GraviticShield(0, 6, 0, 1, 60, 120));
        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 45));
        $this->addAftSystem(new Structure(3, 39));
        $this->addLeftSystem(new Structure(3, 44));
        $this->addRightSystem(new Structure(3, 44));
        $this->addPrimarySystem(new Structure(5, 28));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					9 => "Shield Generator",
					10 => "Comm Jammer",
					11 => "Jump Engine",
					12 => "Hangar",
					14 => "Elint Scanner",
					16 => "Engine",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",	
					7 => "Laser Cutter",
					9 => "Light Particle Beam",
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
					5 => "Thruster",
					7 => "Gravitic Shield",
					9 => "Light Particle Beam",
					11 => "Sensor Spear",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Gravitic Shield",
					9 => "Light Particle Beam",
					11 => "Sensor Spear",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>