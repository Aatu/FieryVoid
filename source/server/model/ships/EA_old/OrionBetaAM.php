<?php
class OrionBetaAM extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1500;
        $this->faction = "Earth Alliance (Early)";
		$this->phpclass = "OrionBetaAM";
		$this->shipClass = "Orion Starbase (Beta)";
		$this->fighters = array("heavy"=>36); 
		
		$this->occurence = "common";
		$this->variantOf = 'Orion Starbase (Alpha)';
 		$this->unofficial = 'S'; //HRT design released after AoG demise
        $this->isd = 2190;

		$this->shipSizeClass = 3;
        $this->Enormous = true;
		$this->iniativebonus = -200;
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->imagePath = "img/ships/orion.png";
		$this->canvasSize = 280;

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Cargo Bay",
				14 => "Scanner",
				16 => "Heavy Plasma Cannon",
				17 => "Hangar",
				18 => "Reactor",
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
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		//I assume "Old" EA is Dilgar War era, at the latest - so no Minbari War-designed Piercing Missile, Starburst or Multiwarhead.
		
		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 27, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Hangar(5, 6));
		$this->addPrimarySystem(new CargoBay(5, 48));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));


		$this->addPrimarySystem(new Structure( 5, 150));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new Railgun(3, 9, 6, $min, $max),
				new AmmoMissileRackSO(3, 0, 0, $min, $max, $ammoMagazine, true), //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
				new InterceptorMKI(3, 4, 1, $min, $max),
				new StdParticleBeam(3, 4, 1, $min, $max),
				new Hangar(3, 6, 6),
				new SubReactorUniversal(3, 18, 0, 0),
				new CargoBay (3, 12),
				new Structure( 3, 100)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "TAG:Class-S Missile Rack",
				2 => "TAG:Railgun",
				3 => "TAG:Standard Particle Beam",
				4 => "TAG:Interceptor I",
				5 => "TAG:Hangar",
				6 => "TAG:Cargo Bay",
				7 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
