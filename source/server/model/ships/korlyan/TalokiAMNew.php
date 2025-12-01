<?php
class TalokiAMNew extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3500;
		$this->faction = "Kor-Lyan Kingdoms";
		$this->phpclass = "TalokiAMNew";
		$this->shipClass = "Taloki Starbase (2240)";
		$this->fighters = array("assault shuttles"=>4, "normal"=>24);

        $this->isd = 2240;
		$this->shipSizeClass = 3; //Enormous is not implemented
        $this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->nonRotating = true; //some bases do not rotate - this attribute is used in combination with $base or $smallBase

		$this->forwardDefense = 21;
		$this->sideDefense = 24;

		$this->imagePath = "img/ships/korlyan_taloki2.png";
		$this->canvasSize = 300; //Enormous Starbase

		$this->locations = array(41, 42, 2, 32, 31, 1);

	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(480); //pass magazine capacity - 10-20 rounds per launcher, plus reload rack 80
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 400); //add full load of basic missiles 
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 80); //add full load of intercept missiles  	      

	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H    
	    $this->enhancementOptionsEnabled[] = 'AMMO_I';//add enhancement options for other missiles - Class-I
	    $this->enhancementOptionsEnabled[] = 'AMMO_J';//add enhancement options for other missiles - Class-J	     
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P    	    	    	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S
	    $this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X	    
	    
		$this->addPrimarySystem(new Reactor(4, 25, 0, 0));
//		$this->addPrimarySystem(new CnC(4, 32, 0, 0)); 

		$cnc = new CnC(4, 16, 0, 0);
		$cnc->startArc = 0;
		$cnc->endArc = 360;
        $this->addPrimarySystem($cnc);
		$cnc = new SecondaryCnC(4, 16, 0, 0);//all-around by default
        $this->addPrimarySystem($cnc);
        
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new CargoBay(4, 75));
		$this->addPrimarySystem(new CargoBay(4, 75));
		$this->addPrimarySystem(new ReloadRack(4, 9));
		$this->addPrimarySystem(new ReloadRack(4, 9));
		$this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new AmmoMissileRackD(4, 0, 0, 0, 360, $ammoMagazine, true));
        $this->addPrimarySystem(new AmmoMissileRackD(4, 0, 0, 0, 360, $ammoMagazine, true));
        $this->addPrimarySystem(new AmmoMissileRackD(4, 0, 0, 0, 360, $ammoMagazine, true));
        $this->addPrimarySystem(new AmmoMissileRackD(4, 0, 0, 0, 360, $ammoMagazine, true));

		//$this->addFrontSystem(new Hangar(4, 14));
		//$this->addFrontSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));

			$hangar = new Hangar(4, 14);
			$hangar->startArc = 300;
			$hangar->endArc = 60;
			$this->addFrontSystem($hangar);		
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 300;
			$subReactor->endArc = 60;
			$this->addFrontSystem($subReactor);	

		/*
		$TargeterA = new ProximityLaser(0, 1, 0, 0, 360, 'A');
		$LauncherA = new ProximityLaserLauncher(4, 0, 0, 270, 90, 'A'); 
		$TargeterA->addLauncher($LauncherA);
		$this->addFrontSystem($TargeterA);
		$this->addFrontSystem($LauncherA);
		$TargeterA->addTag("Front Proximity Laser");
		*/				
		$this->addFrontSystem(new ProximityLaserNew(4, 0, 0, 270, 90));		
		$this->addFrontSystem(new LimpetBoreTorpedoBase(4, 0, 0, 270, 90));
		$this->addFrontSystem(new ProximityLaserNew(4, 0, 0, 270, 90));
		/*
		$TargeterB = new ProximityLaser(0, 1, 0, 0, 360, 'B');
		$LauncherB = new ProximityLaserLauncher(4, 0, 0, 270, 90, 'B'); 
		$TargeterB->addLauncher($LauncherB);
		$this->addFrontSystem($TargeterB);
		$this->addFrontSystem($LauncherB);
		$TargeterB->addTag("Front Proximity Laser");
		*/		
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		
		//$this->addAftSystem(new Hangar(4, 14));
		//$this->addAftSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));

			$hangar = new Hangar(4, 14);
			$hangar->startArc = 120;
			$hangar->endArc = 240;
			$this->addAftSystem($hangar);		
			$subReactor = new SubReactorUniversal(4, 20, 0, 0);
			$subReactor->startArc = 120;
			$subReactor->endArc = 240;
			$this->addAftSystem($subReactor);	

		/*
		$TargeterC = new ProximityLaser(0, 1, 0, 0, 360, 'C');	
		$LauncherC = new ProximityLaserLauncher(4, 0, 0, 90, 270, 'C'); 
		$TargeterC->addLauncher($LauncherC);
		$this->addAftSystem($TargeterC);
		$this->addAftSystem($LauncherC);
		$TargeterC->addTag("Aft Proximity Laser");
		*/				
		$this->addAftSystem(new ProximityLaserNew(4, 0, 0, 90, 270));		
		$this->addAftSystem(new LimpetBoreTorpedoBase(4, 0, 0, 90, 270));
		$this->addAftSystem(new ProximityLaserNew(4, 0, 0, 90, 270));
		/*
		$TargeterD = new ProximityLaser(0, 1, 0, 0, 360, 'D');
		$LauncherD = new ProximityLaserLauncher(4, 0, 0, 90, 270, 'D'); 
		$TargeterD->addLauncher($LauncherD);
		$this->addAftSystem($TargeterD);
		$this->addAftSystem($LauncherD);
		$TargeterD->addTag("Aft Proximity Laser");
		*/			
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		
		//$this->addLeftFrontSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new AmmoMissileRackF(3, 0, 0, 240, 60, $ammoMagazine, true)); 
        $this->addLeftFrontSystem(new AmmoMissileRackF(3, 0, 0, 240, 60, $ammoMagazine, true)); 
        $this->addLeftFrontSystem(new AmmoMissileRackF(3, 0, 0, 240, 60, $ammoMagazine, true)); 
        $this->addLeftFrontSystem(new AmmoMissileRackF(3, 0, 0, 240, 60, $ammoMagazine, true)); 
	
			$subReactor = new SubReactorUniversal(4, 18, 0, 0);
			$subReactor->startArc = 240;
			$subReactor->endArc = 360;
			$this->addLeftFrontSystem($subReactor);	

		//$this->addLeftAftSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new AmmoMissileRackF(3, 0, 0, 120, 300, $ammoMagazine, true)); 
        $this->addLeftAftSystem(new AmmoMissileRackF(3, 0, 0, 120, 300, $ammoMagazine, true)); 
        $this->addLeftAftSystem(new AmmoMissileRackF(3, 0, 0, 120, 300, $ammoMagazine, true)); 
        $this->addLeftAftSystem(new AmmoMissileRackF(3, 0, 0, 120, 300, $ammoMagazine, true)); 

			$subReactor = new SubReactorUniversal(4, 18, 0, 0);
			$subReactor->startArc = 180;
			$subReactor->endArc = 300;
			$this->addLeftAftSystem($subReactor);	

		//$this->addRightFrontSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new AmmoMissileRackF(3, 0, 0, 300, 120, $ammoMagazine, true)); 
        $this->addRightFrontSystem(new AmmoMissileRackF(3, 0, 0, 300, 120, $ammoMagazine, true)); 
        $this->addRightFrontSystem(new AmmoMissileRackF(3, 0, 0, 300, 120, $ammoMagazine, true)); 
        $this->addRightFrontSystem(new AmmoMissileRackF(3, 0, 0, 300, 120, $ammoMagazine, true));  

			$subReactor = new SubReactorUniversal(4, 18, 0, 0);
			$subReactor->startArc = 0;
			$subReactor->endArc = 120;
			$this->addRightFrontSystem($subReactor);	

		//$this->addRightAftSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new AmmoMissileRackF(3, 0, 0, 60, 240, $ammoMagazine, true));  
        $this->addRightAftSystem(new AmmoMissileRackF(3, 0, 0, 60, 240, $ammoMagazine, true)); 
        $this->addRightAftSystem(new AmmoMissileRackF(3, 0, 0, 60, 240, $ammoMagazine, true)); 
        $this->addRightAftSystem(new AmmoMissileRackF(3, 0, 0, 60, 240, $ammoMagazine, true)); 

			$subReactor = new SubReactorUniversal(4, 18, 0, 0);
			$subReactor->startArc = 60;
			$subReactor->endArc = 180;
			$this->addRightAftSystem($subReactor);

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
		/*replaced by TAGed versions!	
        $this->addFrontSystem(new Structure( 4, 150));
        $this->addAftSystem(new Structure( 4, 136));
        $this->addLeftFrontSystem(new Structure( 4, 180));
        $this->addLeftAftSystem(new Structure( 4, 180));
        $this->addRightFrontSystem(new Structure( 4, 180));
        $this->addRightAftSystem(new Structure( 4, 180));        
		$this->addPrimarySystem(new Structure( 4, 182));
		*/
		$this->addPrimarySystem(new Structure( 4, 182));//needs to be called first for some reason - static call apparently fails for the first time...
		$this->addFrontSystem(Structure::createAsOuter(4, 150, 300, 60));
		$this->addAftSystem(Structure::createAsOuter(4, 136, 120, 240));
		$this->addLeftFrontSystem(Structure::createAsOuter(4, 180, 240, 360));
		$this->addLeftAftSystem(Structure::createAsOuter(4, 180, 180, 300));
		$this->addRightFrontSystem(Structure::createAsOuter(4, 180, 0, 120));
		$this->addRightAftSystem(Structure::createAsOuter(4, 180, 60, 180));

		//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
					11 => "Reload Rack",
					12 => "Class-D Missile Rack",
                    14 => "Scanner",
					18 => "Cargo Bay",
                    19 => "Reactor",
                    20 => "TAG:C&C",
           		 ),
            1=> array(
                    2 => "Limpet Bore Torpedo",
					4 => "TAG:Proximity Laser",
					6 => "TAG:Particle Cannon",
					7 => "TAG:Hangar",
					8 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    2 => "Limpet Bore Torpedo",
					4 => "TAG:Proximity Laser",
					6 => "TAG:Particle Cannon",
					7 => "TAG:Hangar",
					8 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    3 => "TAG:Standard Particle Beam",
                    7 => "TAG:Class-F Missile Rack",
					8 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    3 => "TAG:Standard Particle Beam",
                    7 => "TAG:Class-F Missile Rack",
					8 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    3 => "TAG:Standard Particle Beam",
                    7 => "TAG:Class-F Missile Rack",
					8 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    3 => "TAG:Standard Particle Beam",
                    7 => "TAG:Class-F Missile Rack",
					8 => "TAG:Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
            	),
           	);

    }
}
