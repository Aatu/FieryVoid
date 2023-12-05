<?php
class Kalavar extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 250;
        $this->faction = "Custom Ships";
	        $this->variantOf = 'OBSOLETE'; //awaiting all games it's used in, then is to be removed from active ships list
        $this->phpclass = "Kalavar";
        $this->imagePath = "img/ships/korlyan_kalavar.png";
        $this->shipClass = "Kalavar Orbital Satellite (2240)";
        $this->isd = 2240;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60; 

	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(20); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 20); //add full load of basic missiles  	      

	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    
        $this->addPrimarySystem(new Reactor(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 5, 2, 4)); 

//      $this->addAftSystem(new ProximityLaser(3, 6, 6, 180, 360));
		$TargeterA = new ProximityLaser(0, 1, 0, 180, 360, 'A');
		$LauncherA = new ProximityLaserLauncher(3, 0, 0, 180, 360, 'A'); 
		$TargeterA->addLauncher($LauncherA);
		$this->addAftSystem($TargeterA);
		$this->addAftSystem($LauncherA);
        $this->addAftSystem(new Thruster(4, 6, 0, 0, 2));
//        $this->addAftSystem(new ProximityLaser(3, 6, 6, 0, 180));
		$TargeterB = new ProximityLaser(0, 1, 0, 0, 180, 'B');
		$LauncherB = new ProximityLaserLauncher(3, 0, 0, 0, 180, 'B'); 
		$TargeterB->addLauncher($LauncherB);
		$this->addAftSystem($TargeterB);
		$this->addAftSystem($LauncherB);	
        
        $this->addFrontSystem(new RangedFMissileRack(3, 6, 0, 270, 90, true)); 
        $this->addFrontSystem(new AmmoMissileRackD(2, 0, 0, 0, 360, $ammoMagazine, false));
        $this->addFrontSystem(new RangedFMissileRack(3, 6, 0, 270, 90, true)); 
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 30));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    11 => "2:Thruster",
		    		13 => "1:Stabilized Class-F Missile Rack",
		    		15 => "2:Proximity Laser",
                    17 => "Scanner",
                    19 => "Reactor",
                    20 => "1:Class-D Missile Rack",
            )
        );
        
    }
}
?>
