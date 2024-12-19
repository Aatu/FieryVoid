<?php
class Selatra1989 extends StarBaseSixSections{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 2750;
		$this->faction = "Abbai Matriarchate (WotCR)";
		$this->phpclass = "Selatra1989";
		$this->shipClass = "Selatra Shield Base (1989)";
			$this->occurence = 'common'; 
			$this->variantOf = "Selatra Shield Base";
		$this->shipSizeClass = 3; //Enormous is not implemented

        $this->isd = 1989;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 20;
		$this->sideDefense = 20;
		$this->imagePath = "img/ships/AbbaiSelatra.png";
		$this->canvasSize = 200; 
		
		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				12 => "Shield Generator",
				15 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
		);


		/* replaced with proper two C&Cs!
		$this->addPrimarySystem(new ProtectedCnC(6, 40, 0, 0)); //original: 2x20, armor 5
		*/
		$cnc = new CnC(5, 20, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 20, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);
		
		$this->addPrimarySystem(new Reactor(5, 26, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 24, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 24, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 6));
       	$this->addPrimarySystem(new ShieldGenerator(5, 32, 4, 4));
		$this->addPrimarySystem(new Structure(5, 88));

		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

/*some systems need pre-definition to have arcs set for TAGs!*/
			$struct = Structure::createAsOuter(4, 80,$min,$max);
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;
			
			$systems = array(
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new SensorSpear(4, 0, 0, $min, $max),
				new MediumLaser(4, 6, 5, $min, $max),
				new LaserCutter(4, 6, 4, $min, $max),
				new GraviticShield(0, 6, 0, 2, $min, $max),
				$cargoBay, //new CargoBay(4, 25),
				$subReactor, //new SubReactorUniversal(4, 20, 0, 0),
				$struct //new Structure(4, 80)
			);

			$loc = $this->locations[$i];

			/* replaced with TAG system!
			$this->hitChart[$loc] = array(
				1 => "Medium Laser",
				2 => "Laser Cutter",
				4 => "Light Particle Beam",
				5 => "Sensor Spear",
				6 => "Gravitic Shield",	
				8 => "Cargo Bay",
				10 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);
			*/
			$this->hitChart[$loc] = array(
				1 => "TAG:Medium Laser",
				2 => "TAG:Laser Cutter",
				4 => "TAG:Light Particle Beam",
				5 => "TAG:Sensor Spear",
				6 => "TAG:Gravitic Shield",	
				8 => "TAG:Cargo Bay",
				10 => "TAG:Sub Reactor",
				18 => "TAG:Outer Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
?>