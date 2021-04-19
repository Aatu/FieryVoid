<?php
class GromeMahkgar extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2500;
		$this->faction = 'Grome';
		$this->phpclass = "GromeMahkgar";
		$this->shipClass = "Mahkgar Starbase";
		$this->fighters = array("normal"=>36); 
        $this->isd = 2235;

	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Targeting Arrays treated as a 1 point sensors.';

		$this->shipSizeClass = 3;
        $this->Enormous = true;
		$this->iniativebonus = -200;
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 25;
		$this->sideDefense = 25;

		$this->imagePath = "img/ships/GromeMahkgar.png";
		$this->canvasSize = 260;

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				12 => "Structure",
				14 => "Targeting Array",
				16 => "Antiquated Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
		);


		$this->addPrimarySystem(new Reactor(4, 26, 0, 0));
		$this->addPrimarySystem(new CnC(4, 25, 0, 0)); 
		$this->addPrimarySystem(new CnC(4, 25, 0, 0)); 
		$this->addPrimarySystem(new AntiquatedScanner(4, 24, 6, 6));
		$this->addPrimarySystem(new AntiquatedScanner(4, 24, 6, 6));
		$targetingArray = new AntiquatedScanner(4, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
		$targetingArray = new AntiquatedScanner(4, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);
		$targetingArray = new AntiquatedScanner(4, 6, 2, 1);
			$targetingArray->displayName = 'Targeting Array';
			$targetingArray->iconPath = "TargetingArray.png";
			$this->addPrimarySystem($targetingArray);

		$this->addPrimarySystem(new Structure( 4, 240));

		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new HeavyRailGun(4, 12, 9, $min, $max),
				new Railgun(4, 9, 6, $min, $max),
				new Railgun(4, 9, 6, $min, $max),
				new LightRailGun(4, 6, 3, $min, $max),
				new LightRailGun(4, 6, 3, $min, $max),
				new FlakCannon(4, 4, 2, $min, $max),
				new FlakCannon(4, 4, 2, $min, $max),
				new Hangar(4, 7, 6),
				new CargoBay(4, 30),
				new SubReactorUniversal(4, 30, 0, 0),
				new ConnectionStrut(4),
				new Structure( 4, 180)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				2 => "Flak Cannon",
				4 => "Light Railgun",
				6 => "Railgun",
				7 => "Heavy Railgun",
				9 => "Cargo Bay",
				10 => "Hangar",
				11 => "Sub Reactor",
				13 => "Connection Strut",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}