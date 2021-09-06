<?php
class SalbezAvrtz extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 675;
		$this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezAvrtz";
        $this->imagePath = "img/ships/Nexus/salbez_bevtun.png";
        $this->shipClass = "Av'rtz Explorer";
//        $this->shipSizeClass = 3;
		$this->canvasSize = 190; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("normal"=>12);

        $this->Enormous = true;
		$this->isd = 2080;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 19;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(3, 28, 0, 0));
        $this->addPrimarySystem(new CnC(3, 24, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(3, 25, 7, 6));
        $this->addPrimarySystem(new Engine(3, 20, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(2, 18));
		
        $this->addFrontSystem(new Thruster(3, 20, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 20, 0, 4, 1));
		$this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
		$this->addFrontSystem(new LaserCutter(3, 6, 4, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 300, 120));
		$this->addFrontSystem(new CargoBay(2, 20));
		$this->addFrontSystem(new CargoBay(2, 20));

        $this->addAftSystem(new Thruster(3, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 2, 2));
		$this->addAftSystem(new LaserCutter(3, 6, 4, 120, 240));
		$this->addAftSystem(new LaserCutter(3, 6, 4, 120, 240));
		$this->addAftSystem(new CargoBay(2, 20));
		$this->addAftSystem(new JumpEngine(4, 20, 5, 50));

        $this->addLeftSystem(new LaserCutter(2, 6, 4, 240, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new NexusBoltTorpedo(2, 5, 2, 240, 360));
        $this->addLeftSystem(new Thruster(3, 20, 0, 5, 3));
		$this->addLeftSystem(new CargoBay(2, 15));

        $this->addRightSystem(new LaserCutter(2, 6, 4, 0, 120));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new NexusBoltTorpedo(2, 5, 2, 0, 120));
        $this->addRightSystem(new Thruster(2, 20, 0, 5, 4));
		$this->addRightSystem(new CargoBay(2, 15));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 60));
        $this->addAftSystem(new Structure(3, 60));
        $this->addLeftSystem(new Structure(3, 80));
        $this->addRightSystem(new Structure(3, 80));
        $this->addPrimarySystem(new Structure(3, 75));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					12 => "ELINT Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Laser Cutter",
					8 => "Light Particle Beam",
					10 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					7 => "Laser Cutter",
					9 => "Cargo Bay",
					11 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					5 => "Laser Cutter",
					7 => "Light Particle Beam",
					8 => "Bolt Torpedo",
					10 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					5 => "Laser Cutter",
					7 => "Light Particle Beam",
					8 => "Bolt Torpedo",
					10 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
