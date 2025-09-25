<?php
class ApolloWSF extends BaseShip{

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);

        $this->pointCost = 800;
        $this->faction = "Earth Alliance (Custom)";
        $this->phpclass = "ApolloWSF";
        $this->imagePath = "img/ships/apollo.png";
        $this->shipClass = "Apollo Bombardment Cruiser (Gamma)";
        $this->shipSizeClass = 3;
        $this->canvasSize = 200;
        $this->limited = 33;
  	    $this->isd = 2263;
	    $this->notes .= "<br>This is a custom ship only intended for use in a specific scenario, please do not use unless you've agreed it with your opponent first!";  	            

        $this->forwardDefense = 16;
        $this->sideDefense = 17;

        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        //ammo magazine itself (AND its missile options)
        $ammoMagazine = new AmmoMagazine(240); //pass magazine capacity - 12 rounds per class-SO rack, 20 most other shipborne racks, 60 class-B rack and 80 Reload Rack
        $this->addPrimarySystem($ammoMagazine); //fit to ship immediately
        $ammoMagazine->addAmmoEntry(new AmmoMissileB(), 240); //add full load of basic missiles
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
		
        $this->addPrimarySystem(new ReloadRack(5, 9));
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 4));
        $this->addPrimarySystem(new JumpEngine(5, 20, 5, 24));

        $this->addFrontSystem(new Thruster(4, 10, 0, 2, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 2, 1));

        $this->addFrontSystem(new MassDriver(5, 18, 9, 330, 30));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 270, 90));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 270, 90));

        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
        $this->addAftSystem(new AmmoMissileRackL(4, 0, 0, 120, 300, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addAftSystem(new AmmoMissileRackL(4, 0, 0, 60, 240, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base


        $this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));
        $this->addLeftSystem(new AmmoMissileRackL(4, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new AmmoMissileRackLH(4, 0, 0, 240, 60, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
    	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
    	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));

        $this->addRightSystem(new Thruster(4, 15, 0, 6, 4));
        $this->addRightSystem(new AmmoMissileRackL(4, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new AmmoMissileRackLH(4, 0, 0, 300, 120, $ammoMagazine, false)); //$armour, $health (0=auto), $power (0=auto), $startArc, $endArc, $magazine, $base
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
    	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
    	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 50));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 5, 56));

    
        $this->hitChart = array(
            0=> array(
                    8 => "Structure",
                    10 => "Reload Rack",
                    12 => "Jump Engine",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    6 => "Thruster",
                    8 => "Mass Driver",
                    11 => "Interceptor II",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    8 => "Class-L Missile Rack",
                    11 => "Interceptor II",
                    18 => "Structure",
                    20 => "Primary",
            ),
            3=> array(
                    4 => "Thruster",
                    6 => "Class-LH Missile Rack",
                    8 => "Class-L Missile Rack",
                    10 => "Standard Particle Beam",
                    12 => "Interceptor II",
                    18 => "Structure",
                    20 => "Primary",
            ),
            4=> array(
                    4 => "Thruster",
                    6 => "Class-LH Missile Rack",
                    8 => "Class-L Missile Rack",
                    10 => "Standard Particle Beam",
                    12 => "Interceptor II",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}

?>
