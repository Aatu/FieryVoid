<?php
class SalbezBevtun extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 575;
		$this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezBevtun";
        $this->imagePath = "img/ships/Nexus/salbez_bevtun.png";
        $this->shipClass = "Bev'tun Long-Range Miner (2029)";
			$this->variantOf = "Av'rtz Explorer";
			$this->occurence = "common";
//        $this->shipSizeClass = 3;
		$this->canvasSize = 190; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("normal"=>12);

        $this->Enormous = true;
		$this->isd = 2029;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 19;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(3, 25, 0, 0));
        $this->addPrimarySystem(new CnC(3, 16, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(3, 25, 5, 5));
        $this->addPrimarySystem(new Engine(3, 15, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(2, 18));
		$this->addPrimarySystem(new CargoBay(2, 30));
		
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
        $this->addFrontSystem(new Thruster(2, 15, 0, 2, 1));
		$this->addFrontSystem(new LaserCutter(2, 6, 4, 300, 60));
		$this->addFrontSystem(new LaserCutter(2, 6, 4, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(1, 2, 1, 300, 120));
		$this->addFrontSystem(new CargoBay(2, 30));
		$this->addFrontSystem(new CargoBay(2, 30));

        $this->addAftSystem(new Thruster(2, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(2, 16, 0, 2, 2));
		$this->addAftSystem(new LightLaser(2, 4, 3, 180, 360));
		$this->addAftSystem(new LightLaser(2, 4, 3, 0, 180));
		$this->addAftSystem(new CargoBay(2, 30));
		$this->addAftSystem(new JumpEngine(4, 20, 5, 50));

        $this->addLeftSystem(new LaserCutter(2, 6, 4, 240, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(1, 2, 1, 180, 360));
        $this->addLeftSystem(new Thruster(2, 20, 0, 4, 3));
		$this->addLeftSystem(new CustomIndustrialGrappler(2, 5, 0, 300, 0));
		$this->addLeftSystem(new CargoBay(2, 30));

        $this->addRightSystem(new LaserCutter(2, 6, 4, 0, 120));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(1, 2, 1, 0, 180));
        $this->addRightSystem(new Thruster(2, 20, 0, 4, 4));
		$this->addRightSystem(new CustomIndustrialGrappler(2, 5, 0, 0, 60));
		$this->addRightSystem(new CargoBay(2, 30));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(3, 60));
        $this->addAftSystem(new Structure(3, 60));
        $this->addLeftSystem(new Structure(3, 80));
        $this->addRightSystem(new Structure(3, 80));
        $this->addPrimarySystem(new Structure(3, 75));
		
		$this->hitChart = array(
			0=> array(
					8 => "Structure",
					11 => "Cargo Bay",
					13 => "ELINT Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Laser Cutter",
					8 => "Light Particle Beam",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					7 => "Light Laser",
					9 => "Cargo Bay",
					11 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					3 => "Thruster",
					5 => "Laser Cutter",
					7 => "Light Particle Beam",
					8 => "Industrial Grappler",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					5 => "Laser Cutter",
					7 => "Light Particle Beam",
					8 => "Industrial Grappler",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
