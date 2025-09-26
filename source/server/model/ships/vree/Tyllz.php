<?php

class Tyllz extends StarBaseSixSections
{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3500;
		$this->base = true;
		$this->faction = "Vree Conglomerate";
		$this->phpclass = "Tyllz";
		$this->shipClass = "Tyllz Sector Trading Post";
		$this->imagePath = "img/ships/VreeTyllz.png";
		$this->canvasSize = 310;
		$this->fighters = array("normal" => 24);
		$this->isd = 2252;

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
                    11 => "Antimatter Shredder",
                    14 => "Scanner",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "TAG:C&C",
           		 ),
		);


		$this->addPrimarySystem(new Reactor(5, 25, 0, 0));
		$this->addPrimarySystem(new Hangar(5, 30));
//		$this->addPrimarySystem(new ProtectedCnC(6, 32, 0, 0));

		$cnc = new CnC(5, 16, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
		$this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 16, 0, 0);//all-around by default
		$this->addPrimarySystem($cnc);

		$this->addPrimarySystem(new Scanner(5, 18, 5, 9));
		$this->addPrimarySystem(new Scanner(5, 18, 5, 9));
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));	         				
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));	         				
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));	         				
		$this->addPrimarySystem(new AntimatterShredder(4, 0, 0, 0, 360));   
		
        $this->addPrimarySystem(new Structure( 5, 90));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);


			$struct = new Structure( 4, 100, true);
			$struct->addTag("Outer Structure");
			$struct->startArc = $min;
			$struct->endArc = $max;

			$cargoBay = new CargoBay(4, 48);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(4, 23, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;

			$systems = array(
				$cargoBay,
				new AntimatterTorpedo(4, 0, 0, $min, $max),
				new AntimatterTorpedo(4, 0, 0, $min, $max),
				new AntiprotonGun(4, 0, 0, $min, $max),
				new AntiprotonGun(4, 0, 0, $min, $max),
				$subReactor,
				$struct //new Structure( 4, 100)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(                 
                    7 => "TAG:Weapon",                    
                    9 => "TAG:Cargo Bay",
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