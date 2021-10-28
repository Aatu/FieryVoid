<?php
class ChoukaCitadel extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 4500;
		$this->faction = "ZEscalation Chouka Theocracy";
		$this->phpclass = "ChoukaCitadel";
		$this->shipClass = "Citadel Star Fortress";
		$this->fighters = array("heavy"=>36); 
		$this->isd = 1961;
		$this->unofficial = true;

		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 22;
		$this->sideDefense = 22;

		$this->imagePath = "img/ships/EscalationWars/ChoukaHellfireOSAT.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Class-S Missile Rack",
				14 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
		);

		$this->addPrimarySystem(new Reactor(5, 30, 0, 0));
		$this->addPrimarySystem(new ProtectedCnC(6, 50, 0, 0)); //instead of 2 5x15 C&C, make it 1 6x30
		$this->addPrimarySystem(new Scanner(5, 20, 7, 7));
		$this->addPrimarySystem(new Scanner(5, 20, 7, 7));
		$this->addPrimarySystem(new Hangar(5, 21));
		$this->addPrimarySystem(new Hangar(5, 21));
		$this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 360, true));
		$this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 360, true));
		$this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 360, true));
		$this->addPrimarySystem(new SMissileRack(3, 6, 0, 0, 360, true));

		$this->addPrimarySystem(new Structure(5, 132));

		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new HeavyPlasma(4, 8, 5, $min, $max),
				new HeavyPlasma(4, 8, 5, $min, $max),
				new EWHeavyPointPlasmaGun(4, 7, 2, $min, $max),
				new EWHeavyPointPlasmaGun(4, 7, 2, $min, $max),
				new EWTwinLaserCannon(4, 8, 5, $min, $max),
				new EWTwinLaserCannon(4, 8, 5, $min, $max),
				new SMissileRack(4, 6, 0, $min, $max, true),
				new SMissileRack(4, 6, 0, $min, $max, true),
				new SubReactorUniversal(4, 25, 0, 0),
				new CargoBay(4, 30),
				new Structure(4, 110)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				2 => "Heavy Plasma Cannon",
				4 => "Twin Laser Cannon",
				7 => "Class-S Missile Rack",
				8 => "Heavy Point Plasma Gun",
				10 => "Cargo Bay",
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
