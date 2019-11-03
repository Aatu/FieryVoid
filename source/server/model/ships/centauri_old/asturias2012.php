<?php
class Asturias2012 extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 530;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Asturias2012";
        $this->imagePath = "img/ships/astur.png"; 
		$this->canvasSize = 200; 
        $this->shipSizeClass = 3;
        $this->shipClass = "Asturias Carrier (2012)";
        $this->variantOf = "Astur Assault Ship";   
		$this->occurence = "uncommon";
        //$this->limited = 33;
	    $this->isd = 2008;
		

        $this->fighters = array("heavy"=>30); //12 per side hangar, 6 in PRIMARY hangar
		
        $this->forwardDefense = 17;
        $this->sideDefense = 16;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 3;
                 
        $this->addPrimarySystem(new Reactor(5, 18, 0, 0));
        $this->addPrimarySystem(new CnC(5, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 3, 6));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
		$this->addPrimarySystem(new JumpEngine(5, 20, 3, 20));
		$this->addPrimarySystem(new Hangar(5, 9));
		$this->addPrimarySystem(new CargoBay(4, 8));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 240, 60));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 270, 90));
        $this->addFrontSystem(new LightParticleBeamShip(3, 2, 1, 300, 120));
        
        $this->addAftSystem(new Thruster(4, 15, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 15, 0, 4, 2));    
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));
        $this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
        
		$this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addLeftSystem(new Hangar(5, 12));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 240, 60));
        $this->addLeftSystem(new LightParticleBeamShip(3, 2, 1, 180, 360));
        
		$this->addRightSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addRightSystem(new Hangar(5, 12));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 300, 120));
        $this->addRightSystem(new LightParticleBeamShip(3, 2, 1, 0, 180));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 4, 42));
        $this->addRightSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 5, 40));
	    
	    
		//d20 hit chart
		$this->hitChart = array(
			
			0=> array(
				6 => "Structure",
				8 => "Cargo Bay",
				10 => "Scanner",
				13 => "Engine",
				15 => "Jump Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				10 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				9 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				8 => "Light Particle Beam",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				8 => "Light Particle Beam",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}
?>
