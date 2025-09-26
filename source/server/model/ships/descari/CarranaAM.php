<?php
class CarranaAM extends SmallStarBaseThreeSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 750;
		$this->faction = "Descari Committees";
		$this->phpclass = "CarranaAM";
		$this->shipClass = "Carrana Base";
		$this->fighters = array("heavy"=>18); 

		$this->isd = 2246;
		$this->shipSizeClass = 3;
		$this->iniativebonus = -200;
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->forwardDefense = 16;
		$this->sideDefense = 16;
		$this->imagePath = "img/ships/DescariCarrana.png";
		$this->canvasSize = 200;

		$this->locations = array(1, 4, 3);
		
		$this->hitChart = array(			
			0=> array(
				12 => "Structure",
				14 => "Scanner",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
		);


        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(120); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 120); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
		$this->enhancementOptionsEnabled[] = 'AMMO_I';//add enhancement options for other missiles - Class-I	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		$this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X				
		
		$this->addPrimarySystem(new Reactor(5, 30, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 16, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 2));

		$this->addPrimarySystem(new Structure(5, 52));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 300 + ($i*120);
			$max = 60 + ($i*120);

			$struct = Structure::createAsOuter(4, 56,$min,$max);
			$hangar = new Hangar(4, 6);
			$hangar->startArc = $min;
			$hangar->endArc = $max;

			$systems = array(
				new HeavyPlasmaBolter(4, 0, 0, $min, $max),
				new AmmoMissileRackS(4, 0, 0, $min, $max, $ammoMagazine, true),
				new AmmoMissileRackS(4, 0, 0, $min, $max, $ammoMagazine, true),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				new LightParticleBeamShip(4, 2, 1, $min, $max),
				$hangar,
				$struct
			);

			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				4 => "TAG:Heavy Plasma Bolter",
				7 => "TAG:Light Particle Beam",
				10 => "TAG:Class-S Missile Rack",
				12 => "TAG:Hangar",
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