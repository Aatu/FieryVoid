<?php
class wlcChlonasLoFahraFFA extends MediumShipLeftRight{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 375;
        $this->phpclass = "wlcChlonasLoFahraFFA";
        $this->imagePath = "img/ships/ChlonasLoFahra.png";
        $this->canvasSize = 100;
        $this->shipClass = "Lo'Fahra Attack Frigate";
        $this->agile = true;
        $this->canvasSize = 100;
	    
	$this->faction = "Ch'Lonas";
       // $this->variantOf = "Es'Kashi Destroyer";
	$this->isd = 2230;
	$this->unofficial = true;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 2;
        $this->pivotcost = 2;
	$this->iniativebonus = 65;
         
		 
		 
        $this->addPrimarySystem(new Reactor(5, 10, 0, 0));
        $this->addPrimarySystem(new CnC(5, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 10, 3, 5));
        $this->addPrimarySystem(new Engine(5, 10, 0, 5, 2));
	$this->addPrimarySystem(new Hangar(4, 1));
	$this->addPrimarySystem(new Thruster(3, 10, 0, 3, 1));
	$this->addPrimarySystem(new Thruster(3, 14, 0, 5, 2));
	$this->addPrimarySystem(new MatterCannon(4, 7, 4, 330, 30));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 240, 120));
	$this->addPrimarySystem(new LightParticleBeamShip(2, 2, 1, 240, 120));

		
        $this->addLeftSystem(new Thruster(3, 11, 0, 3, 3));
        $this->addLeftSystem(new AssaultLaser(3, 6, 4, 240, 0));
        $this->addLeftSystem(new CustomLightMatterCannon(3, 5, 2, 240, 0));
		
        $this->addRightSystem(new Thruster(3, 11, 0, 3, 4));
        $this->addRightSystem(new AssaultLaser(3, 6, 4, 0, 120));
        $this->addRightSystem(new CustomLightMatterCannon(3, 5, 2, 0, 120));
       
        $this->addPrimarySystem(new Structure( 4, 45));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			9 => "Thruster",
			11 => "Matter Cannon",
			12 => "Light Particle Beam",
			14 => "Scanner",
			16 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "CnC",
		),

		3=> array(
			5 => "Thruster",
			7 => "Light Matter Cannon",
			9 => "Assault Laser",
			17 => "Structure",
			20 => "Primary",
		),

		4=> array(
			5 => "Thruster",
			7 => "Light Matter Cannon",
			9 => "Assault Laser",
			17 => "Structure",
			20 => "Primary",
		),


	);


    }
}
?>
