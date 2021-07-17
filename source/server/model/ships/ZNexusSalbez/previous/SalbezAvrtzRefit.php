<?php
class SalbezAvrtzRefit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 825;
		$this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezAvrtzRefit";
        $this->imagePath = "img/ships/Nexus/salbez_bevtun.png";
        $this->shipClass = "Av'rtz Explorer (2122 Refit)";
			$this->variantOf = "Av'rtz Explorer";
			$this->occurence = "common";
//        $this->shipSizeClass = 3;
		$this->canvasSize = 190; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("normal"=>12);

        $this->Enormous = true;
		$this->isd = 2122;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 19;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 24, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 25, 7, 8));
        $this->addPrimarySystem(new Engine(4, 20, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(2, 18));
		
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 15, 0, 4, 1));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 240, 60));
		$this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 300, 120));
		$this->addFrontSystem(new CargoBay(2, 20));
		$this->addFrontSystem(new CargoBay(2, 20));

        $this->addAftSystem(new Thruster(3, 16, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 2, 2));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 120, 240));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 120, 240));
		$this->addAftSystem(new CargoBay(2, 20));
		$this->addAftSystem(new JumpEngine(4, 20, 5, 50));

        $this->addLeftSystem(new MediumLaser(2, 6, 5, 240, 360));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusSwarmTorpedo(2, 5, 2, 240, 360));
        $this->addLeftSystem(new Thruster(3, 20, 0, 5, 3));

        $this->addRightSystem(new MediumLaser(2, 6, 5, 0, 120));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 180));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 180));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 180));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 180));
        $this->addRightSystem(new Thruster(2, 20, 0, 5, 4));
		$this->addRightSystem(new NexusSwarmTorpedo(2, 5, 2, 240, 360));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 60));
        $this->addAftSystem(new Structure(4, 60));
        $this->addLeftSystem(new Structure(4, 80));
        $this->addRightSystem(new Structure(4, 80));
        $this->addPrimarySystem(new Structure(4, 75));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					12 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					6 => "Medium Laser",
					8 => "Improved Particle Beam",
					11 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					5 => "Thruster",
					7 => "Medium Laser",
					9 => "Cargo Bay",
					11 => "Jump Engine",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					6 => "Medium Laser",
					8 => "Improved Particle Beam",
					10 => "Swarm Torpedo",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Medium Laser",
					8 => "Improved Particle Beam",
					10 => "Swarm Torpedo",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
