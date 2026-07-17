<?php
class TlosEarly extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 240;
		$this->faction = "Civilians";
        $this->phpclass = "TlosEarly";
        $this->imagePath = "img/ships/NarnTlosEarly.png";
        $this->shipClass = "Narn T'los Bulk Freighter";
		$this->canvasSize = 200; //img has 125px per side
		$this->fighters = array("cargo shuttles"=>10);         
	    $this->isd = 2223;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

		
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 4;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        
        $this->addPrimarySystem(new Reactor(5, 14, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 3, 5));
        $this->addPrimarySystem(new Engine(5, 14, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(5, 10));
        $this->addPrimarySystem(new Quarters(4, 9));
        $this->addPrimarySystem(new Quarters(4, 9));

        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));        
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1));
        $this->addFrontSystem(new Thruster(4, 10, 0, 3, 1)); 
        $this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
			
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));
		$this->addAftSystem(new JumpEngine(3, 15, 3, 32));
        $this->addAftSystem(new Thruster(4,9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 2, 2));
		
        $this->addLeftSystem(new Thruster(4, 15, 0, 4, 3));
        $this->addLeftSystem(new CargoBay(2, 45));        
        $this->addLeftSystem(new CargoBay(2, 45));        
        $this->addLeftSystem(new CargoBay(2, 45));        
             			  
		$this->addRightSystem(new Thruster(4, 15, 0, 4, 4));
        $this->addRightSystem(new CargoBay(2, 45));        
        $this->addRightSystem(new CargoBay(2, 45));        
        $this->addRightSystem(new CargoBay(2, 45));        

		//structures
        $this->addFrontSystem(new Structure(4, 44));
        $this->addAftSystem(new Structure(4, 48));
        $this->addLeftSystem(new Structure(4, 58));
        $this->addRightSystem(new Structure(4, 58));
        $this->addPrimarySystem(new Structure(5, 45));
		
		$this->hitChart = array(
			0=> array(
				9 => "Structure",
				11 => "Quarters",
				13 => "Scanner",
				15 => "Engine",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				5 => "Thruster",
				7 => "Light Particle Beam",
				10 => "Cargo Bay A-D",
				18 => "Structure",
				20 => "Primary",
			),
			2=> array(
				10 => "Thruster",
				12 => "Light Particle Beam",
				18 => "Structure",
				20 => "Primary",
			),
			3=> array(
				4 => "Thruster",
				9 => "Cargo Bay A-C",
				18 => "Structure",
				20 => "Primary",
			),
			4=> array(
				4 => "Thruster",
				9 => "Cargo Bay D-F",
				18 => "Structure",
				20 => "Primary",
			),
		);        
    }	
}



?>
