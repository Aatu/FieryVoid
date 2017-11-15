<?php
class Ore extends BaseShip{
    //I'm implimenting this one without a hit chart because we will need a subfunction to pass hits to primary for the cargo bay
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 250;
        $this->faction = "Civilian";
        $this->phpclass = "Ore Barge";
        $this->imagePath = "img/ships/battlewagon.png"; //need to change
        $this->shipClass = "Ore Barge";
        $this->shipSizeClass = 3;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 3;
        $this->iniativebonus = -40;
         
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(4, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 12, 2, 3));
        $this->addPrimarySystem(new Engine(3, 21, 0, 6, 3));
        $this->addPrimarySystem(new Hangar(3, 6));
        $this->addPrimarySystem(new CargoBay(2, 360));
  
        $this->addFrontSystem(new Thruster(3, 17, 0, 3, 1));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));

        $this->addRightSystem(new Thruster(3, 13, 0, 4, 4));
        
        $this->addLeftSystem(new Thruster(3, 13, 0, 4, 3));
        
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 300));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 180));
        $this->addAftSystem(new Thruster(3, 21, 0, 6, 2));
                
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 36));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 3, 36));
        $this->addRightSystem(new Structure( 3, 36));
        $this->addPrimarySystem(new Structure( 5, 40));       
        
        $this->hitChart = array(
        		0=> array(
        				4 => "Structure",
        				12 => "Cargo Bay",
        				14 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				6 => "Thruster",
        				8 => "Standard Particle Beam",
        				12 => "0:Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Standard Particle Beam",
        				12 => "0:Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				10 => "0:Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				10 => "0:Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}