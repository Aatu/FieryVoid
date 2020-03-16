<?php
class wlcChlonasTraVoraCL2240 extends BaseShipNoAft{
    /*Ch'Lonas Tra'Vora light cruiser, variant ISD 2240*/

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        $this->pointCost = 545;
        $this->phpclass = "wlcChlonasTraVoraCL2240";
        $this->imagePath = "img/ships/ChlonasTraVora.png";
        $this->canvasSize = 120;
        $this->shipClass = "Tra'Vora Light Cruiser (2240)";
        $this->fighters = array("heavy" => 12);
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
	    
	$this->faction = "Ch'Lonas";
        $this->variantOf = "Tra'Vora Light Cruiser";
	$this->isd = 2240;
	$this->unofficial = true;
        
        $this->iniativebonus = 15;
        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 15, 6, 6));
        $this->addPrimarySystem(new Engine(5, 13, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 19, 0, 9, 2));

        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 240, 0));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 0, 120));
        $this->addFrontSystem(new AssaultLaser(3, 6, 4, 300, 60));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));

        $this->addLeftSystem(new CustomPulsarLaser(4, 0,0, 240, 60)); //Power and Structure are defined in weapon
	$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
	$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));

        $this->addRightSystem(new CustomPulsarLaser(4, 0,0, 300, 120)); //Power and Structure are defined in weapon
	$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
	$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 44));
        $this->addLeftSystem(new Structure( 4, 48));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 5, 40));




	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Structure",
			12 => "Thruster",
			14 => "Scanner",
			16 => "Engine",
			18 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			4 => "Thruster",
			10 => "Assault Laser",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			4 => "Thruster",
			7 => "Pulsar Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			4 => "Thruster",
			7 => "Pulsar Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
	);


    }
}
?>
