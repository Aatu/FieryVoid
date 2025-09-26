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
		$this->canvasSize = 280;

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


			/*some systems need pre-definition to have arcs set for TAGs!*/
			$struct = Structure::createAsOuter(3, 130,$min,$max);
			$cargoBay = new CargoBay(3, 24);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(3, 20, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;
			$hangar = new Hangar(3, 7, 6);
			$hangar->startArc = $min;
			$hangar->endArc = $max;

			$systems = array(
				new HeavyLaser(3, 8, 6, $min, $max),
				new TwinArray(3, 6, 2, $min, $max),
				new TwinArray(3, 6, 2, $min, $max),
				new TwinArray(3, 6, 2, $min, $max),								
				new LightPulse(3, 4, 2, $min, $max),
				new LightPulse(3, 4, 2, $min, $max),
				/* replaced with arced systems - for TAG
				new SubReactorUniversal(3, 20),
				new CargoBay(3, 24),				
				new Hangar(3, 7),
				new Structure( 3, 130)
				*/
				$subReactor,
				$cargoBay,
				$hangar,
				$struct
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				3 => "TAG:Twin Array",
				5 => "TAG:Light Pulse Cannon",
				7 => "TAG:Heavy Laser",
				9 => "TAG:Cargo Bay",				
				10 => "TAG:Sub Reactor",				
				11 => "TAG:Hangar",
				18 => "Outer Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}

?>