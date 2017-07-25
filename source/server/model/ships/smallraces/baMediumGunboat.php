<?php
class BAMediumGunboat extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 350;
        $this->faction = "Small Races";
        $this->phpclass = "BAMediumGunboat";
        $this->imagePath = "img/ships/BAMediumGunboat.png";
        $this->shipClass = "BA Medium Gunboat";

        $this->isd = 2188;

	$this->canvasSize = 100;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(3, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 6));
	$this->addPrimarySystem(new Hangar(3, 1));
	    /*
        $this->addPrimarySystem(new LtBlastCannon(3, 0, 0, 240, 60));
        $this->addPrimarySystem(new LtBlastCannon(3, 0, 0, 300, 120));
	*/
	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 3));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 4, 4));
		/*
        $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
        $this->addFrontSystem(new MedBlastCannon(3, 0, 0, 270, 90));
        $this->addFrontSystem(new BAInterceptorMkI(2, 4, 1, 270, 90));
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 60));
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 180)); 
		
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Engine(3, 13, 0, 6, 2));
        $this->addAftSystem(new BAInterceptorMkI(2, 0, 0, 90, 270));
	*/
        $this->addPrimarySystem(new Structure(3, 45));
        
		$this->hitChart = array(
                0=> array(
                        8 => "Thruster",
                        12 => "Light Blast Cannon",
                        15 => "Scanner",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        8 => "Medium Blast Cannon",
                        10 => "Standard Particle Beam",
                        11 => "BA Interceptor I",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        10 => "Engine",
                        11 => "BA Interceptor I",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
