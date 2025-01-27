<?php
class wlcChlonasVaLothar extends SmallStarBaseThreeSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3250;
		$this->faction = "Ch'Lonas Cooperative";
		$this->phpclass = "wlcChlonasVaLothar";
		$this->shipClass = "Va'Lothar Battlestation";
		$this->fighters = array("heavy"=>36); 

		$this->shipSizeClass = 3;
        $this->Enormous = true;
		$this->iniativebonus = -200; //starbases move first
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 20;
		$this->sideDefense = 20;
		$this->imagePath = "img/ships/ChlonasVaLothar.png";
		$this->canvasSize = 300;
		$this->isd = 2243;

		$this->locations = array(1, 4, 3);
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				13 => "Light Gatling Mattergun",
				16 => "Scanner",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
		);

		$this->addPrimarySystem(new Reactor(5, 26, 0, 0));
		$this->addPrimarySystem(new CnC(5, 18, 0, 0)); 
		$this->addPrimarySystem(new SecondaryCnC(5, 18, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 6, 7));
		$this->addPrimarySystem(new Scanner(5, 16, 6, 7));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));


		$this->addPrimarySystem(new Structure(5, 120));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 270 + ($i*120);
			$max = 90 + ($i*120);

			/*some systems need pre-definition to have arcs set for TAGs!*/
			$struct = Structure::createAsOuter(4, 154,$min,$max);
			$cargoBay = new CargoBay(4, 24);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$cargoBay2 = new CargoBay(4, 24);
			$cargoBay2->startArc = $min;
			$cargoBay2->endArc = $max;
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;
			$hangar = new Hangar(4, 14, 12, 0);
			$hangar->startArc = $min;
			$hangar->endArc = $max;
			
			$systems = array(
				new CustomPulsarLaser(4, 0, 0, $min, $max),
				new CustomPulsarLaser(4, 0, 0, $min, $max),
				new AssaultLaser(4, 6, 4, $min, $max),
				new AssaultLaser(4, 6, 4, $min, $max),
				new MatterCannon(4, 7, 4, $min, $max),
				new MatterCannon(4, 7, 4, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				$hangar, //new Hangar(4, 14),
				$cargoBay, //new CargoBay(4, 24),
				$cargoBay2, //new CargoBay(4, 24),
				$subReactor, //new SubReactorUniversal(4, 20, 0, 0),
				$struct //new Structure( 4, 154)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				3 => "TAG:Pulsar Laser",
				5 => "TAG:Assault Laser",
				7 => "TAG:Matter Cannon",
				9 => "TAG:Light Particle Beam",
				10 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				12 => "TAG:Sub Reactor",
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
