<?php
class CircasianTaryn extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2500;
		$this->faction = "Escalation Wars Circasian Empire";
		$this->phpclass = "CircasianTaryn";
		$this->shipClass = "Taryn Starbase";
		$this->fighters = array("normal"=>36); 
		$this->isd = 1967;
		$this->unofficial = true;
		$this->Enormous = true;

		$this->shipSizeClass = 3; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 19;
		$this->sideDefense = 19;

		$this->imagePath = "img/ships/EscalationWars/CircasianTaryn.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				12 => "Structure",
				14 => "Cargo Bay",
				17 => "Scanner",
				19 => "Reactor",
				20 => "C&C",
			),
		);

        $this->addPrimarySystem(new CnC(4, 25, 0, 0));
		$this->addPrimarySystem(new Reactor(4, 22, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new CargoBay(4, 20));

		$this->addPrimarySystem(new Structure(4, 96));

		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new ParticleCannon(3, 8, 7, $min, $max),
				new ParticleCannon(3, 8, 7, $min, $max),
				new EWRangedDualHeavyRocketLauncher(3, 8, 3, $min, $max),
				new EWRangedDualHeavyRocketLauncher(3, 8, 3, $min, $max),
				new LightParticleBeamShip(3, 2, 1, $min, $max),
				new LightParticleBeamShip(3, 2, 1, $min, $max),
				new SubReactorUniversal(3, 20, 0, 0),
				new CargoBay(3, 25),
				new Hangar(3, 7, 6),
				new Structure(3, 80)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				2 => "Light Particle Beam",
				4 => "Particle Cannon",
				6 => "Ranged Dual Heavy Rocket Launcher",
				9 => "Cargo Bay",
				10 => "Hangar",
				11 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
