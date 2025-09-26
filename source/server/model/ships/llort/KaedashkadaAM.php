<?php
class KaedashkadaAM extends UnevenBaseFourSections 
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2250;
		$this->base = true;
		$this->smallBase = true; //small = four sections
		$this->faction = "Llort";
		$this->phpclass = "KaedashkadaAM";
		$this->shipClass = "Kaedashkada Starbase";
		$this->imagePath = "img/ships/LlortKaedashkada.png";
		$this->canvasSize = 300;
		$this->fighters = array("normal"=>36);
		$this->isd = 2228;

		$this->shipSizeClass = 3;
		$this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;

		$this->forwardDefense = 23;
		$this->sideDefense = 25;


        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(200); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 200); //add full load of basic missiles
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
		$this->addPrimarySystem(new CnC(5, 30, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 16, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 6));
        $this->addPrimarySystem(new ReloadRack(5, 6));	
        $this->addPrimarySystem(new CargoBay(5, 40));	
		$this->addPrimarySystem(new AmmoMissileRackL(5, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addPrimarySystem(new AmmoMissileRackL(5, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addPrimarySystem(new StdParticleBeam(5, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(5, 4, 1, 0, 360));


		//$this->addFrontSystem(new Hangar(3, 6));
		//$this->addFrontSystem(new CargoBay(4, 25));
		//$this->addFrontSystem(new SubReactorUniversal(4, 24, 0, 0));
		$this->addFrontSystem(new AmmoMissileRackL(4, 0, 0, 300, 60, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 300, 60));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 60));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 60));
		$this->addFrontSystem(new ScatterGun(3, 8, 3, 240, 60));

			$hangar1 = new Hangar(4, 6);
			$hangar1->startArc = 270;
			$hangar1->endArc = 90;
			$this->addFrontSystem($hangar1);					
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 24, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);			

		//$this->addAftSystem(new Hangar(4, 6));
		//$this->addAftSystem(new Hangar(4, 6));		
		//$this->addAftSystem(new CargoBay(4, 25));
		//$this->addAftSystem(new SubReactorUniversal(4, 31, 0, 0));
		$this->addAftSystem(new AmmoMissileRackL(4, 0, 0, 120, 240, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 120, 240));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 120, 240));
		$this->addAftSystem(new StdParticleBeam(4, 4, 1, 120, 240));
		$this->addAftSystem(new TwinArray(4, 6, 2, 120, 240));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 60, 240));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 60, 240));
		$this->addAftSystem(new ScatterGun(3, 8, 3, 60, 240));

		$hangar1 = new Hangar(4, 6);
			$hangar1->startArc = 90;
			$hangar1->endArc = 270;
			$this->addAftSystem($hangar1);
			$hangar2 = new Hangar(4, 6);
			$hangar2->startArc = 90;
			$hangar2->endArc = 270;
			$this->addAftSystem($hangar2);					
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 31, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);	

		//$this->addLeftSystem(new Hangar(4, 6));
		//$this->addLeftSystem(new CargoBay(4, 25));
		//$this->addLeftSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addLeftSystem(new AmmoMissileRackL(4, 0, 0, 180, 300, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 360));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 300));
		$this->addLeftSystem(new StdParticleBeam(4, 4, 1, 180, 300));
		$this->addLeftSystem(new TwinArray(4, 6, 2, 180, 360));

			$hangar1 = new Hangar(4, 6);
			$hangar1->startArc = 180;
			$hangar1->endArc = 360;
			$this->addLeftSystem($hangar1);					
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 180;
			$cargoBay->endArc = 360;
			$this->addLeftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 25, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);				
				
		//$this->addRightSystem(new Hangar(4, 6));
		//$this->addRightSystem(new Hangar(4, 6));		
		//$this->addRightSystem(new CargoBay(4, 25));
		//$this->addRightSystem(new SubReactorUniversal(4, 23, 0, 0));
		$this->addRightSystem(new AmmoMissileRackL(4, 0, 0, 0, 180, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 120));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(4, 4, 1, 0, 180));
		$this->addRightSystem(new TwinArray(4, 6, 2, 0, 120));

			$hangar1 = new Hangar(4, 6);
			$hangar1->startArc = 0;
			$hangar1->endArc = 180;
			$this->addRightSystem($hangar1);
			$hangar2 = new Hangar(4, 6);
			$hangar2->startArc = 0;
			$hangar2->endArc = 180;
			$this->addRightSystem($hangar2);					
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 23, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);		
		
		/*replaced by TAGed versions!
		$this->addFrontSystem(new Structure( 4, 200));
		$this->addAftSystem(new Structure( 4, 230));
		$this->addLeftSystem(new Structure( 4, 210));
		$this->addRightSystem(new Structure( 4, 210));
		$this->addPrimarySystem(new Structure( 5, 180));		
		*/
		$this->addPrimarySystem(new Structure( 4, 180));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 200, 270,90));
		$this->addAftSystem(Structure::createAsOuter(4, 230, 90, 270));
		$this->addLeftSystem(Structure::createAsOuter(4, 210, 180, 360));
		$this->addRightSystem(Structure::createAsOuter(4, 210, 0, 180));		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				10 => "Class-L Missile Rack",
				11 => "Standard Particle Beam",
				14 => "Scanner",
				16 => "Reload Rack",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				3 => "TAG:Scattergun",
				5 => "TAG:Class-L Missile Rack",
				7 => "TAG:Particle Cannon",
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				3 => "TAG:Scattergun",
				4 => "TAG:Class-L Missile Rack",
				5 => "TAG:Particle Cannon",
				6 => "TAG:Twin Array",
				7 => "TAG:Standard Particle Beam",
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				1 => "TAG:Twin Array",
				3 => "TAG:Class-L Missile Rack",
				5 => "TAG:Particle Cannon",
				7 => "TAG:Standard Particle Beam",
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				1 => "TAG:Twin Array",
				3 => "TAG:Class-L Missile Rack",
				5 => "TAG:Particle Cannon",
				7 => "TAG:Standard Particle Beam",
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
		
	}
}


?>