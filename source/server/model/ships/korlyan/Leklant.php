<?php
class Leklant extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 700;
		$this->faction = "Kor-Lyan";
//	$this->faction = "Custom Ships";
        $this->phpclass = "Leklant";
        $this->imagePath = "img/ships/korlyan_leklant2.png";
        $this->shipClass = "Leklant Scout Cruiser";
        $this->shipSizeClass = 3;
		$this->canvasSize = 160; //img has 200px per side

        $this->limited = 33;

		$this->isd = 2222;
        $this->fighters = array("assault shuttles"=>4);

	    $this->notes = 'Atmospheric Capable.';
        
        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1.0;
        $this->turndelaycost = 1.0;
        $this->accelcost = 4;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;

        $this->addPrimarySystem(new Reactor(5, 21, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 20, 7, 9));
        $this->addPrimarySystem(new Engine(5, 18, 0, 8, 4));
		$this->addPrimarySystem(new Hangar(4, 4));
        $this->addPrimarySystem(new JumpEngine(4, 20, 4, 30));
   
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new MultiDefenseLauncher(2, 'D', 300, 60, false));
        $this->addFrontSystem(new DirectLimpetBore(3, 5, 3, 300, 60));
        $this->addFrontSystem(new MultiDefenseLauncher(2, 'D', 300, 60, false));

        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 7, 0, 2, 2));
        $this->addAftSystem(new MultiDefenseLauncher(3, 'D', 120, 300, false));
        $this->addAftSystem(new MultiDefenseLauncher(3, 'D', 60, 240, false));

        $this->addLeftSystem(new MultiDefenseLauncher(2, 'D', 240, 60, false));
        $this->addLeftSystem(new FMissileRack(3, 6, 0, 180, 360, false)); 
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 360));
        $this->addLeftSystem(new StdParticleBeam(3, 4, 1, 180, 360));
        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));

        $this->addRightSystem(new MultiDefenseLauncher(2, 'D', 300, 120, false));
        $this->addRightSystem(new FMissileRack(3, 6, 0, 0, 180, false)); 
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(3, 4, 1, 0, 180));
        $this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 40));
        $this->addAftSystem(new Structure(5, 39));
        $this->addLeftSystem(new Structure(5, 45));
        $this->addRightSystem(new Structure(5, 45));
        $this->addPrimarySystem(new Structure(5, 48));
		
		$this->hitChart = array(
			0=> array(
					10 => "Structure",
					12 => "Jump Engine",
					14 => "Scanner",
					16 => "Engine",
					17 => "Hangar",
					19 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					5 => "Thruster",
					6 => "Limpet Bore Torpedo",
					9 => "Class-D Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			2=> array(
					6 => "Thruster",
					9 => "Class-D Missile Rack",
					18 => "Structure",
					20 => "Primary",
			),
			3=> array(
					5 => "Thruster",
					7 => "Class-F Missile Rack",
					9 => "Class-D Missile Rack",
					11 => "Standard Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
			4=> array(
					5 => "Thruster",
					7 => "Class-F Missile Rack",
					9 => "Class-D Missile Rack",
					11 => "Standard Particle Beam",
					18 => "Structure",
					20 => "Primary",
			),
		);
    }
}

?>
