<?php
class Pillager extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 500;
  		$this->faction = "Raiders";
        $this->phpclass = "Pillager";
        $this->imagePath = "img/ships/battlewagon.png"; //needs to be changed
        $this->shipClass = "Pillager Advanced Cruiser";
        $this->shipSizeClass = 3;
        $this->fighters = array("Normal"=>12);
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1.5;
        $this->accelcost = 3;
        $this->rollcost = 1;
        $this->pivotcost = 5;
        $this->limited = 10;
   
        $this->addPrimarySystem(new Reactor(4, 16, 0, 3));
        $this->addPrimarySystem(new CnC(5, 15, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 4, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(3, 13));
        $this->addPrimarySystem(new JumpEngine(4, 16, 4, 25));
  
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new Thruster(3, 6, 0, 2, 1));
        $this->addFrontSystem(new LightParticleCannon(2, 6, 5, 240, 60));
        $this->addFrontSystem(new GaussCannon(2, 10, 4, 240, 120));
        $this->addFrontSystem(new LightParticleCannon(2, 6, 5, 300, 120));
        
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
		$this->addAftSystem(new GaussCannon(2, 10, 4, 120, 240));
		$this->addAftSystem(new Engine(4, 14, 0, 9, 3));
		$this->addAftSystem(new CargoBay(2, 15));
		$this->addAftSystem(new CargoBay(2, 15));
		
  		$this->addLeftSystem(new Thruster(3, 15, 0, 4, 3));
		$this->addLeftSystem(new LightParticleCannon(2, 6, 5, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		
  		$this->addRightSystem(new Thruster(3, 15, 0, 4, 4));
		$this->addLeftSystem(new LightParticleCannon(2, 6, 5, 0, 180));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
  		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 38));
        $this->addAftSystem(new Structure( 3, 36));
        $this->addLeftSystem(new Structure( 3, 30));
        $this->addRightSystem(new Structure( 3, 30));
        $this->addPrimarySystem(new Structure( 4, 36));
        
        $this->hitChart = array(
        		0=> array(
        				9 => "structure",
        				11 => "scanner",
        				13 => "jump engine",
        				16 => "hangar",
        				18 => "reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "thruster",
        				8 => "Light Particle Cannon",
        				10 => "Gauss Cannon",
        				18 => "structure",
        				20 => "primary",
        		),
        		2=> array(
        				6 => "thruster",
        				7 => "Gauss Cannon",
        				9 => "Engine",
        				11 => "Cargo Bay",
        				18 => "structure",
        				20 => "primary",
        		),
        		3=> array(
        				6 => "thruster",
        				8 => "Light Particle Beam",
        				10 => "Light Particle Cannon",
        				18 => "structure",
        				20 => "primary",
        		),
        		4=> array(
        				6 => "thruster",
        				8 => "Light Particle Beam",
        				10 => "Light Particle Cannon",
        				18 => "structure",
        				20 => "primary",
        		),
        );
    }
}