<?php
class CestusRaider extends HeavyCombatVessel{
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 300;
        $this->faction = "Raiders";
    $this->phpclass = "CestusRaider";
    $this->imagePath = "img/ships/tacit.png";
    $this->canvasSize = 200;
    $this->shipClass = "Raider Cestus Attack Ship";
	$this->isd = 2002;
	$this->variantOf = "Centauri Privateer Cestus Attack Ship";

    $this->forwardDefense = 11;
    $this->sideDefense = 13;

    $this->turncost = 0.5;
    $this->turndelaycost = 0.66;
    $this->accelcost = 2;
    $this->rollcost = 2;
    $this->pivotcost = 2;
    $this->iniativebonus = 30;
        
    $this->addPrimarySystem(new Reactor(5, 16, 0, 0));
    $this->addPrimarySystem(new CnC(5, 12, 0, 0));
    $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
    $this->addPrimarySystem(new Engine(4, 16, 0, 10, 3));
    $this->addPrimarySystem(new Hangar(4, 1));
		$this->addPrimarySystem(new CargoBay(2, 4));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 3));
    $this->addPrimarySystem(new Thruster(2, 10, 0, 4, 4));

    $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
    $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
    $this->addFrontSystem(new LightParticleCannon(2, 6, 5, 300, 60));
    $this->addFrontSystem(new LightParticleCannon(2, 6, 5, 300, 60));
    $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
    
    $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
    $this->addAftSystem(new Thruster(2, 8, 0, 4, 2));
    $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
    $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
    $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
    
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
                    7 => "Light Particle Beam",
                    10 => "Light Particle Cannon",
                    18 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
                    9 => "Light Particle Beam",
                    18 => "Structure",
                    20 => "Primary",
            ),
        );
	
    }

}



?>
