<?php
class Carrana extends SmallStarBaseThreeSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 750;
		$this->faction = "Descari";
		$this->phpclass = "Carrana";
		$this->shipClass = "Carrana Base";
		$this->fighters = array("heavy"=>18); 

		$this->isd = 2246;
		$this->shipSizeClass = 3;
		$this->iniativebonus = -200;
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 16;
		$this->sideDefense = 16;
		$this->imagePath = "img/ships/DescariCarrana.png";
		$this->canvasSize = 200;

		$this->locations = array(1, 4, 3);
		
		$this->hitChart = array(			
			0=> array(
				12 => "Structure",
				14 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
		);

		$this->addPrimarySystem(new Reactor(5, 30, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 16, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 2));

		$this->addPrimarySystem(new Structure(5, 52));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 300 + ($i*120);
			$max = 60 + ($i*120);

			$systems = array(
				new HeavyPlasmaBolter(4, 0, 0, $min, $max),
				new SMissileRack(4, 6, 0, $min, $max),
				new SMissileRack(4, 6, 0, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new Hangar(4, 6),
				new Structure( 4, 56)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				4 => "Heavy Plasma Bolter",
				7 => "Light Particle Beam",
				10 => "Class-S Missile Rack",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}

?>