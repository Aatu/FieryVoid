<?php
class tfsfreedombase extends SmallStarBaseFourSections{ 
/*TFS Freedom Base: unique construction from Raiders&Privateers*/
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 700;
		$this->faction = "Raiders";
		$this->phpclass = "tfsfreedombase";
		$this->shipClass = "TFS Freedom Base";
		$this->fighters = array("medium"=>24); 
		$this->shipSizeClass = 3; //this is Capital base
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 18;
		$this->sideDefense = 18;
		
		$this->notes = "Used only by the Tirrith Free State";
		$this->isd = 2243;
		        $this->occurence = "unique";

		$this->imagePath = "img/ships/RaiderTFSFreedomBase.png";
		$this->canvasSize = 280; 
		
		$this->locations = array(1, 4, 2, 3);
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				15 => "Cargo Bay",
				17 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
		);
		$this->addPrimarySystem(new Reactor(5, 9, 0, 0));
		$this->addPrimarySystem(new CnC(5, 15, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new CargoBay(5, 36));
		$this->addPrimarySystem(new Structure( 5, 80));
		for ($i = 0; $i < sizeof($this->locations); $i++){
			$min = 270 + ($i*90);
			$max = 90 + ($i*90);

			$struct = Structure::createAsOuter(5, 80,$min,$max);
			$hangar = new Hangar(5, 7, 6);
			$hangar->startArc = $min;
			$hangar->endArc = $max;			
			$cargoBay = new CargoBay(5, 36);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(5, 12, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;

			$systems = array(
				new StdParticleBeam(5, 4, 1, $min, $max),
				new StdParticleBeam(5, 4, 1, $min, $max),
				new ParticleCannon(5, 8, 7, $min, $max),
				new ParticleCannon(5, 8, 7, $min, $max),
				$hangar,
				$subReactor,
				$cargoBay,
				$struct
			);
			$loc = $this->locations[$i];
			$this->hitChart[$loc] = array(
				1 => "TAG:Standard Particle Beam",
				3 => "TAG:Particle Cannon",
				8 => "TAG:Cargo Bay",
				9 => "TAG:Hangar",
				10 => "TAG:Sub Reactor",
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
