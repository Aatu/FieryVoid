<?php
class Scoravalaser extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 525;
		$this->faction = "Descari";
        $this->phpclass = "Scoravalaser";
        $this->imagePath = "img/ships/DescariScorava.png";
        $this->shipClass = "Scorava Laser Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("medium"=>24);  
	    $this->isd = 2242;
	    $this->variantOf = "Scorava Cruiser";	    
        $this->occurence = "uncommon";   
		
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 2;

        
        $this->addPrimarySystem(new Reactor(5, 28, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 3));
		$this->addPrimarySystem(new Hangar(5, 26));	
        
        $this->addFrontSystem(new LightLaser (2, 4, 3, 300, 60));
        $this->addFrontSystem(new LightLaser (2, 4, 3, 300, 60));       
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));        
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
			
        $this->addAftSystem(new LightLaser(2, 4, 3, 120, 240));
        $this->addAftSystem(new LightLaser(2, 4, 3, 120, 240));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270)); 
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));	
		$this->addLeftSystem(new MediumLaser(4, 6, 5, 300, 0));
		$this->addLeftSystem(new SMissileRack(4, 6, 0, 180, 60));
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
              			  
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));	
		$this->addRightSystem(new MediumLaser(4, 6, 5, 0, 60));
		$this->addRightSystem(new SMissileRack(4, 6, 0, 300, 180));
		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
        
		
		//structures
        $this->addFrontSystem(new Structure(4, 44));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 62));
        $this->addRightSystem(new Structure(4, 62));
        $this->addPrimarySystem(new Structure(5, 45));
		
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Scanner",
				13 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				8 => "Light Laser",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				9 => "Thruster",
				11 => "Light Laser",
				13 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				6 => "Medium Laser",
				7 => "Class-S Missile Rack",
				9 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				6 => "Medium Laser",
				7 => "Class-S Missile Rack",
				9 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);        
    }	
}



?>
