<?php
class shlassanTriumvirateCruiser extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 600;
		$this->faction = "Small Races";
		$this->phpclass = "shlassanTriumvirateCruiser";
		$this->imagePath = "img/ships/shlassanTriumvirate.png";
		$this->shipClass = "Sh'lassan Triumvirate Cruiser";
		$this->shipSizeClass = 3;
	    $this->isd = 2258;
	    $this->limited = 33; //Limited Deployment
		$this->unofficial = true;
		
		$this->notes = "Only 3 exist.";

		$this->forwardDefense = 13;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 2;
		$this->rollcost = 2;
		$this->pivotcost = 2;
		$this->iniativebonus = 0;



		//ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(20); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 240); //add full load of basic missiles
		
		$this->addPrimarySystem(new Reactor(4, 15, 0, 0));
		$this->addPrimarySystem(new CnC(4, 15, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 3, 6));
		$this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
		$this->addPrimarySystem(new JumpEngine(5, 20, 4, 24));
		$this->addPrimarySystem(new Hangar(4, 4, 2));
		$this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		

		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
        $this->addFrontSystem(new GaussCannon(2, 10, 4, 240, 120));
		$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));

		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 240));

		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new GaussCannon(2, 10, 4, 300, 360));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));

		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
		$this->addRightSystem(new GaussCannon(2, 10, 4, 0, 60));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));

		//0:primary, 1:front, 2:rear, 3:left, 4:right;
		$this->addFrontSystem(new Structure( 4, 48));
		$this->addAftSystem(new Structure( 3, 42));
		$this->addLeftSystem(new Structure( 4, 55));
		$this->addRightSystem(new Structure( 4, 55));
		$this->addPrimarySystem(new Structure( 4, 48));
		
		
		$this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        11 => "Jump Engine",
                        12 => "Class-S Missile Rack",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Standard Particle Beam",
                        8 => "Gauss Cannon",
                        11 => "Light Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Standard Particle Beam",
                        11 => "Light Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        6 => "Gauss Cannon",
                        8 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        6 => "Gauss Cannon",
                        8 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
