<?php
class Cestus extends HeavyCombatVessel{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 280;
        $this->faction = "Raiders";
    $this->phpclass = "Cestus";
    $this->imagePath = "img/ships/tacit.png";
    $this->canvasSize = 200;
    $this->shipClass = "Centauri Privateer Cestus Attack Ship";
	$this->isd = 1870;

	$this->notes = "Used only by Centauri Privateers";

    $this->forwardDefense = 11;
    $this->sideDefense = 13;

    $this->turncost = 0.5;
    $this->turndelaycost = 0.66;
    $this->accelcost = 2;
    $this->rollcost = 2;
    $this->pivotcost = 2;
    $this->iniativebonus = 30;
        
    $this->addPrimarySystem(new Reactor(5, 12, 0, 0));
    $this->addPrimarySystem(new CnC(5, 12, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
    $this->addPrimarySystem(new Engine(4, 16, 0, 10, 3));
    $this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new CargoBay(2, 8));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));

    $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
    $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
    $this->addFrontSystem(new MediumPlasma(2, 5, 3, 300, 60));
    $this->addFrontSystem(new ParticleProjector(2, 6, 1, 270, 90));
    $this->addFrontSystem(new ParticleProjector(2, 6, 1, 270, 90));
    
    $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
    $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
    $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
    $this->addAftSystem(new ParticleProjector(2, 6, 1, 180, 360));
    $this->addAftSystem(new ParticleProjector(2, 6, 1, 0, 180));
    
    $this->addFrontSystem(new Structure( 4, 40));
    $this->addPrimarySystem(new Structure( 5, 40));
    $this->addAftSystem(new Structure( 3, 38));
	
	
        $this->hitChart = array(
            0=> array(
                    9 => "Structure",
                    11 => "Thruster",
                    13 => "Scanner",
                    15 => "Engine",
					16 => "Cargo Bay",
                    17 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    7 => "Medium Plasma Cannon",
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
