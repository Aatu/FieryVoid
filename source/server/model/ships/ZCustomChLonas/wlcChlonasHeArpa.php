<?php
class wlcChlonasHeArpa extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 285;
		$this->faction = "Civilians";
        $this->phpclass = "wlcChlonasHeArpa";
        $this->imagePath = "img/ships/ChlonasHeArpa.png";
        $this->shipClass = "Chlonas He'Arpa Transport";
        $this->canvasSize = 200;
	    
		$this->isd = 2200;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 17;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 5;
        $this->pivotcost = 4;
		$this->iniativebonus = -10;
		
        $this->addPrimarySystem(new Reactor(4, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 6, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 3, 3));
        $this->addPrimarySystem(new Engine(3, 11, 0, 6, 6));
		$this->addPrimarySystem(new Hangar(3, 4));
		$this->addPrimarySystem(new CargoBay(2, 100));
		
        $this->addFrontSystem(new Thruster(2, 15, 0, 4, 1));
        $this->addFrontSystem(new CustomLightMatterCannon(2, 0, 0, 240, 0));
        $this->addFrontSystem(new CustomLightMatterCannon(2, 0, 0, 0, 120));
		
        $this->addAftSystem(new Thruster(2, 14, 0, 6, 2));
        $this->addAftSystem(new CustomLightMatterCannon(2, 0, 0, 180, 300));
        $this->addAftSystem(new CustomLightMatterCannon(2, 0, 0, 60, 180));
       
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 120, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 60));
        $this->addLeftSystem(new CargoBay(3, 50));
        $this->addLeftSystem(new Thruster(2, 11, 0, 4, 3));
		
   		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 300, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 240));
        $this->addRightSystem(new CargoBay(3, 50));
        $this->addRightSystem(new Thruster(2, 11, 0, 4, 4));
		
		
        $this->addFrontSystem(new Structure(3, 28));
        $this->addAftSystem(new Structure(3, 28));
        $this->addLeftSystem(new Structure(3, 28));
        $this->addRightSystem(new Structure(3, 28));
        $this->addPrimarySystem(new Structure(4, 30));
        
		$this->hitChart = array(
			0=> array(
				5 => "Structure",
				11 => "Cargo Bay",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				7 => "Light Matter Cannon",
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				8 => "Light Matter Cannon",
				17 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				6 => "Light Particle Beam",
				9 => "Cargo Bay",
				17 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				6 => "Light Particle Beam",
				9 => "Cargo Bay",
				17 => "Structure",
				20 => "Primary",
			),
		);
    }
}
?>
