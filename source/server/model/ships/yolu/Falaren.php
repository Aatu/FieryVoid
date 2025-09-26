<?php

class Falaren extends StarBaseSixSections
{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 7000;
		$this->base = true;
		$this->faction = "Yolu Confederation";
		$this->phpclass = "Falaren";
		$this->shipClass = "Falaren Starbase";
		$this->imagePath = "img/ships/YoluFalaren.png";
		$this->canvasSize = 300;
		$this->fighters = array("normal" => 24);
		$this->isd = 2113;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 19;
		$this->sideDefense = 19;

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
            0=> array(
                    8 => "Structure",
                    10 => "Cargo Bay",
                    13 => "Scanner",
                    16 => "Hangar",
                    18 => "Reactor",
                    20 => "TAG:C&C",
           		 ),
		);


		$this->addPrimarySystem(new Reactor(8, 25, 0, 18));
		$this->addPrimarySystem(new Hangar(8, 15));
		$this->addPrimarySystem(new Hangar(8, 15));	
		$this->addPrimarySystem(new CargoBay(8, 25));				
//		$this->addPrimarySystem(new ProtectedCnC(9, 40, 0, 0));

		$cnc = new CnC(8, 20, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
		$this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(8, 20, 0, 0);//all-around by default
		$this->addPrimarySystem($cnc);

		$this->addPrimarySystem(new Scanner(8, 18, 6, 11));
		$this->addPrimarySystem(new Scanner(8, 18, 6, 11));   
		
        $this->addPrimarySystem(new Structure( 8, 120));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$struct = Structure::createAsOuter(7, 120,$min,$max);
			$subReactor = new SubReactorUniversal(7, 25, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;

			$systems = array(
				new MolecularFlayer(7, 8, 4, $min, $max),
				new DestabilizerBeam(7, 10, 8, $min, $max),
				new DestabilizerBeam(7, 10, 8, $min, $max),
				new FusionAgitator(7, 10, 4, $min, $max),
				new FusionAgitator(7, 10, 4, $min, $max),
				new FusionCannon(7, 8, 1, $min, $max),
				new FusionCannon(7, 8, 1, $min, $max),				
				$subReactor,
				$struct
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
                    2 => "TAG:Molecular Flayer",
					5 => "TAG:Fusion Cannon",                    
					6 => "TAG:Fusion Agitator",                
					8 => "TAG:Destabilizer Beam",
                    9 => "TAG:Sub Reactor",
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