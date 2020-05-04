<?php
class BAHeavyGunboat extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 475;
        $this->faction = "Small Races";
        $this->phpclass = "BAHeavyGunboat";
        $this->imagePath = "img/ships/BAHeavyGunboat.png";
        $this->shipClass = "BA Heavy Gunboat";
        $this->occurence = "common";
        $this->fighters = array("heavy"=>6); //external racks 

        $this->isd = 2213;
        $this->limited = 33;
        
        $this->forwardDefense = 14;
        $this->sideDefense = 14;
      
        $this->turncost = 0.5;
        $this->turndelaycost = 0.5;
        $this->accelcost = 2;
        $this->rollcost = 1;
        $this->pivotcost = 2;
        $this->iniativebonus = 30;
        
        $this->addPrimarySystem(new Reactor(3, 15, 0, 0));
        $this->addPrimarySystem(new CnC(3, 16, 0, 0));
        $this->addPrimarySystem(new Scanner(3, 14, 4, 7));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
        $this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
	$this->addPrimarySystem(new Hangar(3, 2));
        $this->addPrimarySystem(new LtBlastCannon(3, 4, 1, 240, 60));
        $this->addPrimarySystem(new LtBlastCannon(3, 4, 1, 300, 120));        

        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 8, 0, 3, 1));
	$this->addFrontSystem(new HvyBlastCannon(3, 6, 4, 300, 60));
	$this->addFrontSystem(new MedBlastCannon(3, 5, 2, 270, 90));
        $this->addFrontSystem(new BAInterceptorMkI(3, 4, 1, 270, 90));    
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 180, 60));
	$this->addFrontSystem(new StdParticleBeam(2, 4, 1, 300, 180)); 

        $this->addAftSystem(new Engine(3, 15, 0, 8, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new Thruster(3, 10, 0, 4, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 120, 360));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 240));
        $this->addAftSystem(new BAInterceptorMkI(3, 4, 1, 90, 270));  
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 39));
        $this->addPrimarySystem(new Structure( 4, 36));
        $this->addAftSystem(new Structure( 4, 39));
        
        $this->hitChart = array(
        	0=> array(
        		8 => "Structure",
        		11 => "Thruster",
        		13 => "Light Blast Cannon",
        		15 => "Scanner",
        		16 => "Hangar",
        		19 => "Reactor",
        		20 => "C&C",	
        	),
        	1=> array(
        		6 => "Thruster",
        		8 => "Heavy Blast Cannon",
        		8 => "Medium Blast Cannon",
        		10 => "Standard Particle Beam",
        		11 => "BA Interceptor I",
        		18 => "Structure",
        		20 => "Primary",        			
        	),
        	2=> array(
        		6 => "Thruster",
        		8 => "Standard Particle Beam",
        		10 => "Engine",
        		11 => "BA Interceptor I",
        		18 => "Structure",
        		20 => "Primary",           			
        	),
        );
    }
}
?>
