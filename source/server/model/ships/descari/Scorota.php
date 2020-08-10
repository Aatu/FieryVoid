<?php
class Scorota extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 900;
	$this->faction = "Descari";
        $this->phpclass = "Scorota";
        $this->imagePath = "img/ships/DescariScorota.png";
        $this->shipClass = "Scorota Battleship";
        $this->shipSizeClass = 3;
	    $this->notes = '<br>Unreliable Ship:';
   	    $this->notes .= '<br>Vulnerable to Criticals';
 	    $this->notes .= '<br>Sluggish';
	    $this->isd = 2250;
        $this->limited = 33;
	    
	    
		
        $this->forwardDefense = 17;
        $this->sideDefense = 18;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 6;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        
        $this->iniativebonus = -7;
        $this->critRollMod = 1;
		$this->enhancementOptionsDisabled[] = 'SLUGGISH';
		$this->enhancementOptionsDisabled[] = 'VULN_CRIT';

        
        $this->addPrimarySystem(new Reactor(5, 27, 0, 0));
        $this->addPrimarySystem(new CnC(6, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 6));
        $this->addPrimarySystem(new Engine(5, 23, 0, 18, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
        
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 240, 360));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 0, 120));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 180, 60));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 180));        
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));              
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));        
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
			
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 240));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 240)); 
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		$this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
		
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 0));	
		$this->addLeftSystem(new HeavyPlasmaBolter(4, 0, 0, 300, 0));
		$this->addLeftSystem(new HeavyPlasmaBolter(4, 0, 0, 300, 0));
        $this->addLeftSystem(new Thruster(4, 20, 0, 6, 3));
              			  
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));	
		$this->addRightSystem(new HeavyPlasmaBolter(4, 0, 0, 0, 60));
		$this->addRightSystem(new HeavyPlasmaBolter(4, 0, 0, 0, 60));
		$this->addRightSystem(new Thruster(4, 20, 0, 5, 4));
        
		
		//structures
        $this->addFrontSystem(new Structure(4, 52));
        $this->addAftSystem(new Structure(4, 75));
        $this->addLeftSystem(new Structure(4, 75));
        $this->addRightSystem(new Structure(4, 75));
        $this->addPrimarySystem(new Structure(5, 68));
		
		
		$this->hitChart = array(
			0=> array(
				11 => "Structure",
				14 => "Scanner",
				16 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				8 => "Medium Laser",
				10 => "Class-S Missile Rack",
				11 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				9 => "Thruster",
				11 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				8 => "Heavy Plasma Bolter",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				8 => "Heavy Plasma Bolter",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
		);        
    }	
}



?>
