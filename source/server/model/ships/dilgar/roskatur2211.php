<?php
class Roskatur2211 extends SmallStarBaseThreeSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 840;
		$this->faction = "Dilgar Imperium";
		$this->phpclass = "Roskatur2211";
		$this->shipClass = "Roskatur Garrison Base (2211)";
		$this->fighters = array("LCVs" => 3, "medium"=>18); 

        $this->variantOf = "Roskatur Garrison Base";
        $this->occurence = "common";        

		$this->isd = 2224;
		$this->shipSizeClass = 3;
		$this->iniativebonus = -200;
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 17;
		$this->sideDefense = 17;
		$this->imagePath = "img/ships/roskatur.png";
		$this->canvasSize = 200;

 		$this->unofficial = 'S'; //Showdowns 10		

		$this->locations = array(1, 4, 3);
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Scanner",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
		);

		$this->addPrimarySystem(new Reactor(5, 30, 0, 0));
		$this->addPrimarySystem(new CnC(5, 15, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 6, 8));
		$this->addPrimarySystem(new Hangar(3, 8, 6, 0));
		$this->addPrimarySystem(new Hangar(3, 8, 6, 2));
		$this->addPrimarySystem(new Hangar(3, 8, 6, 5));							
		
		$this->addPrimarySystem(new Structure(5, 49));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = (300 + ($i*120)) % 360;
			$max = (60 + ($i*120)) % 360;


			/*some systems need pre-definition to have arcs set for TAGs!*/
			$struct = Structure::createAsOuter(3, 130,$min,$max);
			$cargoBay = new CargoBay(3, 20);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$LCVRail = new DockingCollar(3, 14, 1);
			$LCVRail->displayName = "LCV Rail"; 
			$LCVRail->startArc = $min;
			$LCVRail->endArc = $max;

			$systems = array(
				new MediumLaser(3, 6, 5, $min, $max),
				new MediumLaser(3, 6, 5, $min, $max),
				new LightPlasma(1, 4, 2, $min, $max),
				new LightPlasma(1, 4, 2, $min, $max),										
				$cargoBay,
				$LCVRail,
				$struct
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				3 => "TAG:Medium Laser",
				6 => "TAG:Light Plasma Cannon",
				9 => "TAG:LCV Rail",				
				12 => "TAG:Cargo Bay",				
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