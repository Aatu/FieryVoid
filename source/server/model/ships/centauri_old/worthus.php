<?php
class worthus extends SmallStarBaseFourSections{
	/*two-section weapons are simply moved to PRIMARY. Hit chart modified to make PRIMARY weapons hittable from outer chart. */
	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2000;
		$this->base = true;
		$this->smallBase = true;
		$this->faction = "Centauri (WotCR)";
		$this->phpclass = "worthus";
		$this->shipClass = "Worthus Starbase";
		$this->imagePath = "img/ships/worthus.png";
		$this->canvasSize = 280; 
		$this->fighters = array("heavy"=>24); 
		$this->shipSizeClass = 3; 
		$this->Enormous = true; 
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 21;
		$this->sideDefense = 21;
		$this->isd = 2001;
		
		$this->addFrontSystem(new Structure( 5, 108));
		$this->addAftSystem(new Structure( 5, 108));
		$this->addLeftSystem(new Structure( 5, 108));
		$this->addRightSystem(new Structure( 5, 108));
		$this->addPrimarySystem(new Structure( 6, 140));
		
		$this->addPrimarySystem(new Reactor(6, 48, 0, 0));
		$this->addPrimarySystem(new ProtectedCnC(7, 50, 0, 0)); //2x 6/25 C&C originally
		$this->addPrimarySystem(new Scanner(6, 24, 4, 8));
		$this->addPrimarySystem(new Scanner(6, 24, 4, 8));
		//4 Hangars from between sections, plus small PRIMARY hangar for shuttles - I make them into one PRIMARY hangar with extra armor
		$this->addPrimarySystem(new Hangar(7, 28));
		//2 all-around Imperial Lasers on PRIMARY, plus four 90-degrees from between outer sections
        	$this->addPrimarySystem(new ImperialLaser(5, 8, 5, 0, 90));
        	$this->addPrimarySystem(new ImperialLaser(5, 8, 5, 90, 180));
        	$this->addPrimarySystem(new ImperialLaser(5, 8, 5, 180, 270));
        	$this->addPrimarySystem(new ImperialLaser(5, 8, 5, 270, 360));
        	$this->addPrimarySystem(new ImperialLaser(6, 8, 5, 0, 360));
        	$this->addPrimarySystem(new ImperialLaser(6, 8, 5, 0, 360));
		//2 all-around Tactical Lasers on PRIMARY
        	$this->addPrimarySystem(new TacLaser(6, 5, 4, 0, 360));
        	$this->addPrimarySystem(new TacLaser(6, 5, 4, 0, 360));
		//8 90-degrees Light Particle Beams from between outer sections
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 0, 90));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 0, 90));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 90, 180));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 90, 180));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 180, 270));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 180, 270));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 270, 360));
        	$this->addPrimarySystem(new LightParticleBeamShip(5, 2, 1, 270, 360));

			$this->addFrontSystem(new TacLaser(5, 5, 4, 300, 60));
        	$this->addFrontSystem(new LightParticleBeamShip(5, 2, 1, 300, 60));
        	$this->addFrontSystem(new LightParticleBeamShip(5, 2, 1, 300, 60));
			$this->addFrontSystem(new CargoBay(5, 25));
			$this->addFrontSystem(new SubReactorUniversal(5, 25, 0, 0));

			$this->addAftSystem(new TacLaser(5, 5, 4, 120, 240));
        	$this->addAftSystem(new LightParticleBeamShip(5, 2, 1, 120, 240));
        	$this->addAftSystem(new LightParticleBeamShip(5, 2, 1, 120, 240));
			$this->addAftSystem(new CargoBay(5, 25));
			$this->addAftSystem(new SubReactorUniversal(5, 25, 0, 0));
		
			$this->addLeftSystem(new TacLaser(5, 5, 4, 210, 330));
        	$this->addLeftSystem(new LightParticleBeamShip(5, 2, 1, 210, 330));
        	$this->addLeftSystem(new LightParticleBeamShip(5, 2, 1, 210, 330));
			$this->addLeftSystem(new CargoBay(5, 25));
			$this->addLeftSystem(new SubReactorUniversal(5, 25, 0, 0));
		
			$this->addRightSystem(new TacLaser(5, 5, 4, 30, 150));
        	$this->addRightSystem(new LightParticleBeamShip(5, 2, 1, 30, 150));
        	$this->addRightSystem(new LightParticleBeamShip(5, 2, 1, 30, 150));
			$this->addRightSystem(new CargoBay(5, 25));
			$this->addRightSystem(new SubReactorUniversal(5, 25, 0, 0));
		
		
		
		$this->hitChart = array(			
			0=> array(
				8 => "Structure",
				9 => "Light Particle Beam",
				11 => "Imperial Laser",
				13 => "Tactical Laser",
				16 => "Scanner",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				1 => "0:Imperial Laser",
				3 => "0:Light Particle Beam",
				5 => "Light Particle Beam",
				7 => "Tactical Laser",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "0:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				1 => "0:Imperial Laser",
				3 => "0:Light Particle Beam",
				5 => "Light Particle Beam",
				7 => "Tactical Laser",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "0:Hangar",
				18 => "Structure",
				20 => "Primary",
			),	
			3=> array(
				1 => "0:Imperial Laser",
				3 => "0:Light Particle Beam",
				5 => "Light Particle Beam",
				7 => "Tactical Laser",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "0:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "0:Imperial Laser",
				3 => "0:Light Particle Beam",
				5 => "Light Particle Beam",
				7 => "Tactical Laser",
				9 => "Cargo Bay",
				10 => "Sub Reactor",
				11 => "0:Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}
?>
