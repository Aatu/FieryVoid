<?php
class wlcChlonasTraVoraCL2169 extends BaseShipNoAft{
    /*Ch'Lonas Tra'Vora light cruiser, variant ISD 2169*/

    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        $this->pointCost = 440;
	$this->faction = "Ch'Lonas";
        $this->phpclass = "wlcChlonasTraVoraCL2169";
        $this->imagePath = "img/ships/brahassa.png";
        $this->shipClass = "Tra'Vora Light Cruiser";
        $this->fighters = array("heavy" => 12);
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
	    
        //$this->variantOf = "Tra'Vora Light Cruiser";
	$this->isd = 2169;
	$this->unofficial = true;
        
        $this->iniativebonus = 15;
        $this->addPrimarySystem(new Reactor(5, 17, 0, 0));
        $this->addPrimarySystem(new CnC(4, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 15, 6, 5));
        $this->addPrimarySystem(new Engine(4, 13, 0, 9, 4));
        $this->addPrimarySystem(new Hangar(4, 2));
        $this->addPrimarySystem(new Thruster(4, 19, 0, 9, 2));

        $this->addFrontSystem(new ImperialLaser(3, 8, 5, 330, 30));
        $this->addFrontSystem(new CustomLightMatterCannon(3, 0,0, 240, 0));//Power and Structure are defined in weapon
	$this->addFrontSystem(new CustomLightMatterCannon(3, 0,0, 0, 120));//Power and Structure are defined in weapon
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));

        $this->addLeftSystem(new TacLaser(3, 6, 4, 240, 0));
        $this->addLeftSystem(new TacLaser(3, 6, 4, 240, 0));
	$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
	$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));

        $this->addRightSystem(new TacLaser(3, 6, 4, 0, 120));
        $this->addRightSystem(new TacLaser(3, 6, 4, 0, 120));
	$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
	$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));

        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 44));
        $this->addLeftSystem(new Structure( 4, 48));
        $this->addRightSystem(new Structure( 4, 48));
        $this->addPrimarySystem(new Structure( 4, 40));




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
			7 => "Light Matter Cannon",
			10 => "Imperial Laser",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			4 => "Thruster",
			7 => "Tactical Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			4 => "Thruster",
			7 => "Tactical Laser",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
	);


    }
}
?>
