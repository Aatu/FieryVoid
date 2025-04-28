<?php
class OrionAM extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3500;
		$this->faction = "Earth Alliance (defenses)";
		$this->phpclass = "OrionAM";
		$this->shipClass = "Orion Battle Station";
		$this->fighters = array("heavy"=>36); 
		$this->isd = 2240;

		$this->shipSizeClass = 3; //Enormous is not implemented
        $this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 20;
		$this->sideDefense = 20;

		$this->imagePath = "img/ships/orion.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				10 => "Structure",
				12 => "Cargo Bay",
				14 => "Scanner",
				16 => "Class-B Missile Rack",
				17 => "Reactor",
				18 => "Hangar",
				20 => "C&C",
			),
		);


        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(600); //pass magazine capacity - class-B launchers hold 60 rounds each!
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 600); //add full load of basic missiles
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
    
		$this->addPrimarySystem(new Reactor(6, 20, 0, -18));
		$this->addPrimarySystem(new CnC(6, 27, 0, 0)); 
		$this->addPrimarySystem(new Scanner(6, 16, 5, 7));
		$this->addPrimarySystem(new Scanner(6, 16, 5, 7));
		$this->addPrimarySystem(new Hangar(6, 6));
		$this->addPrimarySystem(new CargoBay(6, 48));
        $this->addPrimarySystem(new AmmoMissileRackB(6, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackB(6, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackB(6, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackB(6, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
    

		$this->addPrimarySystem(new Structure( 7, 150));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$systems = array(
				new HvyParticleCannon(4, 12, 9, $min, $max),
				new HeavyPulse(4, 6, 4, $min, $max),
        new AmmoMissileRackB(4, 0, 0, $min, $max, $ammoMagazine, true), //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
				new QuadParticleBeam(4, 8, 4, $min, $max),
				new InterceptorMKII(4, 4, 2, $min, $max),
				new InterceptorMKII(4, 4, 2, $min, $max),
				new Hangar(4, 6, 6),
				new SubReactorUniversal(4, 20, 0, 0),
				new Structure( 4, 100)
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "TAG:Class-B Missile Rack",
				2 => "TAG:Heavy Pulse Cannon",
				3 => "TAG:Heavy Particle Cannon",
				5 => "TAG:Interceptor II",
				6 => "TAG:Quad Particle Beam",
				7 => "TAG:Hangar",
				8 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
