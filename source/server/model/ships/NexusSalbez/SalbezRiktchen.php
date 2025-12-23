<?php
class SalbezRiktchen extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 570;
	$this->faction = "Nexus Sal-bez Coalition";
        $this->phpclass = "SalbezRiktchen";
        $this->imagePath = "img/ships/Nexus/salbez_riktchen3.png";
        $this->shipClass = "Rik't'chen Carrier";
			$this->variantOf = "Jer't'kat Heavy Cruiser";
			$this->occurence = "uncommon";
        $this->shipSizeClass = 3;
		$this->canvasSize = 140; //img has 200px per side
		$this->unofficial = true;

        $this->fighters = array("heavy"=>12);

		$this->isd = 2128;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
        $this->addPrimarySystem(new Reactor(5, 16, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 5, 6));
        $this->addPrimarySystem(new Engine(4, 18, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(2, 8));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 270, 90));
		$this->addFrontSystem(new Hangar(3, 6));
		$this->addFrontSystem(new NexusImprovedParticleBeam(2, 3, 1, 270, 90));

        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
		$this->addAftSystem(new LightLaser(3, 4, 3, 90, 270));
		$this->addAftSystem(new LightLaser(3, 4, 3, 90, 270));

        $this->addLeftSystem(new MediumLaser(3, 6, 5, 240, 360));
        $this->addLeftSystem(new NexusSwarmTorpedo(3, 5, 2, 240, 360));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 240, 60));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 240, 60));
		$this->addLeftSystem(new NexusImprovedParticleBeam(2, 3, 1, 120, 300));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));

        $this->addRightSystem(new MediumLaser(3, 6, 5, 0, 120));
        $this->addRightSystem(new NexusSwarmTorpedo(3, 5, 2, 0, 120));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 300, 120));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 300, 120));
		$this->addRightSystem(new NexusImprovedParticleBeam(2, 3, 1, 60, 240));
        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(5, 40));
		
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
					6 => "Thruster",
					9 => "Hangar",
					11 => "Improved Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					10 => "Light Laser",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Medium Laser",
					9 => "Swarm Torpedo",
					11 => "Improved Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Medium Laser",
					9 => "Swarm Torpedo",
					11 => "Improved Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
