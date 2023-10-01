<?php
class KoshaEarlyAM extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
		$this->faction = "Kor-Lyan";
        $this->phpclass = "KoshaEarlyAM";
        $this->imagePath = "img/ships/korlyan_koshaEarly.png";
        $this->shipClass = "Kosha Light Cruiser (early)";
			$this->occurence = "common";
			$this->variantOf = 'Kosha Light Cruiser';
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side
 		$this->unofficial = 'S'; //design released after AoG demise

		$this->isd = 2190;
        $this->fighters = array("assault shuttles"=>2);

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 1;
        $this->pivotcost = 3;
        $this->iniativebonus = 0;

	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(120); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 60); //add full load of basic missiles 
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 60); //add full load of basic missiles  	      

	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
	    $this->enhancementOptionsEnabled[] = 'AMMO_M';//add enhancement options for other missiles - Class-M	    
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P	    	    	    	    
	    //$this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S
		//Stealth missile removed from Early Kor-Lyan ships, as it's not availablee until 2252
        
        $this->addPrimarySystem(new Reactor(5, 15, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 14, 4, 5));
        $this->addPrimarySystem(new Engine(5, 18, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(4, 2));
   
        $this->addFrontSystem(new Thruster(4, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 2, 1));
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 300, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 60));
        $this->addFrontSystem(new AmmoMissileRackD(2, 0, 0, 270, 90, $ammoMagazine, false));
		$this->addFrontSystem(new ParticleCannon(3, 8, 7, 0, 60));

        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 7, 0, 2, 2));
        $this->addAftSystem(new AmmoMissileRackS(3, 0, 0, 120, 240, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        $this->addLeftSystem(new AmmoMissileRackD(2, 0, 0, 180, 360, $ammoMagazine, false));
        $this->addLeftSystem(new AmmoMissileRackS(3, 0, 0, 240, 360, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));

        $this->addRightSystem(new AmmoMissileRackD(2, 0, 0, 0, 180, $ammoMagazine, false));
        $this->addRightSystem(new AmmoMissileRackS(3, 0, 0, 0, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 44));
        $this->addAftSystem(new Structure(4, 36));
        $this->addLeftSystem(new Structure(4, 36));
        $this->addRightSystem(new Structure(4, 36));
        $this->addPrimarySystem(new Structure(5, 44));
		
		$this->hitChart = array(
			0=> array(
					9 => "Structure",
					12 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					4 => "Thruster",
					7 => "Particle Cannon",
					8 => "Class-D Missile Rack",
					9 => "Standard Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					9 => "Class-S Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					4 => "Thruster",
					7 => "Class-S Missile Rack",
					8 => "Class-D Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					4 => "Thruster",
					7 => "Class-S Missile Rack",
					8 => "Class-D Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
