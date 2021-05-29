<?php
class tfsmonitor extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 800;
  		$this->faction = "Raiders";
        $this->phpclass = "tfsmonitor";
        $this->imagePath = "img/ships/tfs_monitor.png";
        $this->shipClass = "TFS System Monitor";
        $this->limited = 33; //Limited Deployment
		$this->shipSizeClass = 3;
        $this->fighters = array("medium"=>6);

		$this->notes = "Used only by the Tirrith Free State";
        
		$this->isd = 2249;

        $this->forwardDefense = 13;
        $this->sideDefense = 15;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 8;
        $this->rollcost = 4;
        $this->pivotcost = 4;

		$this->iniativebonus = 0;
   
        $this->addPrimarySystem(new Reactor(5, 25, 0, -4));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 13, 4, 8));
        $this->addPrimarySystem(new Engine(5, 11, 0, 8, 2));
        $this->addPrimarySystem(new Hangar(4, 8));
  
        $this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 13, 0, 4, 1));        
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 240, 360));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 240, 60));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 240, 60));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 300, 60));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 120));
        $this->addFrontSystem(new StdParticleBeam(3, 4, 1, 300, 120));
		$this->addFrontSystem(new ParticleCannon(4, 8, 7, 0, 120));

        $this->addAftSystem(new Thruster(4, 13, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 13, 0, 4, 2));        
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 180, 300));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 120, 300));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 120, 300));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 120, 240));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 60, 240));
        $this->addAftSystem(new StdParticleBeam(3, 4, 1, 60, 240));
		$this->addAftSystem(new ParticleCannon(4, 8, 7, 60, 180));
        
  		$this->addLeftSystem(new Thruster(4, 13, 0, 4, 3));
		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 300));
		$this->addLeftSystem(new ParticleCannon(4, 8, 7, 240, 360));
		
  		$this->addRightSystem(new Thruster(4, 13, 0, 4, 4));
		$this->addRightSystem(new ParticleCannon(4, 8, 7, 0, 120));
		$this->addRightSystem(new ParticleCannon(4, 8, 7, 60, 180));
  		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 40));
        $this->addAftSystem(new Structure( 4, 40));
        $this->addLeftSystem(new Structure( 4, 40));
        $this->addRightSystem(new Structure( 4, 40));
        $this->addPrimarySystem(new Structure( 5, 64));
        
        $this->hitChart = array(
        		0=> array(
        				11 => "Structure",
        				13 => "Scanner",
        				16 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				8 => "Particle Cannon",
        				12 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				5 => "Thruster",
        				8 => "Particle Cannon",
        				12 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
        				10 => "Particle Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				10 => "Particle Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
