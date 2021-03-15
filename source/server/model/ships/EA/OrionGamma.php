<?php
class OrionGamma extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1900;
		$this->faction = 'EA';
		$this->phpclass = "OrionGamma";
		$this->shipClass = "Orion Battle Station (Gamma)";
		$this->fighters = array("heavy"=>36); 
		
		$this->occurence = "common";
		$this->variantOf = 'Orion Battle Station';
 		$this->unofficial = true;
        $this->isd = 2230;

		$this->shipSizeClass = 3;
        $this->Enormous = true;
		$this->iniativebonus = -200;
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->imagePath = "img/ships/orion.png";
		$this->canvasSize = 280;

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Cargo Bay",
				14 => "Scanner",
				16 => "Class-L Missile Rack",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
		);

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 27, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Hangar(5, 6));
		$this->addPrimarySystem(new CargoBay(5, 48));
		$this->addPrimarySystem(new LMissileRack(5, 6, 0, 0, 360, true));
		$this->addPrimarySystem(new LMissileRack(5, 6, 0, 0, 360, true));

		$this->addPrimarySystem(new Structure( 5, 150));

		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new Railgun(4, 9, 6, $min, $max),
				new LMissileRack(4, 6, 0, $min, $max, true),
				new InterceptorMKI(4, 4, 1, $min, $max),
				new StdParticleBeam(4, 4, 1, $min, $max),
				new Hangar(4, 6, 6),
				new SubReactorUniversal(4, 18, 0, 0),
				new CargoBay (4, 12),
				new Structure( 4, 100)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "Class-L Missile Rack",
				2 => "Railgun",
				3 => "Standard Particle Beam",
				4 => "Interceptor I",
				5 => "Hangar",
				6 => "Cargo Bay",
				7 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}