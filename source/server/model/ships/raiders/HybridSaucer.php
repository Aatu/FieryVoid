<?php
class HybridSaucer extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 215; //225 full cost if/when Hybrid Drive added
        $this->faction = "Raiders";
        $this->phpclass = "HybridSaucer";
        $this->imagePath = "img/ships/VreeVymish.png";
        $this->shipClass = "Raider Hybrid Saucer";
  	    $this->canvasSize = 100;        
	    
	    $this->isd = 2242;

        $this->forwardDefense = 13;
        $this->sideDefense = 13;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 3;
        $this->rollcost = 2;
        $this->pivotcost = 1;
    //    $this->gravitic = true;    
	      $this->unofficial = true; //Hybrid Drive not yet implemented             
        
        $this->iniativebonus = 12 *5;

        
        $this->addPrimarySystem(new Reactor(3, 10, 0, 0));
        $this->addPrimarySystem(new CnC(3, 8, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 8, 6, 6));
		$this->addPrimarySystem(new Hangar(3, 2));
		$this->addPrimarySystem(new CargoBay(3, 36));		
		$this->addPrimarySystem(new Thruster(3, 12, 0, 6, 3));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 6, 4));
        $this->addPrimarySystem(new Thruster(3, 12, 0, 6, 1)); 
        $this->addPrimarySystem(new Thruster(3, 12, 0, 6, 2));                        
        $this->addPrimarySystem(new MediumLaser(2, 6, 5, 0, 360));
		        
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 0));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 0, 120));

        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 300));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 180));
        $this->addAftSystem(new Engine(3, 9, 0, 6, 3));                
       

		//structures
        $this->addPrimarySystem(new Structure(3, 60));
		
		$this->hitChart = array(
			0=> array(
				11 => "Cargo Bay",
				15 => "Scanner",
				17 => "Hangar",
				19 => "Reactor",
				20 => "C&C",
			),
			1=> array(
				6 => "0:Thruster",			
				8 => "Standard Particle Beam",
				9 => "0:Medium Laser",		
				17 => "Structure",
				20 => "Primary",
			),
			2=> array(
				6 => "0:Thruster",			
				8 => "Standard Particle Beam",
				9 => "0:Medium Laser",		
				17 => "Structure",
				20 => "Primary",
			),
		); 
    }
}
