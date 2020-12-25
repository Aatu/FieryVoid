<?php
class OrionDelta extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2400;
		$this->faction = 'EA';
		$this->phpclass = "OrionDelta";
		$this->shipClass = "Orion Battle Station (Delta)";
		$this->fighters = array("heavy"=>36); 
		
		$this->occurence = "common";
		$this->variantOf = 'Orion Battle Station';
        $this->isd = 2240;

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
				16 => "Class-B Missile Rack",
				17 => "Reactor",
				18 => "Hangar",
				20 => "C&C",
			),
		);


		$this->addPrimarySystem(new Reactor(6, 20, 0, 0));
		$this->addPrimarySystem(new CnC(6, 27, 0, 0)); 
		$this->addPrimarySystem(new Scanner(6, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(6, 16, 4, 6));
		$this->addPrimarySystem(new Hangar(6, 6));
		$this->addPrimarySystem(new CargoBay(6, 48));
		$this->addPrimarySystem(new BMissileRack(6, 9, 0, 0, 360, true));
		$this->addPrimarySystem(new BMissileRack(6, 9, 0, 0, 360, true));


		$this->addPrimarySystem(new Structure( 7, 150));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new Railgun(4, 9, 6, $min, $max),
				new HeavyPulse(4, 6, 4, $min, $max),
				new BMissileRack(4, 9, 0, $min, $max, true),
				new InterceptorMKI(4, 4, 1, $min, $max),
				new InterceptorMKI(4, 4, 1, $min, $max),
				new Hangar(4, 6, 6),
				new SubReactorUniversal(4, 20, 0, 0),
				new Structure( 4, 100)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "Class-B Missile Rack",
				2 => "Heavy Pulse Cannon",
				3 => "Railgun",
				5 => "Interceptor I",
				6 => "Hangar",
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