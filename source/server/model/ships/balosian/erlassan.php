<?php
class Erlassan extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 500;
	$this->faction = "Balosian";
        $this->phpclass = "Erlassan";
        $this->imagePath = "img/ships/kraasus.png";
        $this->shipClass = "Erlassan Scout";
        $this->variantOf = "Kraasus Destroyer";
        $this->occurence = "uncommon";
        $this->limited = 33;
		$this->isd = 2248;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;

	$this->addPrimarySystem(new Reactor(4, 15, 0, 0));
	$this->addPrimarySystem(new CnC(5, 12, 0, 0));
	$this->addPrimarySystem(new ElintScanner(4,15, 9, 8));
	$this->addPrimarySystem(new Engine(4, 14, 0, 10, 3));
	$this->addPrimarySystem(new Hangar(4, 2));
	$this->addPrimarySystem(new Thruster(4, 15, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(4, 15, 0, 4, 4));
	  
	$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
	$this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
	$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));	
	$this->addFrontSystem(new StdParticleBeam(3, 4, 1, 270, 90));
	$this->addFrontSystem(new IonCannon(4, 6, 4, 300, 60));	

	$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
	$this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(4, 8, 0, 3, 2));
	$this->addAftSystem(new Thruster(4, 6, 0, 2, 2));
	$this->addAftSystem(new StdParticleBeam(3, 4, 1, 240, 0));	
	$this->addAftSystem(new StdParticleBeam(3, 4, 1, 180, 0));
	$this->addAftSystem(new StdParticleBeam(3, 4, 1, 0, 180));
	$this->addAftSystem(new StdParticleBeam(3, 4, 1, 0, 120));	
	$this->addAftSystem(new StdParticleBeam(3, 4, 1, 90, 270));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(4, 48));
        $this->addAftSystem(new Structure(4, 50));
        $this->addPrimarySystem(new Structure(4, 48));

		$this->hitChart = array(
            0=> array(
                    7 => "Structure",
					11 => "Thruster",
					14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    9 => "Ion Cannon",
                    11 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    7 => "Thruster",
                    11 => "Standard Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
    }
}
?>