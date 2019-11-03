<?php
class Tacit extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
        $this->faction = "Centauri (WotCR)";
    $this->phpclass = "Tacit";
    $this->imagePath = "img/ships/tacit.png";
    $this->canvasSize = 200;
    $this->shipClass = "Tacit Police Cruiser";
	$this->isd = 1820;

    $this->forwardDefense = 11;
    $this->sideDefense = 13;

    $this->turncost = 0.5;
    $this->turndelaycost = 0.66;
    $this->accelcost = 2;
    $this->rollcost = 2;
    $this->pivotcost = 2;
    $this->iniativebonus = 30;
        
    $this->addPrimarySystem(new Reactor(5, 12, 0, -2));
    $this->addPrimarySystem(new CnC(5, 12, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
    $this->addPrimarySystem(new Engine(4, 20, 0, 12, 2));
    $this->addPrimarySystem(new Hangar(4, 3));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));

    $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
    $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
    $this->addFrontSystem(new TacLaser(2, 5, 4, 300, 60));
    $this->addFrontSystem(new ParticleProjector(2, 6, 1, 270, 90));
    $this->addFrontSystem(new ParticleProjector(2, 6, 1, 270, 90));
    
    $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
    $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
    $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
    $this->addAftSystem(new ParticleProjector(2, 6, 1, 180, 360));
    $this->addAftSystem(new ParticleProjector(2, 6, 1, 0, 180));
    
    $this->addFrontSystem(new Structure( 4, 40));
    $this->addPrimarySystem(new Structure( 5, 40));
    $this->addAftSystem(new Structure( 3, 38));
	
	
        $this->hitChart = array(
            0=> array(
                    10 => "Structure",
                    12 => "Thruster",
                    14 => "Scanner",
                    16 => "Engine",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Tactical Laser",
                    10 => "Particle Projector",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    9 => "Particle Projector",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
	
    }

}



?>
