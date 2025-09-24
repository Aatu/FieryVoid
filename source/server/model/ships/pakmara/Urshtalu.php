<?php

class Urshtalu extends StarBaseSixSections
{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3500;
		$this->base = true;
		$this->faction = "Pak'ma'ra Confederacy";
		$this->phpclass = "Urshtalu";
		$this->shipClass = "Urshtalu Starbase";
		$this->imagePath = "img/ships/PakmaraUrshtalu.png";
		$this->canvasSize = 310;
		$this->fighters = array("normal" => 36);
		$this->isd = 2221;

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
                    9 => "Structure",
                    10 => "Medium Plasma Cannon",
                    13 => "Scanner",
                    16 => "Hangar",
                    18 => "Reactor",
                    20 => "TAG:C&C",
           		 ),
		);


		
		/* let's replace this with appropriate set of THREE C&Cs!
		$this->addPrimarySystem(new ProtectedCnC(8, 48, 0, 0)); //It actually has 3, so I gave +2 armour instead of the usual +1 when two CnCs are combined.
		*/		
		$cnc = new CnC(6, 16, 0, 0); //regular, not Pak C&C - no point with a starbase...
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$this->addPrimarySystem(new SecondaryCnC(6, 16, 0, 0));//all-around by default
		$this->addPrimarySystem(new SecondaryCnC(6, 16, 0, 0));//all-around by default	
        
		$this->addPrimarySystem(new Reactor(6, 25, 0, 0));
		$this->addPrimarySystem(new Hangar(6, 6));
		$this->addPrimarySystem(new Scanner(6, 18, 6, 8));
		$this->addPrimarySystem(new Scanner(6, 18, 6, 8));
		$this->addPrimarySystem(new MediumPlasma(6, 5, 3, 0, 360));	         				
		$this->addPrimarySystem(new MediumPlasma(6, 5, 3, 0, 360));	 
		$this->addPrimarySystem(new MediumPlasma(6, 5, 3, 0, 360));	          				
		$this->addPrimarySystem(new MediumPlasma(6, 5, 3, 0, 360));	 
		$this->addPrimarySystem(new MediumPlasma(6, 5, 3, 0, 360));	         				
		$this->addPrimarySystem(new MediumPlasma(6, 5, 3, 0, 360));	   
		
        $this->addPrimarySystem(new Structure(6, 100));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);


			/*some systems need pre-definition to have arcs set for TAGs!*/
			$struct = Structure::createAsOuter(5, 100,$min,$max);
			$cargoBay = new CargoBay(5, 35);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(5, 23, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;
			$hangar = new Hangar(5, 6, 0, 0);
			$hangar->startArc = $min;
			$hangar->endArc = $max;
			$battery = new PlasmaBattery(5, 6, 0, 0);
			$battery->startArc = $min;
			$battery->endArc = $max;

			$systems = array(
				new RangedFuser(5, 0, 0, $min, $max),
				new MegaPlasma(5, 0, 0, $min, $max),
				new PlasmaAccelerator(5, 10, 5, $min, $max),
				new PakmaraPlasmaWeb(5, 0, 0, $min, $max),
				new PakmaraPlasmaWeb(5, 0, 0, $min, $max),
				/* replaced with arced systems - for TAG
				new CargoBay(5, 35),
				new PlasmaBattery(5, 6, 0, 6),	
				new Hangar(5, 6),								
				new SubReactorUniversal(5, 23),
				new Structure(5, 100)
				*/
				$battery,
				$cargoBay,
				$subReactor,
				$hangar,
				$struct
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
                    1 => "TAG:Ranged Fuser",
                    2 => "TAG:Mega Plasma Cannon", 
                    3 => "TAG:Plasma Accelerator",  
                    5 => "TAG:Plasma Web",   
                    6 => "TAG:Plasma Battery",                                                       
                    7 => "TAG:Hangar",                    
                    11 => "TAG:Cargo Bay",
                    12 => "TAG:Sub Reactor",
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