<?php

class triadBanshee extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 3000;
        $this->faction = "The Triad";
        $this->phpclass = "triadBanshee";
        $this->imagePath = "img/ships/triadBanshee.png";
        $this->shipClass = "Neutrality: Banshee";
	    $this->isd = 'Primordial';
        $this->shipSizeClass = 2; 
		$this->factionAge = 4; //1 - Young, 2 - Middleborn, 3 - Ancient, 4 - Primordial

        $this->gravitic = true;
		$this->advancedArmor = true;  

        $this->forwardDefense = 15;
        $this->sideDefense = 13;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 2;
        $this->iniativebonus = 40;

		$this->fighters = array("Triad Fighter"=>6);

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 20 rounds per launcher, plus reload rack 80
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
        
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
	    $this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X		    	    	    	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S
//		$this->enhancementOptionsEnabled[] = 'AMMO_HM';//add enhancement options for other missiles - Class-HM

		/*Triad use their own enhancement set */		
		Enhancements::nonstandardEnhancementSet($this, 'TriadShip');
         
        $this->addPrimarySystem(new Reactor(7, 24, 0, 0));
        $this->addPrimarySystem(new CnC(8, 16, 0, 0));
		$scanner = new Scanner(8, 24, 0, 13);
			$scanner->markAdvanced();
			$this->addPrimarySystem($scanner);			
		$this->addPrimarySystem(new Engine(7, 25, 0, 16, 4));
        $this->addPrimarySystem(new StructureSelfRepair(7, 20, 9)); //armor, structure, output
        $this->addPrimarySystem(new AdvParticleBlastGun(7, 16, 7, 0, 360));	
        $this->addPrimarySystem(new GraviticThruster(7, 20, 0, 9, 3)); 		
        $this->addPrimarySystem(new GraviticThruster(7, 20, 0, 9, 4)); 				

        $this->addFrontSystem(new GraviticThruster(6, 13, 0, 5, 1));
        $this->addFrontSystem(new GraviticThruster(6, 13, 0, 5, 1));
        $this->addFrontSystem(new AdvParticleBlastGun(6, 16, 8, 240, 360));	
        $this->addFrontSystem(new AmmoMissileRackTriad(7, 6, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackTriad(7, 6, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AsteroidSalvo(8, 30, 10, 270, 90));	
        $this->addFrontSystem(new AmmoMissileRackTriad(7, 6, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AmmoMissileRackTriad(7, 6, 0, 240, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addFrontSystem(new AdvParticleBlastGun(6, 16, 8, 0, 120));	
        
        $this->addAftSystem(new GraviticThruster(6, 13, 0, 5, 2));
        $this->addAftSystem(new GraviticThruster(6, 15, 0, 6, 2));
        $this->addAftSystem(new GraviticThruster(6, 13, 0, 5, 2));
        $this->addAftSystem(new AmmoMissileRackTriad(7, 6, 0, 150, 30, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackTriad(7, 6, 0, 60, 300, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AdvParticleBlastGun(6, 16, 8, 120, 240));	
		$this->addAftSystem(new JumpEngine(7, 20, 6, 8));        
        $this->addAftSystem(new SelfRepair(7, 6, 3)); //armor, structure, output
        $this->addAftSystem(new AmmoMissileRackTriad(7, 6, 0, 60, 300, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackTriad(7, 6, 0, 330, 210, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 7, 63));
        $this->addAftSystem(new Structure( 7, 60));
        $this->addPrimarySystem(new Structure( 8, 60));

        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    11 => "Thruster",
					12 => "Structure Self Repair",
                    14 => "Scanner",
                    16 => "Engine",
					17 => "Advanced Particle Blast Gun",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    4 => "Thruster",
                    8 => "Triad Missile Rack",
					10 => "Asteroid Salvo",
					12 => "Advanced Particle Blast Gun",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					7 => "Self Repair",
                    10 => "Triad Missile Rack",
					11 => "Advanced Particle Blast Gun",
                    13 => "Jump Engine",
                    18 => "Structure",
                    20 => "Primary",
            ),
      );
    }
}

?>
