<?php
class JaDul extends SmallStarBaseThreeSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1500;
		$this->faction = "Narn Regime";
		$this->phpclass = "JaDul";
		$this->shipClass = "Ja'Dul Civilian Starbase";
		$this->fighters = array("medium"=>18); 

		$this->isd = 2242;
		$this->shipSizeClass = 3;
        $this->Enormous = true;		
		$this->iniativebonus = -200;
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 20;
		$this->sideDefense = 20;
		$this->imagePath = "img/ships/JaDul.png";
		$this->canvasSize = 200;

		$this->locations = array(1, 4, 3);
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Cargo Bay",
				14 => "Twin Array",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
		);

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 21, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 20, 3, 8));
		$this->addPrimarySystem(new CargoBay(5, 24));
		$this->addPrimarySystem(new TwinArray(5, 6, 2, 0, 360));
		$this->addPrimarySystem(new TwinArray(5, 6, 2, 0, 360));		
		$this->addPrimarySystem(new TwinArray(5, 6, 2, 0, 360));	
		
		$this->addPrimarySystem(new Structure(5, 130));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 300 + ($i*120);
			$max = 60 + ($i*120);

			$systems = array(
				new HeavyLaser(3, 8, 6, $min, $max),
				new TwinArray(3, 6, 2, $min, $max),
				new TwinArray(3, 6, 2, $min, $max),
				new TwinArray(3, 6, 2, $min, $max),								
				new LightPulse(3, 4, 2, $min, $max),
				new LightPulse(3, 4, 2, $min, $max),
				new SubReactorUniversal(3, 20),
				new CargoBay(3, 24),				
				new Hangar(3, 7),
				new Structure( 3, 130)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				3 => "Twin Array",
				5 => "Light Pulse Cannon",
				7 => "Heavy Laser",
				9 => "Cargo Bay",				
				10 => "Sub Reactor",				
				11 => "Hangar",
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