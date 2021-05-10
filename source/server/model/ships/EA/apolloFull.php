<?php
class ApolloFull extends BaseShip{
/*Apollo with fully decked out Multi-Missile Launchers!*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
//pricing proposal: standard launchers + 25/launcher, improved range additional +10, improved fire rate: double these numbers (so class-S +25, class-L +35, class-R +50, class-LH +70)
        $this->pointCost = 900+6*35+2*70; //900 Apollo, added 35 per L-launcher, plus 70 per LH-/turn launcher
        $this->faction = "EA";
        $this->phpclass = "ApolloFull";
        $this->variantOf = "Apollo Bombardment Cruiser";
        $this->imagePath = "img/ships/apollo.png";
        $this->shipClass = "Apollo Bombardment Cruiser (full)";
        $this->shipSizeClass = 3;
        $this->canvasSize = 200;
        $this->limited = 33;
	    $this->unofficial = true;
  	    $this->isd = 2264;	    

        $this->forwardDefense = 16;
        $this->sideDefense = 17;

        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 3;

        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 5, 8));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
        $this->addPrimarySystem(new Hangar(4, 4));
        $this->addPrimarySystem(new JumpEngine(5, 20, 5, 24));
        $this->addPrimarySystem(new ReloadRack(5, 9));

        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        /*
        $this->addFrontSystem(new LMissileRack(4, 6, 0, 240, 60));
        $this->addFrontSystem(new LMissileRack(4, 6, 0, 300, 120));
        */        
        $this->addFrontSystem(new MultiMissileLauncher(4, 'L', 240, 60, false));
        $this->addFrontSystem(new MultiMissileLauncher(4, 'L', 300, 120, false));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 270, 90));
        $this->addFrontSystem(new InterceptorMkII(2, 4, 2, 270, 90));

        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new Thruster(4, 14, 0, 6, 2));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
        $this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
        /*
        $this->addAftSystem(new LMissileRack(4, 6, 0, 120, 300));
        $this->addAftSystem(new LMissileRack(4, 6, 0, 60, 240));
        */
        $this->addAftSystem(new MultiMissileLauncher(4, 'L', 120, 300, false));
        $this->addAftSystem(new MultiMissileLauncher(4, 'L', 60, 240, false));

        $this->addLeftSystem(new Thruster(4, 15, 0, 6, 3));
        /*
        $this->addLeftSystem(new LMissileRack(4, 6, 0, 240, 60));
        $this->addLeftSystem(new LHMissileRack(4, 8, 0, 240, 60));
        */
        $this->addLeftSystem(new MultiMissileLauncher(4, 'L', 240, 60, false));
        $this->addLeftSystem(new MultiMissileLauncher(4, 'LH', 240, 60, false));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
    	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
    	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addLeftSystem(new InterceptorMkII(2, 4, 2, 180, 0));

        $this->addRightSystem(new Thruster(4, 15, 0, 6, 4));
        /*
        $this->addRightSystem(new LMissileRack(4, 6, 0, 300, 120));
        $this->addRightSystem(new LHMissileRack(4, 8, 0, 300, 120 ));
        */
        $this->addRightSystem(new MultiMissileLauncher(4, 'L', 300, 120, false));
        $this->addRightSystem(new MultiMissileLauncher(4, 'LH', 300, 120, false ));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
    	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
    	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new InterceptorMkII(2, 4, 2, 0, 180));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
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
                    8 => "Class-L Missile Rack",
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
