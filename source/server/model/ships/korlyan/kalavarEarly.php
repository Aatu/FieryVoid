<?php
class KalavarEarly extends OSAT{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 240;
		$this->faction = "Kor-Lyan";
        $this->phpclass = "KalavarEarly";
        $this->imagePath = "img/ships/korlyan_kalavar.png";
        $this->shipClass = "Kalavar Orbital Satellite (early)";
			$this->occurence = "common";
			$this->variantOf = 'Kalavar Orbital Satellite';
        $this->isd = 2216;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 10;
        
        $this->turncost = 0;
        $this->turndelaycost = 0;
        $this->accelcost = 0;
        $this->rollcost = 0;
        $this->pivotcost = 0;	
        $this->iniativebonus = 60; 

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
        $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-L
        $this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P

        $this->addPrimarySystem(new Reactor(3, 5, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 5, 2, 4)); 

        $this->addAftSystem(new ProximityLaser(3, 6, 6, 180, 360));
        $this->addAftSystem(new Thruster(4, 6, 0, 0, 2));
        $this->addAftSystem(new ProximityLaser(3, 6, 6, 0, 180));
        
        $this->addFrontSystem(new AmmoMissileRackL(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new MultiDefenseLauncher(2, 'D', 0, 360, true));
        $this->addFrontSystem(new AmmoMissileRackL(3, 0, 0, 270, 90, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        
        $this->addPrimarySystem(new Structure(4, 30));
        
        	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    11 => "2:Thruster",
		    		13 => "1:Class-L Missile Rack",
		    		15 => "2:Proximity Laser",
                    17 => "Scanner",
                    19 => "Reactor",
                    20 => "1:Class-D Missile Launcher",
            )
        );
        
    }
}