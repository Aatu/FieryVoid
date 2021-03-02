<?php
class SorithianTyanCarrier extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 450;
        $this->faction = "Small Races";
        $this->phpclass = "SorithianTyanCarrier";
        $this->imagePath = "img/ships/BASurveyShip.png";
        $this->shipClass = "Sorithian Tyan Carrier";
        $this->shipSizeClass = 3;
        $this->fighters = array("medium"=>24);
	    
        $this->isd = 2212;
        $this->unofficial = true;

	    $this->notes = 'Uses Light missiles.';
	
        $this->forwardDefense = 15;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
 	$this->iniativebonus = -20;
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 5));
        $this->addPrimarySystem(new Engine(4, 14, 0, 8, 4));
        $this->addPrimarySystem(new Hangar(3, 14));

        $this->addFrontSystem(new Hangar(2, 12));
        $this->addFrontSystem(new Thruster(2, 20, 0, 6, 1));
        $this->addFrontSystem(new LightLaser(2, 4, 3, 300, 60));
        $this->addFrontSystem(new LightLaser(2, 4, 3, 300, 60));
	$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 360));
	$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));
        $this->addFrontSystem(new CargoBay(1, 10));
        $this->addFrontSystem(new CargoBay(1, 10));

        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem(new Thruster(2, 12, 0, 4, 2));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 360));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 240));

        $this->addLeftSystem(new Thruster(2, 13, 0, 5, 3));
        $this->addLeftSystem(new CustomLightSoMissileRack(3, 6, 0, 240, 360));
        $this->addLeftSystem(new CargoBay(2, 24));
        $this->addLeftSystem(new CargoBay(2, 24));

        $this->addRightSystem(new Thruster(2, 13, 0, 5, 4));
        $this->addRightSystem(new CustomLightSoMissileRack(3, 6, 0, 0, 120));
        $this->addRightSystem(new CargoBay(2, 24));
        $this->addRightSystem(new CargoBay(2, 24));


        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 32));
        $this->addAftSystem(new Structure( 4, 32));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 4, 32));
        $this->hitChart = array(
                0=> array(
                        7 => "Structure",
                        10 => "Hangar",
                        12 => "Scanner",
                        15 => "Engine",
                        18 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Light Laser",
                        8 => "Cargo Bay",
                        10 => "Hangar",
                        12 => "Light Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Light Particle Beam",
                        17 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Cargo Bay",
			10 => "Class-SO Missile Rack",
                        17 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Cargo Bay",
			10 => "Class-SO Missile Rack",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
?>
