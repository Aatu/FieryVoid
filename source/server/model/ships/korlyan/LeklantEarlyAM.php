<?php
class LeklantEarlyAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 690;
		$this->faction = "Kor-Lyan Kingdoms";
//	$this->faction = "Custom Ships";
        $this->phpclass = "LeklantEarlyAM";
        $this->imagePath = "img/ships/korlyan_leklant.png";
        $this->shipClass = "Leklant Scout Cruiser (Early)";
			$this->occurence = "common";
			$this->variantOf = 'Leklant Scout Cruiser';
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side
		$this->unofficial = true; 

        $this->limited = 33;

		$this->isd = 2222;
        $this->fighters = array("assault shuttles"=>4);

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;

	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(160); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles 
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 120); //add full load of basic missiles  	      

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
        
        $this->addPrimarySystem(new Reactor(5, 21, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 20, 7, 9));
        $this->addPrimarySystem(new Engine(5, 18, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(4, 4));
        $this->addPrimarySystem(new JumpEngine(4, 20, 4, 30));
   
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new AmmoMissileRackD(2, 0, 0, 240, 60, $ammoMagazine, false));
        $this->addFrontSystem(new LimpetBoreTorpedo(3, 0, 0, 300, 60));
        $this->addFrontSystem(new AmmoMissileRackD(2, 0, 0, 300, 120, $ammoMagazine, false));

        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new AmmoMissileRackD(3, 0, 0, 120, 300, $ammoMagazine, false));
        $this->addAftSystem(new AmmoMissileRackD(3, 0, 0, 60, 240, $ammoMagazine, false));

        $this->addLeftSystem(new AmmoMissileRackD(2, 0, 0, 240, 60, $ammoMagazine, false));
        $this->addLeftSystem(new AmmoMissileRackL(3, 0, 0, 180, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 360));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 360));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new AmmoMissileRackD(2, 0, 0, 300, 120, $ammoMagazine, false));
        $this->addRightSystem(new AmmoMissileRackL(3, 0, 0, 0, 180, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 180));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 40));
        $this->addAftSystem(new Structure(5, 39));
        $this->addLeftSystem(new Structure(5, 45));
        $this->addRightSystem(new Structure(5, 45));
        $this->addPrimarySystem(new Structure(5, 48));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Jump Engine",
					14 => "ELINT Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					6 => "Limpet Bore Torpedo",
					9 => "Class-D Missile Rack",
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
					7 => "Class-L Missile Rack",
					9 => "Class-D Missile Rack",
					11 => "Standard Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Class-L Missile Rack",
					9 => "Class-D Missile Rack",
					11 => "Standard Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
