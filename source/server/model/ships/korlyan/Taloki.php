<?php
class Taloki extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3500;
		$this->faction = 'Kor-Lyan';
		$this->phpclass = "Taloki";
		$this->shipClass = "Taloki Starbase (2240)";
		$this->fighters = array("assault shuttles"=>2, "normal"=>24);

        $this->isd = 2240;
		$this->shipSizeClass = 3; //Enormous is not implemented
        $this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->nonRotating = true; //some bases do not rotate - this attribute is used in combination with $base or $smallBase

		$this->forwardDefense = 21;
		$this->sideDefense = 24;

		$this->imagePath = "img/ships/korlyan_taloki.png";
		$this->canvasSize = 260; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);

		$this->addPrimarySystem(new Reactor(4, 25, 0, 0));
		$this->addPrimarySystem(new CnC(4, 32, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new CargoBay(4, 75));
		$this->addPrimarySystem(new CargoBay(4, 75));
		$this->addPrimarySystem(new ReloadRack(4, 9));
		$this->addPrimarySystem(new ReloadRack(4, 9));
		$this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new MultiDefenseLauncher(2, 'D', 0, 360, true));
        $this->addPrimarySystem(new MultiDefenseLauncher(2, 'D', 0, 360, true));
        $this->addPrimarySystem(new MultiDefenseLauncher(2, 'D', 0, 360, true));
        $this->addPrimarySystem(new MultiDefenseLauncher(2, 'D', 0, 360, true));

		$this->addFrontSystem(new Hangar(4, 14));
		$this->addFrontSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		$this->addFrontSystem(new ProximityLaser(4, 6, 6, 270, 90));
		$this->addFrontSystem(new LimpetBoreBase(4, 5, 3, 270, 90));
		$this->addFrontSystem(new ProximityLaser(4, 6, 6, 270, 90));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		
		$this->addAftSystem(new Hangar(4, 14));
		$this->addAftSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		$this->addAftSystem(new ProximityLaser(4, 6, 6, 90, 270));
		$this->addAftSystem(new LimpetBoreBase(4, 5, 3, 90, 270));
		$this->addAftSystem(new ProximityLaser(4, 6, 6, 90, 270));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		
		$this->addLeftFrontSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new FMissileRack(3, 'F', 240, 60, true)); 
        $this->addLeftFrontSystem(new FMissileRack(3, 'F', 240, 60, true)); 
        $this->addLeftFrontSystem(new FMissileRack(3, 'F', 240, 60, true)); 
        $this->addLeftFrontSystem(new FMissileRack(3, 'F', 240, 60, true)); 

		$this->addLeftAftSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new FMissileRack(3, 'F', 120, 300, true)); 
        $this->addLeftAftSystem(new FMissileRack(3, 'F', 120, 300, true)); 
        $this->addLeftAftSystem(new FMissileRack(3, 'F', 120, 300, true)); 
        $this->addLeftAftSystem(new FMissileRack(3, 'F', 120, 300, true)); 

		$this->addRightFrontSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new FMissileRack(3, 'F', 300, 120, true)); 
        $this->addRightFrontSystem(new FMissileRack(3, 'F', 300, 120, true)); 
        $this->addRightFrontSystem(new FMissileRack(3, 'F', 300, 120, true)); 
        $this->addRightFrontSystem(new FMissileRack(3, 'F', 300, 120, true)); 

		$this->addRightAftSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new FMissileRack(3, 'F', 60, 240, true)); 
        $this->addRightAftSystem(new FMissileRack(3, 'F', 60, 240, true)); 
        $this->addRightAftSystem(new FMissileRack(3, 'F', 60, 240, true)); 
        $this->addRightAftSystem(new FMissileRack(3, 'F', 60, 240, true)); 

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 130));
        $this->addAftSystem(new Structure( 3, 130));
        $this->addLeftFrontSystem(new Structure( 3, 150));
        $this->addLeftAftSystem(new Structure( 3, 150));
        $this->addRightFrontSystem(new Structure( 3, 150));
        $this->addRightAftSystem(new Structure( 3, 150));        
		$this->addPrimarySystem(new Structure( 4, 180));

	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
					11 => "Reload Rack",
					12 => "Class-D Missile Launcher",
                    14 => "Scanner",
					18 => "Cargo Bay",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    2 => "Base Limpet Bore Torpedo",
					4 => "Proximity Laser",
					6 => "Particle Cannon",
					7 => "Hangar",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    2 => "Base Limpet Bore Torpedo",
					4 => "Proximity Laser",
					6 => "Particle Cannon",
					7 => "Hangar",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    3 => "Standard Particle Beam",
                    7 => "Class-F Missile Rack",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    3 => "Standard Particle Beam",
                    7 => "Class-F Missile Rack",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    3 => "Standard Particle Beam",
                    7 => "Class-F Missile Rack",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    3 => "Standard Particle Beam",
                    7 => "Class-F Missile Rack",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
            	),
           	);

    }
}