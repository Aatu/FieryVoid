<?php
class Curragh extends HeavyCombatVesselLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 475;
		$this->faction = "Raiders";
        $this->phpclass = "Curragh";
        $this->imagePath = "img/ships/RaiderFreebooter.png";
        $this->shipClass = "Curragh";
		$this->occurence = "rare";
		$this->variantOf = "Freebooter";
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";		
		$this->isd = 2230;
		
		$this->unofficial = true;
		
        $this->forwardDefense = 15;
        $this->sideDefense = 14;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(3, 16, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Engine(6, 14, 0, 10, 3));
        $this->addPrimarySystem(new Hangar(2, 4));
        
		$this->addFrontSystem(new Thruster(3, 12, 0, 3, 1));
		
        $this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));

        $this->addLeftSystem(new MediumLaser(6, 6, 5, 240, 60));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 240, 60));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 240, 60));
		$this->addLeftSystem(new ParticleCannon(3, 8, 7, 180, 0));
        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));

        $this->addRightSystem(new MediumLaser(6, 6, 5, 300, 120));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 300, 120));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 300, 120));
		$this->addRightSystem(new ParticleCannon(3, 8, 7, 0, 180));
        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addPrimarySystem(new Structure(5, 50));
        $this->addLeftSystem(new Structure(4, 40));
        $this->addRightSystem(new Structure(4, 40));
		
		$this->hitChart = array(
			0=> array(
					7 => "Structure",
					11 => "Thruster",
					15 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			3=> array(
					4 => "Thruster",
					6 => "Standard Particle Beam",
					8 => "Medium Laser Cannon",
					11 => "Particle Cannon",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					6 => "Standard Particle Beam",
					8 => "Medium Laser Cannon",
					11 => "Particle Cannon",
					18 => "Structure",
					20 => "Primary",
			),
		);
		
    }
}
