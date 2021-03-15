<?php
class OrionAlpha extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1250;
		$this->faction = 'EA';
		$this->phpclass = "OrionAlpha";
		$this->shipClass = "Orion Starbase (Alpha)";
		$this->fighters = array("heavy"=>36); 
		
		$this->occurence = "common";
		$this->variantOf = 'Orion Battle Station';
 		$this->unofficial = true;
        $this->isd = 2168;

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
				16 => "Heavy Plasma Cannon",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
		);


		$this->addPrimarySystem(new Reactor(4, 20, 0, 0));
		$this->addPrimarySystem(new CnC(4, 27, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 14, 4, 5));
		$this->addPrimarySystem(new Scanner(4, 14, 4, 5));
		$this->addPrimarySystem(new Hangar(4, 6));
		$this->addPrimarySystem(new CargoBay(4, 48));
		$this->addPrimarySystem(new HeavyPlasma(4, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(4, 8, 5, 0, 360));


		$this->addPrimarySystem(new Structure( 4, 150));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new SoMissileRack(3, 6, 0, $min, $max, true),
				new InterceptorPrototype(3, 4, 1, $min, $max),
				new LightParticleBeamShip(3, 2, 1, $min, $max),
				new Hangar(3, 6, 6),
				new CargoBay(3, 24),
				new SubReactorUniversal(3, 15, 0, 0),
				new Structure( 3, 100)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "Class-SO Missile Rack",
				2 => "Light Particle Beam",
				3 => "Interceptor Prototype",
				4 => "Hangar",
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