<?php
class Dragonship extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
  		$this->pointCost = 540;
  		$this->faction = "Raiders";
        $this->phpclass = "Dragonship";
        $this->imagePath = "img/ships/dragonship.png";
        $this->shipClass = "Dragonship";
        $this->shipSizeClass = 3;
        $this->fighters = array("heavy"=>12);
        
		$this->notes = "Generic raider unit.";
		$this->notes .= "<br> ";

		$this->isd = 2245;
        
        $this->forwardDefense = 15;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 3;
   
        $this->addPrimarySystem(new Reactor(5, 25, 0, 0));
        $this->addPrimarySystem(new CnC(5, 12, 0, 0));
        $this->addPrimarySystem(new Scanner(5, 12, 4, 6));
        $this->addPrimarySystem(new Engine(5, 20, 0, 12, 2));
        $this->addPrimarySystem(new Hangar(5, 14));
        $this->addPrimarySystem(new CargoBay(5, 12));
        $this->addPrimarySystem(new JumpEngine(5, 15, 4, 24));
  
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));
        $this->addFrontSystem(new Thruster(4, 15, 0, 4, 1));        
        $this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 60));
        $this->addFrontSystem(new PlasmaAccelerator(4, 10, 5, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 300, 60));
        $this->addFrontSystem(new MediumPulse(4, 6, 3, 300, 60));

        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 16, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 8, 0, 2, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 300));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 240));
        
  		$this->addLeftSystem(new Thruster(4, 15, 0, 5, 3));
		$this->addLeftSystem(new ParticleCannon(3, 8, 7, 180, 360));
		$this->addLeftSystem(new TwinArray(2, 6, 2, 180, 360));
		
  		$this->addRightSystem(new Thruster(4, 15, 0, 5, 4));
  		$this->addRightSystem(new ParticleCannon(3, 8, 7, 0, 180));
  		$this->addRightSystem(new TwinArray(2, 6, 2, 0, 180));
  		
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 4, 52));
        $this->addLeftSystem(new Structure( 5, 52));
        $this->addRightSystem(new Structure( 5, 52));
        $this->addPrimarySystem(new Structure( 5, 55));
        
        $this->hitChart = array(
        		0=> array(
        				7 => "Structure",
        				8 => "Cargo Bay",
        				10 => "Jump Engine",
        				13 => "Scanner",
        				15 => "Engine",
        				17 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
        				6 => "Medium Pulse Cannon",
        				8 => "Plasma Accelerator",
        				10 => "Heavy Plasma Cannon",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				8 => "Thruster",
        				10 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				4 => "Thruster",
        				5 => "Particle Cannon",
        				7 => "Twin Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				4 => "Thruster",
        				5 => "Particle Cannon",
        				7 => "Twin Array",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}
