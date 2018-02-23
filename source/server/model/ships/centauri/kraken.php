<?php
class Kraken extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 5000;
		$this->faction = "Centauri";
		$this->phpclass = "Kraken";
		$this->shipClass = "Kraken Star Fortress";
		$this->fighters = array("heavy"=>36); 

		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 24;
		$this->sideDefense = 24;

		$this->imagePath = "img/ships/kraken.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Battle Laser",
				14 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			)
		);

		$this->addPrimarySystem(new Reactor(7, 35, 0, 0));
		$this->addPrimarySystem(new Hangar(7, 14, 12));
		$this->addPrimarySystem(new Hangar(7, 14, 12));
		$this->addPrimarySystem(new Hangar(7, 14, 12));
		$this->addPrimarySystem(new CnC(8, 60, 0, 0)); //2x 7/30 C&C combined into one much more powerful - but still making it much easier to critically damage
		//$this->addPrimarySystem(new CnC(7, 30, 0, 0)); 
		$this->addPrimarySystem(new Scanner(7, 32, 5, 10));
		$this->addPrimarySystem(new Scanner(7, 32, 5, 10));
		$this->addPrimarySystem(new BattleLaser(7, 6, 6, 0, 360));
		$this->addPrimarySystem(new BattleLaser(7, 6, 6, 0, 360));
		$this->addPrimarySystem(new BattleLaser(7, 6, 6, 0, 360));
		$this->addPrimarySystem(new BattleLaser(7, 6, 6, 0, 360));
		$this->addPrimarySystem(new BattleLaser(7, 6, 6, 0, 360));
		$this->addPrimarySystem(new BattleLaser(7, 6, 6, 0, 360));

		$this->addPrimarySystem(new Structure( 7, 145));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new MatterCannon(5, 7, 4, $min, $max),
				new BattleLaser(5, 6, 6, $min, $max),
				new MatterCannon(5, 7, 4, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				new CargoBay(5, 30),
				new SubReactor(5, 30, 0, 0),
				new Structure(5, 115)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				3 => "Twin Array",
				5 => "Matter Cannon",
				6 => "Battle Laser",
				8 => "Cargo Bay",
				9 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
