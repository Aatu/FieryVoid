<?php
class ErlorraEarlyPods extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 600;
        $this->faction = "Llort";
        $this->phpclass = "ErlorraEarlyPods";
        $this->imagePath = "img/ships/LlortErlorra.png";
        $this->shipClass = "Erlorra Early Cruiser (w/Cargo Pods)";
        $this->shipSizeClass = 3;
        $this->fighters = array("normal"=>12);
     
        $this->occurence = "common";
        $this->variantOf = 'Erlorra Raiding Cruiser';
        $this->isd = 2215;
   
        $this->forwardDefense = 17;
        $this->sideDefense = 17;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 2;
         
        $this->addPrimarySystem(new Reactor(5, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 13, 5, 7));
        $this->addPrimarySystem(new Engine(5, 20, 0, 11, 3));
        $this->addPrimarySystem(new Hangar(5, 14,12));
        $this->addPrimarySystem(new JumpEngine(5, 15, 5, 20));
  
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
		$this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
        $this->addFrontSystem(new ParticleCannon(4, 8, 7, 240, 30));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
		$this->addFrontSystem(new HeavyPlasma(4, 8, 5, 0, 120));
        
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 360));
		$this->addAftSystem(new HeavyPlasma(4, 8, 5, 120, 240));
		$this->addAftSystem(new LightParticleBeamShip(1, 2, 1, 90, 270));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 6, 0, 2, 2));

        $this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
        $this->addLeftSystem(new ParticleCannon(4, 8, 7, 180, 360));
        $this->addLeftSystem(new ScatterGun(3, 8, 3, 180, 360));
  
        $this->addRightSystem(new Thruster(4, 13, 0, 4, 4));
        $this->addRightSystem(new CargoBay(5, 20));
        $this->addRightSystem(new CargoBay(5, 20));
        $this->addRightSystem(new CargoBay(5, 20));
        $this->addRightSystem(new CargoBay(5, 20));
        $this->addRightSystem(new CargoBay(5, 20));
        $this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 44));
        $this->addAftSystem(new Structure( 5, 48));
        $this->addLeftSystem(new Structure( 5, 56));
        $this->addRightSystem(new Structure( 5, 64));
        $this->addPrimarySystem(new Structure( 5, 50));
        
        $this->hitChart = array(
        		0=> array(
        				8 => "Structure",
        				11 => "Jump Engine",
        				13 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				5 => "Thruster",
        				7 => "Particle Cannon",
        				8 => "Standard Particle Beam",
        				10 => "Heavy Plasma Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
        				8 => "Heavy Plasma Cannon",
        				10 => "Standard Particle Beam",
        				11 => "Light Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				5 => "Thruster",
        				7 => "Particle Cannon",
					9 => "Scattergun",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
        				8 => "Twin Array",
        				11 => "Cargo Bay",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
