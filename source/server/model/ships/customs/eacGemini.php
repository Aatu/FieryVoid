<?php
class eacGemini extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);       

		$this->pointCost = 1025;
		$this->faction = "EA";
		$this->phpclass = "eacGemini";
		$this->imagePath = "img/ships/gemini.png";
		$this->shipClass = "Gemini Fleet Destroyer";
		$this->shipSizeClass = 3;
		$this->canvasSize = 200;

		$this->fighters = array("normal"=>6);

      	$this->occurence = "common";
      	$this->isd = 2272;
      	$this->unofficial = true;

		$this->forwardDefense = 15;
		$this->sideDefense = 17;
		$this->turncost = 0.8;
		$this->turndelaycost = 0.8;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 3; 

		$this->addPrimarySystem(new Reactor(6, 20, 0, 0));
		$this->addPrimarySystem(new CnC(6, 14, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 16, 4, 8));
		$this->addPrimarySystem(new Engine(6, 17, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(5, 8, 6));
		$this->addPrimarySystem(new JumpEngine(6, 20, 3, 14));

		$this->addFrontSystem(new LaserPulseArray(4, 9, 5, 300, 60));
		$this->addFrontSystem(new HvyParticleCannon(5, 12, 9, 330, 30));
		$this->addFrontSystem(new LaserPulseArray(4, 9, 5, 300, 60));
		$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
		$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));

		$this->addAftSystem(new MediumPulse(4, 6, 3, 120, 300)); 
		$this->addAftSystem(new LaserPulseArray(4, 9, 5, 120, 240));
		$this->addAftSystem(new LaserPulseArray(4, 9, 5, 120, 240));
		$this->addAftSystem(new MediumPulse(4, 6, 3, 60, 240));
		$this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
		$this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));

		$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
		$this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 0)); 
		$this->addLeftSystem(new LMissileRack(4, 6, 0, 240, 60));
		$this->addLeftSystem(new LaserPulseArray(4, 9, 5, 240, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));

		$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
		$this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180)); 
		$this->addRightSystem(new LMissileRack(4, 6, 0, 300, 120));
		$this->addRightSystem(new LaserPulseArray(4, 9, 5, 0, 120));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));


		$this->addFrontSystem(new Structure( 6, 58));
		$this->addAftSystem(new Structure( 4, 50));
		$this->addLeftSystem(new Structure( 5, 64));
		$this->addRightSystem(new Structure( 5, 64));
		$this->addPrimarySystem(new Structure( 6,60));

    }
}

?>
