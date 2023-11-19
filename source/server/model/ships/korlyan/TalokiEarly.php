<?php
class TalokiEarly extends StarBaseSixSections{

	function __construct($id, $userid, $name,  $slot){
		parent::__construct($id, $userid, $name,  $slot);

		$this->pointCost = 3400;
		$this->faction = "Kor-Lyan Kingdoms";
		$this->phpclass = "TalokiEarly";
		$this->shipClass = "Taloki Starbase (2220)";
		$this->fighters = array("assault shuttles"=>4, "normal"=>24); 

        $this->isd = 2220;
		$this->shipSizeClass = 3; //Enormous is not implemented
        $this->Enormous = true;
		$this->iniativebonus = -200; //no voluntary movement anyway
		$this->turncost = 0;
		$this->turndelaycost = 0;
		$this->nonRotating = true; //some bases do not rotate - this attribute is used in combination with $base or $smallBase

		$this->forwardDefense = 21;
		$this->sideDefense = 24;

		$this->imagePath = "img/ships/korlyan_taloki.png";
		$this->canvasSize = 260; //Enormous Starbase

	//ammo magazine itself (AND its missile options)
	$ammoMagazine = new AmmoMagazine(400); //pass magazine capacity 
	    $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
	    $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 320); //add full load of basic missiles 
	    $ammoMagazine->addAmmoEntry(new AmmoMissileI(), 80); //add full load of basic missiles  	      

	    $this->enhancementOptionsEnabled[] = 'AMMO_A';//add enhancement options for other missiles - Class-A
	    $this->enhancementOptionsEnabled[] = 'AMMO_C';//add enhancement options for other missiles - Class-C
	    $this->enhancementOptionsEnabled[] = 'AMMO_F';//add enhancement options for other missiles - Class-F
	    $this->enhancementOptionsEnabled[] = 'AMMO_H';//add enhancement options for other missiles - Class-H
	    $this->enhancementOptionsEnabled[] = 'AMMO_K';//add enhancement options for other missiles - Class-K   
	    $this->enhancementOptionsEnabled[] = 'AMMO_L';//add enhancement options for other missiles - Class-L
		$this->enhancementOptionsEnabled[] = 'AMMO_P';//add enhancement options for other missiles - Class-P	    	    	    	    
	    $this->enhancementOptionsEnabled[] = 'AMMO_S';//add enhancement options for other missiles - Class-S

		$this->locations = array(41, 42, 2, 32, 31, 1);

		$this->addPrimarySystem(new Reactor(4, 25, 0, 0));
		$this->addPrimarySystem(new CnC(4, 32, 0, 0)); 
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new Scanner(4, 16, 4, 6));
		$this->addPrimarySystem(new CargoBay(4, 75));
		$this->addPrimarySystem(new CargoBay(4, 75));
		$this->addPrimarySystem(new ReloadRack(4, 9));
		$this->addPrimarySystem(new ReloadRack(4, 9));
		$this->addPrimarySystem(new ReloadRack(4, 9));
        $this->addPrimarySystem(new AmmoMissileRackD(4, 0, 0, 0, 360, $ammoMagazine, false));
        $this->addPrimarySystem(new AmmoMissileRackD(4, 0, 0, 0, 360, $ammoMagazine, false));
        $this->addPrimarySystem(new AmmoMissileRackD(4, 0, 0, 0, 360, $ammoMagazine, false));
        $this->addPrimarySystem(new AmmoMissileRackD(4, 0, 0, 0, 360, $ammoMagazine, false));

		$this->addFrontSystem(new Hangar(4, 14));
		$this->addFrontSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		$this->addFrontSystem(new ProximityLaser(4, 6, 6, 270, 90));
		$this->addFrontSystem(new LimpetBoreBase(4, 5, 3, 270, 90));
		$this->addFrontSystem(new ProximityLaser(4, 6, 6, 270, 90));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 270, 90));
		
		$this->addAftSystem(new Hangar(4, 14));
		$this->addAftSystem(new SubReactorUniversal(4, 20, 0, 0));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		$this->addAftSystem(new ProximityLaser(4, 6, 6, 90, 270));
		$this->addAftSystem(new LimpetBoreBase(4, 5, 3, 90, 270));
		$this->addAftSystem(new ProximityLaser(4, 6, 6, 90, 270));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 90, 270));
		
		$this->addLeftFrontSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new StdParticleBeam(4, 4, 1, 240, 60));
        $this->addLeftFrontSystem(new AmmoMissileRackL(4, 0, 0, 240, 60, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftFrontSystem(new AmmoMissileRackL(4, 0, 0, 240, 60, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftFrontSystem(new AmmoMissileRackL(4, 0, 0, 240, 60, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftFrontSystem(new AmmoMissileRackL(4, 0, 0, 240, 60, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

		$this->addLeftAftSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new StdParticleBeam(4, 4, 1, 120, 300));
        $this->addLeftAftSystem(new AmmoMissileRackL(4, 0, 0, 120, 300, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftAftSystem(new AmmoMissileRackL(4, 0, 0, 120, 300, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftAftSystem(new AmmoMissileRackL(4, 0, 0, 120, 300, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftAftSystem(new AmmoMissileRackL(4, 0, 0, 120, 300, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

		$this->addRightFrontSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new StdParticleBeam(4, 4, 1, 300, 120));
        $this->addRightFrontSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightFrontSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightFrontSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightFrontSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

		$this->addRightAftSystem(new SubReactorUniversal(4, 18, 0, 0));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new StdParticleBeam(4, 4, 1, 60, 240));
        $this->addRightAftSystem(new AmmoMissileRackL(4, 0, 0, 60, 240, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightAftSystem(new AmmoMissileRackL(4, 0, 0, 60, 240, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightAftSystem(new AmmoMissileRackL(4, 0, 0, 60, 240, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightAftSystem(new AmmoMissileRackL(4, 0, 0, 60, 240, $ammoMagazine, true)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 150));
        $this->addAftSystem(new Structure( 4, 136));
        $this->addLeftFrontSystem(new Structure( 4, 180));
        $this->addLeftAftSystem(new Structure( 4, 180));
        $this->addRightFrontSystem(new Structure( 4, 180));
        $this->addRightAftSystem(new Structure( 4, 180));        
		$this->addPrimarySystem(new Structure( 4, 182));

	//d20 hit chart
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
					11 => "Reload Rack",
					12 => "Class-D Missile Rack",
                    14 => "Scanner",
					18 => "Cargo Bay",
                    19 => "Reactor",
                    20 => "C&C",
           		 ),
            1=> array(
                    2 => "Base Limpet Bore Torpedo",
					4 => "Proximity Laser",
					6 => "Particle Cannon",
					7 => "Hangar",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            2=> array(
                    2 => "Base Limpet Bore Torpedo",
					4 => "Proximity Laser",
					6 => "Particle Cannon",
					7 => "Hangar",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            31=> array(
                    3 => "Standard Particle Beam",
                    7 => "Class-L Missile Rack",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            32=> array(
                    3 => "Standard Particle Beam",
                    7 => "Class-L Missile Rack",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
            41=> array(
                    3 => "Standard Particle Beam",
                    7 => "Class-L Missile Rack",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
           		 ),
       		42=> array(
                    3 => "Standard Particle Beam",
                    7 => "Class-L Missile Rack",
					8 => "Sub Reactor",
                    18 => "Structure",
                    20 => "Primary",
            	),
           	);

    }
}
