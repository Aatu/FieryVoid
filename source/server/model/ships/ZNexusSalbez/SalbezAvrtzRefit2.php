<?php
class SalbezAvrtzRefit2 extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 825;
		$this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezAvrtzRefit2";
        $this->imagePath = "img/ships/Nexus/salbez_bevtun.png";
        $this->shipClass = "Av'rtz Explorer (2153 Refit)";
			$this->variantOf = "Av'rtz Explorer";
			$this->occurence = "common";
//        $this->shipSizeClass = 3;
		$this->canvasSize = 190; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 10;

        $this->fighters = array("normal"=>12);

        $this->Enormous = true;
		$this->isd = 2153;
        
        $this->forwardDefense = 17;
        $this->sideDefense = 19;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 34, 0, 0));
        $this->addPrimarySystem(new CnC(5, 32, 0, 0));
        $this->addPrimarySystem(new ELINTScanner(4, 25, 7, 10));
        $this->addPrimarySystem(new Engine(4, 25, 0, 12, 4));
		$this->addPrimarySystem(new Hangar(2, 18));
		
        $this->addFrontSystem(new Thruster(3, 20, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 20, 0, 4, 1));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 240, 60));
		$this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 300, 120));
		$this->addFrontSystem(new CargoBay(2, 15));
		$this->addFrontSystem(new CargoBay(2, 15));

        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 16, 0, 3, 2));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 120, 240));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 120, 240));
		$this->addAftSystem(new CargoBay(2, 30));
		$this->addAftSystem(new JumpEngine(4, 20, 5, 50));

        $this->addLeftSystem(new MediumLaser(2, 6, 5, 240, 360));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 180, 360));
		$this->addLeftSystem(new NexusSwarmTorpedo(2, 5, 2, 240, 360));
        $this->addLeftSystem(new Thruster(3, 20, 0, 6, 3));

        $this->addRightSystem(new MediumLaser(2, 6, 5, 0, 120));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 180));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 180));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 180));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 0, 180));
		$this->addRightSystem(new NexusSwarmTorpedo(2, 5, 2, 0, 120));
        $this->addRightSystem(new Thruster(2, 20, 0, 6, 4));

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
					3 => "Thruster",
					5 => "Medium Laser",
					6 => "Improved Particle Beam",
					8 => "Swarm Torpedo",
					10 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					3 => "Thruster",
					5 => "Medium Laser",
					6 => "Improved Particle Beam",
					8 => "Swarm Torpedo",
					10 => "Cargo Bay",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
