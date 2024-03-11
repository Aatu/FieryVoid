<?php
class KoskovaTestbed extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 800;

	$this->faction = "Custom Ships";
        $this->phpclass = "KoskovaTestbed";
        $this->imagePath = "img/ships/korlyan_koskova2.png";
        $this->shipClass = "Koskova Testbed";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side

		$this->isd = 2255;
        $this->fighters = array("assault shuttles"=>2);

	    $this->notes = 'Kor-Lyan Testbed';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 17;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 5;
        $this->rollcost = 1;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(160); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 80); //add full load of Interceptor Missiles for D-Racks-only	    
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 80); //add full load of basic missiles 	     	      

	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P	    	    	    	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S
	    $this->enhancementOptionsEnabled[] = 'AMMO_X';//add enhancement options for other missiles - Class-X	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_I';//add enhancement options for other missiles - Class-I
	    $this->enhancementOptionsEnabled[] = 'AMMO_J';//add enhancement options for other missiles - Class-J	     	    

        $this->addPrimarySystem(new Reactor(6, 25, 0, 1));
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
        
		$Targeter1 = new ProximityLaser(4, 6, 6, 300, 60, 'A'); 
		$Launcher1 = new ProximityLaserLauncher(0, 1, 0, 300, 60, 'A');
		$Targeter1->addLauncher($Launcher1);
		$this->addFrontSystem($Targeter1);
		$this->addFrontSystem($Launcher1);        

		$Targeter2 = new ProximityLaser(4, 6, 6, 300, 60, 'B'); 
		$Launcher2 = new ProximityLaserLauncher(0, 1, 0, 300, 60, 'B');
		$Targeter2->addLauncher($Launcher2);
		$this->addFrontSystem($Targeter2);
		$this->addFrontSystem($Launcher2);  
		
		$Targeter3 = new ProximityLaser(4, 6, 6, 300, 60, 'C'); 
		$Launcher3 = new ProximityLaserLauncher(0, 1, 0, 300, 60, 'C');
		$Targeter3->addLauncher($Launcher3);
		$this->addFrontSystem($Targeter3);
		$this->addFrontSystem($Launcher3);  		        
        
		$this->addFrontSystem(new AmmoMissileRackF(3, 0, 0, 300, 60, $ammoMagazine, false));
        $this->addFrontSystem(new AmmoMissileRackF(3, 0, 0, 300, 60, $ammoMagazine, false));

		$this->addFrontSystem(new LimpetBoreTorpedoBase(2, 5, 3, 270, 90));


        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new AmmoMissileRackD(2, 0, 0, 120, 300, $ammoMagazine, false));
        $this->addAftSystem(new AmmoMissileRackD(2, 0, 0, 60, 240, $ammoMagazine, false));

        $this->addLeftSystem(new AmmoMissileRackD(4, 0, 0, 240, 60, $ammoMagazine, false));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new AmmoMissileRackD(4, 0, 0, 300, 120, $ammoMagazine, false));
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
					7 => "Particle Cannon",
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
					9 => "Proximity Laser",
					11 => "Class-D Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Class-F Missile Rack",
					9 => "Proximity Laser",
					11 => "Class-D Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
