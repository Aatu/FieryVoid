<?php
class TrylkanAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
	
		$this->pointCost = 525;
		$this->faction = "Kor-Lyan Kingdoms";
        $this->phpclass = "TrylkanAM";
        $this->imagePath = "img/ships/korlyan_solyrn2.png";
        $this->shipClass = "Trylkan Ballistic Destroyer";
			$this->occurence = "uncommon";
			$this->variantOf = 'OBSELETE';			
        $this->limited = 10;
	    $this->isd = 2251;

		$this->canvasSize = 130; 

	    $this->notes = 'Atmospheric Capable.';
        $this->fighters = array("assault shuttles"=>2);
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.75;
        $this->turndelaycost = 0.75;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;
        
		$this->IFFSystem = false; 
      
	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(144); //pass magazine capacity. 80 Intercept and up to 64 Mines 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 80); //add full load of Interceptor missiles  	      
	    $ammoMagazine->addAmmoEntry(new AmmoBLMineB(), 0); //add full load of basic missiles
//	    $ammoMagazine->addAmmoEntry(new AmmoBLMineW(), 0); //add full load of basic missiles 
//	    $ammoMagazine->addAmmoEntry(new AmmoBLMineH(), 0); //add full load of basic missiles 

	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
		$this->enhancementOptionsEnabled[] = 'MINE_BLB';//add enhancement options for mines - Basic Mines
		$this->enhancementOptionsEnabled[] = 'MINE_BLW';//add enhancement options for mines - Wide-Range Mines
		$this->enhancementOptionsEnabled[] = 'MINE_BLH';//add enhancement options for mines - Wide-Range Mines
		$this->enhancementOptionsEnabled[] = 'IFF_SYS'; //Abilty to choose IFF enhancement.		 	  
         
        $this->addPrimarySystem(new Reactor(4, 11, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 5, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 9, 2));
        $this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 4));        
        
        
        $this->addFrontSystem(new Thruster(4, 18, 0, 6, 1));
        $this->addFrontSystem(new AmmoMissileRackD(2, 0, 0, 240, 60, $ammoMagazine, false));
		$this->addFrontSystem(new BallisticMineLauncher(3, 0, 0, 300, 60, $ammoMagazine, false));
		$this->addFrontSystem(new BallisticMineLauncher(3, 0, 0, 300, 60, $ammoMagazine, false));
		
//		$TargeterA = new ProximityLaser(3, 0, 0, 240, 60, 'A');
		$TargeterA = new ProximityLaser(3, 0, 0, 0, 360, 'A');
		$LauncherA = new ProximityLaserLauncher(0, 1, 0, 240, 60, 'A'); 
		$TargeterA->addLauncher($LauncherA);
		$this->addFrontSystem($TargeterA);		  
		$this->addFrontSystem($LauncherA);		
		$TargeterA->addTag("Front Proximity Laser");		

//		$TargeterB = new ProximityLaser(3, 0, 0, 300, 120, 'B');
		$TargeterB = new ProximityLaser(3, 0, 0, 0, 360, 'B');		
		$LauncherB = new ProximityLaserLauncher(0, 1, 0, 300, 120, 'B'); 
		$TargeterB->addLauncher($LauncherB);
		$this->addFrontSystem($TargeterB);		
		$this->addFrontSystem($LauncherB);		
		$TargeterB->addTag("Front Proximity Laser");
		
        $this->addFrontSystem(new AmmoMissileRackD(2, 0, 0, 300, 120, $ammoMagazine, false));//$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

		
        $this->addAftSystem(new AmmoMissileRackD(2, 0, 0, 120, 300, $ammoMagazine, false));//$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addAftSystem(new BallisticMineLauncher(3, 0, 0, 120, 300, $ammoMagazine, false));
		$this->addAftSystem(new BallisticMineLauncher(3, 0, 0, 60, 240, $ammoMagazine, false));		
        $this->addAftSystem(new AmmoMissileRackD(2, 0, 0, 60, 240, $ammoMagazine, false));//$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 4, 48));
        
        
        $this->hitChart = array(
                0=> array(
                        7 => "Structure",
                        9 => "Thruster",
						11 => "Reload Rack",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Ballistic Mine Launcher",
                        8 => "TAG:Front Proximity Laser",
                        10 => "Class-D Missile Rack",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Ballistic Mine Launcher",
                        10 => "Class-D Missile Rack",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }

}



?>
