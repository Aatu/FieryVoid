<?php
class OrestesDelta extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 625;
		$this->faction = 'EA';//"EA defenses";
		$this->phpclass = "OrestesDelta";
		$this->imagePath = "img/ships/orestes.png";
		$this->shipClass = "Orestes System Monitor (Delta)";
			$this->unofficial = true;
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>12);
	    
	        $this->variantOf = "Orestes System Monitor (Epsilon)";
	        $this->isd = 2231;

		$this->forwardDefense = 16;
		$this->sideDefense = 16;

		$this->turncost = 1;
		$this->turndelaycost = 1;
		$this->accelcost = 5;
		$this->rollcost = 3;
		$this->pivotcost = 4;

		$this->iniativebonus = -20;

		$this->addPrimarySystem(new Reactor(5, 20, 0, 0));
		$this->addPrimarySystem(new CnC(5, 16, 0, 0));
		$this->addPrimarySystem(new Scanner(5, 16, 3, 6));
		$this->addPrimarySystem(new Engine(5, 11, 0, 5, 4));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 0, 360));

		$this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
		$this->addFrontSystem(new RailGun(4, 9, 6, 180, 60));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
		$this->addFrontSystem(new MediumLaser(3, 6, 5, 300, 60));
		$this->addFrontSystem(new RailGun(4, 9, 6, 300, 180));

		$this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 120, 240));
		$this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
		$this->addAftSystem(new MediumLaser(3, 6, 5, 120, 240));

		$this->addLeftSystem(new Thruster(3, 15, 0, 3, 3));
		$this->addLeftSystem(new MediumLaser(3, 6, 5, 180, 0));
		$this->addLeftSystem(new MediumLaser(3, 6, 5, 180, 0));

		$this->addRightSystem(new Thruster(3, 15, 0, 3, 4));
		$this->addRightSystem(new MediumLaser(3, 6, 5, 0, 180));
		$this->addRightSystem(new MediumLaser(3, 6, 5, 0, 180));
        
        $this->addFrontSystem(new Structure( 5, 52));
        $this->addAftSystem(new Structure( 5, 40));
        $this->addLeftSystem(new Structure( 5, 60));
        $this->addRightSystem(new Structure( 5, 60));
        $this->addPrimarySystem(new Structure( 5, 60));
		
		
		$this->hitChart = array(
                0=> array(
                        9 => "Structure",
                        11 => "Standard Particle Beam",
                        13 => "Scanner",
                        15 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        6 => "Medium Laser",
                        9 => "Railgun",
                        11 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Medium Laser",
                        10 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Medium Laser",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Medium Laser",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
