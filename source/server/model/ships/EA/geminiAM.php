<?php
class GeminiAM extends BaseShip{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);       
		$this->pointCost = 1025;
        $this->faction = "Earth Alliance (Custom)";
		$this->phpclass = "GeminiAM";
		$this->shipClass = "Gemini Fleet Destroyer";
		$this->shipSizeClass = 3;
		$this->imagePath = "img/ships/gemini.png";
		$this->canvasSize = 200;
		
		$this->fighters = array("heavy"=>6);
		
      	$this->occurence = "common";
      	$this->isd = 2272;		
      	$this->unofficial = true;
		
		$this->forwardDefense = 15;
		$this->sideDefense = 17;
		$this->turncost = 0.8;
		$this->turndelaycost = 0.8;
		$this->accelcost = 3;
		$this->rollcost = 2;
		$this->pivotcost = 3; 
		

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(40); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 40); //add full load of basic missiles
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
		
		
		$this->addPrimarySystem(new Reactor(6, 20, 0, 0));
		$this->addPrimarySystem(new CnC(6, 14, 0, 0));
		$this->addPrimarySystem(new Scanner(6, 16, 4, 8));
		$this->addPrimarySystem(new Engine(6, 17, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(5, 8, 6));
		$this->addPrimarySystem(new JumpEngine(6, 20, 3, 14));
		
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new MLPA(4, 9, 5, 300, 60));
		$this->addFrontSystem(new MLPA(4, 9, 5, 300, 60));
		$this->addFrontSystem(new HvyParticleCannon(5, 12, 9, 330, 30));
		$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
		$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));
		
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
		$this->addAftSystem(new MediumPulse(4, 6, 3, 120, 300)); 
		$this->addAftSystem(new MediumPulse(4, 6, 3, 60, 240));
		$this->addAftSystem(new MLPA(4, 9, 5, 120, 240));
		$this->addAftSystem(new MLPA(4, 9, 5, 120, 240));
		$this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
		$this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
		
		$this->addLeftSystem(new Thruster(4, 14, 0, 5, 3));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
		$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new AmmoMissileRackL(4, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addLeftSystem(new MLPA(4, 9, 5, 240, 0));
		$this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 0)); 
		
		$this->addRightSystem(new Thruster(4, 14, 0, 5, 4));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
		$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
		$this->addRightSystem(new MLPA(4, 9, 5, 0, 120));
		$this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180)); 
		
		$this->addFrontSystem(new Structure( 6, 58));
		$this->addAftSystem(new Structure( 4, 50));
		$this->addLeftSystem(new Structure( 5, 64));
		$this->addRightSystem(new Structure( 5, 64));
		$this->addPrimarySystem(new Structure( 6,60));
		

		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Jump Engine",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Heavy Particle Cannon",
                        9 => "Medium Laser-Pulse Array",
                        11 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Medium Laser-Pulse Array",
                        10 => "Medium Pulse Cannon",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        5 => "Class-L Missile Rack",
						7 => "Medium Laser-Pulse Array",
						10 => "Standard Particle Beam",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        5 => "Class-L Missile Rack",
						7 => "Medium Laser-Pulse Array",
						10 => "Standard Particle Beam",
                        12 => "Interceptor II",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
		
		
    }
}
?>
