<?php
class ShlassanVelmakEscortCarrier extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 325;
        $this->faction = "Small Races";
        $this->phpclass = "ShlassanVelmakEscortCarrier";
        $this->imagePath = "img/ships/shlassanVeltar.png";
        $this->shipClass = "Sh'lassan Velmak Escort Carrier";
        $this->fighters = array("fighters" => 6);
	    $this->variantOf = "Sh'lassan Veltar Frigate";
        $this->occurence = "common";
        $this->canvasSize = 100;
		$this->unofficial = true;
        
        $this->isd = 2258;
	    $this->notes = 'Atmospheric Capable.';

        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 2;
		$this->iniativebonus = 60;
         
        $this->addPrimarySystem(new Reactor(3, 13, 0, 0));
        $this->addPrimarySystem(new CnC(4, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 4, 5));
		$this->addPrimarySystem(new Hangar(3, 2, 1));
		$this->addPrimarySystem(new Hangar(3, 6, 6));
		$this->addPrimarySystem(new Thruster(3, 8, 0, 3, 3));
		$this->addPrimarySystem(new Thruster(3, 8, 0, 3, 4));
		
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
		
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 3, 2));
        $this->addAftSystem(new Engine(3, 11, 0, 7, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
	
        $this->addPrimarySystem(new Structure( 3, 40));

        $this->hitChart = array(
            0=> array(
					10 => "Structure",
                    11 => "Thruster",
					13 => "Cargo Bay",
                    14 => "Scanner",
					16 => "Hangar",
                    19 => "Reactor",
                    20 => "C&C",
            ),
            1=> array(
                    5 => "Thruster",
                    10 => "Light Particle Beam",
					17 => "Structure",
                    20 => "Primary",
            ),
            2=> array(
                    6 => "Thruster",
					8 => "Light Particle Beam",
                    10 => "Engine",
                    17 => "Structure",
                    20 => "Primary",
            ),
        );		
    }

}



?>
