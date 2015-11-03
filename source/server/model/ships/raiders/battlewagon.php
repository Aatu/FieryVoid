<?php
class Battlewagon extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  $this->pointCost = 540;
  $this->faction = "Raiders";
        $this->phpclass = "Battlewagon";
        $this->imagePath = "img/ships/battlewagon.png";
        $this->shipClass = "Battlewagon";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>24);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1.33;
        $this->turndelaycost = 1.33;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 3;
  
        
         
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 18, 3, 6));
        $this->addPrimarySystem(new Engine(5, 16, 0, 8, 3));
        $this->addPrimarySystem(new Hangar(4, 26));
        $this->addPrimarySystem(new CargoBay(4, 18));
  
        $this->addFrontSystem(new Thruster(4, 32, 0, 6, 1));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 300, 60));
        $this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
        $this->addFrontSystem(new MediumPulse(3, 6, 3, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 360));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));

        $this->addAftSystem(new JumpEngine(4, 16, 3, 24));
  $this->addAftSystem(new TwinArray(2, 6, 2, 120, 300));
        $this->addAftSystem(new TwinArray(2, 6, 2, 60, 240));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 2, 2));
        
  $this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
        $this->addLeftSystem(new StdParticleBeam(2, 4, 1, 180, 360));
  
  $this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addRightSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 56));
        $this->addAftSystem(new Structure( 4, 48));
        $this->addLeftSystem(new Structure( 4, 52));
        $this->addRightSystem(new Structure( 4, 52));
        $this->addPrimarySystem(new Structure( 5, 50));
        
        $this->hitChart = array(
        		0=> array(
        				1 => "structure",
        				2 => "structure",
        				3 => "structure",
        				4 => "structure",
        				5 => "structure",
        				6 => "structure",
        				7 => "structure",
        				8 => "cargoBay",
        				9 => "jumpEngine",
        				10 => "jumpEngine",
        				11 => "scanner",
        				12 => "scanner",
        				13 => "scanner",
        				14 => "engine",
        				15 => "engine",
        				16 => "hanger",
        				17 => "hanger",
        				18 => "reactor",
        				19 => "reactor",
        				20 => "CnC",
        		),
        		1=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "mediumPulse",
        				6 => "mediumPulse",
        				7 => "mediumLaser",
        				8 => "stdParticleBeam",
        				9 => "stdParticleBeam",
        				10 => "stdParticleBeam",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        		2=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "thruster",
        				6 => "thruster",
        				7 => "twinArray",
        				8 => "twinArray",
        				9 => "structure",
        				10 => "structure",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        		3=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "stdParticleBeam",
        				6 => "stdParticleBeam",
        				7 => "stdParticleBeam",
        				8 => "structure",
        				9 => "structure",
        				10 => "structure",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        		4=> array(
        				1 => "thruster",
        				2 => "thruster",
        				3 => "thruster",
        				4 => "thruster",
        				5 => "stdParticleBeam",
        				6 => "stdParticleBeam",
        				7 => "stdParticleBeam",
        				8 => "structure",
        				9 => "structure",
        				10 => "structure",
        				11 => "structure",
        				12 => "structure",
        				13 => "structure",
        				14 => "structure",
        				15 => "structure",
        				16 => "structure",
        				17 => "structure",
        				18 => "structure",
        				19 => "primary",
        				20 => "primary",
        		),
        );
    }
}