<?php
class TechnicalTestbed extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 325;
		$this->faction = "Custom Ships";
        $this->phpclass = "TechnicalTestbed";
        $this->shipClass = "New Technology Testbed";
        $this->imagePath = "img/ships/olympus.png";
        $this->isd = 0;
        //$this->canvasSize = 200;
	    
	    
        $this->forwardDefense = 15;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        $this->trueStealth = true;
        $this->canPreOrder = true;

		$this->advancedArmor = true;   
		$this->hardAdvancedArmor = true;   
        
         
        $this->addPrimarySystem(new Reactor(6, 20, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 16, 4, 7));
        $this->addPrimarySystem(new Engine(6, 15, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(6, 2));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 5, 4)); 
        $this->addPrimarySystem(new CloakingDevice(4, 13, 10, 5));         
	    
	//ammo magazine itself
	$ammoMagazine = new AmmoMagazine(6); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 200); //add full load of basic missiles
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P
        
        
        $this->addFrontSystem(new Thruster(6, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(6, 8, 0, 3, 1));
		
        $this->addFrontSystem(new MediumPulse(2, 6, 3, 240, 0));
        $this->addFrontSystem(new MediumPulse(2, 6, 3, 240, 0));
        $this->addFrontSystem(new MediumPulse(2, 6, 3, 0, 120));
        $this->addFrontSystem(new MediumPulse(2, 6, 3, 0, 120));
		
        $this->addFrontSystem(new InterceptorMkI(1, 4, 1, 270, 90));
        $this->addFrontSystem(new RailGun(4, 9, 6, 0, 0));
		
        //$this->addAftSystem(new RailGun(4, 9, 6, 0, 0));   
//        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
	    //new launchers - using ammo	    
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 0, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
	    
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 8, 48));
        $this->addAftSystem(new Structure( 6, 500));
        $this->addPrimarySystem(new Structure( 6, 50));
        
        
        $this->hitChart = array(
                0=> array(
                        8 => "Structure",
                        11 => "Thruster",
                        13 => "Scanner",
                        15 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        5 => "Medium Pulse Cannon",
                        7 => "Railgun",
                        9 => "Interceptor MK I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
						20 => "Structure",
 /*                       6 => "Thruster",
                        8 => "Class-S Missile Rack",
                        10 => "Railgun",
                        12 => "Interceptor MK I",
                        18 => "Structure",
                        20 => "Primary", */
                ),
        );
    }

}



?>
