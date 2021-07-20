<?php
class OracleScoutAlpha extends BaseShip{
    
    function __construct($id, $userid, $name,  $slot){
        parent::__construct($id, $userid, $name,  $slot);
        
	$this->pointCost = 425;
	$this->faction = "EA (early)";
        $this->phpclass = "OracleScoutAlpha";
        $this->imagePath = "img/ships/oracle.png";
        $this->shipClass = "Oracle Explorer (Alpha)";
//			$this->occurence = "common";
//	        $this->variantOf = "Oracle Scout Cruiser (Gamma)";
			$this->limited = 10;
		$this->unofficial = true;
        $this->shipSizeClass = 3;
	    
		$this->isd = 2163;
        
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
		$this->addPrimarySystem(new EWOMissileRack(3, 6, 0, 0, 360));

		$this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
		$this->addFrontSystem(new Thruster(3, 8, 0, 2, 1));
		$this->addFrontSystem(new LightParticleBeamShip(2, 2, 1, 270, 90));
		$this->addFrontSystem(new CargoBay(2, 24));

		$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
		$this->addAftSystem(new Thruster(3, 10, 0, 3, 2));
		$this->addAftSystem(new LightParticleBeamShip(2, 2, 1, 90, 270));

		$this->addLeftSystem(new Thruster(3, 15, 0, 5, 3));
		$this->addLeftSystem(new CargoBay(2, 24));

		$this->addRightSystem(new Thruster(3, 15, 0, 5, 4));
		$this->addRightSystem(new CargoBay(2, 24));
        
        //0:primary, 1:front, 2:rear, 3:left, 4:right;
        $this->addFrontSystem(new Structure( 3, 48));
        $this->addAftSystem(new Structure( 3, 42));
        $this->addLeftSystem(new Structure( 3, 55));
        $this->addRightSystem(new Structure( 3, 55));
        $this->addPrimarySystem(new Structure( 4, 48));
		
	
		$this->hitChart = array(
                0=> array(
                        10 => "Structure",
                        11 => "Jump Engine",
                        12 => "Class-O Missile Rack",
                        14 => "ELINT Scanner",
                        16 => "Engine",
                        17 => "Hangar",
                        19 => "Reactor",
                        20 => "C&C",
                ),
                1=> array(
                        6 => "Thruster",
                        7 => "Light Particle Beam",
                        10 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                2=> array(
                        8 => "Thruster",
                        10 => "Light Particle Beam",
                        18 => "Structure",
                        20 => "Primary",
                ),
                3=> array(
                        5 => "Thruster",
                        8 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
                4=> array(
                        5 => "Thruster",
                        8 => "Cargo Bay",
                        18 => "Structure",
                        20 => "Primary",
                ),
        );
    }
}
