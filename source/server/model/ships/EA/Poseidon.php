<?php
class Poseidon extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 950;
		$this->faction = "EA";
		$this->phpclass = "Poseidon";
		$this->imagePath = "img/ships/Poseidon.png";
		$this->shipClass = "Poseidon";
		$this->shipSizeClass = 3;
		$this->canvasSize= 400;
		$this->limited = 10;
		$this->fighters = array("normal"=>96);
		$this->customFighter = array("Thunderbolt"=>96);
	        $this->isd = 2255;
	        $this->notes = 'Thunderbolt capable';
	        $this->notes .= '<br>Provides +5 Initiative for all friendly EA units';

		$this->forwardDefense = 16;
		$this->sideDefense = 19;

		$this->turncost = 1.5;
		$this->turndelaycost = 1.5;
		$this->accelcost = 4;
		$this->rollcost = 3;
		$this->pivotcost = 4;
		
		$this->iniativebonus = 5;

		$this->addPrimarySystem(new Reactor(5, 28, 0, 0));
		$this->addPrimarySystem(new CnC(6, 24, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 20, 3, 8));
		$this->addPrimarySystem(new Engine(6, 25, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 2));
		$this->addPrimarySystem(new Jumpengine(5, 20, 4, 24));

		$this->addFrontSystem(new MediumPulse(3, 6, 3, 240, 0));
		$this->addFrontSystem(new MediumPulse(3, 6, 3, 0, 120));
		$this->addFrontSystem(new HeavyInterceptorBattery(2, 6, 3, 240, 0));
		$this->addFrontSystem(new HeavyInterceptorBattery(2, 6, 3, 0, 120));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));		

		$this->addAftSystem(new MediumPulse(3, 6, 3, 180, 300));
		$this->addAftSystem(new MediumPulse(3, 6, 3, 60, 180));
		$this->addAftSystem(new HeavyInterceptorBattery(2, 6, 3, 180, 0));
		$this->addAftSystem(new HeavyInterceptorBattery(2, 6, 3, 60, 180));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 10, 0, 3, 2));

		$this->addLeftSystem(new Thruster(3, 20, 0, 5, 3));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
		$this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 300));
		$this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 360));
		$this->addLeftSystem(new InterceptorMkII(2, 4, 2, 240, 360));
		$this->addLeftSystem(new Hangar(4, 12));
		$this->addLeftSystem(new Hangar(4, 12));	
		$this->addLeftSystem(new Hangar(4, 12));	
		$this->addLeftSystem(new Hangar(4, 12));											

		$this->addRightSystem(new Thruster(3, 20, 0, 5, 4));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 120));
		$this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180));
		$this->addRightSystem(new InterceptorMkII(2, 4, 2, 60, 180));
		$this->addRightSystem(new Hangar(4, 12));
		$this->addRightSystem(new Hangar(4, 12));	
		$this->addRightSystem(new Hangar(4, 12));	
		$this->addRightSystem(new Hangar(4, 12));	
		

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 70));
        $this->addAftSystem(new Structure( 4, 70));
        $this->addLeftSystem(new Structure( 4, 80));
        $this->addRightSystem(new Structure( 4, 80));
        $this->addPrimarySystem(new Structure(5, 75));
		
		$this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        12 => "Jump Drive",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Medium Pulse Cannon",
                        9 => "Heavy Interceptor Battery",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        9 => "Medium Pulse Cannon",
                        12 => "Heavy Interceptor Battery",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        3 => "Thruster",
                        4 => "Standard Particle Beam",
                        7 => "Interceptor II",
                        12 => "Hangar",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        3 => "Thruster",
                        4 => "Standard Particle Beam",
                        7 => "Interceptor II",
                        12 => "Hangar",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
