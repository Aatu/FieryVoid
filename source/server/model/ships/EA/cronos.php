<?php
class Cronos extends HeavyCombatVessel{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
        $this->pointCost = 800;
        $this->faction = "EA";
        $this->phpclass = "Cronos";
        $this->imagePath = "img/ships/cronos.png";
        $this->shipClass = "Cronos Attack Frigate";
        
        $this->forwardDefense = 14;
        $this->sideDefense = 16;
        
        $this->turncost = 0.66;
        $this->turndelaycost = 0.66;
        $this->accelcost = 2;
        $this->rollcost = 3;
        $this->pivotcost = 3;
        $this->iniativebonus = 30;

	$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
	$this->addPrimarySystem(new CnC(5, 12, 0, 0));
	$this->addPrimarySystem(new Scanner(5, 18, 5, 8));
	$this->addPrimarySystem(new Engine(5, 18, 0, 9, 2));
	$this->addPrimarySystem(new Hangar(5, 2));
	$this->addPrimarySystem(new Thruster(5, 15, 0, 5, 3));
	$this->addPrimarySystem(new Thruster(5, 15, 0, 5, 4));
	$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 180, 0));
	$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 180, 0));
	$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 0, 180));
	$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 0, 180));
	  
	$this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
	$this->addFrontSystem(new Thruster(5, 10, 0, 3, 1));
	$this->addFrontSystem(new HeavyPulse(5, 6, 4, 240, 0));	
	$this->addFrontSystem(new HeavyPulse(5, 6, 4, 0, 120));
	$this->addFrontSystem(new Railgun(4, 9, 6, 240, 0));	
	$this->addFrontSystem(new Railgun(4, 9, 6, 0, 120));
	$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 240, 60));
	$this->addFrontSystem(new InterceptorMkII(2, 4, 2, 300, 120));

	$this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
	$this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
	$this->addAftSystem(new Thruster(5, 9, 0, 3, 2));
	$this->addAftSystem(new HeavyPulse(5, 6, 4, 120, 240));
	$this->addAftSystem(new Railgun(4, 9, 6, 180, 300));        
	$this->addAftSystem(new Railgun(4, 9, 6, 60, 180));
	$this->addAftSystem(new InterceptorMkII(2, 4, 2, 120, 300));
	$this->addAftSystem(new InterceptorMkII(2, 4, 2, 60, 240));
 
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure(5, 48));
        $this->addAftSystem(new Structure(5, 48));
        $this->addPrimarySystem(new Structure(5, 50));
    }
}
?>
