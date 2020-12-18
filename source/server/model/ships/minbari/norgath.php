<?php
class Norgath extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 9000;
		$this->faction = 'Minbari';
		$this->phpclass = "norgath";
		$this->shipClass = "Norgath Starbase";
		$this->fighters = array("heavy"=>36); 

		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->isd = 2082;

		$this->forwardDefense = 21;
		$this->sideDefense = 21;

		$this->imagePath = "img/ships/orion.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				11 => "Jammer",
				12 => "Tractor Beam",
				15 => "Scanner",
				16 => "Hangar",	
				18 => "Reactor",
				20 => "C&C",
			),
		);


		$this->addPrimarySystem(new Reactor(8, 25, 0, 0));
		$this->addPrimarySystem(new ProtectedCnC(9, 72, 0, 0)); //2x 8/36
		//$this->addPrimarySystem(new CnC(8, 36, 0, 0)); 
		$this->addPrimarySystem(new Scanner(8, 36, 4, 12));
		$this->addPrimarySystem(new Scanner(8, 36, 4, 12));
		$this->addPrimarySystem(new Hangar(8, 6));
		$this->addPrimarySystem(new Jammer(8, 8, 5));
		$this->addPrimarySystem(new Jammer(8, 8, 5));
		$this->addPrimarySystem(new TractorBeam(8, 4, 0, 0));
		
		$this->addPrimarySystem(new Structure(8, 200));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new CargoBay(6, 25),
				new ElectroPulseGun(6, 6, 3, $min, $max),
				new NeutronLaser(6, 10, 6, $min, $max),
				new NeutronLaser(6, 10, 6, $min, $max),
				new FusionCannon(6, 8, 1, $min, $max),
				new FusionCannon(6, 8, 1, $min, $max),
				new FusionCannon(6, 8, 1, $min, $max),
				new Hangar(6, 6, 6),
				new SubReactorUniversal(6, 30, 0, 0),
				new Structure(6, 180)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "Electro-Pulse Gun",
				3 => "Neutron Laser",
				5 => "Fusion Cannon",
				6 => "Hangar",
				8 => "Cargo",
				9 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
