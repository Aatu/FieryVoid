<?php
class Capit extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 210;
  		$this->faction = "Civilians";
        $this->phpclass = "Capit";
        $this->imagePath = "img/ships/centauriArmusCapit.png"; 
        $this->shipClass = "Capit Priority Freighter";
        $this->shipSizeClass = 3;
        $this->fighters = array("cargoshuttles"=>3);
		$this->canvasSize = 175;		
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup



		$this->isd = 1970;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 5;
        $this->iniativebonus = -5;
         
        $this->addPrimarySystem(new Reactor(3, 8, 0, 0));
        $this->addPrimarySystem(new CnC(3, 9, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 10, 2, 3));
        $this->addPrimarySystem(new Hangar(3, 3, 1));
		$this->addPrimarySystem(new JumpEngine(4, 12, 3, 40));
		
        $this->addFrontSystem(new Thruster(3, 6, 0, 1, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 1, 1));		
		$this->addFrontSystem(new CargoBay(2, 15));
		$this->addFrontSystem(new CargoBay(2, 15));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 120));
         
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new CargoBay(2, 15));
        $this->addAftSystem(new CargoBay(2, 15));
        $this->addAftSystem(new Engine(3, 10, 0, 6, 3));
         
        $this->addLeftSystem(new Thruster(3, 13, 0, 3, 3));
        $this->addLeftSystem(new CargoBay(2, 15));		
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		
		$this->addRightSystem(new Thruster(3, 13, 0, 3, 4));
         $this->addRightSystem(new CargoBay(2, 15));       
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 32));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 3, 30));
        $this->addRightSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 3, 36));
        
        $this->hitChart = array(
        		0=> array(
        				10 => "Structure",
        				12 => "Scanner",
        				14 => "Jump Engine",
        				18 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				7 => "Light Particle Beam",
        				10 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Engine",
        				10 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				7 => "Light Particle Beam",
        				9 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				7 => "Light Particle Beam",
        				9 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
