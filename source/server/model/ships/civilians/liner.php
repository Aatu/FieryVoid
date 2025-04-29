<?php
class Liner extends BaseShip{
    //I'm implimenting this one without a hit chart because we will need a subfunction to pass hits to primary for the cargo bay
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 160;
        $this->faction = "Civilians";
        $this->phpclass = "Liner";
        $this->imagePath = "img/ships/LuxuryLiner1.png";
        $this->shipClass = "Luxury Liner";
        $this->shipSizeClass = 3;
	    $this->isCombatUnit = false; //not a combat unit, it will never be present in a regular battlegroup
        $this->isd = 2247;
        
        $this->forwardDefense = 19;
        $this->sideDefense = 19;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 6;
        $this->iniativebonus = -20;
         
        $this->addPrimarySystem(new Reactor(3, 9, 0, 0));
        $this->addPrimarySystem(new CnC(3, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 2, 3));
        $this->addPrimarySystem(new Engine(3, 6, 0, 6, 4));
        $this->addPrimarySystem(new Hangar(3, 8));
  
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 120));
		$this->addFrontSystem(new CargoBay(3, 16));
        $this->addFrontSystem(new CargoBay(3, 16));
        
        $this->addRightSystem(new Thruster(3, 10, 0, 3, 4));
        $this->addRightSystem(new CargoBay(3, 16));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        
        $this->addLeftSystem(new Thruster(3, 10, 0, 3, 3));
        $this->addLeftSystem(new CargoBay(3, 16));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new Thruster(3, 9, 0, 2, 2));
        $this->addAftSystem(new CargoBay(3, 16));
        $this->addAftSystem(new CargoBay(3, 16));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 36));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 3, 36));
        $this->addRightSystem(new Structure( 3, 36));
        $this->addPrimarySystem(new Structure( 5, 40));       
        
        $this->hitChart = array(
        		0=> array(
        				10 => "Structure",
        				12 => "Engine",
        				14 => "Scanner",
        				16 => "Hangar",
        				18 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Standard Particle Beam",
        				12 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Standard Particle Beam",
        				13 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				5 => "Thruster",
        				7 => "Standard Particle Beam",
        				12 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				5 => "Thruster",
        				7 => "Standard Particle Beam",
        				12 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
