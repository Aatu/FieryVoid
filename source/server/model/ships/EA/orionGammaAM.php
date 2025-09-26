<?php
class OrionGammaAM extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 1900;
		//$this->faction = "Earth Alliance (defenses)";
        $this->faction = "Earth Alliance";       
		$this->phpclass = "OrionGammaAM";
		$this->shipClass = "Orion Battle Station (Gamma)";
		$this->fighters = array("heavy"=>36); 
		
		$this->occurence = "common";
		$this->variantOf = 'Orion Battle Station';
 		$this->unofficial = true;
        $this->isd = 2230;

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
				16 => "Class-L Missile Rack",
				17 => "Hangar",
				18 => "Reactor",
				20 => "C&C",
			),
		);

    
        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(160); //pass magazine capacity - class-L launchers hold 20 rounds each!
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 160); //add full load of basic missiles
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
    
		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 27, 0, 0)); 
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 6));
		$this->addPrimarySystem(new Hangar(5, 6));
		$this->addPrimarySystem(new CargoBay(5, 48));
        $this->addPrimarySystem(new AmmoMissileRackL(5, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackL(5, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base


		$this->addPrimarySystem(new Structure( 5, 150));

		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$struct = Structure::createAsOuter(4, 100,$min,$max);
			$hangar = new Hangar(4, 6, 6);
			$hangar->startArc = $min;
			$hangar->endArc = $max;
			$subReactor = new SubReactorUniversal(4, 18, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;
			$cargo = new CargoBay(4, 12);
			$cargo->startArc = $min;
			$cargo->endArc = $max;			

			$systems = array(
				new Railgun(4, 9, 6, $min, $max),
       			new AmmoMissileRackL(4, 0, 0, $min, $max, $ammoMagazine, true), //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
				new InterceptorMKI(4, 4, 1, $min, $max),
				new StdParticleBeam(4, 4, 1, $min, $max),
				$hangar,
				$subReactor,
				$cargo,
				$struct
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				1 => "TAG:Class-L Missile Rack",
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
