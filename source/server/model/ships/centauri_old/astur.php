<?php
class Astur extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 430;
        $this->faction = "Centauri (WotCR)";
        $this->phpclass = "Astur";
        $this->imagePath = "img/ships/astur.png"; 
		$this->canvasSize = 200; 
        $this->shipSizeClass = 3;
        $this->shipClass = "Astur Assault Ship";
        //$this->limited = 33;
	    $this->isd = 1860;

        $this->fighters = array("assault shuttles"=>24);
		
        $this->forwardDefense = 17;
        $this->sideDefense = 16;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 4;
        $this->rollcost = 4;
        $this->pivotcost = 3;
                 
        $this->addPrimarySystem(new Reactor(5, 23, 0, 0));
        $this->addPrimarySystem(new CnC(5, 25, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 16, 3, 5));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 4));
		$this->addPrimarySystem(new JumpEngine(5, 20, 3, 20));
		$this->addPrimarySystem(new Hangar(5, 5));
		$this->addPrimarySystem(new CargoBay(4, 16));
        
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
        $this->addFrontSystem(new ParticleProjector(2, 6, 1, 270, 90));
        
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 15, 0, 4, 2));    
        $this->addAftSystem(new ParticleProjector(2, 6, 1, 90, 270));
        
		$this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
		$this->addLeftSystem(new Hangar(5, 12));
        $this->addLeftSystem(new HeavyPlasma(3, 8, 5, 240, 360));
        $this->addLeftSystem(new ParticleProjector(2, 6, 1, 180, 360));
        
		$this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
		$this->addRightSystem(new Hangar(5, 12));
        $this->addRightSystem(new HeavyPlasma(3, 8, 5, 0, 120));
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
				7 => "Particle Projector",
				10 => "Heavy Plasma Cannon",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "Thruster",
				9 => "Particle Projector",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				6 => "Particle Projector",
				8 => "Heavy Plasma Cannon",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				6 => "Particle Projector",
				8 => "Heavy Plasma Cannon",
				12 => "Hangar",
				18 => "Structure",
				20 => "Primary",
			),
		);
		
    }
}
?>
