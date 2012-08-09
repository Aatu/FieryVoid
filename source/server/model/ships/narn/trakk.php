<?php
class Trakk extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 350;
		$this->faction = "Narn";
        $this->phpclass = "Trakk";
        $this->imagePath = "img/ships/trakk.png";
        $this->shipClass = "T'Rakk";
       
        
		
        $this->forwardDefense = 10;
        $this->sideDefense = 16;
        
        $this->turncost = 0.33;
        $this->turndelaycost = 0.50;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        
        $this->iniativebonus = 30;

        
        $this->addPrimarySystem(new Reactor(4, 12, 0, 0));
        $this->addPrimarySystem(new CnC(4, 7, 0, 0));
        $this->addPrimarySystem(new Scanner(4, 12, 3, 6));
        $this->addPrimarySystem(new Engine(4, 8, 0, 6, 3));
		$this->addPrimarySystem(new Hangar(3, 1));
		$this->addPrimarySystem(new Thruster(4, 10, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(4, 10, 0, 4, 4));
        
        //front
        
        
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 300, 0));
        $this->addFrontSystem(new HeavyPlasma(4, 8, 5, 0, 60));
        
        $this->addFrontSystem(new Thruster(4, 10, 0, 4, 1));
    
        
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 240, 120));

		//aft
		          

		
		$this->addAftSystem(new StdParticleBeam(2, 4, 1, 60, 300));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        $this->addAftSystem(new Thruster(4, 9, 0, 3, 2));
        
		
     
        
		
		//structures
        $this->addFrontSystem(new Structure(4, 36));
        $this->addAftSystem(new Structure(4, 40));
        $this->addPrimarySystem(new Structure(4, 36));
        
    }

}



?>
