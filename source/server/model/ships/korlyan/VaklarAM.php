<?php
class VaklarAM extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Kor-Lyan Kingdoms";
        $this->phpclass = "VaklarAM";
        $this->imagePath = "img/ships/korlyan_vaklar2.png";
        $this->shipClass = "Vaklar Logistics Frigate";
	    $this->isd = 2208;
		$this->canvasSize = 130;
 		$this->unofficial = 'S'; //design released after AoG demise

	    $this->notes = 'Atmospheric Capable.';
		$this->notes .= "<br>Official Vaklar Logistics Frigate with Ballistic Mine replaced by Proximity Laser"; 
        
        $this->forwardDefense = 12;
        $this->sideDefense = 12;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
        $this->iniativebonus = 20;
        
      
	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(80); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles 
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 40); //add full load of basic missiles  	      

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
	    //$this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S
		//Stealth missile removed from Early Kor-Lyan ships, as it's not available until 2252

        
        $this->addPrimarySystem(new Reactor(4, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 5, 6));
        $this->addPrimarySystem(new Engine(4, 15, 0, 8, 2));
        $this->addPrimarySystem(new ReloadRack(3, 9));
        $this->addPrimarySystem(new ReloadRack(3, 9));
        $this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addPrimarySystem(new AmmoMissileRackS(3, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addPrimarySystem(new CargoBay(3, 54));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 13, 0, 4, 4));        
        
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new AmmoMissileRackD(2, 0, 0, 240, 60, $ammoMagazine, false));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
//		$this->addFrontSystem(new ProximityLaser(3, 6, 6, 300, 60));
		$TargeterA = new ProximityLaser(0, 1, 0, 300, 60, 'A');
		$LauncherA = new ProximityLaserLauncher(3, 0, 0, 300, 60, 'A'); 
		$TargeterA->addLauncher($LauncherA);
		$this->addFrontSystem($TargeterA);
		$this->addFrontSystem($LauncherA);		 
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120));
        $this->addFrontSystem(new AmmoMissileRackD(2, 0, 0, 300, 120, $ammoMagazine, false));
		
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 300));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 240));
        $this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addPrimarySystem(new Structure( 4, 36));
        
        
        $this->hitChart = array(
                0=> array(
                        4 => "Structure",
                        6 => "Thruster",
						8 => "Cargo Bay",
						10 => "Class-S Missile Rack",
						12 => "Reload Rack",
                        14 => "Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
						7 => "Standard Particle Beam",
                        9 => "Class-D Missile Rack",
						10 => "Proxmity Laser",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        10 => "Standard Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }

}



?>
