<?php
class SalbezJertkat extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "ZNexus Playtest Sal-bez";
        $this->phpclass = "SalbezJertkat";
        $this->imagePath = "img/ships/Nexus/salbez_jertkat.png";
        $this->shipClass = "Jer't'kat Heavy Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 135; //img has 200px per side
		$this->unofficial = true;
        $this->limited = 33;

        $this->fighters = array("normal"=>6);

		$this->isd = 2104;
        
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
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));

        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 2, 2));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 180, 300));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 60, 180));

        $this->addLeftSystem(new MediumLaser(3, 6, 5, 240, 360));
        $this->addLeftSystem(new NexusSwarmTorpedo(3, 4, 2, 240, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));

        $this->addRightSystem(new MediumLaser(3, 6, 5, 0, 120));
        $this->addRightSystem(new NexusSwarmTorpedo(3, 4, 2, 0, 120));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
        $this->addRightSystem(new Thruster(3, 15, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 40));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(4, 40));
		
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
					9 => "Medium Laser",
					10 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					8 => "Thruster",
					10 => "Medium Laser",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Medium Laser",
					9 => "Swarm Torpedo",
					11 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Medium Laser",
					9 => "Swarm Torpedo",
					11 => "Light Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
