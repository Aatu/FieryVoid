<?php
class Cokra extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 280;
		$this->faction = "Kor-Lyan";
        $this->phpclass = "Cokra";
        $this->imagePath = "img/ships/korlyan_cokra.png";
        $this->shipClass = "Cokra Blockade Runner";
        $this->canvasSize = 70;
	    
	    $this->isd = 2214;
        $this->fighters = array("assault shuttles"=>1);

	    $this->notes = 'Atmospheric Capable.';
        $this->agile = true;
        
        $this->forwardDefense = 10;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 65;
         
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 5, 6));
        $this->addPrimarySystem(new Engine(3, 11, 0, 5, 1));
		$this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Thruster(3, 11, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 11, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new StdParticleBeam(1, 4, 1, 240, 60));
        $this->addFrontSystem(new MultiDefenseLauncher(2, 'D', 240, 60, false));
        $this->addFrontSystem(new MultiDefenseLauncher(2, 'D', 300, 120, false));
        $this->addFrontSystem(new StdParticleBeam(1, 4, 1, 300, 120));
		
        $this->addAftSystem(new Thruster(3, 14, 0, 5, 2));
		$this->addAftSystem(new CargoBay(2, 24));
        $this->addAftSystem(new MultiDefenseLauncher(2, 'D', 90, 270, false));
		$this->addAftSystem(new CargoBay(2, 24));
	
        $this->addPrimarySystem(new Structure( 3, 48));
        
		$this->hitChart = array(
                0=> array(
                        7 => "Thruster",
                        10 => "Scanner",
                        13 => "Engine",
						15 => "Hangar",
                        18 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        5 => "Thruster",
						7 => "Standard Particle Beam",
                        9 => "Class-D Missile Launcher",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        7 => "Class-D Missile Launcher",
						10 => "Cargo Bay",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );

    }

}



?>
