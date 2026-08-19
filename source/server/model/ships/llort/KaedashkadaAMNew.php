<?php
class KaedashkadaAMNew extends UnevenStarBaseEightSections 
{

	function __construct($id, $userid, $name,  $slot)
	{
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 2250;
		$this->base = true;
		$this->smallBase = true; //small = four sections
		$this->faction = "Llort";
		$this->phpclass = "KaedashkadaAMNew";
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
		$this->enhancementOptionsEnabled[] = 'AMMO_Z';//add enhancement options for other missiles - Class-Z   				
		
		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 30, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 5, 7));
		$this->addPrimarySystem(new Scanner(5, 16, 5, 7));
		$this->addPrimarySystem(new Hangar(5, 6, 1));
        $this->addPrimarySystem(new ReloadRack(5, 6));	
        $this->addPrimarySystem(new CargoBay(5, 40));	
		$missile = new AmmoMissileRackL(5, 0, 0, 0, 360, $ammoMagazine, true); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$missile->addTag("Weapon");
		$this->addPrimarySystem($missile);
		$missile = new AmmoMissileRackL(5, 0, 0, 0, 360, $ammoMagazine, true); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$missile->addTag("Weapon");
		$this->addPrimarySystem($missile);
		$stdBeam = new StdParticleBeam(5, 4, 1, 0, 360);
		$stdBeam->addTag("Weapon");
		$this->addPrimarySystem($stdBeam);
		$stdBeam = new StdParticleBeam(5, 4, 1, 0, 360);
		$stdBeam->addTag("Weapon");
		$this->addPrimarySystem($stdBeam);

		$missile = new AmmoMissileRackL(4, 0, 0, 300, 60, $ammoMagazine, true); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$missile->addTag("Weapon");
		$this->addFrontSystem($missile);
		$particle = new ParticleCannon(4, 8, 7, 300, 60);
		$particle->addTag("Weapon");
		$this->addFrontSystem($particle);
			$hangar1 = new Hangar(4, 6);
			$hangar1->startArc = 270;
			$hangar1->endArc = 90;
			$this->addFrontSystem($hangar1);					
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 270;
			$cargoBay->endArc = 90;
			$this->addFrontSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 16, 0, 0);
			$subReactor->startArc = 270;
			$subReactor->endArc = 90;
			$this->addFrontSystem($subReactor);			


		$missile = new AmmoMissileRackL(4, 0, 0, 120, 240, $ammoMagazine, true); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$missile->addTag("Weapon");
		$this->addAftSystem($missile);
		$particle = new ParticleCannon(4, 8, 7, 120, 240);
		$particle->addTag("Weapon");
		$this->addAftSystem($particle);
		$stdBeam = new StdParticleBeam(4, 4, 1, 120, 240);
		$stdBeam->addTag("Weapon");
		$this->addAftSystem($stdBeam);
		$stdBeam = new StdParticleBeam(4, 4, 1, 120, 240);
		$stdBeam->addTag("Weapon");
		$this->addAftSystem($stdBeam);
		$twin = new TwinArray(4, 6, 2, 120, 240);
		$twin->addTag("Weapon");
		$this->addAftSystem($twin);
			$hangar1 = new Hangar(4, 6, 6);
			$hangar1->startArc = 90;
			$hangar1->endArc = 270;
			$this->addAftSystem($hangar1);					
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 90;
			$cargoBay->endArc = 270;
			$this->addAftSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 23, 0, 0);
			$subReactor->startArc = 90;
			$subReactor->endArc = 270;
			$this->addAftSystem($subReactor);	


		//Fwd Port Section
		$hangar1 = new Hangar(4, 6, 6);
		$hangar1->startArc = 270;
		$hangar1->endArc = 90;				
		$this->addLeftFrontSystem($hangar1);
		$subReactor1 = new SubReactorUniversal(4, 8, 0, 0);
		$subReactor1->startArc = 270;
		$subReactor1->endArc = 90;				
		$this->addLeftFrontSystem($subReactor1);						
		$scatter1 = new ScatterGun(3, 8, 3, 240, 60);
		$scatter1->addTag("Weapon");	
		$this->addLeftFrontSystem($scatter1);				
		$scatter2 = new ScatterGun(3, 8, 3, 240, 60);
		$scatter2->addTag("Weapon");
		$this->addLeftFrontSystem($scatter2);	
		$scatter3 = new ScatterGun(3, 8, 3, 240, 60);
		$scatter3->addTag("Weapon");
		$this->addLeftFrontSystem($scatter3);	

		//Port Section
		$particle = new ParticleCannon(4, 8, 7, 180, 360);
		$particle->addTag("Weapon");
		$this->addLeftSystem($particle);
		$twin = new TwinArray(4, 6, 2, 180, 360);
		$twin->addTag("Weapon");
		$this->addLeftSystem($twin);				
			$hangar = new Hangar(4, 6, 6);
			$hangar->startArc = 180;
			$hangar->endArc = 360;
			$this->addLeftSystem($hangar);
			$subReactor = new SubReactorUniversal(4, 10, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 360;
			$this->addLeftSystem($subReactor);				
				

		//Aft Port Section
		$hangar1 = new Hangar(4, 6, 6);
		$hangar1->startArc = 180;
		$hangar1->endArc = 300;				
		$this->addLeftAftSystem($hangar1);
		$subReactor1 = new SubReactorUniversal(4, 13, 0, 0);
		$subReactor1->startArc = 180;
		$subReactor1->endArc = 300;				
		$this->addLeftAftSystem($subReactor1);						
		$missile = new AmmoMissileRackL(4, 0, 0, 180, 300, $ammoMagazine, true);
		$missile->addTag("Weapon");	
		$this->addLeftAftSystem($missile);				
		$stdBeam1 = new StdParticleBeam(4, 4, 1, 180, 300);
		$stdBeam1->addTag("Weapon");	
		$this->addLeftAftSystem($stdBeam1);	
		$stdBeam2 = new StdParticleBeam(4, 4, 1, 180, 300);
		$stdBeam2->addTag("Weapon");	
		$this->addLeftAftSystem($stdBeam2);	


		//Fwd Stbd Section
		$hangar1 = new Hangar(4, 6, 6);
		$hangar1->startArc = 0;
		$hangar1->endArc = 120;				
		$this->addRightFrontSystem($hangar1);
		$subReactor1 = new SubReactorUniversal(4, 10, 0, 0);
		$subReactor1->startArc = 0;
		$subReactor1->endArc = 120;				
		$this->addRightFrontSystem($subReactor1);						
		$twin = new TwinArray(4, 6, 2, 0, 120);
		$twin->addTag("Weapon");
		$this->addRightFrontSystem($twin);				
		$particle = new ParticleCannon(4, 8, 7, 0, 120);
		$particle->addTag("Weapon");	
		$this->addRightFrontSystem($particle);	


		//Stb Section
		$missile = new AmmoMissileRackL(4, 0, 0, 0, 180, $ammoMagazine, true); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$missile->addTag("Weapon");
		$this->addRightSystem($missile);
		$stdBeam = new StdParticleBeam(4, 4, 1, 0, 180);
		$stdBeam->addTag("Weapon");
		$this->addRightSystem($stdBeam);
		$stdBeam = new StdParticleBeam(4, 4, 1, 0, 180);
		$stdBeam->addTag("Weapon");
		$this->addRightSystem($stdBeam);
			$hangar1 = new Hangar(4, 6);
			$hangar1->startArc = 0;
			$hangar1->endArc = 180;
			$this->addRightSystem($hangar1);					
			$cargoBay = new CargoBay(4, 25);
			$cargoBay->startArc = 0;
			$cargoBay->endArc = 180;
			$this->addRightSystem($cargoBay);
			$subReactor = new SubReactorUniversal(4, 13, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 180;
			$this->addRightSystem($subReactor);		


		//Aft Stbd Section
		$hangar1 = new Hangar(4, 6, 6);
		$hangar1->startArc = 60;
		$hangar1->endArc = 240;				
		$this->addRightAftSystem($hangar1);
		$subReactor1 = new SubReactorUniversal(4, 8, 0, 0);
		$subReactor1->startArc = 60;
		$subReactor1->endArc = 240;			
		$this->addRightAftSystem($subReactor1);						
		$scatter1 = new ScatterGun(3, 8, 3, 60, 240);
		$scatter1->addTag("Weapon");
		$this->addRightAftSystem($scatter1);				
		$scatter2 = new ScatterGun(3, 8, 3, 60, 240);
		$scatter2->addTag("Weapon");
		$this->addRightAftSystem($scatter2);	
		$scatter3 = new ScatterGun(3, 8, 3, 60, 240);
		$scatter3->addTag("Weapon");
		$this->addRightAftSystem($scatter3);



		

		$this->addPrimarySystem(new Structure( 4, 180));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 100, 300, 60));
		$this->addAftSystem(Structure::createAsOuter(4, 130, 120, 240));
		$this->addLeftFrontSystem(Structure::createAsOuter(4, 100, 240, 60));
		$this->addLeftSystem(Structure::createAsOuter(4, 100, 180, 360));
		$this->addLeftAftSystem(Structure::createAsOuter(4, 110, 180, 300));
		$this->addRightFrontSystem(Structure::createAsOuter(4, 100, 0, 120));
		$this->addRightSystem(Structure::createAsOuter(4, 110, 0, 180));		
		$this->addRightAftSystem(Structure::createAsOuter(4, 100, 60, 240));

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
                7 => "TAG:Weapon", 
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
                7 => "TAG:Weapon", 
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
                7 => "TAG:Weapon", 
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			31=> array(
                7 => "TAG:Weapon", 
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			32=> array(
                7 => "TAG:Weapon", 
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),	
			4=> array(
                7 => "TAG:Weapon", 
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),								
			41=> array(
                7 => "TAG:Weapon", 
				9 => "TAG:Hangar",
				11 => "TAG:Cargo Bay",
				13 => "TAG:Sub Reactor",
				18 => "Structure",
				20 => "Primary",
			),
			42=> array(
                7 => "TAG:Weapon", 
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