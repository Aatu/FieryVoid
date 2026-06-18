<?php
class baradaTrukDestroyer extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 300;
        $this->faction = "Barada Imperium";
        $this->phpclass = "baradaTrukDestroyer";
        $this->imagePath = "img/ships/baradaTrukDestroyer.png";
        $this->shipClass = "Truk Destroyer";
		$this->fighters = array("normal"=>6);
		$this->unofficial = true;

        $this->isd = 2230;

		$this->canvasSize = 100;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 14;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;

        $this->addPrimarySystem(new Reactor(5, 16, 0, 0));
        $this->addPrimarySystem(new CnC(6, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 4, 5));
		$this->addPrimarySystem(new Hangar(5, 8, 6));
		$this->addAftSystem(new Engine(5, 16, 0, 5, 2));

		$this->addPrimarySystem(new Thruster(3, 6, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 6, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 6, 0, 3, 4));
		$this->addPrimarySystem(new Thruster(3, 6, 0, 3, 4));


        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
		$this->addFrontSystem(new Thruster(3, 4, 0, 1, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 4, 0, 1, 1));
		
        $this->addFrontSystem(new HeavyParticleBeam(3, 6, 2, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(3, 6, 2, 270, 90));
        $this->addFrontSystem(new StdParticleBeam(3, 6, 2, 270, 90));

		
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
		
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));


        $this->addPrimarySystem(new Structure(3, 48));
        
		$this->hitChart = array(
                0=> array(
                        10 => "Thruster",
                        12 => "Scanner",
						14 => "Engine",
                        16 => "Hangar",
                        18 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Heavy Particle Beam",
                        10 => "Standard Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        7 => "Thruster",
                        11 => "Light Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
