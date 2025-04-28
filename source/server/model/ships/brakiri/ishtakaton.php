<?php

class Ishtakaton extends StarBaseSixSections
{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 4500;
		$this->base = true;
		$this->faction = "Brakiri Syndicracy";
		$this->phpclass = "Ishtakaton";
		$this->shipClass = "Ishtakaton Techbase";
		$this->imagePath = "img/ships/BrakiriIshtaka.png";
		$this->canvasSize = 320;
		$this->fighters = array("normal" => 24);
		$this->isd = 2256;
		$this->notes = 'Im-Rehsa Technologies';//Corporation producing the design
		$this->notes .= "<br>Official Ishtaka Techbase with Grav Shifters replaced by Grav Cannons"; 		
	    $this->unofficial = 'S'; //Semi-official - added as reasonably close to official Ishtaka, while Grav Shifters are unavailable in FV
     
		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 22;
		$this->sideDefense = 22;

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
            0=> array(
                    8 => "Structure",
                    10 => "Shield Generator",
                    13 => "Scanner",
                    16 => "Hangar",
                    18 => "Reactor",
                    20 => "TAG:C&C",
           		 ),
		);

		/* let's replace this with appropriate two C&Cs!
		$this->addPrimarySystem(new ProtectedCnC(6, 36, 0, 0));
		*/
		$cnc = new CnC(5, 20, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 20, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);
		$this->addPrimarySystem(new Reactor(5, 25, 0, 12));
		$this->addPrimarySystem(new Hangar(5, 15));
		$this->addPrimarySystem(new Hangar(5, 15));		
		$this->addPrimarySystem(new Scanner(5, 18, 6, 9));
		$this->addPrimarySystem(new Scanner(5, 18, 6, 9));
		$this->addPrimarySystem(new ShieldGenerator(5, 20, 6, 4));        					
        $this->addPrimarySystem(new Structure( 5, 120));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

/*some systems need pre-definition to have arcs set for TAGs!*/
			$struct = Structure::createAsOuter(5, 120,$min,$max);
			$cargoBay = new CargoBay(5, 25);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(5, 25, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;

			$systems = array(
				new GraviticLance(5, 12, 16, $min, $max),
				new GravitonBeam(5, 8, 8, $min, $max),
				new GravitonPulsar(5, 5, 2, $min, $max),
				new GravitonPulsar(5, 5, 2, $min, $max),
				new GraviticCannon(5, 6, 5, $min, $max),				
				new GraviticShield(0, 6, 0, 4, $min, $max),	
				$subReactor, //new SubReactorUniversal(5, 25),
				$cargoBay, //new CargoBay(5, 25),			
				$struct //new Structure( 5, 120)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
                    1 => "TAG:Gravitic Shield",
                    3 => "TAG:Gravitic Lance",                    
                    5 => "TAG:Graviton Beam",
                    7 => "TAG:Graviton Pulsar", 
                    8 => "TAG:Gravitic Cannon",                                                              
                    11 => "TAG:Cargo Bay",
                    12 => "TAG:Sub Reactor",
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