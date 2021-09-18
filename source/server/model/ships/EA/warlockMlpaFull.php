<?php
class WarlockMlpaFull extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 1800+2*35+2*70;
		$this->faction = "EA";
		$this->phpclass = "WarlockMlpaFull";
		$this->imagePath = "img/ships/warlock.png";
		$this->shipClass = "Warlock (full)";
	    		$this->unofficial = true;
		$this->variantOf = "Warlock";
		$this->shipSizeClass = 3;
		$this->canvasSize= 400;
		$this->limited = 10;
		$this->fighters = array("normal"=>24);
		$this->customFighter = array("Thunderbolt"=>24);
	        $this->isd = 2261;
	        $this->notes = 'Thunderbolt capable';
		$this->forwardDefense = 15;
		$this->sideDefense = 19;
		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 4;
		$this->rollcost = 2;
		$this->pivotcost = 3;
		$this->iniativebonus = 0;
		$this->addPrimarySystem(new Reactor(6, 30, 0, 0));
		$this->addPrimarySystem(new CnC(6, 20, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 20, 5, 9));
		$this->addPrimarySystem(new Engine(6, 23, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(5, 26));
		$this->addPrimarySystem(new Jumpengine(6, 20, 4, 16));
		$this->addFrontSystem(new Railgun(4, 9, 6, 300, 60));
		$this->addFrontSystem(new Railgun(4, 9, 6, 300, 60));
		$this->addFrontSystem(new HvyParticleCannon(5, 12, 9, 330, 30));
		$this->addFrontSystem(new HvyParticleCannon(5, 12, 9, 330, 30));
		$this->addFrontSystem(new MLPA(4, 9, 5, 300, 60));
		$this->addFrontSystem(new MLPA(4, 9, 5, 300, 60));
		$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
		$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));
		$this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
		$this->addAftSystem(new Railgun(4, 9, 6, 120, 240));
		$this->addAftSystem(new Railgun(4, 9, 6, 120, 240));
		$this->addAftSystem(new MLPA(4, 9, 5, 120, 240));
		$this->addAftSystem(new MLPA(4, 9, 5, 120, 240));
		$this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
		$this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
		$this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
		$this->addAftSystem(new Thruster(4, 12, 0, 2, 2));
		$this->addAftSystem(new Thruster(3, 6, 0, 1, 2));
		$this->addAftSystem(new Thruster(3, 6, 0, 1, 2));
		$this->addLeftSystem(new MultiMissileLauncher(3, 'L', 240, 0));
		$this->addLeftSystem(new MultiMissileLauncher(3, 'LH', 240, 0));
		$this->addLeftSystem(new MLPA(4, 9, 5, 240, 0));
		$this->addLeftSystem(new MLPA(4, 9, 5, 240, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 360));
		$this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
		$this->addRightSystem(new MultiMissileLauncher(3, 'L', 0, 120));
		$this->addRightSystem(new MultiMissileLauncher(3, 'LH', 0, 120));
		$this->addRightSystem(new MLPA(4, 9, 5, 0, 120));
		$this->addRightSystem(new MLPA(4, 9, 5, 0, 120));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180));
		$this->addRightSystem(new Thruster(4, 20, 0, 6, 4));
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 72));
        $this->addAftSystem(new Structure( 5, 60));
        $this->addLeftSystem(new Structure( 5, 80));
        $this->addRightSystem(new Structure( 5, 80));
        $this->addPrimarySystem(new Structure(6, 60));
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Jump Drive",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Heavy Particle Cannon",
                        8 => "Medium Laser-Pulse Array",
                        10 => "Railgun",
                        13 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Medium Laser-Pulse Array",
                        10 => "Railgun",
                        13 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        5 => "Class-L Missile Rack",
                        6 => "Class-LH Missile Rack",
						8 => "Medium Laser-Pulse Array",
						11 => "Standard Particle Beam",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        5 => "Class-L Missile Rack",
                        6 => "Class-LH Missile Rack",
						8 => "Medium Laser-Pulse Array",
						11 => "Standard Particle Beam",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
