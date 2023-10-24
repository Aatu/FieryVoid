<?php

class RingBase extends StarBaseSixSections
{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3200;
		$this->base = true;
		$this->faction = "Ipsha";
		$this->phpclass = "RingBase";
		$this->shipClass = "Ring Base";
		$this->imagePath = "img/ships/IpshaRingBase.png";
		$this->canvasSize = 280;
		$this->fighters = array("normal" => 24);
		$this->isd = 2226;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
            0=> array(
                    9 => "Structure",
                    11 => "Cargo Bay",
                    13 => "Scanner",
                    15 => "Spark Field",
                    17 => "Reactor",
                    18 => "Hangar",
                    20 => "C&C",
           		 ),
		);


		$this->addPrimarySystem(new Reactor(6, 49, 0, 90));
		$this->addPrimarySystem(new Hangar(6, 16));
		$this->addPrimarySystem(new Hangar(6, 16));		
		$this->addPrimarySystem(new ProtectedCnC(6, 40, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 16, 4, 7));
		$this->addPrimarySystem(new Scanner(6, 16, 4, 7));
		$this->addPrimarySystem(new CargoBay(6, 36));	
		$this->addPrimarySystem(new SparkField(6, 8, 2, 0, 360));	         				
		$this->addPrimarySystem(new SparkField(6, 8, 2, 0, 360));
 		
        $this->addPrimarySystem(new Structure( 6, 166));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new ResonanceGenerator(4, 8, 6, $min, $max),
				new EmPulsar(4, 6, 3, $min, $max),
				new EmPulsar(4, 6, 3, $min, $max),
				new SurgeCannon(4, 6, 3, $min, $max),
				new SurgeCannon(4, 6, 3, $min, $max),
				new SurgeCannon(4, 6, 3, $min, $max),
				new SurgeCannon(4, 6, 3, $min, $max),
				new SurgeCannon(4, 6, 3, $min, $max),												
				new Structure( 4, 112)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
                    1 => "Resonance Generator",
                    3 => "EM Pulsar",                   
                    7 => "Surge Cannon",                    
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