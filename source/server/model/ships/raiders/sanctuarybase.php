<?php
class SanctuaryBase extends StarBaseSixSections{
/*Raider Sanctuary Base: Ships of the Fleet*/
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);
		$this->pointCost = 600;
		$this->faction = "Raiders";
		$this->phpclass = "sanctuarybase";
		$this->shipClass = "Sanctuary Base";
		$this->fighters = array("medium"=>12, "heavy"=>36); 
		$this->shipSizeClass = 3; //Enormous units not implemented
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";
        
		$this->imagePath = "img/ships/orion.png";
		$this->canvasSize = 280; 

		$this->locations = array(41, 42, 2, 32, 31, 1);

		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Cargo Bay",
				14 => "Scanner",
				16 => "Heavy Plasma Cannon",
				17 => "Reactor",
				18 => "Hangar",
				20 => "C&C",
			),
		);

		$this->addPrimarySystem(new Reactor(5, 15, 0, 0));
		$this->addPrimarySystem(new CnC(5, 21, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Hangar(5, 12));
		$this->addPrimarySystem(new CargoBay(5, 48));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));
		$this->addPrimarySystem(new Structure( 5, 90));
		for ($i = 0; $i < sizeof($this->locations); $i++){
			$min = 0 + ($i*60);
			$max = 120 + ($i*60);
			$systems = array(
				new StdParticleBeam(3, 4, 1, $min, $max),
				new StdParticleBeam(3, 4, 1, $min, $max),
				new Hangar(3, 1),
				new SubReactorUniversal(3, 8, 0, 0),
				new CargoBay(3, 36),
				new Structure(3, 76)
			);
			$loc = $this->locations[$i];
			$this->hitChart[$loc] = array(
				3 => "Standard Particle Beam",
				7 => "Cargo",
				8 => "Hangar",
				9 => "Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);
			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
