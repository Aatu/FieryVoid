<?php
class Roskorbase2240AM extends SmallStarBaseFourSections
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2000;
		$this->base = true;
		$this->smallBase = true; //"small" as in 4 outer sections
		$this->faction = "Hurr Republic";
		$this->phpclass = "Roskorbase2240AM";
		$this->shipClass = "Roskor Command Post (2240)";
		$this->imagePath = "img/ships/HurrRoskor.png";
		$this->canvasSize = 256;
		$this->fighters = array("normal"=>48);
		$this->isd = 2240;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 19;
		$this->sideDefense = 19;



        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(400); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 400); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
        //$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
		//Hurr developed their missiles from Dilgar tech - they have L,H,F and A missiles (even if only after Dilgar War)
		//they developed P missiles as well (just before Show era), but they remain very rare (tabletop limit of 1 per ship (2 per dedicated missile ship)). I opted to skip these missiles instead. 
		

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
//		$this->addPrimarySystem(new ProtectedCnC(6, 36, 0, 0)); //3x 5/12

		$cnc = new CnC(5, 12, 0, 0); //regular, not Pak C&C - no point with a starbase...
		$cnc->startArc = 0;
		$cnc->endArc = 360;
		$this->addPrimarySystem($cnc);
		$this->addPrimarySystem(new SecondaryCnC(5, 12, 0, 0));//all-around by default
		$this->addPrimarySystem(new SecondaryCnC(5, 12, 0, 0));//all-around by default

		$this->addPrimarySystem(new Scanner(5, 16, 4, 5));
		$this->addPrimarySystem(new Scanner(5, 16, 4, 5));
		$this->addPrimarySystem(new Hangar(5, 2));
		$this->addPrimarySystem(new ReloadRack(5, 9));		
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));
		$this->addPrimarySystem(new HeavyPlasma(5, 8, 5, 0, 360));

		//$this->addFrontSystem(new Hangar(4, 12));
		//$this->addFrontSystem(new CargoBay(4, 24));
		//$this->addFrontSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addFrontSystem(new AmmoMissileRackS(4, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackS(4, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackS(4, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new AmmoMissileRackS(4, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));
		$this->addFrontSystem(new StdParticleBeam(4, 4, 1, 270, 90));

			$hangar = new Hangar(4, 12);
			$hangar->startArc = 270;
			$hangar->endArc = 90;
			$this->addFrontSystem($hangar);
			$cargoBay = new CargoBay(4, 24);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);

		//$this->addAftSystem(new Hangar(4, 12));
		//$this->addAftSystem(new CargoBay(4, 24));
		//$this->addAftSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addAftSystem(new AmmoMissileRackS(4, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackS(4, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackS(4, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new AmmoMissileRackS(4, 0, 0, 90, 270, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 90, 270));

			$hangar = new Hangar(4, 12);
			$hangar->startArc = 90;
			$hangar->endArc = 270;
			$this->addAftSystem($hangar);
			$cargoBay = new CargoBay(4, 24);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);

		//$this->addLeftSystem(new Hangar(4, 12));
		//$this->addLeftSystem(new CargoBay(4, 24));
		//$this->addLeftSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addLeftSystem(new AmmoMissileRackS(4, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new AmmoMissileRackS(4, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new AmmoMissileRackS(4, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new AmmoMissileRackS(4, 0, 0, 180, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 360));

			$hangar = new Hangar(4, 12);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
			$cargoBay = new CargoBay(4, 24);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);	

		//$this->addRightSystem(new Hangar(4, 12));
		//$this->addRightSystem(new CargoBay(4, 24));
		//$this->addRightSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addRightSystem(new AmmoMissileRackS(4, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new AmmoMissileRackS(4, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new AmmoMissileRackS(4, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new AmmoMissileRackS(4, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));

			$hangar = new Hangar(4, 12);
			$hangar->startArc = 0;
			$hangar->endArc = 180;
			$this->addRightSystem($hangar);
			$cargoBay = new CargoBay(4, 24);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);
			
		/*replaced by TAGed versions!				
		$this->addFrontSystem(new Structure( 4, 100));
		$this->addAftSystem(new Structure( 4, 100));
		$this->addLeftSystem(new Structure( 4, 100));
		$this->addRightSystem(new Structure( 4, 100));
		$this->addPrimarySystem(new Structure( 5, 180));		
		*/
		$this->addPrimarySystem(new Structure( 5, 180));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 100, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 100, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 100, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 100, 0, 180));			
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				13 => "Heavy Plasma Cannon",
				15 => "Scanner",
				16 => "Hangar",
				18 => "Reactor",
				20 => "TAG:C&C",
			),
			1=> array(
				3 => "TAG:Standard Particle Beam",
				7 => "TAG:Class-S Missile Rack",
				8 => "TAG:Hangar",
				10 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "TAG:Standard Particle Beam",
				7 => "TAG:Class-S Missile Rack",
				8 => "TAG:Hangar",
				10 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				3 => "TAG:Standard Particle Beam",
				7 => "TAG:Class-S Missile Rack",
				8 => "TAG:Hangar",
				10 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				3 => "TAG:Standard Particle Beam",
				7 => "TAG:Class-S Missile Rack",
				8 => "TAG:Hangar",
				10 => "TAG:Cargo Bay",
				11 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>