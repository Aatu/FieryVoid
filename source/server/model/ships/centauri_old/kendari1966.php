<?php
class Kendari1966 extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 540;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Kendari1966";
        $this->imagePath = "img/ships/kendari.png";
        $this->shipClass = "Kendari Fleet Scout (1966)";	    
        	$this->variantOf = "Kendari Fleet Scout";
        $this->shipSizeClass = 3;
        $this->fighters = array("heavy"=>6);
        $this->limited = 33;
	    $this->isd = 1966;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;    
         
        $this->addPrimarySystem(new Reactor(6, 14, 0, 0));
        $this->addPrimarySystem(new CnC(6, 16, 0, 0));
        $this->addPrimarySystem(new ElintScanner(5, 25, 7, 10));
        $this->addPrimarySystem(new Engine(5, 18, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 8));      
		
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 8, 0, 3, 1));        
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 120));
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 120));
        $this->addFrontSystem(new SentinelPointDefense(1, 4, 1, 240, 60));
        $this->addFrontSystem(new SentinelPointDefense(1, 4, 1, 300, 120));
	    
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 14, 0, 4, 2));
        $this->addAftSystem(new JumpEngine(5, 25, 3, 20));
        $this->addAftSystem(new SentinelPointDefense(1, 4, 1, 120, 240));
        
	$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        $this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
        
	$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0 , 180));
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0 , 180));     
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 44));
        $this->addLeftSystem(new Structure( 4, 45));
        $this->addRightSystem(new Structure( 4, 45));
        $this->addPrimarySystem(new Structure( 5, 40));
	    
	    
	//d20 hit chart
	$this->hitChart = array(
		
		0=> array(
			10 => "Structure",
			13 => "ELINT Scanner",
			15 => "Engine",
			17 => "Hangar",
			19 => "Reactor",
			20 => "C&C",
		),
		1=> array(
			6 => "Thruster",
			8 => "Sentinel Point Defense",
			10 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
		2=> array(
			6 => "Thruster",
			7 => "Sentinel Point Defense",
			12 => "Jump Engine",
			18 => "Structure",
			20 => "Primary",
		),
		3=> array(
			6 => "Thruster",
			9 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
		4=> array(
			6 => "Thruster",
			9 => "Light Particle Beam",
			18 => "Structure",
			20 => "Primary",
		),
	);
	    
    }
}

?>
