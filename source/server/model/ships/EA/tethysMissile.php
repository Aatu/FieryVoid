<?php
class TethysMissile extends MediumShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 375;
		$this->faction = "EA";
		$this->phpclass = "TethysMissile";
		$this->imagePath = "img/ships/tethys.png";
		$this->shipClass = "Tethys Missile Boat (Zeta)";
		$this->canvasSize = 100;
		$this->occurence = "rare";
	        $this->variantOf = 'Tethys Police Cutter (Kappa)';
	        $this->isd = 2212;

		$this->forwardDefense = 13;
		$this->sideDefense = 13;

		$this->turncost = 0.33;
		$this->turndelaycost = 0.5;
		$this->accelcost = 2;
		$this->rollcost = 1;
		$this->pivotcost = 1;
		$this->iniativebonus = 60;

		$this->addPrimarySystem(new Reactor(4, 13, 0, 3));
		$this->addPrimarySystem(new CnC(4, 9, 0, 0));
		$this->addPrimarySystem(new Scanner(4, 10, 3, 5));
		$this->addPrimarySystem(new Engine(4, 11, 0, 8, 3));
		$this->addPrimarySystem(new Hangar(4, 2));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 3));
		$this->addPrimarySystem(new Thruster(3, 13, 0, 4, 4));
		
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new Thruster(3, 7, 0, 3, 1));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 180, 0));
        $this->addFrontSystem(new StdParticleBeam(2, 4, 1, 270, 90));
        $this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
        $this->addFrontSystem(new SMissileRack(3, 6, 0, 0, 180));
		
        $this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
        $this->addAftSystem(new Thruster(4, 8, 0, 4, 2));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 180, 0));
        $this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
        $this->addAftSystem(new StdParticleBeam(2, 4, 1, 0, 180));
        $this->addAftSystem(new SMissileRack(2, 6, 0, 90, 270));
	
        $this->addPrimarySystem(new Structure( 4, 38));
		
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Thruster",
                        11 => "Scanner",
                        14 => "Engine",
                        16 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        8 => "Class-S Missile Rack",
                        9 => "Standard Particle Beam",
                        11 => "Interceptor I",
                        17 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        9 => "Class-S Missile Rack",
                        11 => "Standard Particle Beam",
                        12 => "Interceptor I",
                        17 => "Structure",
                        20 => "Primary",
                ),
        );
    }

}
?>
