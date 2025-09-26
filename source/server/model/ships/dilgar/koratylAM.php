<?php
class KoratylAM extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2700;
		$this->faction = "Dilgar Imperium";
		$this->phpclass = "KoratylAM";
		$this->shipClass = "Koratyl Defense Base";
		$this->fighters = array("heavy"=>36); 
		$this->isd = 2227;

		$this->shipSizeClass = 3; //Enormous is not implemented
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 22;
		$this->sideDefense = 22;

		$this->imagePath = "img/ships/koratyl.png";
		$this->canvasSize = 280; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);
		$this->hitChart = array(			
			0=> array(
				9 => "Structure",
				10 => "Heavy Bolter",
				13 => "Scanner",
				16 => "Cargo Bay",
				17 => "Hangar",
				19 => "Reactor",
				20 => "TAG:C&C",
			),
		);



        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(240); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 240); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F        
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        //$this->enhancementOptionsEnabled[] = 'AMMO_K';//Dilgar were wiped out before Starburst missile was devised
        //$this->enhancementOptionsEnabled[] = 'AMMO_M';//Dilgar were wiped out before Multiwarhead missile was devised
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//Dilgar were wiped out before Piercing missile was devised                

		$this->addPrimarySystem(new Reactor(5, 25, 0, 0)); 
//		$this->addPrimarySystem(new ProtectedCnC(6, 30, 0, 0)); //instead of 2 5x15 C&C, make it 1 6x30

		$cnc = new CnC(5, 15, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
		$this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(5, 15, 0, 0);//all-around by default
		$this->addPrimarySystem($cnc);

		$this->addPrimarySystem(new Scanner(5, 20, 5, 8));
		$this->addPrimarySystem(new Scanner(5, 20, 5, 8));
		$this->addPrimarySystem(new Hangar(5, 4));
		$this->addPrimarySystem(new CargoBay(5, 25));
		$this->addPrimarySystem(new HeavyBolter(5, 10, 6, 0, 360));
		$this->addPrimarySystem(new HeavyBolter(5, 10, 6, 0, 360));

		$this->addPrimarySystem(new Structure(5, 100));


		for ($i = 0; $i < sizeof($this->locations); $i++){

			$min = 0 + ($i*60);
			$max = 120 + ($i*60);

			$struct = Structure::createAsOuter(4, 90,$min,$max);
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = $min;
			$cargoBay->endArc = $max;
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = $min;
			$subReactor->endArc = $max;
			$hangar = new Hangar(4, 6);
			$hangar->startArc = $min;
			$hangar->endArc = $max;			

			$systems = array(
				new HeavyBolter(4, 10, 6, $min, $max),
				new HeavyBolter(4, 10, 6, $min, $max),
				new MediumLaser(4, 6, 5, $min, $max),
				new MediumLaser(4, 6, 5, $min, $max),
				new ScatterPulsar(4, 4, 2, $min, $max),
				new ScatterPulsar(4, 4, 2, $min, $max),
				new AmmoMissileRackS(4, 0, 0, $min, $max, $ammoMagazine, true), //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
				new AmmoMissileRackS(4, 0, 0, $min, $max, $ammoMagazine, true), //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
				$subReactor,
				$hangar,
				$cargoBay,
				$struct
			);


			$loc = $this->locations[$i];

			$this->hitChart[$loc] = array(
				2 => "TAG:Heavy Bolter",
				4 => "TAG:Medium Laser",
				6 => "TAG:Class-S Missile Rack",
				8 => "TAG:Scatter Pulsar",
				10 => "TAG:Cargo Bay",
				11 => "TAG:Hangar",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			);

			foreach ($systems as $system){
				$this->addSystem($system, $loc);
			}
		}
    }
}
