<?php
class Koratyl extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2700;
		$this->faction = "Dilgar";
		$this->phpclass = "Koratyl";
		$this->shipClass = "Koratyl Defense Base";
		$this->fighters = array("heavy"=>36); 
		$this->isd = 2227;

		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 22;
		$this->sideDefense = 22;

		$this->imagePath = "img/ships/koratyl.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				10 => "Heavy Bolter",
				13 => "Scanner",
				16 => "Cargo Bay",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
		);


		$this->addPrimarySystem(new Reactor(5, 25, 0, 0));
		//$this->addPrimarySystem(new CnC(5, 15, 0, 0)); 
		$this->addPrimarySystem(new ProtectedCnC(6, 30, 0, 0)); //instead of 2 5x15 C&C, make it 1 6x30
		$this->addPrimarySystem(new Scanner(5, 20, 5, 8));
		$this->addPrimarySystem(new Scanner(5, 20, 5, 8));
		$this->addPrimarySystem(new Hangar(5, 4));
		$this->addPrimarySystem(new CargoBay(5, 25));
		$this->addPrimarySystem(new HeavyBolter(5, 10, 6, 0, 360));
		$this->addPrimarySystem(new HeavyBolter(5, 10, 6, 0, 360));

		$this->addPrimarySystem(new Structure(5, 100));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new HeavyBolter(4, 10, 6, $min, $max),
				new HeavyBolter(4, 10, 6, $min, $max),
				new MediumLaser(4, 6, 5, $min, $max),
				new MediumLaser(4, 6, 5, $min, $max),
				new ScatterPulsar(4, 4, 2, $min, $max),
				new ScatterPulsar(4, 4, 2, $min, $max),
				new SMissileRack(4, 6, 0, $min, $max, true),
				new SMissileRack(4, 6, 0, $min, $max, true),
				new SubReactorUniversal(4, 25, 0, 0),
				new Hangar(4, 6, 6),
				new CargoBay(4, 25),
				new Structure(4, 90)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				2 => "Heavy Bolter",
				4 => "Medium Laser",
				6 => "Class-S Missile Rack",
				8 => "Scatter Pulsar",
				10 => "Cargo Bay",
				11 => "Hangar",
				13 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
