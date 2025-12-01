<?php
class KoloskAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 850;
		$this->faction = "Kor-Lyan Kingdoms";
        $this->phpclass = "KoloskAM";
        $this->imagePath = "img/ships/korlyan_koskova3.png";
        $this->shipClass = "Kolosk Proximity Cruiser";
			$this->occurence = "uncommon";
			$this->variantOf = 'OBSELETE';
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side

		$this->isd = 2258;
        $this->fighters = array("assault shuttles"=>2);

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 5;
        $this->rollcost = 1;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;
        
	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(200); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 120); //add full load of basic missiles 
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 80); //add full load of basic missiles  	      

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

	    
        $this->addPrimarySystem(new Reactor(6, 25, 0, 4));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 16, 4, 6));
        $this->addPrimarySystem(new Engine(6, 20, 0, 10, 4));
		$this->addPrimarySystem(new Hangar(6, 2));
        $this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new StdParticleBeam(4, 4, 1, 0, 360));
        $this->addPrimarySystem(new StdParticleBeam(4, 4, 1, 0, 360));
        $this->addPrimarySystem(new JumpEngine(4, 20, 4, 30));
   

        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1)); 
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));

//		$TargeterA = new ProximityLaser(3, 0, 0, 300, 60, 'A');
		$TargeterA = new ProximityLaser(3, 0, 0, 0, 360, 'A');
		$LauncherA = new ProximityLaserLauncher(0, 1, 0, 300, 60, 'A'); 
		$TargeterA->addLauncher($LauncherA);
        $this->addFrontSystem(new AmmoMissileRackF(3, 0, 0, 240, 60, $ammoMagazine, false));   
		$this->addFrontSystem($LauncherA);
		$TargeterA->addTag("Front Proximity Laser");		

//		$TargeterB = new ProximityLaser(3, 0, 0, 300, 60, 'B');
		$TargeterB = new ProximityLaser(3, 0, 0, 0, 360, 'B');		
		$LauncherB = new ProximityLaserLauncher(0, 1, 0, 300, 60, 'B'); 
		$TargeterB->addLauncher($LauncherB);
		$this->addFrontSystem($TargeterA);
		$this->addFrontSystem($LauncherB);
		$TargeterB->addTag("Front Proximity Laser");		

//		$TargeterC = new ProximityLaser(3, 0, 0, 300, 60, 'C');
		$TargeterC = new ProximityLaser(3, 0, 0, 0, 360, 'C');		
		$LauncherC = new ProximityLaserLauncher(0, 1, 0, 300, 60, 'C'); 
		$TargeterC->addLauncher($LauncherC);
        $this->addFrontSystem(new AmmoMissileRackF(3, 0, 0, 300, 120, $ammoMagazine, false));
		$this->addFrontSystem($LauncherC);
		$this->addFrontSystem($TargeterB);		
		$this->addFrontSystem($TargeterC);
		$TargeterC->addTag("Front Proximity Laser");		            

        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new AmmoMissileRackD(2, 0, 0, 120, 300, $ammoMagazine, false));
        $this->addAftSystem(new AmmoMissileRackD(2, 0, 0, 60, 240, $ammoMagazine, false));

        $this->addLeftSystem(new AmmoMissileRackD(4, 0, 0, 240, 60, $ammoMagazine, false));

//		$TargeterD = new ProximityLaser(3, 0, 0, 240, 60, 'D');
		$TargeterD = new ProximityLaser(3, 0, 0, 0, 360, 'D');		
		$LauncherD = new ProximityLaserLauncher(0, 1, 0, 240, 60, 'D'); 
		$TargeterD->addLauncher($LauncherD);
		$this->addLeftSystem($TargeterD);
		$this->addLeftSystem($LauncherD);        
        $this->addLeftSystem(new AmmoMissileRackF(3, 0, 0, 180, 360, $ammoMagazine, false));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new AmmoMissileRackD(4, 0, 0, 300, 120, $ammoMagazine, false));

//		$TargeterE = new ProximityLaser(3, 0, 0, 300, 120, 'E');
		$TargeterE = new ProximityLaser(3, 0, 0, 0, 360, 'E');		
		$LauncherE = new ProximityLaserLauncher(0, 1, 0, 300, 120, 'E'); 
		$TargeterE->addLauncher($LauncherE);
		$this->addRightSystem($TargeterE);
		$this->addRightSystem($LauncherE);
        $this->addRightSystem(new AmmoMissileRackF(3, 0, 0, 0, 180, $ammoMagazine, false));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 48));
        $this->addAftSystem(new Structure(5, 45));
        $this->addLeftSystem(new Structure(5, 55));
        $this->addRightSystem(new Structure(5, 55));
        $this->addPrimarySystem(new Structure(6, 60));
		
		$this->hitChart = array(
			0=> array(
					6 => "Structure",
					8 => "Reload Rack",
					10 => "Standard Particle Beam",
					12 => "Jump Engine",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "TAG:Front Proximity Laser",
					10 => "Class-F Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Class-D Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Class-F Missile Rack",
					9 => "Proximity Laser D",
					11 => "Class-D Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Class-F Missile Rack",
					9 => "Proximity Laser E",
					11 => "Class-D Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
