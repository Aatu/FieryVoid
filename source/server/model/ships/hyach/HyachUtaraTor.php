<?php
class HyachUtaraTor extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 8000;
		$this->faction = "Hyach Gerontocracy";
		$this->phpclass = "HyachUtaraTor";
		$this->shipClass = "Utara Tor Stellar Fortress";
		$this->fighters = array("heavy"=>36); 

        $this->isd = 2216;

		$this->shipSizeClass = 3; //Enormous is not implemented
        $this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 22;
		$this->sideDefense = 22;

		$this->imagePath = "img/ships/orion.png";
		$this->canvasSize = 280; //Enormous Starbase
		
		$this->computerHyach = true;		

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				11 => "Interdictor",
				14 => "Computer",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
		);

		$this->addPrimarySystem(new Reactor(6, 45, 0, 0));
//        $this->addPrimarySystem(new CnC(6, 72, 0, 0));
			$cnc = new CnC(6, 36, 0, 0);
			$cnc->startArc = 0;
			$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
			$cnc = new SecondaryCnC(6, 36, 0, 0);//all-around by default
			$this->addPrimarySystem($cnc);		
			
		$sensors = new Scanner(6, 36, 6, 12);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
		$sensors = new Scanner(6, 36, 6, 12);
			$sensors->markHyach();
			$this->addPrimarySystem($sensors); 
			
		$this->addPrimarySystem(new Interdictor(6, 4, 1, 0, 360));
		$this->addPrimarySystem(new Interdictor(6, 4, 1, 0, 360));
		$this->addPrimarySystem(new HyachComputer(6, 28, 0, 4));//$armour, $maxhealth, $powerReq, $output			

		$this->addPrimarySystem(new Structure( 6, 186));

		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new SpinalLaser(5, 12, 12, ($min+30), ($max-30)),
				new BlastLaser(5, 10, 5, $min, $max),
				new BlastLaser(5, 10, 5, $min, $max),
				new Maser(5, 6, 3, $min, $max),
				new Maser(5, 6, 3, $min, $max),
				new MediumLaser(5, 6, 5, $min, $max),
				new MediumLaser(5, 6, 5, $min, $max),
				new MediumLaser(5, 6, 5, $min, $max),
				new MediumLaser(5, 6, 5, $min, $max),
				new Interdictor(5, 4, 1, $min, $max),
				new Interdictor(5, 4, 1, $min, $max),
				new Hangar(5, 8, 6),
				new CargoBay(5, 36),
				new SubReactorUniversal(5, 35, 0, 0),
				new Structure( 5, 150)
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "Spinal Laser",
				2 => "Blast Laser",
				4 => "Medium Laser",
				5 => "Maser",
				6 => "Interdictor",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
