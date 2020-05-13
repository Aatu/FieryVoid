<?php
class VaLothar extends StarBaseThreeSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3250;
		$this->faction = 'Chlonas';
		$this->phpclass = "wlcChlonasVaLothar";
		$this->shipClass = "Va'Lothar Battlestation";
		$this->fighters = array("heavy"=>36); 

		$this->shipSizeClass = 3;
        $this->Enormous = true;
		$this->iniativebonus = -200;
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 20;
		$this->sideDefense = 20;
		$this->imagePath = "img/ships/ChlonasVaLothar.png";
		$this->canvasSize = 200;

		$this->locations = array(1, 32, 42);
		
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				13 => "Light Gatling Mattergun",
				16 => "Scanner",
				18 => "Reactor",
				20 => "C&C",
			),
		);

		$this->addPrimarySystem(new Reactor(5, 26, 0, 0));
		$this->addPrimarySystem(new CnC(5, 36, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 16, 6, 7));
		$this->addPrimarySystem(new Scanner(5, 16, 6, 7));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));
		$this->addPrimarySystem(new CustomGatlingMattergunLight(2, 0, 0, 0, 360));


		$this->addPrimarySystem(new Structure(5, 120));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 270 + ($i*120);
			$max = 90 + ($i*120);

			$systems = array(
				new CustomPulsarLaser(4, 0, 0, $min, $max),
				new CustomPulsarLaser(4, 0, 0, $min, $max),
				new AssaultLaser(4, 6, 4, $min, $max),
				new AssaultLaser(4, 6, 4, $min, $max),
				new MatterCannon(4, 7, 4, $min, $max),
				new MatterCannon(4, 7, 4, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max)
				new LightParticleBeamShip(4, 2, 1, $min, $max)
				new LightParticleBeamShip(4, 2, 1, $min, $max)
				new Hangar(4, 14),
				new Cargo Bay(4, 24),
				new Cargo Bay(4, 24),
				new SubReactor(4, 20, 0, 0),
				new Structure( 4, 154)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				3 => "Pulsar Laser",
				5 => "Assault Laser",
				7 => "Matter Cannon",
				9 => "Light Particle Beam",
				10 => "Hangar",
				11 => "Cargo Bay",
				12 => "Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}