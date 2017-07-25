<?php
class BACloseEscort extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "Small Races";
        $this->phpclass = "BACloseEscort";
        $this->imagePath = "img/ships/BACloseEscort.png";
        $this->shipClass = "BA Close Escort";

        $this->isd = 2226;

	$this->canvasSize = 100;
	$this->agile = true;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 12;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 65;

        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 5));
        $this->addPrimarySystem(new Engine(3, 9, 0, 4, 2));
	$this->addPrimarySystem(new CargoBay(3, 40));
	$this->addPrimarySystem(new Hangar(3, 1));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 3, 3));
	$this->addPrimarySystem(new Thruster(2, 8, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 3, 1));
        $this->addFrontSystem(new BAInterceptorMkI(2, 4, 1, 270, 90));
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 120));
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 60));
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 120)); 
		
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new BAInterceptorMkI(2, 4, 1, 90, 270));
	$this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 300));
	$this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 240)); 
	
        $this->addPrimarySystem(new Structure(3, 36));
        
		$this->hitChart = array(
                0=> array(
                        5 => "Thruster",
                        9 => "Cargo Bay",
                        12 => "Engine",
                        15 => "Scanner",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        10 => "Standard Particle Beam",
                        11 => "BA Interceptor I",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        10 => "Standard Particle Beam",
                        11 => "BA Interceptor I",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
