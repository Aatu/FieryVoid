<?php
class SorithianFylonFrigate extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 300;
        $this->faction = "Small Races";
        $this->phpclass = "SorithianFylonFrigate";
        $this->imagePath = "img/ships/BAMediumGunboat.png";
        $this->shipClass = "Sorithian Fylon Frigate";
        $this->occurence = "common";
        $this->isd = 2209;
	$this->canvasSize = 100;
	
	$this->unofficial = true;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 11;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.5;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
        $this->iniativebonus = 60;
 
        $this->addPrimarySystem(new Reactor(3, 12, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 3, 4));
        $this->addPrimarySystem(new Engine(3, 8, 0, 6, 2));
	$this->addPrimarySystem(new Thruster(2, 6, 0, 3, 3));
	$this->addPrimarySystem(new Thruster(2, 6, 0, 3, 4));

        $this->addFrontSystem(new Thruster(2, 6, 0, 2, 1));
        $this->addFrontSystem(new LightLaser(2, 4, 3, 300, 60));
        $this->addFrontSystem(new LightLaser(2, 4, 3, 300, 60));
	$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
	$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));
        $this->addFrontSystem(new SoMissileRack(3, 6, 0, 240, 360));
        $this->addFrontSystem(new SoMissileRack(3, 6, 0, 0, 120));

	$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 300));
	$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 180));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));
        $this->addAftSystem(new Thruster(2, 6, 0, 3, 2));

        $this->addPrimarySystem(new Structure(3, 36));
        
		$this->hitChart = array(
                0=> array(
                        8 => "Thruster",
                        11 => "Scanner",
                        14 => "Engine",
                        17 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        3 => "Thruster",
                        7 => "Light Laser",
			10 => "Class-SO Missile Rack",
                        12 => "Light Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        5 => "Thruster",
                        7 => "Light Particle Beam",
                        14 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
