<?php
class Kraken extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 5000;
		$this->faction = "Centauri Republic";
		$this->phpclass = "Kraken";
		$this->shipClass = "Kraken Star Fortress";
		$this->fighters = array("heavy"=>36); 
		$this->isd = 2202;

		$this->shipSizeClass = 3; //Enormous is not implemented
        $this->Enormous = true;
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
		$this->addPrimarySystem(new ProtectedCnC(8, 60, 0, 0)); //originally 2 systems with sructure 30, armor 7 each
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
			
/*some systems need pre-definition to have arcs set for TAGs!*/
			$struct = new Structure(5, 115);
			$struct->addTag("Outer Structure");
			$struct->startArc = $min;
			$struct->endArc = $max;
			$cargoBay = new CargoBay(5, 30);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(5, 30, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;
		
			$systems = array(
				new MatterCannon(5, 7, 4, $min, $max),
				new BattleLaser(5, 6, 6, $min, $max),
				new MatterCannon(5, 7, 4, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				new TwinArray(5, 6, 2, $min, $max),
				$cargoBay, //new CargoBay(5, 30),
				$subReactor, //new SubReactorUniversal(5, 30, 0, 0),
				$struct //new Structure(5, 115)
			);

			$loc = $this->locations[$i];

			/* replaced with TAG system!
			$this->hitChart[$loc] = array(
				3 => "Twin Array",
				5 => "Matter Cannon",
				6 => "Battle Laser",
				8 => "Cargo Bay",
				9 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);
			*/			
			$this->hitChart[$loc] = array(
				3 => "TAG:Twin Array",
				5 => "TAG:Matter Cannon",
				6 => "TAG:Battle Laser",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Sub Reactor",
				18 => "TAG:Outer Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
