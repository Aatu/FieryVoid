<?php
class wlcChlonasMerTanCRV2230 extends MediumShip{
    /*Ch'Lonas Mer'Tan corvette, variant ISD 2230*/
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 280;
        $this->phpclass = "wlcChlonasMerTanCRV2230";
        $this->imagePath = "img/ships/ChlonasMerTan.png";
        $this->shipClass = "Mer'Tan Corvette (2230)";
        $this->agile = true;
        $this->canvasSize = 200;
	    
		$this->faction = "Ch'Lonas";
        $this->variantOf = "Mer'Tan Corvette";
		$this->isd = 2230;
		$this->unofficial = true;
        
        $this->forwardDefense = 11;
        $this->sideDefense = 13;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.33;
        $this->accelcost = 1;
        $this->rollcost = 1;
        $this->pivotcost = 1;
		$this->iniativebonus = 13 *5;
         
        $this->addPrimarySystem(new Reactor(4, 9, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 3, 4));
        $this->addPrimarySystem(new Engine(3, 10, 0, 4, 4));
		$this->addPrimarySystem(new Hangar(2, 1));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 3));
        $this->addPrimarySystem(new Thruster(2, 10, 0, 2, 4));

		$this->addFrontSystem(new Thruster(2, 8, 0, 2, 1));
        $this->addFrontSystem(new AssaultLaser(4, 6, 4, 270, 90));
        $this->addFrontSystem(new CustomLightMatterCannon(2, 5, 2, 240, 0));
        $this->addFrontSystem(new CustomLightMatterCannon(2, 5, 2, 0, 120));

		$this->addAftSystem(new Thruster(2, 15, 0, 4, 2));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
       
        $this->addPrimarySystem(new Structure( 4, 32));


	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Thruster",
			13 => "Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),

		1=> array(
			3 => "Thruster",
			6 => "Assault Laser",
			8 => "Light Matter Cannon",
			17 => "Structure",
			20 => "Primary",
		),

		2=> array(
			5 => "Thruster",
			7 => "Light Particle Beam",
			17 => "Structure",
			20 => "Primary",
		),


	);


    }
}
?>
