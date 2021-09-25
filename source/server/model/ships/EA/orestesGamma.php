<?php
class OrestesGamma extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
		$this->pointCost = 550;
		$this->faction = 'EA';//"EA defenses";
		$this->phpclass = "OrestesGamma";
		$this->imagePath = "img/ships/orestes.png";
		$this->shipClass = "Orestes System Monitor (Gamma)";
		$this->shipSizeClass = 3;
		$this->fighters = array("normal"=>12);
	    
	        $this->variantOf = "Orestes System Monitor (Epsilon)";
	        $this->isd = 2219;

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
		$this->addPrimarySystem(new Scanner(5, 14, 3, 5));
		$this->addPrimarySystem(new Engine(5, 11, 0, 5, 4));
		$this->addPrimarySystem(new Hangar(5, 14));
		$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 0, 360));
		$this->addPrimarySystem(new StdParticleBeam(3, 4, 1, 0, 360));

		$this->addFrontSystem(new Thruster(3, 20, 0, 5, 1));
		$this->addFrontSystem(new RailGun(4, 9, 6, 180, 60));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		$this->addFrontSystem(new InterceptorMkI(2, 4, 1, 270, 90));
		$this->addFrontSystem(new HeavyPlasma(3, 8, 5, 300, 60));
		$this->addFrontSystem(new RailGun(4, 9, 6, 300, 180));

		$this->addAftSystem(new Thruster(4, 20, 0, 5, 2));
		$this->addAftSystem(new ParticleCannon(3, 8, 7, 120, 240));
		$this->addAftSystem(new InterceptorMkI(2, 4, 1, 90, 270));
		$this->addAftSystem(new ParticleCannon(3, 8, 7, 120, 240));

		$this->addLeftSystem(new Thruster(3, 15, 0, 3, 3));
		$this->addLeftSystem(new MediumPlasma(3, 6, 3, 180, 0));
		$this->addLeftSystem(new MediumPlasma(3, 6, 3, 180, 0));

		$this->addRightSystem(new Thruster(3, 15, 0, 3, 4));
		$this->addRightSystem(new MediumPlasma(3, 6, 3, 0, 180));
		$this->addRightSystem(new MediumPlasma(3, 6, 3, 0, 180));
        
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
                        6 => "Heavy Plasma Cannon",
                        9 => "Railgun",
                        11 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Particle Cannon",
                        10 => "Interceptor I",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        9 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        9 => "Medium Plasma Cannon",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );

    }
}
?>
