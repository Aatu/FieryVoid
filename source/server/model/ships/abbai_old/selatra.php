<?php
class Selatra extends StarBaseSixSections{
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 3000;
	$this->faction = "Abbai (WotCR)";
		$this->phpclass = "Selatra";
		$this->shipClass = "Selatra Shield Base";
		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 20;
		$this->sideDefense = 20;
		$this->imagePath = "img/ships/AbbaiSelatra.png";
		$this->canvasSize = 250; //Enormous Starbase
		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				12 => "Shield Generator",
				15 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
		);
		$this->addPrimarySystem(new Reactor(5, 26, 0, 0));
		$this->addPrimarySystem(new CnC(5, 40, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 24, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 24, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 6));
        	$this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 2));
        	$this->addPrimarySystem(new ShieldGenerator(5, 16, 4, 2));
		$this->addPrimarySystem(new Structure(5, 88));
		for ($i = 0; $i < sizeof($this->locations); $i++){
			$min = 0 + ($i*60);
			$max = 120 + ($i*60);
			$systems = array(
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new SensorSpear(4, 0, 0, $min, $max),
				new AssaultLaser(4, 6, 4, $min, $max),
				new AssaultLaser(4, 6, 4, $min, $max),
				new GraviticShield(4, 6, 0, 2, $min, $max),
				new CargoBay(4, 25),
				new SubReactor(4, 20, 0, 0),
				new Structure(4, 80)
			);
			$loc = $this->locations[$i];
			$this->hitChart[$loc] = array(
				2 => "Assault Laser",
				4 => "Light Particle Beam",
				5 => "Sensor Spear",
				6 => "Gravitic Shield",	
				8 => "Cargo Bay",
				10 => "Reactor",
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
