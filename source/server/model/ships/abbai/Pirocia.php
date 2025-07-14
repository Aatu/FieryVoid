<?php

class pirocia extends StarBaseSixSections
{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 4500;
		$this->base = true;
		$this->faction = "Abbai Matriarchate";
		$this->phpclass = "pirocia";
		$this->shipClass = "Pirocia Starbase";
		$this->imagePath = "img/ships/AbbaiPirocia.png";
		$this->canvasSize = 330;
		$this->fighters = array("normal" => 36);
		$this->isd = 2230;

     
		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 21;
		$this->sideDefense = 21;

		$this->locations = array(41, 42, 2, 32, 31, 1);

		$this->hitChart = array(			
            0=> array(
                    8 => "Structure",
                    11 => "Shield Generator",
                    14 => "Scanner",
                    16 => "Hangar",
                    18 => "Reactor",
					20 => "TAG:C&C", //TAG to allow secondary C&C!
           		 ),
		);

		$cnc = new CnC(5, 24, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 24, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);

		$this->addPrimarySystem(new Reactor(5, 25, 0, 0));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new Hangar(5, 14));				
		$this->addPrimarySystem(new Scanner(5, 20, 8, 10));
		$this->addPrimarySystem(new Scanner(5, 20, 8, 10));
		$this->addPrimarySystem(new ShieldGenerator(6, 32, 10, 8));//Combine into single Shield Generator (original: 2x armor 5, structure 16, rating 4)
//		$this->addPrimarySystem(new ShieldGenerator(5, 16, 5, 4)); 		        					
        $this->addPrimarySystem(new Structure( 5, 125));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$struct = Structure::createAsOuter(4, 100,$min,$max);
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;

			$systems = array(
				new CombatLaser(4, 0, 0, $min, $max),
				new CombatLaser(4, 0, 0, $min, $max),
				new QuadArray(4, 0, 0, $min, $max),
				new QuadArray(4, 0, 0, $min, $max),
				new AbbaiShieldProjector(4, 0, 0, $min, $max, 3),					
				new Particleimpeder(4, 0, 0, $min, $max),
				new Particleimpeder(4, 0, 0, $min, $max),								
				new GraviticShield(0, 6, 0, 4, $min, $max),
				$cargoBay, //new CargoBay(4, 25),
				$subReactor, //new SubReactorUniversal(4, 25, 0, 0),
				$struct					
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
                    1 => "TAG:Gravitic Shield",
                    3 => "TAG:Combat Laser",                    
                    5 => "TAG:Quad Array",
                    7 => "TAG:Particle Impeder", 
                    8 => "TAG:Shield Projector",                                                              
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
