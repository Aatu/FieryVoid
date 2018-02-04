<?php
class Lystala extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 415;
	$this->faction = "Abbai_old";
        $this->phpclass = "Lystala";
        $this->imagePath = "img/ships/AbbaiKastona.png";
        $this->shipClass = "Lystala Patrol Cruiser";
        $this->shipSizeClass = 3;

        $this->occurence = "rare";
        $this->variantOf = 'Kastona Cruiser';
        $this->isd = 2025;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 4, 6));
        $this->addPrimarySystem(new Engine(5, 14, 0, 8, 3));
 	$this->addPrimarySystem(new Hangar(5, 1));
        $this->addPrimarySystem(new ShieldGenerator(6, 12, 4, 2));
   
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new LaserCutter(3, 0, 0, 240, 360));
        $this->addFrontSystem(new SensorSpike(2, 0, 0, 240, 360));
        $this->addFrontSystem(new ImpCommJammer(3, 0, 0, 300, 60));
        $this->addFrontSystem(new SensorSpike(2, 0, 0, 0, 120));
        $this->addFrontSystem(new LaserCutter(3, 0, 0, 0, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));

        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new JumpEngine(5, 12, 4, 32));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 180, 270));
        $this->addAftSystem(new GraviticShield(0, 6, 0, 1, 90, 180));

        $this->addLeftSystem(new GraviticShield(0, 6, 0, 1, 270, 360));
        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));

        $this->addRightSystem(new GraviticShield(0, 6, 0, 1, 0, 90));
        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 39));
        $this->addAftSystem(new Structure(4, 39));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(5, 28));
		
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
					6 => "Laser Cutter",
					7 => "Improved Comm Jammer",
					9 => "Sensor Spike",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",	
					8 => "Light Particle Beam",
					10 => "Jump Engine",
					17 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",
					17 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Gravitic Shield",
					17 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
