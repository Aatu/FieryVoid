<?php
class boroca extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 500;
        $this->faction = "Hurr";
        $this->phpclass = "boroca";
        $this->imagePath = "img/ships/hurrBoroca.png";
        $this->shipClass = "Boroca Gunship";
        $this->shipSizeClass = 3;

        $this->isd = 2225;
        
        $this->forwardDefense = 16;
        $this->sideDefense = 17;
        
        $this->turncost = 1.5;
        $this->turndelaycost = 1.5;
        $this->accelcost = 6;
        $this->rollcost = 4;
        $this->pivotcost = 4;
         
        $this->addPrimarySystem(new Reactor(4, 20, 0, 0));
        $this->addPrimarySystem(new CnC(5, 20, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 16, 4, 5));
        $this->addPrimarySystem(new Hangar(2, 2));
   
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 300, 0));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 0, 60));
	$this->addFrontSystem(new SMissileRack(3, 6, 0, 300, 60));
        
	$this->addAftSystem(new Thruster(3, 16, 0, 4, 2));
	$this->addAftSystem(new Engine(4, 14, 0, 4, 5));
       	$this->addAftSystem(new JumpEngine(4, 20, 4, 36));
	$this->addAftSystem(new Engine(4, 14, 0, 4, 5));
	$this->addAftSystem(new Thruster(3, 16, 0, 4, 2));
                
        $this->addLeftSystem(new Thruster(3, 12, 0, 4, 3));
	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 270, 90)); 
	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 270, 90)); 
	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 90, 270)); 
	$this->addLeftSystem(new StdParticleBeam(2, 4, 1, 90, 270));  

        $this->addRightSystem(new Thruster(3, 12, 0, 4, 4));
	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 270, 90)); 
	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 270, 90)); 
	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 90, 270)); 
	$this->addRightSystem(new StdParticleBeam(2, 4, 1, 90, 270)); 
        
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 5, 42));
        $this->addAftSystem(new Structure( 5, 50));
        $this->addLeftSystem(new Structure( 4, 55));
        $this->addRightSystem(new Structure( 4, 55));
        $this->addPrimarySystem(new Structure( 5, 55));
        
        $this->hitChart = array(
        		0=> array(
        				12 => "Structure",
        				15 => "Scanner",
        				16 => "Hangar",
        				19 => "Reactor",
        				20 => "C&C",
        		),
        		1=> array(
        				4 => "Thruster",
                                	8 => "Heavy Plasma Cannon",
                        		11 => "Class-S Missile Rack",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		2=> array(
        				6 => "Thruster",
                        		8 => "Jump Engine",
                        		13 => "Engine",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		3=> array(
        				6 => "Thruster",
                        		10 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        		4=> array(
        				6 => "Thruster",
                        		10 => "Standard Particle Beam",
        				18 => "Structure",
        				20 => "Primary",
        		),
        );
    }
}


?>
