<?php
class wlcChlonasForVoraScout extends BaseShipNoAft{
    /*Ch'Lonas For'Vora scout*/

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        $this->pointCost = 500;
        $this->phpclass = "wlcChlonasForVoraScout";
        $this->imagePath = "img/ships/brahassa.png";
        $this->shipClass = "For'Vora Scout";
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
	$this->occurence = "uncommon";
	$this->isd = 2215;
	$this->unofficial = true;
        
        $this->iniativebonus = 15;
        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 20, 7, 6));
        $this->addPrimarySystem(new Engine(4, 13, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 19, 0, 9, 2));
	$this->addPrimarySystem(new JumpEngine(5, 10, 6, 48));

        $this->addFrontSystem(new ElintScanner(4, 9, 6, 4));
	$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
	$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
	$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
	$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));

        $this->addLeftSystem(new AssaultLaser(3, 6, 4, 240, 0));
	$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 240, 0));
	$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 300));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));

        $this->addRightSystem(new AssaultLaser(3, 6, 4, 0, 120));
	$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 120));
	$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 60, 180));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 44));
        $this->addLeftSystem(new Structure( 4, 48));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 5, 40));

	//d20 hit chart
	$this->hitChart = array(		
		0=> array(
			8 => "Structure",
			10 => "Jump Engine",
			12 => "Thruster",
			14 => "Elint Scanner",
			16 => "Engine",
			18 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			4 => "Thruster",
			7 => "Light Particle Beam",
			10 => "Elint Scanner",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			4 => "Thruster",
			6 => "Assault Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			4 => "Thruster",
			6 => "Assault Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
	);


    }
}
?>
