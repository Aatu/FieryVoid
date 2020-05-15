<?php
class ArcticAlpha extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 875;
		$this->faction = "EA";
		$this->phpclass = "ArcticAlpha";
		$this->imagePath = "img/ships/arctic.png";
		$this->shipClass = "Arctic Heavy Cruiser (Alpha)";

        	$this->occurence = "unique";
	    	$this->unofficial = true;

		$this->shipSizeClass = 3;
		$this->limited = 33;

        	$this->fighters = array("heavy" => 12);
			$this->customFighter = array("Thunderbolt"=>12);
	        $this->isd = 2258;

		$this->forwardDefense = 15;
		$this->sideDefense = 17;
		$this->turncost = 0.66;
		$this->turndelaycost = 0.66;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 3;
		$this->iniativebonus = 0;


		$this->addPrimarySystem(new Reactor(7, 26, 0, 0));
		$this->addPrimarySystem(new CnC(6, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 20, 4, 8));
		$this->addPrimarySystem(new Engine(6, 24, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(6, 16));
		$this->addPrimarySystem(new Jumpengine(6, 20, 3, 20));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 240, 120));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 240, 120));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 120, 240));
		$this->addPrimarySystem(new StdParticleBeam(2, 4, 1, 120, 240));

		$this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
		$this->addFrontSystem(new HvyParticleCannon(4, 12, 9, 330, 30));
		$this->addFrontSystem(new HeavyLaser(4, 8, 6, 300, 60));
		$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
		$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));
		$this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));

		$this->addAftSystem(new MLPA(4, 9, 5, 90, 270));
		$this->addAftSystem(new MLPA(4, 9, 5, 90, 270));
		$this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
		$this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
		$this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
		$this->addAftSystem(new Thruster(4, 20, 0, 5, 2));

		$this->addLeftSystem(new MLPA(4, 9, 5, 240, 120));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));

		$this->addRightSystem(new MLPA(4, 9, 5, 240, 120));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 6, 66));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addLeftSystem(new Structure( 5, 65));
        $this->addRightSystem(new Structure( 5, 65));
        $this->addPrimarySystem(new Structure(6, 50));
		
		$this->hitChart = array(
                0=> array(
                        7 => "Structure",
                        9 => "Jump Drive",
			11 => "Standard Particle Beam",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Heavy Laser",
                        8 => "Interceptor II",
                        10 => "Heavy Particle Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Medium Laser-Pulse Array",
                        10 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        6 => "Thruster",
			8 => "Standard Particle Beam",
			10 => "Medium Laser-Pulse Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        6 => "Thruster",
			8 => "Standard Particle Beam",
			10 => "Medium Laser-Pulse Array",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
