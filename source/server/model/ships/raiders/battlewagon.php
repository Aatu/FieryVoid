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
    }
}