<?php
class Scorran extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 725;
	$this->faction = "Descari";
        $this->phpclass = "Scorran";
        $this->imagePath = "img/ships/DescariScorran.png";
        $this->shipClass = "Scorran New Heavy Cruiser";
        $this->shipSizeClass = 3;
	    $this->isd = 2250;
        $this->limited = 10;
		$this->fighters = array("normal"=>12);
	    
		
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        
        
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(6, 20, 4, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 10, 3));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new JumpEngine(5, 24, 3, 28));
        
        $this->addFrontSystem(new HeavyPlasmaBolter(4, 0, 0, 300, 60));
        $this->addFrontSystem(new HeavyPlasmaBolter(4, 0, 0, 300, 60));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));        
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
			
        $this->addAftSystem(new LightPlasmaBolter(3, 0, 0, 120, 240));
        $this->addAftSystem(new LightPlasmaBolter(3, 0, 0, 120, 240)); 
        $this->addAftSystem(new Thruster(4, 20, 0, 10, 2));

		
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));				
		$this->addLeftSystem(new MediumLaser(4, 6, 5, 240, 360));
		$this->addLeftSystem(new MediumLaser(4, 6, 5, 180, 300));
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
              			  
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));			
		$this->addRightSystem(new MediumLaser(4, 6, 5, 0, 120));
		$this->addRightSystem(new MediumLaser(4, 6, 5, 60, 180));
		$this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
        
		
		//structures
        $this->addFrontSystem(new Structure(5, 42));
        $this->addAftSystem(new Structure(4, 40));
        $this->addLeftSystem(new Structure(5, 48));
        $this->addRightSystem(new Structure(5, 48));
        $this->addPrimarySystem(new Structure(5, 60));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				10 => "Jump Engine",
				12 => "Scanner",
				14 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				4 => "Thruster",
				6 => "Light Particle Beam",
				10 => "Heavy Plasma Bolter",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				9 => "Light Plasma Bolter",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				7 => "Medium Laser",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				7 => "Medium Laser",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);        
    }	
}



?>
