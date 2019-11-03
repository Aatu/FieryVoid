<?php
class Jenas extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 275;
        $this->faction = "Centauri (WotCR)";
    $this->phpclass = "Jenas";
    $this->imagePath = "img/ships/jenas.png";
    $this->canvasSize = 100;
    $this->shipClass = "Jenas Attack Frigate";
	$this->isd = 2006;

    $this->forwardDefense = 11;
    $this->sideDefense = 12;

    $this->agile = true;
    $this->turncost = 0.5;
    $this->turndelaycost = 0.5;
    $this->accelcost = 2;
    $this->rollcost = 2;
    $this->pivotcost = 2;
    $this->iniativebonus = 60;
	
	$this->notes = "Atmospheric capable";
	
        
    $this->addPrimarySystem(new Reactor(4, 8, 0, 0));
    $this->addPrimarySystem(new CnC(4, 9, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 12, 2, 5));
    $this->addPrimarySystem(new Engine(3, 9, 0, 8, 2));
    $this->addPrimarySystem(new Hangar(3, 1));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));
    $this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 0, 360));

    $this->addFrontSystem(new Thruster(3, 12, 0, 4, 1));
    $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 180, 60));
    $this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
    $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 180));

    $this->addAftSystem(new Thruster(1, 10, 0, 4, 2));
    $this->addAftSystem(new Thruster(1, 10, 0, 4, 2));

    $this->addPrimarySystem(new Structure( 4, 40));
	
	
		$this->hitChart = array(
			0=> array(
					8 => "Thruster",
					9 => "Light Particle Beam",
					12 => "Scanner",
					15 => "Engine",
					17 => "Hangar",
					18 => "Reactor",
					20 => "C&C",
			),
			1=> array(
					6 => "Thruster",
					8 => "Medium Plasma Cannon",
					10 => "Light Particle Beam",
					17 => "Structure",
					20 => "Primary",
			),
			2=> array(
					7 => "Thruster",
					17 => "Structure",
					20 => "Primary",
			),
		);
	
	
    }

}



?>
