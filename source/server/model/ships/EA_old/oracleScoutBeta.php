<?php
class OracleScoutBeta extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 475;
	$this->faction = "EA (early)";
        $this->phpclass = "OracleScoutBeta";
        $this->imagePath = "img/ships/oracle.png";
        $this->shipClass = "Oracle Armed Explorer (Beta)";
			$this->occurence = "common";
	        $this->variantOf = "Oracle Explorer (Alpha)";
			$this->limited = 33;
		$this->unofficial = true;
        $this->shipSizeClass = 3;
	    
		$this->isd = 2168;
        
        $this->forwardDefense = 13;
        $this->sideDefense = 16;
        
        $this->turncost = 1;
        $this->turndelaycost = 1;
        $this->accelcost = 2;
        $this->rollcost = 2;
        $this->pivotcost = 2;
        $this->iniativebonus = 0;
		
        $this->addPrimarySystem(new Reactor(4, 16, 0, 0));
        $this->addPrimarySystem(new CnC(4, 20, 0, 0));
        $this->addPrimarySystem(new ElintScanner(4, 16, 4, 5));
        $this->addPrimarySystem(new Engine(4, 12, 0, 6, 3));
        $this->addPrimarySystem(new JumpEngine(4, 20, 4, 30));
		$this->addPrimarySystem(new Hangar(4, 3));
		$this->addPrimarySystem(new SoMissileRack(3, 6, 0, 0, 360));

		$this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
		$this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));
		$this->addFrontSystem(new MediumPlasma(3, 5, 3, 300, 60));
		$this->addFrontSystem(new InterceptorPrototype(2, 4, 1, 270, 90));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 300, 60));

		$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 240));
		$this->addAftSystem(new InterceptorPrototype(2, 4, 1, 90, 270));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 120, 240));

		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new LightParticleBeamShip(2, 2, 1, 180, 360));
		$this->addLeftSystem(new CargoBay(2, 24));

		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
		$this->addRightSystem(new LightParticleBeamShip(2, 2, 1, 0, 180));
		$this->addRightSystem(new CargoBay(2, 24));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 4, 48));
        $this->addAftSystem(new Structure( 4, 42));
        $this->addLeftSystem(new Structure( 4, 55));
        $this->addRightSystem(new Structure( 4, 55));
        $this->addPrimarySystem(new Structure( 4, 48));
		
	
		$this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        11 => "Jump Engine",
                        12 => "Class-SO Missile Rack",
                        14 => "ELINT Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        4 => "Thruster",
                        7 => "Light Particle Beam",
                        9 => "Medium Plasma Cannon",
						11 => "Interceptor Prototype",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        6 => "Thruster",
                        8 => "Light Particle Beam",
						11 => "Interceptor Prototype",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        4 => "Thruster",
                        6 => "Cargo Bay",
						8 => "Light Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        4 => "Thruster",
                        6 => "Cargo Bay",
						8 => "Light Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
