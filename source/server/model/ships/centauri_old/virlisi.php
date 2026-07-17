<?php
class Virlisi extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 280;
  		$this->faction = "Centauri Republic (WotCR)";
        $this->phpclass = "Virlisi";
        $this->imagePath = "img/ships/centauriVirlisi.png"; 
        $this->shipClass = "Virlisi Logistics Ship";
        $this->shipSizeClass = 3;
		$this->canvasSize = 210;		
        
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup

		$this->isd = 1969;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 18;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 5;
        $this->rollcost = 4;
        $this->pivotcost = 4;
        $this->iniativebonus = 0;
         
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 3, 5));
        $this->addPrimarySystem(new Hangar(4, 3, 1));
        $this->addPrimarySystem(new Engine(4, 16, 0, 8, 3));
		
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Thruster(3, 10, 0, 4, 1));
        $this->addFrontSystem(new Hangar(3, 8, 2));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 240, 60));
 		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 120));
        
        $this->addAftSystem(new Thruster(4, 15, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 15, 0, 4, 2));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 60, 240));
 		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 300));

         
        $this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
        $this->addLeftSystem(new CargoBay(2, 66));
        $this->addLeftSystem(new CargoBay(2, 66));		
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		
		$this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
         $this->addRightSystem(new CargoBay(2, 66));  
         $this->addRightSystem(new CargoBay(2, 66));      
        $this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 44));
        $this->addAftSystem(new Structure( 4, 36));
        $this->addLeftSystem(new Structure( 3, 40));
        $this->addRightSystem(new Structure( 3, 40));
        $this->addPrimarySystem(new Structure( 4, 40));
        
        $this->hitChart = array(
        		0=> array(
        				10 => "Structure",
        				12 => "Scanner",
        				14 => "Engine",
					16 => "Hangar",
        				18 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				7 => "Light Particle Beam",
        				10 => "Hangar",
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
        				5 => "Light Particle Beam",
        				12 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				5 => "Light Particle Beam",
        				12 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
