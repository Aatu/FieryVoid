<?php
class KalavarOriginalAM extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 210;
		$this->faction = "Kor-Lyan";
        $this->phpclass = "KalavarOriginalAM";
        $this->imagePath = "img/ships/korlyan_kalavar.png";
        $this->shipClass = "Kalavar Orbital Satellite (2194)";
			$this->occurence = "common";
			$this->variantOf = 'Kalavar Orbital Satellite (2240)';
        $this->isd = 2194;
 		$this->unofficial = 'S'; //design released after AoG demise
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60; 

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(60); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 60); //add full load of basic missiles
        
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P	    	    	    	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S	        

        $this->addPrimarySystem(new Reactor(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 5, 2, 4)); 

        $this->addAftSystem(new Thruster(4, 6, 0, 0, 2));
        
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 0, 360, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackS(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 30));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "2:Thruster",
		    		16 => "1:Class-L Missile Rack",
                    18 => "Scanner",
                    20 => "Reactor",
            )
        );
        
    }
}
?>
