<?php
class Aurillia extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 575;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Aurillia";
        $this->imagePath = "img/ships/astur.png"; 
		$this->canvasSize = 200; 
        $this->shipSizeClass = 3;
        $this->shipClass = "Aurillia Cruiser";
        $this->variantOf = "Astur Assault Ship";   
		$this->occurence = "uncommon";
        //$this->limited = 33;
	    $this->isd = 1872;
		

        $this->fighters = array("heavy"=>6); 
		
        $this->forwardDefense = 17;
        $this->sideDefense = 16;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 3;
                 
        $this->addPrimarySystem(new Reactor(5, 30, 0, 0));
        $this->addPrimarySystem(new CnC(5, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 4, 7));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
		$this->addPrimarySystem(new JumpEngine(5, 20, 3, 20));
		$this->addPrimarySystem(new Hangar(5, 9));
        
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new TacLaser(3, 5, 4, 270, 90));
        $this->addFrontSystem(new TacLaser(3, 5, 4, 270, 90));
        
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));    
        $this->addAftSystem(new TacLaser(2, 5, 4, 90, 270));
        
		$this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addLeftSystem(new Hangar(5, 12));
        $this->addLeftSystem(new ImperialLaser(3, 8, 5, 300, 60));
        $this->addLeftSystem(new ParticleProjector(3, 6, 1, 240, 60));
        $this->addLeftSystem(new ParticleProjector(2, 6, 1, 180, 360));
        
		$this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
		$this->addRightSystem(new Hangar(5, 12));
        $this->addRightSystem(new ImperialLaser(3, 8, 5, 300, 60));
        $this->addRightSystem(new ParticleProjector(3, 6, 1, 300, 120));
        $this->addRightSystem(new ParticleProjector(2, 6, 1, 0, 180));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 50));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 4, 42));
        $this->addRightSystem(new Structure( 4, 42));
        $this->addPrimarySystem(new Structure( 5, 40));
	    
	    
		//d20 hit chart
		$this->hitChart = array(			
			0=> array(
				7 => "Structure",
				10 => "Scanner",
				12 => "Engine",
				14 => "Jump Engine",
				16 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "Thruster",
				9 => "Tactical Laser",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				9 => "Tactical Laser",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				5 => "Thruster",
				8 => "Particle Projector",
				10 => "Imperial Laser",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				5 => "Thruster",
				8 => "Particle Projector",
				10 => "Imperial Laser",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}
?>
