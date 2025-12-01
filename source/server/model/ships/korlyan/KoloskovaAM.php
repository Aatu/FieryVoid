<?php
class KoloskovaAM extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 325;
		$this->faction = "Kor-Lyan Kingdoms";
        $this->phpclass = "KoloskovaAM";
        $this->imagePath = "img/ships/korlyan_koloskova.png";
        $this->shipClass = "Koloskova Auxiliary Cruiser";
        $this->canvasSize = 85;
 		$this->unofficial = 'S'; //design released after AoG demise
	    
	    $this->isd = 1972;
        $this->fighters = array("assault shuttles"=>1);

	    $this->notes = 'Antiquated Sensors (cannot be boosted).';
	    $this->notes .= '<br>Atmospheric Capable';
		$this->notes .= "Showdowns-9";        
        
        $this->forwardDefense = 13;
        $this->sideDefense = 14;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 55;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(24); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 24); //add full load of basic missiles
        
	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H    
	    $this->enhancementOptionsEnabled[] = 'AMMO_I';//add enhancement options for other missiles - Class-I
//	    $this->enhancementOptionsEnabled[] = 'AMMO_J';//add enhancement options for other missiles - Class-J	     
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P    	    	    	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X		    	    	    	    
	    //$this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S
		//Stealth missile removed from Early Kor-Lyan ships, as it's not availablee until 2252 
         
        $this->addPrimarySystem(new Reactor(5, 8, 0, 0));
        $this->addPrimarySystem(new CnC(5, 5, 0, 0));
        $this->addPrimarySystem(new AntiquatedScanner(5, 10, 3, 4));
        $this->addPrimarySystem(new Engine(5, 9, 0, 6, 4));
		$this->addPrimarySystem(new Hangar(5, 1));
		$this->addPrimarySystem(new Thruster(3, 8, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 8, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new LightParticleBolt(1, 3, 1, 270, 90));
        $this->addFrontSystem(new AmmoMissileRackSO(2, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackSO(2, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new LightParticleCannon(3, 6, 5, 300, 60));
		
        $this->addAftSystem(new Thruster(3, 10, 0, 6, 2));
        $this->addAftSystem(new LightParticleBolt(1, 3, 1, 90, 270));
	
        $this->addPrimarySystem(new Structure( 3, 50));
        
		$this->hitChart = array(
                0=> array(
                        8 => "Thruster",
                        10 => "Hangar",
						13 => "Scanner",
                        16 => "Engine",
                        18 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
						6 => "Class-SO Missile Rack",
						8 => "Light Particle Cannon",
                        9 => "Light Particle Bolt",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        4 => "Thruster",
                        6 => "Light Particle Bolt",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
