<?php
class Orion extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3500;
		$this->faction = 'EA';//"EA defenses";
		$this->phpclass = "Orion";
		$this->shipClass = "Orion Battle Station";
		$this->fighters = array("heavy"=>36); 

		$this->shipSizeClass = 3; //Enormous is not implemented
        $this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->imagePath = "img/ships/orion.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Cargo Bay",
				14 => "Scanner",
				16 => "Class-B Missile Rack",
				17 => "Reactor",
				18 => "Hangar",
				20 => "C&C",
			),
		);


		$this->addPrimarySystem(new Reactor(6, 20, 0, -18));
		$this->addPrimarySystem(new CnC(6, 27, 0, 0)); 
		$this->addPrimarySystem(new Scanner(6, 16, 5, 7));
		$this->addPrimarySystem(new Scanner(6, 16, 5, 7));
		$this->addPrimarySystem(new Hangar(6, 6));
		$this->addPrimarySystem(new CargoBay(6, 48));
		$this->addPrimarySystem(new BMissileRack(6, 9, 0, 0, 360, true));
		$this->addPrimarySystem(new BMissileRack(6, 9, 0, 0, 360, true));
		$this->addPrimarySystem(new BMissileRack(6, 9, 0, 0, 360, true));
		$this->addPrimarySystem(new BMissileRack(6, 9, 0, 0, 360, true));

		$this->addPrimarySystem(new Structure( 7, 150));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new HvyParticleCannon(4, 12, 9, $min, $max),
				new HeavyPulse(4, 6, 4, $min, $max),
				new BMissileRack(4, 9, 0, $min, $max, true),
				new QuadParticleBeam(4, 8, 4, $min, $max),
				new InterceptorMKII(4, 4, 2, $min, $max),
				new InterceptorMKII(4, 4, 2, $min, $max),
				new Hangar(4, 6, 6),
				new SubReactorUniversal(4, 20, 0, 0),
				new Structure( 4, 100)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "Class-B Missile Rack",
				2 => "Heavy Pulse Cannon",
				3 => "Heavy Particle Cannon",
				5 => "Interceptor II",
				6 => "Quad Particle Beam",
				7 => "Hangar",
				8 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
